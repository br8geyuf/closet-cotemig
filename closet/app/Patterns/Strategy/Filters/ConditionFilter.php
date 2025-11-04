<?php

namespace App\Patterns\Strategy\Filters;

use App\Patterns\Strategy\FilterStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Estratégia de filtro por condição do item
 */
class ConditionFilter implements FilterStrategyInterface
{
    public function apply(Builder $query, $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn('condition', $value);
        }
        
        return $query->where('condition', $value);
    }

    public function getName(): string
    {
        return 'condition';
    }

    public function getDescription(): string
    {
        return 'Filtra itens por condição/estado de conservação';
    }

    public function isValidValue($value): bool
    {
        $validConditions = $this->getPossibleValues();
        
        if (is_array($value)) {
            return !empty($value) && count(array_intersect($value, $validConditions)) === count($value);
        }
        
        return in_array($value, $validConditions);
    }

    public function getPossibleValues(): ?array
    {
        return ['novo', 'usado_excelente', 'usado_bom', 'usado_regular', 'danificado'];
    }
}
