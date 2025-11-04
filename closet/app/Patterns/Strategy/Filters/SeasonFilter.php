<?php

namespace App\Patterns\Strategy\Filters;

use App\Patterns\Strategy\FilterStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Estratégia de filtro por estação
 */
class SeasonFilter implements FilterStrategyInterface
{
    public function apply(Builder $query, $value): Builder
    {
        if ($value === 'todas') {
            return $query; // Não aplica filtro
        }
        
        if (is_array($value)) {
            return $query->whereIn('season', $value);
        }
        
        return $query->where('season', $value);
    }

    public function getName(): string
    {
        return 'season';
    }

    public function getDescription(): string
    {
        return 'Filtra itens por estação do ano';
    }

    public function isValidValue($value): bool
    {
        $validSeasons = $this->getPossibleValues();
        
        if (is_array($value)) {
            return !empty($value) && count(array_intersect($value, $validSeasons)) === count($value);
        }
        
        return in_array($value, $validSeasons);
    }

    public function getPossibleValues(): ?array
    {
        return ['primavera', 'verao', 'outono', 'inverno', 'todas'];
    }
}
