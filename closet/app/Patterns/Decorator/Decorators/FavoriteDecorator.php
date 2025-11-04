<?php

namespace App\Patterns\Decorator\Decorators;

use App\Patterns\Decorator\BaseItemDecorator;

/**
 * Decorador para itens favoritos
 * 
 * Adiciona informações e funcionalidades específicas
 * para itens marcados como favoritos.
 */
class FavoriteDecorator extends BaseItemDecorator
{
    public function getDecoratorName(): string
    {
        return 'favorite';
    }

    public function getAdditionalInfo(): array
    {
        if (!$this->isFavorite()) {
            return [];
        }

        return [
            'is_favorite' => true,
            'favorite_badge' => '⭐',
            'favorite_since' => $this->getFavoriteSince(),
            'favorite_rank' => $this->getFavoriteRank(),
            'usage_frequency' => $this->getUsageFrequency(),
            'style_tips' => $this->getStyleTips(),
            'care_reminders' => $this->getCareReminders()
        ];
    }

    public function shouldApply(array $itemData): bool
    {
        return $this->isFavorite();
    }

    public function getPriority(): int
    {
        return 10; // Alta prioridade para favoritos
    }

    /**
     * Verifica se o item é favorito
     * 
     * @return bool
     */
    private function isFavorite(): bool
    {
        return !empty($this->itemData['is_favorite']) || 
               !empty($this->itemData['favorited_at']);
    }

    /**
     * Obtém a data desde quando é favorito
     * 
     * @return string|null
     */
    private function getFavoriteSince(): ?string
    {
        if (isset($this->itemData['favorited_at'])) {
            return $this->itemData['favorited_at'];
        }

        return null;
    }

    /**
     * Calcula o ranking do favorito
     * 
     * @return int
     */
    private function getFavoriteRank(): int
    {
        // Em uma implementação real, isso consultaria o banco
        // para determinar o ranking baseado em uso, data, etc.
        return $this->itemData['favorite_rank'] ?? 1;
    }

    /**
     * Obtém a frequência de uso
     * 
     * @return string
     */
    private function getUsageFrequency(): string
    {
        $usageCount = $this->itemData['usage_count'] ?? 0;
        
        if ($usageCount >= 20) {
            return 'muito_alta';
        } elseif ($usageCount >= 10) {
            return 'alta';
        } elseif ($usageCount >= 5) {
            return 'media';
        } elseif ($usageCount >= 1) {
            return 'baixa';
        }
        
        return 'nunca_usado';
    }

    /**
     * Obtém dicas de estilo para o item favorito
     * 
     * @return array
     */
    private function getStyleTips(): array
    {
        $tips = [];
        $category = strtolower($this->itemData['category_name'] ?? '');
        $colors = $this->itemData['colors'] ?? [];

        // Dicas baseadas na categoria
        switch ($category) {
            case 'jeans':
                $tips[] = 'Combine com camisetas básicas para um look casual';
                $tips[] = 'Use com blazer para um visual mais arrumado';
                break;
            case 'vestido':
                $tips[] = 'Adicione acessórios para variar o estilo';
                $tips[] = 'Experimente diferentes calçados para ocasiões distintas';
                break;
            case 'blazer':
                $tips[] = 'Versátil para looks casuais e formais';
                $tips[] = 'Combine com jeans para um casual chic';
                break;
        }

        // Dicas baseadas nas cores
        if (in_array('preto', $colors)) {
            $tips[] = 'Preto combina com qualquer cor - aproveite!';
        }
        
        if (in_array('branco', $colors)) {
            $tips[] = 'Branco é atemporal e combina com tudo';
        }

        return array_unique($tips);
    }

    /**
     * Obtém lembretes de cuidados especiais
     * 
     * @return array
     */
    private function getCareReminders(): array
    {
        $reminders = [];
        
        // Como é favorito, merece cuidados especiais
        $reminders[] = 'Item favorito - cuidado extra recomendado';
        
        $condition = $this->itemData['condition'] ?? 'usado_bom';
        if ($condition !== 'novo') {
            $reminders[] = 'Verifique regularmente o estado de conservação';
        }

        $fabric = strtolower($this->itemData['fabric_type'] ?? '');
        if (in_array($fabric, ['seda', 'la', 'cashmere'])) {
            $reminders[] = 'Tecido delicado - considere lavagem profissional';
        }

        $usageFrequency = $this->getUsageFrequency();
        if (in_array($usageFrequency, ['alta', 'muito_alta'])) {
            $reminders[] = 'Uso frequente - atenção ao desgaste';
        }

        return $reminders;
    }
}
