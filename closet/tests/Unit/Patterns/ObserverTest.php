<?php

namespace Tests\Unit\Patterns;

use Tests\TestCase;
use App\Patterns\Observer\EventSubject;
use App\Patterns\Observer\Observers\ItemObserver;
use App\Patterns\Observer\Observers\BudgetObserver;
use App\Patterns\Observer\ObserverInterface;

/**
 * Testes para o padrão Observer
 */
class ObserverTest extends TestCase
{
    private EventSubject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new EventSubject();
    }

    /**
     * Testa registro de observador
     */
    public function test_attaches_observer()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);

        $observers = $this->subject->getObservers();
        $this->assertCount(1, $observers);
        $this->assertEquals('item_observer', array_values($observers)[0]['name']);
    }

    /**
     * Testa remoção de observador
     */
    public function test_detaches_observer()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);
        $this->subject->detach($observer);

        $observers = $this->subject->getObservers();
        $this->assertCount(0, $observers);
    }

    /**
     * Testa remoção por nome
     */
    public function test_detaches_observer_by_name()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);
        $this->subject->detachByName('item_observer');

        $observers = $this->subject->getObservers();
        $this->assertCount(0, $observers);
    }

    /**
     * Testa notificação de eventos
     */
    public function test_notifies_observers()
    {
        // Cria um observador mock
        $mockObserver = $this->createMock(ObserverInterface::class);
        $mockObserver->method('getName')->willReturn('mock_observer');
        $mockObserver->method('getInterestedEvents')->willReturn(['test.event']);
        
        // Espera que o método update seja chamado uma vez
        $mockObserver->expects($this->once())
                    ->method('update')
                    ->with('test.event', ['data' => 'test']);

        $this->subject->attach($mockObserver);
        $this->subject->notify('test.event', ['data' => 'test']);
    }

    /**
     * Testa filtro de eventos por interesse
     */
    public function test_filters_events_by_interest()
    {
        // Observador interessado apenas em eventos de item
        $itemObserver = new ItemObserver();
        
        // Observador interessado apenas em eventos de orçamento
        $budgetObserver = new BudgetObserver();

        $this->subject->attach($itemObserver);
        $this->subject->attach($budgetObserver);

        // Dispara evento de item - apenas ItemObserver deve ser notificado
        $this->subject->notify('item.created', ['item_id' => 1]);

        $history = $this->subject->getEventHistory();
        $this->assertCount(1, $history);
        $this->assertEquals('item.created', $history[0]['event']);
    }

    /**
     * Testa histórico de eventos
     */
    public function test_maintains_event_history()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);

        $this->subject->notify('event1', ['data' => 1]);
        $this->subject->notify('event2', ['data' => 2]);

        $history = $this->subject->getEventHistory();
        $this->assertCount(2, $history);
        $this->assertEquals('event1', $history[0]['event']);
        $this->assertEquals('event2', $history[1]['event']);
    }

    /**
     * Testa limpeza do histórico
     */
    public function test_clears_event_history()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);

        $this->subject->notify('test.event', []);
        $this->assertCount(1, $this->subject->getEventHistory());

        $this->subject->clearHistory();
        $this->assertCount(0, $this->subject->getEventHistory());
    }

    /**
     * Testa remoção de todos os observadores
     */
    public function test_detaches_all_observers()
    {
        $this->subject->attach(new ItemObserver());
        $this->subject->attach(new BudgetObserver());

        $this->assertCount(2, $this->subject->getObservers());

        $this->subject->detachAll();
        $this->assertCount(0, $this->subject->getObservers());
    }

    /**
     * Testa estatísticas do subject
     */
    public function test_provides_stats()
    {
        $observer = new ItemObserver();
        $this->subject->attach($observer);
        $this->subject->notify('test.event', []);

        $stats = $this->subject->getStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_observers', $stats);
        $this->assertArrayHasKey('total_events_fired', $stats);
        $this->assertArrayHasKey('event_counts', $stats);
        $this->assertEquals(1, $stats['total_observers']);
        $this->assertEquals(1, $stats['total_events_fired']);
    }

    /**
     * Testa configuração do subject
     */
    public function test_configures_subject()
    {
        $config = [
            'log_events' => false,
            'max_history' => 50
        ];

        $this->subject->configure($config);
        $stats = $this->subject->getStats();

        $this->assertFalse($stats['config']['log_events']);
        $this->assertEquals(50, $stats['config']['max_history']);
    }

    /**
     * Testa limite do histórico
     */
    public function test_respects_history_limit()
    {
        $this->subject->configure(['max_history' => 2]);
        $observer = new ItemObserver();
        $this->subject->attach($observer);

        // Dispara 3 eventos
        $this->subject->notify('event1', []);
        $this->subject->notify('event2', []);
        $this->subject->notify('event3', []);

        $history = $this->subject->getEventHistory();
        
        // Deve manter apenas os 2 últimos
        $this->assertCount(2, $history);
        $this->assertEquals('event2', $history[0]['event']);
        $this->assertEquals('event3', $history[1]['event']);
    }

    /**
     * Testa tratamento de erro em observador
     */
    public function test_handles_observer_errors()
    {
        // Cria observador que lança exceção
        $faultyObserver = $this->createMock(ObserverInterface::class);
        $faultyObserver->method('getName')->willReturn('faulty_observer');
        $faultyObserver->method('getInterestedEvents')->willReturn(['test.event']);
        $faultyObserver->method('update')->willThrowException(new \Exception('Test error'));

        $this->subject->attach($faultyObserver);

        // Não deve lançar exceção, apenas logar o erro
        $this->subject->notify('test.event', []);

        // Evento deve estar no histórico mesmo com erro
        $history = $this->subject->getEventHistory();
        $this->assertCount(1, $history);
    }

    /**
     * Testa padrões wildcard em eventos
     */
    public function test_wildcard_event_patterns()
    {
        // Cria observador que aceita padrões wildcard
        $wildcardObserver = $this->createMock(ObserverInterface::class);
        $wildcardObserver->method('getName')->willReturn('wildcard_observer');
        $wildcardObserver->method('getInterestedEvents')->willReturn(['item.*']);
        
        $wildcardObserver->expects($this->exactly(2))
                        ->method('update');

        $this->subject->attach($wildcardObserver);

        // Ambos eventos devem notificar o observador
        $this->subject->notify('item.created', []);
        $this->subject->notify('item.updated', []);

        // Este não deve notificar
        $this->subject->notify('budget.created', []);
        
        // Este não deve notificar
        $this->subject->notify('budget.created', []);
    }
}
