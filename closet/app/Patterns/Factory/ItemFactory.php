<?php

namespace App\Patterns\Factory;

use App\Patterns\Factory\ItemTypes\ClothingItem;
use App\Patterns\Factory\ItemTypes\AccessoryItem;
use App\Patterns\Factory\ItemTypes\ShoeItem;
use App\Patterns\Factory\ItemTypes\BagItem;
use App\Patterns\Factory\ItemTypes\JewelryItem;
use Illuminate\Support\Facades\Log;

/**
 * Factory Method para criação de diferentes tipos de itens
 * 
 * Este padrão permite criar objetos sem especificar suas classes concretas,
 * facilitando a adição de novos tipos de itens sem modificar código existente.
 * 
 * Benefícios:
 * - Flexibilidade: Fácil adição de novos tipos
 * - Manutenibilidade: Lógica de criação centralizada
 * - Extensibilidade: Suporte a comportamentos específicos
 * - Testabilidade: Cada tipo pode ser testado isoladamente
 */
class ItemFactory
{
    /**
     * Mapeamento de categorias para tipos de itens
     */
    private static array $TYPE_MAPPING = [
        // Roupas
        'camiseta' => ClothingItem::class,
        'camisa' => ClothingItem::class,
        'blusa' => ClothingItem::class,
        'vestido' => ClothingItem::class,
        'saia' => ClothingItem::class,
        'calca' => ClothingItem::class,
        'shorts' => ClothingItem::class,
        'jaqueta' => ClothingItem::class,
        'casaco' => ClothingItem::class,
        'blazer' => ClothingItem::class,
        'sueter' => ClothingItem::class,
        'moletom' => ClothingItem::class,
        'lingerie' => ClothingItem::class,
        'pijama' => ClothingItem::class,
        'roupa_intima' => ClothingItem::class,
        
        // Calçados
        'tenis' => ShoeItem::class,
        'sapato' => ShoeItem::class,
        'sandalia' => ShoeItem::class,
        'chinelo' => ShoeItem::class,
        'bota' => ShoeItem::class,
        'sapatilha' => ShoeItem::class,
        'salto' => ShoeItem::class,
        'calcado_esportivo' => ShoeItem::class,
        
        // Bolsas
        'bolsa' => BagItem::class,
        'mochila' => BagItem::class,
        'carteira' => BagItem::class,
        'clutch' => BagItem::class,
        'necessaire' => BagItem::class,
        'mala' => BagItem::class,
        
        // Acessórios
        'cinto' => AccessoryItem::class,
        'chapeu' => AccessoryItem::class,
        'bone' => AccessoryItem::class,
        'oculos' => AccessoryItem::class,
        'lenco' => AccessoryItem::class,
        'cachecol' => AccessoryItem::class,
        'luva' => AccessoryItem::class,
        'gravata' => AccessoryItem::class,
        'suspensorio' => AccessoryItem::class,
        
        // Joias
        'colar' => JewelryItem::class,
        'pulseira' => JewelryItem::class,
        'anel' => JewelryItem::class,
        'brinco' => JewelryItem::class,
        'relogio' => JewelryItem::class,
        'broche' => JewelryItem::class,
    ];

    /**
     * Cria um tipo de item baseado na categoria
     * 
     * @param string $categoryName Nome da categoria
     * @param array $itemData Dados do item
     * @return ItemTypeInterface
     * @throws \InvalidArgumentException
     */
    public static function createItemType(string $categoryName, array $itemData = []): ItemTypeInterface
    {
        $normalizedCategory = self::normalizeCategory($categoryName);
        
        if (!isset(self::$TYPE_MAPPING[$normalizedCategory])) {
            Log::warning('ItemFactory: Categoria não mapeada, usando AccessoryItem como padrão', [
                'category' => $categoryName,
                'normalized' => $normalizedCategory
            ]);
            
            // Usa AccessoryItem como padrão para categorias não mapeadas
            $className = AccessoryItem::class;
        } else {
            $className = self::$TYPE_MAPPING[$normalizedCategory];
        }

        Log::info('ItemFactory: Criando tipo de item', [
            'category' => $categoryName,
            'type_class' => $className
        ]);

        return new $className($itemData);
    }

    /**
     * Cria um tipo de item baseado no tipo específico
     * 
     * @param string $type Tipo específico (clothing, accessory, shoe, bag, jewelry)
     * @param array $itemData Dados do item
     * @return ItemTypeInterface
     * @throws \InvalidArgumentException
     */
    public static function createByType(string $type, array $itemData = []): ItemTypeInterface
    {
        $typeMapping = [
            'clothing' => ClothingItem::class,
            'accessory' => AccessoryItem::class,
            'shoe' => ShoeItem::class,
            'bag' => BagItem::class,
            'jewelry' => JewelryItem::class,
        ];

        if (!isset($typeMapping[$type])) {
            throw new \InvalidArgumentException("Tipo de item não suportado: {$type}");
        }

        $className = $typeMapping[$type];
        
        Log::info('ItemFactory: Criando item por tipo específico', [
            'type' => $type,
            'type_class' => $className
        ]);

        return new $className($itemData);
    }

    /**
     * Obtém todos os tipos disponíveis
     * 
     * @return array
     */
    public static function getAvailableTypes(): array
    {
        return [
            'clothing' => 'Roupas',
            'accessory' => 'Acessórios',
            'shoe' => 'Calçados',
            'bag' => 'Bolsas',
            'jewelry' => 'Joias',
        ];
    }

    /**
     * Obtém as categorias mapeadas para um tipo específico
     * 
     * @param string $type
     * @return array
     */
    public static function getCategoriesForType(string $type): array
    {
        $typeClass = match($type) {
            'clothing' => ClothingItem::class,
            'accessory' => AccessoryItem::class,
            'shoe' => ShoeItem::class,
            'bag' => BagItem::class,
            'jewelry' => JewelryItem::class,
            default => null
        };

        if (!$typeClass) {
            return [];
        }

        return array_keys(array_filter(self::$TYPE_MAPPING, fn($class) => $class === $typeClass));
    }

    /**
     * Valida se uma categoria é suportada
     * 
     * @param string $categoryName
     * @return bool
     */
    public static function isCategorySupported(string $categoryName): bool
    {
        $normalizedCategory = self::normalizeCategory($categoryName);
        return isset(self::$TYPE_MAPPING[$normalizedCategory]);
    }

    /**
     * Obtém informações sobre um tipo de item sem instanciá-lo
     * 
     * @param string $categoryName
     * @return array
     */
    public static function getTypeInfo(string $categoryName): array
    {
        $normalizedCategory = self::normalizeCategory($categoryName);
        $className = self::$TYPE_MAPPING[$normalizedCategory] ?? AccessoryItem::class;
        
        // Cria uma instância temporária para obter informações
        $tempInstance = new $className([]);
        
        return [
            'type' => $tempInstance->getType(),
            'characteristics' => $tempInstance->getCharacteristics(),
            'validation_rules' => $tempInstance->getValidationRules(),
            'care_instructions' => $tempInstance->getCareInstructions(),
            'recommended_seasons' => $tempInstance->getRecommendedSeasons(),
        ];
    }

    /**
     * Normaliza o nome da categoria para busca no mapeamento
     * 
     * @param string $categoryName
     * @return string
     */
    private static function normalizeCategory(string $categoryName): string
    {
        return strtolower(
            str_replace([' ', '-', '_'], '_', 
                trim(
                    preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $categoryName)
                )
            )
        );
    }

    /**
     * Registra um novo tipo de item
     * 
     * @param string $categoryName
     * @param string $className
     * @throws \InvalidArgumentException
     */
    public static function registerItemType(string $categoryName, string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Classe não existe: {$className}");
        }

        if (!in_array(ItemTypeInterface::class, class_implements($className))) {
            throw new \InvalidArgumentException("Classe deve implementar ItemTypeInterface: {$className}");
        }

        $normalizedCategory = self::normalizeCategory($categoryName);
        self::$TYPE_MAPPING[$normalizedCategory] = $className;

        Log::info('ItemFactory: Novo tipo de item registrado', [
            'category' => $categoryName,
            'normalized' => $normalizedCategory,
            'class' => $className
        ]);
    }
}
