<?php

namespace App\Patterns\Decorator\Decorators;

use App\Patterns\Decorator\BaseItemDecorator;

/**
 * Decorador para itens em promoção
 * 
 * Adiciona informações sobre promoções, descontos
 * e oportunidades de compra relacionadas ao item.
 */
class PromotionDecorator extends BaseItemDecorator
{
    public function getDecoratorName(): string
    {
        return 'promotion';
    }

    public function getAdditionalInfo(): array
    {
        $promotions = $this->getActivePromotions();
        
        if (empty($promotions)) {
            return [];
        }

        return [
            'has_promotions' => true,
            'promotion_badge' => '🏷️',
            'active_promotions' => $promotions,
            'best_discount' => $this->getBestDiscount($promotions),
            'savings_potential' => $this->calculateSavings($promotions),
            'promotion_urgency' => $this->getPromotionUrgency($promotions),
            'similar_items_on_sale' => $this->getSimilarItemsOnSale()
        ];
    }

    public function shouldApply(array $itemData): bool
    {
        return !empty($this->getActivePromotions());
    }

    public function getPriority(): int
    {
        return 20; // Alta prioridade para promoções
    }

    /**
     * Obtém promoções ativas relacionadas ao item
     * 
     * @return array
     */
    private function getActivePromotions(): array
    {
        // Em uma implementação real, isso consultaria o banco de dados
        // para buscar promoções ativas baseadas na categoria, marca, loja, etc.
        
        $promotions = [];
        $category = $this->itemData['category_name'] ?? '';
        $brand = $this->itemData['brand'] ?? '';
        
        // Simula algumas promoções baseadas nos dados do item
        if (str_contains(strtolower($category), 'camiseta')) {
            $promotions[] = [
                'id' => 1,
                'title' => 'Promoção Camisetas',
                'description' => '30% off em todas as camisetas',
                'discount_percentage' => 30,
                'store' => 'Loja Fashion',
                'valid_until' => '2024-12-31',
                'type' => 'percentage'
            ];
        }
        
        if (!empty($brand) && strtolower($brand) === 'nike') {
            $promotions[] = [
                'id' => 2,
                'title' => 'Nike Week',
                'description' => 'Até 40% off em produtos Nike',
                'discount_percentage' => 40,
                'store' => 'Nike Store',
                'valid_until' => '2024-11-30',
                'type' => 'percentage'
            ];
        }
        
        return $promotions;
    }

    /**
     * Obtém o melhor desconto disponível
     * 
     * @param array $promotions
     * @return array|null
     */
    private function getBestDiscount(array $promotions): ?array
    {
        if (empty($promotions)) {
            return null;
        }

        $bestPromotion = null;
        $bestDiscount = 0;

        foreach ($promotions as $promotion) {
            $discount = $promotion['discount_percentage'] ?? 0;
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestPromotion = $promotion;
            }
        }

        return $bestPromotion;
    }

    /**
     * Calcula o potencial de economia
     * 
     * @param array $promotions
     * @return array
     */
    private function calculateSavings(array $promotions): array
    {
        $originalPrice = $this->itemData['purchase_price'] ?? 100; // Preço estimado
        $bestDiscount = $this->getBestDiscount($promotions);
        
        if (!$bestDiscount) {
            return [
                'original_price' => $originalPrice,
                'discounted_price' => $originalPrice,
                'savings_amount' => 0,
                'savings_percentage' => 0
            ];
        }

        $discountPercentage = $bestDiscount['discount_percentage'] ?? 0;
        $savingsAmount = $originalPrice * ($discountPercentage / 100);
        $discountedPrice = $originalPrice - $savingsAmount;

        return [
            'original_price' => $originalPrice,
            'discounted_price' => round($discountedPrice, 2),
            'savings_amount' => round($savingsAmount, 2),
            'savings_percentage' => $discountPercentage
        ];
    }

    /**
     * Determina a urgência da promoção
     * 
     * @param array $promotions
     * @return string
     */
    private function getPromotionUrgency(array $promotions): string
    {
        $now = now();
        $minDaysLeft = PHP_INT_MAX;

        foreach ($promotions as $promotion) {
            $validUntil = $promotion['valid_until'] ?? null;
            if ($validUntil) {
                $endDate = \Carbon\Carbon::parse($validUntil);
                $daysLeft = $now->diffInDays($endDate, false);
                
                if ($daysLeft >= 0 && $daysLeft < $minDaysLeft) {
                    $minDaysLeft = $daysLeft;
                }
            }
        }

        if ($minDaysLeft <= 1) {
            return 'urgente'; // Termina hoje ou amanhã
        } elseif ($minDaysLeft <= 7) {
            return 'alta'; // Termina em uma semana
        } elseif ($minDaysLeft <= 30) {
            return 'media'; // Termina em um mês
        }

        return 'baixa'; // Mais de um mês
    }

    /**
     * Obtém itens similares em promoção
     * 
     * @return array
     */
    private function getSimilarItemsOnSale(): array
    {
        // Em uma implementação real, isso buscaria itens similares
        // baseados na categoria, cor, marca, etc.
        
        $category = $this->itemData['category_name'] ?? '';
        $colors = $this->itemData['colors'] ?? [];
        
        $similarItems = [];
        
        // Simula alguns itens similares
        if (!empty($category)) {
            $similarItems[] = [
                'name' => "Outro item de {$category}",
                'discount' => '25% off',
                'store' => 'Loja Online',
                'price' => 'R$ 89,90'
            ];
        }
        
        if (!empty($colors)) {
            $color = $colors[0] ?? 'azul';
            $similarItems[] = [
                'name' => "Item {$color} similar",
                'discount' => '20% off',
                'store' => 'Fashion Store',
                'price' => 'R$ 79,90'
            ];
        }
        
        return $similarItems;
    }
}
