<?php

namespace App\Patterns\Strategy;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface para estratégias de filtro
 * 
 * Define o contrato que todas as estratégias de filtro devem implementar,
 * permitindo diferentes algoritmos de filtragem serem aplicados dinamicamente.
 */
interface FilterStrategyInterface
{
    /**
     * Aplica o filtro à query
     * 
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function apply(Builder $query, $value): Builder;

    /**
     * Obtém o nome do filtro
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Obtém a descrição do filtro
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * Valida se o valor é válido para este filtro
     * 
     * @param mixed $value
     * @return bool
     */
    public function isValidValue($value): bool;

    /**
     * Obtém os valores possíveis para este filtro (se aplicável)
     * 
     * @return array|null
     */
    public function getPossibleValues(): ?array;
}
