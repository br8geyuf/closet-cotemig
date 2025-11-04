<?php

namespace App\Patterns\Factory\ItemTypes;

use App\Patterns\Factory\ItemTypeInterface;

/**
 * Implementação específica para calçados
 * 
 * Define comportamentos e características específicas para calçados,
 * incluindo validações de numeração e cuidados específicos.
 */
class ShoeItem implements ItemTypeInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return 'shoe';
    }

    public function getCharacteristics(): array
    {
        return [
            'has_size' => true,
            'has_material' => true,
            'has_heel_height' => true,
            'requires_special_care' => true,
            'size_specific' => true,
            'comfort_rating' => true,
            'weather_dependent' => true,
            'specific_attributes' => [
                'heel_height',
                'sole_type',
                'closure_type',
                'waterproof',
                'breathable',
                'comfort_level',
                'arch_support',
                'shoe_category'
            ]
        ];
    }

    public function validateData(array $data): bool
    {
        // Validações específicas para calçados
        $required = ['name', 'size'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Valida numeração
        if (isset($data['size'])) {
            $size = (float) $data['size'];
            if ($size < 30 || $size > 50) {
                return false;
            }
        }

        // Valida altura do salto se fornecida
        if (isset($data['heel_height'])) {
            $heel = (float) $data['heel_height'];
            if ($heel < 0 || $heel > 20) { // 0 a 20 cm
                return false;
            }
        }

        // Valida categoria do calçado
        if (isset($data['shoe_category'])) {
            $validCategories = [
                'casual', 'formal', 'esportivo', 'social', 'festa', 
                'praia', 'casa', 'trabalho', 'caminhada', 'corrida'
            ];
            if (!in_array(strtolower($data['shoe_category']), $validCategories)) {
                return false;
            }
        }

        return true;
    }

    public function processData(array $data): array
    {
        $processed = $data;

        // Normaliza o tamanho
        if (isset($processed['size'])) {
            $processed['size'] = number_format((float) $processed['size'], 0);
        }

        // Define categoria automaticamente se não fornecida
        if (!isset($processed['shoe_category'])) {
            $processed['shoe_category'] = $this->inferCategory($processed);
        }

        // Calcula nível de conforto baseado nas características
        $processed['comfort_level'] = $this->calculateComfortLevel($processed);

        // Calcula durabilidade estimada
        $processed['estimated_durability'] = $this->calculateDurability($processed['condition'] ?? 'usado_bom');

        // Define ocasiões recomendadas
        $processed['recommended_occasions'] = $this->getRecommendedOccasions($processed);

        // Adiciona tags automáticas
        $processed['auto_tags'] = $this->generateAutoTags($processed);

        // Define cuidados específicos
        $processed['care_instructions'] = $this->getCareInstructions();

        return $processed;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'size' => 'required|numeric|min:30|max:50',
            'heel_height' => 'nullable|numeric|min:0|max:20',
            'sole_type' => 'nullable|string|in:borracha,couro,sintetico,eva,gel,ar',
            'closure_type' => 'nullable|string|in:cadarco,velcro,fivela,slip_on,ziper,elastico',
            'waterproof' => 'nullable|boolean',
            'breathable' => 'nullable|boolean',
            'arch_support' => 'nullable|boolean',
            'shoe_category' => 'nullable|string|in:casual,formal,esportivo,social,festa,praia,casa,trabalho,caminhada,corrida',
            'comfort_level' => 'nullable|integer|min:1|max:10',
        ];
    }

    public function getCareInstructions(): array
    {
        $material = strtolower($this->data['material_type'] ?? 'couro');
        $category = strtolower($this->data['shoe_category'] ?? 'casual');
        
        $baseInstructions = [
            'couro' => [
                'Limpar com pano úmido após o uso',
                'Usar produtos específicos para couro',
                'Deixar secar naturalmente',
                'Usar forma para manter formato',
                'Hidratar o couro periodicamente',
                'Evitar exposição direta ao sol'
            ],
            'sintetico' => [
                'Limpar com pano úmido e sabão neutro',
                'Secar à sombra',
                'Evitar produtos químicos agressivos',
                'Guardar em local arejado',
                'Verificar desgaste regularmente'
            ],
            'tecido' => [
                'Pode ser lavado na máquina (ciclo delicado)',
                'Usar água fria',
                'Secar à sombra',
                'Não usar alvejante',
                'Remover cadarços antes da lavagem'
            ],
            'borracha' => [
                'Lavar com água e sabão',
                'Secar completamente',
                'Evitar exposição ao calor',
                'Guardar em local fresco',
                'Verificar rachaduras'
            ]
        ];

        $instructions = $baseInstructions[$material] ?? $baseInstructions['couro'];

        // Adiciona instruções específicas para calçados esportivos
        if ($category === 'esportivo') {
            $instructions[] = 'Trocar palmilhas regularmente';
            $instructions[] = 'Deixar arejar entre os usos';
            $instructions[] = 'Usar meias adequadas';
        }

        return $instructions;
    }

    public function calculateDurability(string $condition): int
    {
        $baseDurability = 18; // 1.5 anos para calçados em geral
        
        $conditionMultiplier = match($condition) {
            'novo' => 1.0,
            'usado_excelente' => 0.8,
            'usado_bom' => 0.6,
            'usado_regular' => 0.4,
            'danificado' => 0.2,
            default => 0.6
        };

        // Ajusta baseado no material
        $material = strtolower($this->data['material_type'] ?? 'couro');
        $materialMultiplier = match($material) {
            'couro' => 1.5,
            'sintetico' => 1.0,
            'tecido' => 0.7,
            'borracha' => 1.2,
            default => 1.0
        };

        // Ajusta baseado na categoria
        $category = strtolower($this->data['shoe_category'] ?? 'casual');
        $categoryMultiplier = match($category) {
            'formal', 'social' => 1.3, // Uso menos frequente
            'casual' => 1.0,
            'esportivo', 'corrida' => 0.8, // Uso mais intenso
            'casa' => 1.5, // Uso leve
            default => 1.0
        };

        return (int) round($baseDurability * $conditionMultiplier * $materialMultiplier * $categoryMultiplier);
    }

    public function getRecommendedSeasons(): array
    {
        $shoeType = strtolower($this->data['name'] ?? '');
        $category = strtolower($this->data['shoe_category'] ?? 'casual');
        
        // Calçados de inverno
        if (str_contains($shoeType, 'bota') || $category === 'trabalho') {
            return ['outono', 'inverno'];
        }
        
        // Calçados de verão
        if (str_contains($shoeType, 'sandalia') || str_contains($shoeType, 'chinelo') || 
            $category === 'praia') {
            return ['primavera', 'verao'];
        }
        
        // Calçados esportivos são versáteis
        if ($category === 'esportivo') {
            return ['todas'];
        }
        
        return ['todas']; // Maioria dos calçados são versáteis
    }

    private function inferCategory(array $data): string
    {
        $name = strtolower($data['name'] ?? '');
        
        if (str_contains($name, 'tenis') || str_contains($name, 'corrida')) {
            return 'esportivo';
        }
        
        if (str_contains($name, 'social') || str_contains($name, 'oxford')) {
            return 'formal';
        }
        
        if (str_contains($name, 'sandalia') || str_contains($name, 'chinelo')) {
            return 'praia';
        }
        
        if (str_contains($name, 'salto') || str_contains($name, 'scarpin')) {
            return 'festa';
        }
        
        return 'casual';
    }

    private function calculateComfortLevel(array $data): int
    {
        $comfort = 5; // Base
        
        // Altura do salto afeta conforto
        $heelHeight = (float) ($data['heel_height'] ?? 0);
        if ($heelHeight == 0) {
            $comfort += 2;
        } elseif ($heelHeight <= 3) {
            $comfort += 1;
        } elseif ($heelHeight > 8) {
            $comfort -= 2;
        }
        
        // Tipo de sola
        $soleType = strtolower($data['sole_type'] ?? '');
        if (in_array($soleType, ['eva', 'gel', 'ar'])) {
            $comfort += 2;
        } elseif ($soleType === 'borracha') {
            $comfort += 1;
        }
        
        // Suporte do arco
        if (!empty($data['arch_support'])) {
            $comfort += 1;
        }
        
        // Respirabilidade
        if (!empty($data['breathable'])) {
            $comfort += 1;
        }
        
        return max(1, min(10, $comfort));
    }

    private function generateAutoTags(array $data): array
    {
        $tags = [];
        
        // Tags baseadas na categoria
        if (isset($data['shoe_category'])) {
            $tags[] = 'categoria_' . $data['shoe_category'];
        }
        
        // Tags baseadas na altura do salto
        $heelHeight = (float) ($data['heel_height'] ?? 0);
        if ($heelHeight == 0) {
            $tags[] = 'sem_salto';
        } elseif ($heelHeight <= 3) {
            $tags[] = 'salto_baixo';
        } elseif ($heelHeight <= 7) {
            $tags[] = 'salto_medio';
        } else {
            $tags[] = 'salto_alto';
        }
        
        // Tags baseadas no conforto
        $comfort = $this->calculateComfortLevel($data);
        if ($comfort >= 8) {
            $tags[] = 'muito_confortavel';
        } elseif ($comfort >= 6) {
            $tags[] = 'confortavel';
        }
        
        // Tags baseadas nas características
        if (!empty($data['waterproof'])) {
            $tags[] = 'a_prova_dagua';
        }
        
        if (!empty($data['breathable'])) {
            $tags[] = 'respiravel';
        }
        
        if (!empty($data['arch_support'])) {
            $tags[] = 'suporte_arco';
        }
        
        return array_unique($tags);
    }

    private function getRecommendedOccasions(array $data): array
    {
        $category = strtolower($data['shoe_category'] ?? 'casual');
        
        return match($category) {
            'formal', 'social' => ['trabalho', 'formal', 'festa'],
            'esportivo' => ['esporte', 'casual', 'caminhada'],
            'festa' => ['festa', 'formal'],
            'praia' => ['casual', 'praia', 'casa'],
            'casa' => ['casa', 'casual'],
            'trabalho' => ['trabalho', 'casual'],
            'casual' => ['casual', 'trabalho'],
            default => ['todas']
        };
    }
}
