<?php

namespace App\Patterns\Decorator;

use App\Patterns\Decorator\Decorators\FavoriteDecorator;
use App\Patterns\Decorator\Decorators\PromotionDecorator;
use App\Patterns\Decorator\Decorators\UsageDecorator;
use Illuminate\Support\Facades\Log;

/**
 * Gerenciador de decoradores de itens
 * 
 * Coordena a aplicação de múltiplos decoradores aos itens,
 * permitindo composição flexível de funcionalidades.
 * 
 * Este padrão permite adicionar responsabilidades aos objetos
 * dinamicamente, sem alterar sua estrutura básica.
 * 
 * Benefícios:
 * - Composição: Combinação flexível de funcionalidades
 * - Responsabilidade única: Cada decorador tem uma função específica
 * - Extensibilidade: Novos decoradores podem ser adicionados facilmente
 * - Reutilização: Decoradores podem ser aplicados a diferentes itens
 */
class ItemDecoratorManager
{
    /**
     * Decoradores registrados
     */
    private array $decorators = [];

    /**
     * Configurações do gerenciador
     */
    private array $config = [
        'auto_sort_by_priority' => true,
        'log_decorations' => true,
        'skip_invalid_decorators' => true,
    ];

    /**
     * Construtor que registra decoradores padrão
     */
    public function __construct()
    {
        $this->registerDefaultDecorators();
    }

    /**
     * Registra os decoradores padrão
     */
    private function registerDefaultDecorators(): void
    {
        $this->registerDecorator('favorite', FavoriteDecorator::class);
        $this->registerDecorator('promotion', PromotionDecorator::class);
        $this->registerDecorator('usage', UsageDecorator::class);
    }

    /**
     * Registra um decorador
     * 
     * @param string $name
     * @param string $className
     * @return void
     * @throws \InvalidArgumentException
     */
    public function registerDecorator(string $name, string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Classe do decorador não existe: {$className}");
        }

        if (!in_array(ItemDecoratorInterface::class, class_implements($className))) {
            throw new \InvalidArgumentException("Classe deve implementar ItemDecoratorInterface: {$className}");
        }

        $this->decorators[$name] = $className;

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Decorador registrado', [
                'name' => $name,
                'class' => $className
            ]);
        }
    }

    /**
     * Remove um decorador
     * 
     * @param string $name
     * @return void
     */
    public function unregisterDecorator(string $name): void
    {
        if (isset($this->decorators[$name])) {
            unset($this->decorators[$name]);

            if ($this->config['log_decorations']) {
                Log::info('ItemDecoratorManager: Decorador removido', [
                    'name' => $name
                ]);
            }
        }
    }

    /**
     * Decora um item com todos os decoradores aplicáveis
     * 
     * @param array $itemData
     * @param array|null $specificDecorators Lista específica de decoradores a aplicar
     * @return array
     */
    public function decorateItem(array $itemData, ?array $specificDecorators = null): array
    {
        $startTime = microtime(true);
        $decoratorsToApply = $specificDecorators ?? array_keys($this->decorators);
        $appliedDecorators = [];

        // Cria instâncias dos decoradores aplicáveis
        $decoratorInstances = [];
        foreach ($decoratorsToApply as $decoratorName) {
            if (!isset($this->decorators[$decoratorName])) {
                if (!$this->config['skip_invalid_decorators']) {
                    throw new \InvalidArgumentException("Decorador não encontrado: {$decoratorName}");
                }
                continue;
            }

            $className = $this->decorators[$decoratorName];
            $instance = new $className($itemData);

            if ($instance->shouldApply($itemData)) {
                $decoratorInstances[] = [
                    'name' => $decoratorName,
                    'instance' => $instance,
                    'priority' => $instance->getPriority()
                ];
            }
        }

        // Ordena por prioridade se configurado
        if ($this->config['auto_sort_by_priority']) {
            usort($decoratorInstances, function($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });
        }

        // Aplica os decoradores em cadeia
        $decoratedData = $itemData;
        $previousDecorator = null;

        foreach ($decoratorInstances as $decoratorInfo) {
            $decorator = $decoratorInfo['instance'];
            $decoratorName = $decoratorInfo['name'];

            try {
                if ($previousDecorator) {
                    $decorator->setNextDecorator($previousDecorator);
                }

                $decoratedData = array_merge($decoratedData, $decorator->getData());
                $appliedDecorators[] = $decoratorName;
                $previousDecorator = $decorator;

            } catch (\Exception $e) {
                Log::error('ItemDecoratorManager: Erro ao aplicar decorador', [
                    'decorator' => $decoratorName,
                    'item_id' => $itemData['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);

                if (!$this->config['skip_invalid_decorators']) {
                    throw $e;
                }
            }
        }

        $executionTime = microtime(true) - $startTime;

        // Adiciona metadados sobre a decoração
        $decoratedData['decoration_metadata'] = [
            'applied_decorators' => $appliedDecorators,
            'execution_time' => $executionTime,
            'total_decorators_available' => count($this->decorators),
            'decorators_applied' => count($appliedDecorators),
            'decorated_at' => now()->toISOString()
        ];

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Item decorado', [
                'item_id' => $itemData['id'] ?? 'unknown',
                'applied_decorators' => $appliedDecorators,
                'execution_time' => $executionTime
            ]);
        }

        return $decoratedData;
    }

    /**
     * Decora múltiplos itens
     * 
     * @param array $items
     * @param array|null $specificDecorators
     * @return array
     */
    public function decorateItems(array $items, ?array $specificDecorators = null): array
    {
        $decoratedItems = [];
        $startTime = microtime(true);

        foreach ($items as $item) {
            $decoratedItems[] = $this->decorateItem($item, $specificDecorators);
        }

        $executionTime = microtime(true) - $startTime;

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Múltiplos itens decorados', [
                'items_count' => count($items),
                'execution_time' => $executionTime
            ]);
        }

        return $decoratedItems;
    }

    /**
     * Aplica apenas um decorador específico
     * 
     * @param array $itemData
     * @param string $decoratorName
     * @return array
     * @throws \InvalidArgumentException
     */
    public function applySingleDecorator(array $itemData, string $decoratorName): array
    {
        if (!isset($this->decorators[$decoratorName])) {
            throw new \InvalidArgumentException("Decorador não encontrado: {$decoratorName}");
        }

        $className = $this->decorators[$decoratorName];
        $decorator = new $className($itemData);

        if (!$decorator->shouldApply($itemData)) {
            return $itemData; // Retorna dados originais se não deve aplicar
        }

        return $decorator->getData();
    }

    /**
     * Obtém informações sobre decoradores registrados
     * 
     * @return array
     */
    public function getRegisteredDecorators(): array
    {
        return array_map(function($className, $name) {
            // Cria instância temporária para obter informações
            $tempInstance = new $className([]);
            
            return [
                'name' => $name,
                'class' => $className,
                'decorator_name' => $tempInstance->getDecoratorName(),
                'priority' => $tempInstance->getPriority()
            ];
        }, $this->decorators, array_keys($this->decorators));
    }

    /**
     * Verifica se um decorador está registrado
     * 
     * @param string $name
     * @return bool
     */
    public function hasDecorator(string $name): bool
    {
        return isset($this->decorators[$name]);
    }

    /**
     * Obtém a classe de um decorador
     * 
     * @param string $name
     * @return string|null
     */
    public function getDecoratorClass(string $name): ?string
    {
        return $this->decorators[$name] ?? null;
    }

    /**
     * Configura o comportamento do gerenciador
     * 
     * @param array $config
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Configuração atualizada', [
                'config' => $this->config
            ]);
        }
    }

    /**
     * Obtém estatísticas do gerenciador
     * 
     * @return array
     */
    public function getStats(): array
    {
        return [
            'registered_decorators' => count($this->decorators),
            'decorator_names' => array_keys($this->decorators),
            'config' => $this->config
        ];
    }

    /**
     * Limpa todos os decoradores registrados
     * 
     * @return void
     */
    public function clearDecorators(): void
    {
        $count = count($this->decorators);
        $this->decorators = [];

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Todos os decoradores removidos', [
                'count' => $count
            ]);
        }
    }

    /**
     * Redefine para decoradores padrão
     * 
     * @return void
     */
    public function resetToDefaults(): void
    {
        $this->clearDecorators();
        $this->registerDefaultDecorators();

        if ($this->config['log_decorations']) {
            Log::info('ItemDecoratorManager: Resetado para decoradores padrão');
        }
    }
}
