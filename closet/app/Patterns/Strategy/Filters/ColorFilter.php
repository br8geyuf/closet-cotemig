<?php

namespace App\Patterns\Strategy\Filters;

use App\Patterns\Strategy\FilterStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Estratégia de filtro por cor
 */
class ColorFilter implements FilterStrategyInterface
{
    public function apply(Builder $query, $value): Builder
    {
        if (is_array($value)) {
            return $query->where(function($q) use ($value) {
                foreach ($value as $color) {
                    $q->orWhereJsonContains('colors', $color);
                }
            });
        }
        
        return $query->whereJsonContains('colors', $value);
    }

    public function getName(): string
    {
        return 'color';
    }

    public function getDescription(): string
    {
        return 'Filtra itens por cor específica ou múltiplas cores';
    }

    public function isValidValue($value): bool
    {
        $validColors = $this->getPossibleValues();
        
        if (is_array($value)) {
            return !empty($value) && count(array_intersect($value, $validColors)) === count($value);
        }
        
        return in_array($value, $validColors);
    }

    public function getPossibleValues(): ?array
    {
        return [
            'preto', 'branco', 'cinza', 'azul', 'vermelho', 'verde', 'amarelo',
            'rosa', 'roxo', 'laranja', 'marrom', 'bege', 'dourado', 'prateado',
            'navy', 'vinho', 'nude', 'off-white', 'creme'
        ];
    }
}
