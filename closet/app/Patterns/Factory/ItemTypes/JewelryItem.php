<?php

namespace App\Patterns\Factory\ItemTypes;

use App\Patterns\Factory\ItemTypeInterface;

/**
 * Implementação específica para joias e bijuterias
 */
class JewelryItem implements ItemTypeInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return 'jewelry';
    }

    public function getCharacteristics(): array
    {
        return [
            'has_material' => true,
            'has_value' => true,
            'delicate' => true,
            'requires_special_care' => true,
            'can_tarnish' => true,
            'specific_attributes' => [
                'metal_type',
                'gemstone',
                'karat',
                'hypoallergenic',
                'adjustable',
                'jewelry_type'
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
            'metal_type' => 'nullable|string|in:ouro,prata,aco,bronze,bijuteria',
            'karat' => 'nullable|integer|min:1|max:24',
            'hypoallergenic' => 'nullable|boolean',
            'adjustable' => 'nullable|boolean',
        ];
    }

    public function getCareInstructions(): array
    {
        $metal = strtolower($this->data['metal_type'] ?? 'bijuteria');
        
        $instructions = [
            'ouro' => [
                'Limpar com pano macio',
                'Evitar produtos químicos',
                'Guardar separadamente',
                'Limpar com água morna e sabão neutro'
            ],
            'prata' => [
                'Limpar com pano específico para prata',
                'Evitar exposição ao ar',
                'Usar produtos anti-oxidação',
                'Guardar em saquinhos'
            ],
            'bijuteria' => [
                'Evitar contato com água',
                'Limpar com pano seco',
                'Guardar em local seco',
                'Evitar perfumes e cremes'
            ]
        ];

        return $instructions[$metal] ?? $instructions['bijuteria'];
    }

    public function calculateDurability(string $condition): int
    {
        $baseDurability = 60; // 5 anos para joias
        
        $conditionMultiplier = match($condition) {
            'novo' => 1.0,
            'usado_excelente' => 0.9,
            'usado_bom' => 0.7,
            'usado_regular' => 0.5,
            'danificado' => 0.3,
            default => 0.7
        };

        $metal = strtolower($this->data['metal_type'] ?? 'bijuteria');
        $materialMultiplier = match($metal) {
            'ouro' => 2.0,
            'prata' => 1.5,
            'aco' => 1.3,
            'bronze' => 1.0,
            'bijuteria' => 0.5,
            default => 1.0
        };

        return (int) round($baseDurability * $conditionMultiplier * $materialMultiplier);
    }

    public function getRecommendedSeasons(): array
    {
        return ['todas'];
    }

    private function generateAutoTags(array $data): array
    {
        $tags = [];
        
        if (isset($data['metal_type'])) {
            $tags[] = 'metal_' . $data['metal_type'];
        }
        
        if (isset($data['hypoallergenic']) && $data['hypoallergenic']) {
            $tags[] = 'hipoalergenico';
        }
        
        return $tags;
    }
}
