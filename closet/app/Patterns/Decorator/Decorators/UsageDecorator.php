<?php

namespace App\Patterns\Decorator\Decorators;

use App\Patterns\Decorator\BaseItemDecorator;

/**
 * Decorador para informações de uso do item
 * 
 * Adiciona estatísticas e insights sobre como
 * e quando o item é utilizado.
 */
class UsageDecorator extends BaseItemDecorator
{
    public function getDecoratorName(): string
    {
        return 'usage';
    }

    public function getAdditionalInfo(): array
    {
        return [
            'usage_stats' => $this->getUsageStats(),
            'usage_patterns' => $this->getUsagePatterns(),
            'recommendations' => $this->getUsageRecommendations(),
            'cost_per_wear' => $this->calculateCostPerWear(),
            'seasonal_usage' => $this->getSeasonalUsage(),
            'occasion_usage' => $this->getOccasionUsage()
        ];
    }

    public function getPriority(): int
    {
        return 50; // Prioridade média
    }

    /**
     * Obtém estatísticas básicas de uso
     * 
     * @return array
     */
    private function getUsageStats(): array
    {
        $usageCount = $this->itemData['usage_count'] ?? 0;
        $lastWorn = $this->itemData['last_worn'] ?? null;
        $firstWorn = $this->itemData['first_worn'] ?? null;
        
        $stats = [
            'total_uses' => $usageCount,
            'last_worn' => $lastWorn,
            'first_worn' => $firstWorn,
            'usage_frequency' => $this->calculateUsageFrequency($usageCount),
            'days_since_last_use' => $this->getDaysSinceLastUse($lastWorn),
            'average_uses_per_month' => $this->calculateAverageUsesPerMonth($usageCount, $firstWorn)
        ];

        return $stats;
    }

    /**
     * Identifica padrões de uso
     * 
     * @return array
     */
    private function getUsagePatterns(): array
    {
        $usageCount = $this->itemData['usage_count'] ?? 0;
        $lastWorn = $this->itemData['last_worn'] ?? null;
        
        $patterns = [];
        
        // Padrão de frequência
        if ($usageCount >= 20) {
            $patterns[] = 'item_essencial';
        } elseif ($usageCount >= 10) {
            $patterns[] = 'uso_regular';
        } elseif ($usageCount >= 5) {
            $patterns[] = 'uso_ocasional';
        } elseif ($usageCount >= 1) {
            $patterns[] = 'uso_raro';
        } else {
            $patterns[] = 'nunca_usado';
        }
        
        // Padrão temporal
        $daysSinceLastUse = $this->getDaysSinceLastUse($lastWorn);
        if ($daysSinceLastUse > 365) {
            $patterns[] = 'esquecido';
        } elseif ($daysSinceLastUse > 180) {
            $patterns[] = 'pouco_usado_recentemente';
        } elseif ($daysSinceLastUse <= 7) {
            $patterns[] = 'usado_recentemente';
        }
        
        return $patterns;
    }

    /**
     * Gera recomendações baseadas no uso
     * 
     * @return array
     */
    private function getUsageRecommendations(): array
    {
        $recommendations = [];
        $patterns = $this->getUsagePatterns();
        $usageCount = $this->itemData['usage_count'] ?? 0;
        
        if (in_array('nunca_usado', $patterns)) {
            $recommendations[] = [
                'type' => 'suggestion',
                'message' => 'Que tal experimentar este item? Ainda não foi usado!',
                'action' => 'try_on'
            ];
        }
        
        if (in_array('esquecido', $patterns)) {
            $recommendations[] = [
                'type' => 'reminder',
                'message' => 'Este item não é usado há mais de um ano. Considere doá-lo ou vendê-lo.',
                'action' => 'consider_removal'
            ];
        }
        
        if (in_array('item_essencial', $patterns)) {
            $recommendations[] = [
                'type' => 'care',
                'message' => 'Item muito usado! Verifique o estado e considere cuidados especiais.',
                'action' => 'check_condition'
            ];
        }
        
        if (in_array('uso_regular', $patterns)) {
            $recommendations[] = [
                'type' => 'suggestion',
                'message' => 'Item versátil! Experimente combinações diferentes.',
                'action' => 'try_combinations'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Calcula o custo por uso
     * 
     * @return array
     */
    private function calculateCostPerWear(): array
    {
        $purchasePrice = $this->itemData['purchase_price'] ?? 0;
        $usageCount = $this->itemData['usage_count'] ?? 0;
        
        if ($usageCount === 0) {
            return [
                'cost_per_wear' => null,
                'total_cost' => $purchasePrice,
                'total_uses' => 0,
                'value_rating' => 'not_used'
            ];
        }
        
        $costPerWear = $purchasePrice / $usageCount;
        
        // Avalia o valor baseado no custo por uso
        $valueRating = 'poor';
        if ($costPerWear <= 5) {
            $valueRating = 'excellent';
        } elseif ($costPerWear <= 15) {
            $valueRating = 'good';
        } elseif ($costPerWear <= 30) {
            $valueRating = 'fair';
        }
        
        return [
            'cost_per_wear' => round($costPerWear, 2),
            'total_cost' => $purchasePrice,
            'total_uses' => $usageCount,
            'value_rating' => $valueRating
        ];
    }

    /**
     * Analisa uso sazonal
     * 
     * @return array
     */
    private function getSeasonalUsage(): array
    {
        // Em uma implementação real, isso analisaria dados históricos
        $season = $this->itemData['season'] ?? 'todas';
        $currentSeason = $this->getCurrentSeason();
        
        return [
            'preferred_season' => $season,
            'current_season' => $currentSeason,
            'in_season' => $season === 'todas' || $season === $currentSeason,
            'seasonal_recommendation' => $this->getSeasonalRecommendation($season, $currentSeason)
        ];
    }

    /**
     * Analisa uso por ocasião
     * 
     * @return array
     */
    private function getOccasionUsage(): array
    {
        $occasion = $this->itemData['occasion'] ?? 'casual';
        
        return [
            'primary_occasion' => $occasion,
            'versatility' => $this->calculateOccasionVersatility($occasion),
            'occasion_suggestions' => $this->getOccasionSuggestions($occasion)
        ];
    }

    /**
     * Calcula frequência de uso
     * 
     * @param int $usageCount
     * @return string
     */
    private function calculateUsageFrequency(int $usageCount): string
    {
        if ($usageCount >= 50) return 'muito_alta';
        if ($usageCount >= 20) return 'alta';
        if ($usageCount >= 10) return 'media';
        if ($usageCount >= 5) return 'baixa';
        if ($usageCount >= 1) return 'muito_baixa';
        return 'nunca';
    }

    /**
     * Calcula dias desde o último uso
     * 
     * @param string|null $lastWorn
     * @return int|null
     */
    private function getDaysSinceLastUse(?string $lastWorn): ?int
    {
        if (!$lastWorn) {
            return null;
        }
        
        try {
            $lastWornDate = \Carbon\Carbon::parse($lastWorn);
            return now()->diffInDays($lastWornDate);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calcula média de usos por mês
     * 
     * @param int $usageCount
     * @param string|null $firstWorn
     * @return float|null
     */
    private function calculateAverageUsesPerMonth(int $usageCount, ?string $firstWorn): ?float
    {
        if (!$firstWorn || $usageCount === 0) {
            return null;
        }
        
        try {
            $firstWornDate = \Carbon\Carbon::parse($firstWorn);
            $monthsOwned = max(1, now()->diffInMonths($firstWornDate));
            return round($usageCount / $monthsOwned, 2);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtém a estação atual
     * 
     * @return string
     */
    private function getCurrentSeason(): string
    {
        $month = now()->month;
        
        if (in_array($month, [12, 1, 2])) return 'verao';
        if (in_array($month, [3, 4, 5])) return 'outono';
        if (in_array($month, [6, 7, 8])) return 'inverno';
        return 'primavera';
    }

    /**
     * Gera recomendação sazonal
     * 
     * @param string $itemSeason
     * @param string $currentSeason
     * @return string
     */
    private function getSeasonalRecommendation(string $itemSeason, string $currentSeason): string
    {
        if ($itemSeason === 'todas') {
            return 'Item versátil para qualquer estação';
        }
        
        if ($itemSeason === $currentSeason) {
            return 'Perfeito para a estação atual!';
        }
        
        return "Melhor para {$itemSeason}. Guarde para a próxima estação.";
    }

    /**
     * Calcula versatilidade por ocasião
     * 
     * @param string $occasion
     * @return string
     */
    private function calculateOccasionVersatility(string $occasion): string
    {
        $versatileOccasions = ['casual', 'todas'];
        
        if (in_array($occasion, $versatileOccasions)) {
            return 'alta';
        }
        
        return 'especifica';
    }

    /**
     * Sugere ocasiões de uso
     * 
     * @param string $primaryOccasion
     * @return array
     */
    private function getOccasionSuggestions(string $primaryOccasion): array
    {
        $suggestions = match($primaryOccasion) {
            'casual' => ['trabalho', 'encontros', 'compras'],
            'trabalho' => ['reuniões', 'apresentações', 'eventos corporativos'],
            'festa' => ['casamentos', 'aniversários', 'eventos sociais'],
            'esporte' => ['academia', 'caminhada', 'atividades ao ar livre'],
            default => ['casual', 'trabalho']
        };
        
        return $suggestions;
    }
}
