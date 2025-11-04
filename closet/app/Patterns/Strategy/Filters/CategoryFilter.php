<?php

namespace App\Patterns\Strategy\Filters;

use App\Patterns\Strategy\FilterStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Estratégia de filtro por categoria
 */
class CategoryFilter implements FilterStrategyInterface
{
    public function apply(Builder $query, $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn('category_id', $value);
        }
        
        return $query->where('category_id', $value);
    }

    public function getName(): string
    {
        return 'category';
    }

    public function getDescription(): string
    {
        return 'Filtra itens por categoria específica ou múltiplas categorias';
    }

    public function isValidValue($value): bool
    {
        if (is_array($value)) {
            return !empty($value) && count(array_filter($value, 'is_numeric')) === count($value);
        }
        
        return is_numeric($value) && $value > 0;
    }

    public function getPossibleValues(): ?array
    {
        // Em uma implementação real, isso buscaria as categorias do banco
        return null; // Valores dinâmicos
    }
}
