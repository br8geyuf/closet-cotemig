<?php

namespace App\Patterns\Observer;

/**
 * Interface Observer para o padrão Observer
 * 
 * Define o contrato que todos os observadores devem implementar
 * para receber notificações de eventos do sistema.
 */
interface ObserverInterface
{
    /**
     * Método chamado quando um evento é disparado
     * 
     * @param string $event Nome do evento
     * @param array $data Dados relacionados ao evento
     * @return void
     */
    public function update(string $event, array $data): void;

    /**
     * Obtém o nome do observador
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Obtém os eventos que este observador está interessado
     * 
     * @return array
     */
    public function getInterestedEvents(): array;
}
