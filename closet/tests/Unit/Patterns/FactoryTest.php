<?php

namespace Tests\Unit\Patterns;

use Tests\TestCase;
use App\Patterns\Factory\ItemFactory;
use App\Patterns\Factory\ItemTypes\ClothingItem;
use App\Patterns\Factory\ItemTypes\AccessoryItem;
use App\Patterns\Factory\ItemTypes\ShoeItem;

/**
 * Testes para o padrão Factory Method
 */
class FactoryTest extends TestCase
{
    /**
     * Testa criação de item de roupa
     */
    public function test_creates_clothing_item()
    {
        $item = ItemFactory::createItemType('camiseta', ['name' => 'Teste']);

        $this->assertInstanceOf(ClothingItem::class, $item);
        $this->assertEquals('clothing', $item->getType());
    }

    /**
     * Testa criação de calçado
     */
    public function test_creates_shoe_item()
    {
        $item = ItemFactory::createItemType('tenis', ['name' => 'Teste']);

        $this->assertInstanceOf(ShoeItem::class, $item);
        $this->assertEquals('shoe', $item->getType());
    }

    /**
     * Testa criação de acessório
     */
    public function test_creates_accessory_item()
    {
        $item = ItemFactory::createItemType('cinto', ['name' => 'Teste']);

        $this->assertInstanceOf(AccessoryItem::class, $item);
        $this->assertEquals('accessory', $item->getType());
    }

    /**
     * Testa criação por tipo específico
     */
    public function test_creates_by_specific_type()
    {
        $clothing = ItemFactory::createByType('clothing', ['name' => 'Teste']);
        $shoe = ItemFactory::createByType('shoe', ['name' => 'Teste']);
        $accessory = ItemFactory::createByType('accessory', ['name' => 'Teste']);

        $this->assertEquals('clothing', $clothing->getType());
        $this->assertEquals('shoe', $shoe->getType());
        $this->assertEquals('accessory', $accessory->getType());
    }

    /**
     * Testa tipos disponíveis
     */
    public function test_returns_available_types()
    {
        $types = ItemFactory::getAvailableTypes();

        $this->assertIsArray($types);
        $this->assertArrayHasKey('clothing', $types);
        $this->assertArrayHasKey('shoe', $types);
        $this->assertArrayHasKey('accessory', $types);
    }

    /**
     * Testa validação de categoria suportada
     */
    public function test_validates_supported_category()
    {
        $this->assertTrue(ItemFactory::isCategorySupported('camiseta'));
        $this->assertTrue(ItemFactory::isCategorySupported('tenis'));
        $this->assertFalse(ItemFactory::isCategorySupported('categoria_inexistente'));
    }

    /**
     * Testa obtenção de informações do tipo
     */
    public function test_gets_type_info()
    {
        $info = ItemFactory::getTypeInfo('camiseta');

        $this->assertIsArray($info);
        $this->assertArrayHasKey('type', $info);
        $this->assertArrayHasKey('characteristics', $info);
        $this->assertArrayHasKey('validation_rules', $info);
        $this->assertArrayHasKey('care_instructions', $info);
    }

    /**
     * Testa categorias para tipo específico
     */
    public function test_gets_categories_for_type()
    {
        $clothingCategories = ItemFactory::getCategoriesForType('clothing');
        $shoeCategories = ItemFactory::getCategoriesForType('shoe');

        $this->assertIsArray($clothingCategories);
        $this->assertIsArray($shoeCategories);
        $this->assertContains('camiseta', $clothingCategories);
        $this->assertContains('tenis', $shoeCategories);
    }

    /**
     * Testa registro de novo tipo
     */
    public function test_registers_new_item_type()
    {
        // Cria uma classe de teste que implementa a interface
        $testClass = new class([]) implements \App\Patterns\Factory\ItemTypeInterface {
            private array $data;
            
            public function __construct(array $data = []) {
                $this->data = $data;
            }
            
            public function getType(): string { return 'test'; }
            public function getCharacteristics(): array { return []; }
            public function validateData(array $data): bool { return true; }
            public function processData(array $data): array { return $data; }
            public function getValidationRules(): array { return []; }
            public function getCareInstructions(): array { return []; }
            public function calculateDurability(string $condition): int { return 12; }
            public function getRecommendedSeasons(): array { return ['todas']; }
        };

        $className = get_class($testClass);
        
        ItemFactory::registerItemType('teste_categoria', $className);
        
        $this->assertTrue(ItemFactory::isCategorySupported('teste_categoria'));
    }

    /**
     * Testa erro ao registrar classe inexistente
     */
    public function test_throws_error_for_nonexistent_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Classe não existe');
        
        ItemFactory::registerItemType('teste', 'ClasseInexistente');
    }

    /**
     * Testa erro ao criar tipo inexistente
     */
    public function test_throws_error_for_invalid_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de item não suportado');
        
        ItemFactory::createByType('tipo_inexistente', []);
    }

    /**
     * Testa processamento de dados específicos do tipo
     */
    public function test_processes_type_specific_data()
    {
        $clothingItem = ItemFactory::createItemType('camiseta', [
            'name' => 'Camiseta Teste',
            'size' => 'm',
            'condition' => 'novo'
        ]);

        $processedData = $clothingItem->processData([
            'name' => 'Camiseta Teste',
            'size' => 'm',
            'condition' => 'novo'
        ]);

        $this->assertIsArray($processedData);
        $this->assertEquals('M', $processedData['size']); // Deve normalizar para maiúsculo
        $this->assertArrayHasKey('estimated_durability', $processedData);
        $this->assertArrayHasKey('auto_tags', $processedData);
    }

    /**
     * Testa validação de dados específicos do tipo
     */
    public function test_validates_type_specific_data()
    {
        $shoeItem = ItemFactory::createItemType('tenis', []);

        // Dados válidos
        $validData = [
            'name' => 'Tênis Teste',
            'size' => '40'
        ];
        $this->assertTrue($shoeItem->validateData($validData));

        // Dados inválidos
        $invalidData = [
            'name' => 'Tênis Teste',
            'size' => '100' // Tamanho inválido
        ];
        $this->assertFalse($shoeItem->validateData($invalidData));
    }
}
