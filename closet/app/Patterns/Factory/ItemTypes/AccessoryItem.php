<?php

namespace App\Patterns\Factory\ItemTypes;

use App\Patterns\Factory\ItemTypeInterface;

/**
 * Implementação específica para acessórios
 * 
 * Define comportamentos e características específicas para acessórios,
 * incluindo validações e cuidados específicos.
 */
class AccessoryItem implements ItemTypeInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return 'accessory';
    }

    public function getCharacteristics(): array
    {
        return [
            'has_size' => true,
            'has_material' => true,
            'has_color' => true,
            'requires_special_care' => true,
            'can_be_combined' => true,
            'seasonal_usage' => false,
            'versatile' => true,
            'specific_attributes' => [
                'material_type',
                'adjustable',
                'waterproof',
                'uv_protection',
                'care_level',
                'style_category'
            ]
        ];
    }

    public function validateData(array $data): bool
    {
        // Validações específicas para acessórios
        $required = ['name'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Valida material se fornecido
        if (isset($data['material_type'])) {
            $validMaterials = [
                'couro', 'metal', 'plastico', 'tecido', 'madeira', 'vidro', 
                'ceramica', 'borracha', 'silicone', 'ouro', 'prata', 'aco'
            ];
            if (!in_array(strtolower($data['material_type']), $validMaterials)) {
                return false;
            }
        }

        // Valida categoria de estilo se fornecida
        if (isset($data['style_category'])) {
            $validStyles = ['casual', 'formal', 'esportivo', 'vintage', 'moderno', 'boho', 'minimalista'];
            if (!in_array(strtolower($data['style_category']), $validStyles)) {
                return false;
            }
        }

        return true;
    }

    public function processData(array $data): array
    {
        $processed = $data;

        // Define nível de cuidado baseado no material
        if (!isset($processed['care_level'])) {
            $processed['care_level'] = $this->determineCareLevel($processed);
        }

        // Calcula durabilidade estimada
        $processed['estimated_durability'] = $this->calculateDurability($processed['condition'] ?? 'usado_bom');

        // Define versatilidade do acessório
        $processed['versatility_score'] = $this->calculateVersatility($processed);

        // Adiciona tags automáticas
        $processed['auto_tags'] = $this->generateAutoTags($processed);

        // Define ocasiões recomendadas
        $processed['recommended_occasions'] = $this->getRecommendedOccasions($processed);

        return $processed;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'material_type' => 'nullable|string|in:couro,metal,plastico,tecido,madeira,vidro,ceramica,borracha,silicone,ouro,prata,aco',
            'adjustable' => 'nullable|boolean',
            'waterproof' => 'nullable|boolean',
            'uv_protection' => 'nullable|boolean',
            'style_category' => 'nullable|string|in:casual,formal,esportivo,vintage,moderno,boho,minimalista',
            'care_level' => 'nullable|string|in:baixo,medio,alto',
            'size' => 'nullable|string|max:50',
        ];
    }

    public function getCareInstructions(): array
    {
        $material = strtolower($this->data['material_type'] ?? 'tecido');
        
        $instructions = [
            'couro' => [
                'Limpar com pano úmido',
                'Usar produtos específicos para couro',
                'Evitar exposição direta ao sol',
                'Guardar em local arejado',
                'Hidratar periodicamente'
            ],
            'metal' => [
                'Limpar com pano seco',
                'Evitar contato com água',
                'Usar produtos anti-oxidação',
                'Guardar em local seco',
                'Polir ocasionalmente'
            ],
            'ouro' => [
                'Limpar com pano macio',
                'Evitar produtos químicos',
                'Guardar separadamente',
                'Limpar com água morna e sabão neutro',
                'Secar completamente'
            ],
            'prata' => [
                'Limpar com pano específico',
                'Evitar exposição ao ar',
                'Usar produtos anti-oxidação',
                'Guardar em saquinhos',
                'Polir regularmente'
            ],
            'tecido' => [
                'Lavar conforme instruções',
                'Secar à sombra',
                'Passar se necessário',
                'Guardar limpo e seco',
                'Evitar dobras excessivas'
            ],
            'plastico' => [
                'Limpar com água e sabão',
                'Evitar produtos abrasivos',
                'Secar completamente',
                'Guardar em local fresco',
                'Evitar exposição ao calor'
            ]
        ];

        return $instructions[$material] ?? $instructions['tecido'];
    }

    public function calculateDurability(string $condition): int
    {
        $baseDurability = 36; // 3 anos para acessórios em geral
        
        $conditionMultiplier = match($condition) {
            'novo' => 1.0,
            'usado_excelente' => 0.8,
            'usado_bom' => 0.6,
            'usado_regular' => 0.4,
            'danificado' => 0.2,
            default => 0.6
        };

        // Ajusta baseado no material
        $material = strtolower($this->data['material_type'] ?? 'tecido');
        $materialMultiplier = match($material) {
            'ouro', 'prata', 'aco' => 2.0,
            'couro', 'metal' => 1.5,
            'madeira', 'ceramica' => 1.2,
            'tecido', 'plastico' => 1.0,
            'borracha', 'silicone' => 0.8,
            default => 1.0
        };

        return (int) round($baseDurability * $conditionMultiplier * $materialMultiplier);
    }

    public function getRecommendedSeasons(): array
    {
        $accessoryType = strtolower($this->data['name'] ?? '');
        
        // Acessórios de inverno
        if (str_contains($accessoryType, 'cachecol') || str_contains($accessoryType, 'luva') || 
            str_contains($accessoryType, 'gorro')) {
            return ['outono', 'inverno'];
        }
        
        // Acessórios de verão
        if (str_contains($accessoryType, 'oculos') || str_contains($accessoryType, 'chapeu') || 
            str_contains($accessoryType, 'bone')) {
            return ['primavera', 'verao'];
        }
        
        return ['todas']; // Maioria dos acessórios são versáteis
    }

    private function determineCareLevel(array $data): string
    {
        $material = strtolower($data['material_type'] ?? 'tecido');
        
        return match($material) {
            'ouro', 'prata', 'couro' => 'alto',
            'metal', 'madeira', 'ceramica' => 'medio',
            'tecido', 'plastico', 'borracha', 'silicone' => 'baixo',
            default => 'medio'
        };
    }

    private function calculateVersatility(array $data): int
    {
        $score = 5; // Base score
        
        // Aumenta score para cores neutras
        $colors = $data['colors'] ?? [];
        $neutralColors = ['preto', 'branco', 'cinza', 'bege', 'marrom'];
        foreach ($colors as $color) {
            if (in_array(strtolower($color), $neutralColors)) {
                $score += 2;
            }
        }
        
        // Aumenta score para estilos versáteis
        $style = strtolower($data['style_category'] ?? '');
        if (in_array($style, ['casual', 'minimalista', 'moderno'])) {
            $score += 2;
        }
        
        // Aumenta score se for ajustável
        if (!empty($data['adjustable'])) {
            $score += 1;
        }
        
        return min($score, 10); // Máximo 10
    }

    private function generateAutoTags(array $data): array
    {
        $tags = [];
        
        // Tags baseadas no material
        if (isset($data['material_type'])) {
            $tags[] = 'material_' . $data['material_type'];
        }
        
        // Tags baseadas no estilo
        if (isset($data['style_category'])) {
            $tags[] = 'estilo_' . $data['style_category'];
        }
        
        // Tags baseadas nas características
        if (!empty($data['waterproof'])) {
            $tags[] = 'a_prova_dagua';
        }
        
        if (!empty($data['uv_protection'])) {
            $tags[] = 'protecao_uv';
        }
        
        if (!empty($data['adjustable'])) {
            $tags[] = 'ajustavel';
        }
        
        // Tags baseadas na versatilidade
        $versatility = $this->calculateVersatility($data);
        if ($versatility >= 8) {
            $tags[] = 'muito_versatil';
        } elseif ($versatility >= 6) {
            $tags[] = 'versatil';
        }
        
        return array_unique($tags);
    }

    private function getRecommendedOccasions(array $data): array
    {
        $style = strtolower($data['style_category'] ?? 'casual');
        $material = strtolower($data['material_type'] ?? 'tecido');
        
        $occasions = [];
        
        // Baseado no estilo
        switch ($style) {
            case 'formal':
                $occasions = ['trabalho', 'festa', 'formal'];
                break;
            case 'esportivo':
                $occasions = ['esporte', 'casual'];
                break;
            case 'casual':
                $occasions = ['casual', 'trabalho'];
                break;
            default:
                $occasions = ['todas'];
        }
        
        // Ajusta baseado no material
        if (in_array($material, ['ouro', 'prata'])) {
            $occasions[] = 'festa';
            $occasions[] = 'formal';
        }
        
        return array_unique($occasions);
    }
}
