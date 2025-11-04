<?php

namespace App\Patterns\Factory\ItemTypes;

use App\Patterns\Factory\ItemTypeInterface;

/**
 * Implementação específica para bolsas e acessórios de armazenamento
 */
class BagItem implements ItemTypeInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return 'bag';
    }

    public function getCharacteristics(): array
    {
        return [
            'has_size' => true,
            'has_capacity' => true,
            'has_compartments' => true,
            'has_closure' => true,
            'portable' => true,
            'functional' => true,
            'specific_attributes' => [
                'capacity_liters',
                'compartments_count',
                'closure_type',
                'strap_type',
                'waterproof',
                'security_features'
            ]
        ];
    }

    public function validateData(array $data): bool
    {
        $required = ['name'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        if (isset($data['capacity_liters']) && $data['capacity_liters'] < 0) {
            return false;
        }

        return true;
    }

    public function processData(array $data): array
    {
        $processed = $data;
        $processed['estimated_durability'] = $this->calculateDurability($processed['condition'] ?? 'usado_bom');
        $processed['auto_tags'] = $this->generateAutoTags($processed);
        return $processed;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'capacity_liters' => 'nullable|numeric|min:0',
            'compartments_count' => 'nullable|integer|min:1',
            'closure_type' => 'nullable|string|in:ziper,botao,ima,velcro,fivela',
            'strap_type' => 'nullable|string|in:alca,tiracolo,mochila,mao,nenhuma',
            'waterproof' => 'nullable|boolean',
        ];
    }

    public function getCareInstructions(): array
    {
        return [
            'Limpar regularmente por dentro e por fora',
            'Usar produtos adequados ao material',
            'Secar completamente antes de guardar',
            'Manter formato com enchimento quando não usar',
            'Evitar sobrecarga de peso'
        ];
    }

    public function calculateDurability(string $condition): int
    {
        $baseDurability = 30; // 2.5 anos
        
        $conditionMultiplier = match($condition) {
            'novo' => 1.0,
            'usado_excelente' => 0.8,
            'usado_bom' => 0.6,
            'usado_regular' => 0.4,
            'danificado' => 0.2,
            default => 0.6
        };

        return (int) round($baseDurability * $conditionMultiplier);
    }

    public function getRecommendedSeasons(): array
    {
        return ['todas'];
    }

    private function generateAutoTags(array $data): array
    {
        $tags = [];
        
        if (isset($data['waterproof']) && $data['waterproof']) {
            $tags[] = 'a_prova_dagua';
        }
        
        if (isset($data['capacity_liters'])) {
            $capacity = (float) $data['capacity_liters'];
            if ($capacity <= 5) {
                $tags[] = 'pequena';
            } elseif ($capacity <= 15) {
                $tags[] = 'media';
            } else {
                $tags[] = 'grande';
            }
        }
        
        return $tags;
    }
}
