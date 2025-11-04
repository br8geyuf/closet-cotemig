<?php

namespace App\Patterns\Observer;

use Illuminate\Support\Facades\Log;

/**
 * Subject do padrão Observer
 * 
 * Gerencia a lista de observadores e notifica sobre eventos.
 * Este padrão permite desacoplar ações de suas reações,
 * facilitando a adição de novas funcionalidades sem modificar código existente.
 * 
 * Benefícios:
 * - Desacoplamento: Separação entre ações e reações
 * - Escalabilidade: Fácil adição de novos observadores
 * - Flexibilidade: Observadores podem ser ativados/desativados dinamicamente
 * - Manutenibilidade: Lógica de notificação organizada
 */
class EventSubject
{
    /**
     * Lista de observadores registrados
     */
    private array $observers = [];

    /**
     * Histórico de eventos disparados
     */
    private array $eventHistory = [];

    /**
     * Configurações do subject
     */
    private array $config = [
        'log_events' => true,
        'max_history' => 100,
        'async_notifications' => false,
    ];

    /**
     * Registra um observador
     * 
     * @param ObserverInterface $observer
     * @return void
     */
    public function attach(ObserverInterface $observer): void
    {
        $observerName = $observer->getName();
        
        if (!isset($this->observers[$observerName])) {
            $this->observers[$observerName] = $observer;
            
            Log::info('EventSubject: Observador registrado', [
                'observer' => $observerName,
                'interested_events' => $observer->getInterestedEvents()
            ]);
        }
    }

    /**
     * Remove um observador
     * 
     * @param ObserverInterface $observer
     * @return void
     */
    public function detach(ObserverInterface $observer): void
    {
        $observerName = $observer->getName();
        
        if (isset($this->observers[$observerName])) {
            unset($this->observers[$observerName]);
            
            Log::info('EventSubject: Observador removido', [
                'observer' => $observerName
            ]);
        }
    }

    /**
     * Remove um observador pelo nome
     * 
     * @param string $observerName
     * @return void
     */
    public function detachByName(string $observerName): void
    {
        if (isset($this->observers[$observerName])) {
            unset($this->observers[$observerName]);
            
            Log::info('EventSubject: Observador removido por nome', [
                'observer' => $observerName
            ]);
        }
    }

    /**
     * Notifica todos os observadores interessados sobre um evento
     * 
     * @param string $event Nome do evento
     * @param array $data Dados do evento
     * @return void
     */
    public function notify(string $event, array $data = []): void
    {
        $eventData = [
            'event' => $event,
            'data' => $data,
            'timestamp' => now(),
            'notified_observers' => []
        ];

        if ($this->config['log_events']) {
            Log::info('EventSubject: Evento disparado', [
                'event' => $event,
                'data_keys' => array_keys($data)
            ]);
        }

        $notifiedCount = 0;

        foreach ($this->observers as $observerName => $observer) {
            try {
                // Verifica se o observador está interessado neste evento
                if ($this->isObserverInterested($observer, $event)) {
                    
                    if ($this->config['async_notifications']) {
                        // Em um ambiente real, isso poderia usar queues
                        $this->notifyObserverAsync($observer, $event, $data);
                    } else {
                        $observer->update($event, $data);
                    }
                    
                    $eventData['notified_observers'][] = $observerName;
                    $notifiedCount++;
                }
            } catch (\Exception $e) {
                Log::error('EventSubject: Erro ao notificar observador', [
                    'observer' => $observerName,
                    'event' => $event,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Adiciona ao histórico
        $this->addToHistory($eventData);

        if ($this->config['log_events']) {
            Log::info('EventSubject: Notificação concluída', [
                'event' => $event,
                'observers_notified' => $notifiedCount,
                'total_observers' => count($this->observers)
            ]);
        }
    }

    /**
     * Verifica se um observador está interessado em um evento
     * 
     * @param ObserverInterface $observer
     * @param string $event
     * @return bool
     */
    private function isObserverInterested(ObserverInterface $observer, string $event): bool
    {
        $interestedEvents = $observer->getInterestedEvents();
        
        // Se não especificou eventos, assume interesse em todos
        if (empty($interestedEvents)) {
            return true;
        }

        // Verifica se o evento está na lista de interesse
        if (in_array($event, $interestedEvents)) {
            return true;
        }

        // Verifica padrões com wildcard
        foreach ($interestedEvents as $pattern) {
            if (fnmatch($pattern, $event)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Notifica um observador de forma assíncrona (simulado)
     * 
     * @param ObserverInterface $observer
     * @param string $event
     * @param array $data
     * @return void
     */
    private function notifyObserverAsync(ObserverInterface $observer, string $event, array $data): void
    {
        // Em um ambiente real, isso seria implementado com queues do Laravel
        // Por enquanto, apenas simula com um delay mínimo
        $observer->update($event, $data);
    }

    /**
     * Adiciona evento ao histórico
     * 
     * @param array $eventData
     * @return void
     */
    private function addToHistory(array $eventData): void
    {
        $this->eventHistory[] = $eventData;

        // Mantém apenas os últimos eventos conforme configuração
        if (count($this->eventHistory) > $this->config['max_history']) {
            $this->eventHistory = array_slice($this->eventHistory, -$this->config['max_history']);
        }
    }

    /**
     * Obtém o histórico de eventos
     * 
     * @param int|null $limit
     * @return array
     */
    public function getEventHistory(?int $limit = null): array
    {
        if ($limit) {
            return array_slice($this->eventHistory, -$limit);
        }

        return $this->eventHistory;
    }

    /**
     * Obtém lista de observadores registrados
     * 
     * @return array
     */
    public function getObservers(): array
    {
        return array_map(function($observer) {
            return [
                'name' => $observer->getName(),
                'interested_events' => $observer->getInterestedEvents(),
                'class' => get_class($observer)
            ];
        }, $this->observers);
    }

    /**
     * Obtém estatísticas do subject
     * 
     * @return array
     */
    public function getStats(): array
    {
        $eventCounts = [];
        foreach ($this->eventHistory as $event) {
            $eventName = $event['event'];
            $eventCounts[$eventName] = ($eventCounts[$eventName] ?? 0) + 1;
        }

        return [
            'total_observers' => count($this->observers),
            'total_events_fired' => count($this->eventHistory),
            'event_counts' => $eventCounts,
            'most_fired_event' => !empty($eventCounts) ? array_keys($eventCounts, max($eventCounts))[0] : null,
            'config' => $this->config
        ];
    }

    /**
     * Configura o comportamento do subject
     * 
     * @param array $config
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        
        Log::info('EventSubject: Configuração atualizada', [
            'config' => $this->config
        ]);
    }

    /**
     * Limpa o histórico de eventos
     * 
     * @return void
     */
    public function clearHistory(): void
    {
        $this->eventHistory = [];
        Log::info('EventSubject: Histórico de eventos limpo');
    }

    /**
     * Remove todos os observadores
     * 
     * @return void
     */
    public function detachAll(): void
    {
        $count = count($this->observers);
        $this->observers = [];
        
        Log::info('EventSubject: Todos os observadores removidos', [
            'count' => $count
        ]);
    }
}
