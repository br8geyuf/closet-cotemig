<?php

namespace App\Patterns\Decorator;

/**
 * Interface para decoradores de itens
 * 
 * Define o contrato que todos os decoradores devem implementar,
 * permitindo adicionar funcionalidades aos itens de forma flexível.
 */
interface ItemDecoratorInterface
{
    /**
     * Obtém os dados do item decorado
     * 
     * @return array
     */
    public function getData(): array;

    /**
     * Obtém informações adicionais fornecidas pelo decorador
     * 
     * @return array
     */
    public function getAdditionalInfo(): array;

    /**
     * Obtém o nome do decorador
     * 
     * @return string
     */
    public function getDecoratorName(): string;

    /**
     * Verifica se o decorador deve ser aplicado ao item
     * 
     * @param array $itemData
     * @return bool
     */
    public function shouldApply(array $itemData): bool;

    /**
     * Obtém a prioridade do decorador (menor número = maior prioridade)
     * 
     * @return int
     */
    public function getPriority(): int;
}
