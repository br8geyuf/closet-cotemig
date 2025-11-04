<?php

namespace App\Patterns\Factory\ItemTypes;

use App\Patterns\Factory\ItemTypeInterface;

/**
 * Implementação específica para itens de roupa
 * 
 * Define comportamentos e características específicas para roupas,
 * incluindo validações, cuidados e durabilidade específicos.
 */
class ClothingItem implements ItemTypeInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return 'clothing';
    }

    public function getCharacteristics(): array
    {
        return [
            'has_size' => true,
            'has_fabric' => true,
            'has_fit' => true,
            'requires_washing' => true,
            'can_be_ironed' => true,
            'seasonal_usage' => true,
            'layerable' => true,
            'specific_attributes' => [
                'fabric_type',
                'fit_type',
                'sleeve_length',
                'neckline',
                'closure_type',
                'pattern',
                'washing_instructions'
            ]
        ];
    }

    public function validateData(array $data): bool
    {
        // Validações específicas para roupas
        $required = ['name', 'size'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Valida tamanho
        if (isset($data['size'])) {
            $validSizes = ['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', '34', '36', '38', '40', '42', '44', '46', '48', '50', '52'];
            if (!in_array(strtoupper($data['size']), $validSizes)) {
                return false;
            }
        }

        // Valida tipo de tecido se fornecido
        if (isset($data['fabric_type'])) {
            $validFabrics = [
                'algodao', 'poliester', 'viscose', 'linho', 'seda', 'la', 'jeans', 
                'elastano', 'nylon', 'modal', 'cashmere', 'couro', 'sintetico'
            ];
            if (!in_array(strtolower($data['fabric_type']), $validFabrics)) {
                return false;
            }
        }

        return true;
    }

    public function processData(array $data): array
    {
        // Processa dados específicos de roupas
        $processed = $data;

        // Normaliza o tamanho
        if (isset($processed['size'])) {
            $processed['size'] = strtoupper($processed['size']);
        }

        // Define instruções de lavagem padrão se não fornecidas
        if (!isset($processed['washing_instructions'])) {
            $processed['washing_instructions'] = $this->getDefaultWashingInstructions($processed);
        }

        // Calcula durabilidade estimada
        $processed['estimated_durability'] = $this->calculateDurability($processed['condition'] ?? 'usado_bom');

        // Define estações recomendadas baseado no tipo de roupa
        if (!isset($processed['recommended_seasons'])) {
            $processed['recommended_seasons'] = $this->inferSeasonsFromClothing($processed);
        }

        // Adiciona tags automáticas
        $processed['auto_tags'] = $this->generateAutoTags($processed);

        return $processed;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'size' => 'required|string|in:PP,P,M,G,GG,XG,XXG,34,36,38,40,42,44,46,48,50,52',
            'fabric_type' => 'nullable|string|in:algodao,poliester,viscose,linho,seda,la,jeans,elastano,nylon,modal,cashmere,couro,sintetico',
            'fit_type' => 'nullable|string|in:slim,regular,loose,oversized,cropped',
            'sleeve_length' => 'nullable|string|in:sem_manga,manga_curta,manga_3_4,manga_longa',
            'neckline' => 'nullable|string|in:redondo,v,canoa,gola_alta,ombro_a_ombro,decote_profundo',
            'closure_type' => 'nullable|string|in:botao,ziper,velcro,amarracao,elastico,nenhum',
            'pattern' => 'nullable|string|in:liso,listrado,xadrez,floral,geometrico,animal_print,abstrato',
            'washing_instructions' => 'nullable|string|max:500',
        ];
    }

    public function getCareInstructions(): array
    {
        $fabric = strtolower($this->data['fabric_type'] ?? 'algodao');
        
        $instructions = [
            'algodao' => [
                'Lavar em água fria ou morna (30°C)',
                'Pode usar máquina de lavar',
                'Secar à sombra',
                'Passar com ferro morno',
                'Pode usar amaciante'
            ],
            'poliester' => [
                'Lavar em água fria (30°C)',
                'Ciclo delicado na máquina',
                'Secar rapidamente',
                'Ferro baixo ou não passar',
                'Evitar amaciante'
            ],
            'seda' => [
                'Lavar à mão ou lavagem a seco',
                'Água fria apenas',
                'Não torcer ou esfregar',
                'Secar na horizontal',
                'Ferro baixo com pano protetor'
            ],
            'la' => [
                'Lavagem a seco recomendada',
                'Se lavar à mão, água fria',
                'Não torcer',
                'Secar na horizontal',
                'Guardar com antitraça'
            ],
            'jeans' => [
                'Lavar do avesso',
                'Água fria para preservar cor',
                'Secar à sombra',
                'Ferro morno',
                'Evitar alvejante'
            ]
        ];

        return $instructions[$fabric] ?? $instructions['algodao'];
    }

    public function calculateDurability(string $condition): int
    {
        $baseDurability = 24; // 2 anos para roupas em geral
        
        $conditionMultiplier = match($condition) {
            'novo' => 1.0,
            'usado_excelente' => 0.8,
            'usado_bom' => 0.6,
            'usado_regular' => 0.4,
            'danificado' => 0.2,
            default => 0.6
        };

        // Ajusta baseado no tipo de tecido
        $fabric = strtolower($this->data['fabric_type'] ?? 'algodao');
        $fabricMultiplier = match($fabric) {
            'jeans', 'couro' => 1.5,
            'la', 'cashmere' => 1.3,
            'algodao', 'linho' => 1.0,
            'poliester', 'nylon' => 0.8,
            'seda', 'viscose' => 0.7,
            default => 1.0
        };

        return (int) round($baseDurability * $conditionMultiplier * $fabricMultiplier);
    }

    public function getRecommendedSeasons(): array
    {
        // Inferir estações baseado no tipo de roupa
        $clothingType = strtolower($this->data['name'] ?? '');
        
        if (str_contains($clothingType, 'casaco') || str_contains($clothingType, 'jaqueta') || str_contains($clothingType, 'sueter')) {
            return ['outono', 'inverno'];
        }
        
        if (str_contains($clothingType, 'shorts') || str_contains($clothingType, 'regata') || str_contains($clothingType, 'biquini')) {
            return ['primavera', 'verao'];
        }
        
        if (str_contains($clothingType, 'vestido') || str_contains($clothingType, 'saia')) {
            return ['primavera', 'verao', 'outono'];
        }
        
        return ['todas']; // Para itens versáteis
    }

    private function getDefaultWashingInstructions(array $data): string
    {
        $fabric = strtolower($data['fabric_type'] ?? 'algodao');
        
        return match($fabric) {
            'seda', 'la', 'cashmere' => 'Lavagem a seco recomendada',
            'jeans' => 'Lavar do avesso em água fria',
            'poliester' => 'Ciclo delicado, água fria',
            default => 'Lavar em água fria, secar à sombra'
        };
    }

    private function inferSeasonsFromClothing(array $data): array
    {
        $name = strtolower($data['name'] ?? '');
        $fabric = strtolower($data['fabric_type'] ?? '');
        
        // Roupas de inverno
        if (str_contains($name, 'casaco') || str_contains($name, 'jaqueta') || 
            str_contains($name, 'sueter') || str_contains($name, 'moletom') ||
            $fabric === 'la' || $fabric === 'cashmere') {
            return ['outono', 'inverno'];
        }
        
        // Roupas de verão
        if (str_contains($name, 'shorts') || str_contains($name, 'regata') || 
            str_contains($name, 'biquini') || str_contains($name, 'maio')) {
            return ['primavera', 'verao'];
        }
        
        return ['todas'];
    }

    private function generateAutoTags(array $data): array
    {
        $tags = [];
        
        // Tags baseadas no tecido
        if (isset($data['fabric_type'])) {
            $tags[] = 'tecido_' . $data['fabric_type'];
        }
        
        // Tags baseadas no ajuste
        if (isset($data['fit_type'])) {
            $tags[] = 'ajuste_' . $data['fit_type'];
        }
        
        // Tags baseadas na estação
        $seasons = $this->getRecommendedSeasons();
        foreach ($seasons as $season) {
            $tags[] = 'estacao_' . $season;
        }
        
        // Tags baseadas na condição
        if (isset($data['condition'])) {
            $tags[] = 'condicao_' . $data['condition'];
        }
        
        return array_unique($tags);
    }
}
