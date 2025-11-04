<?php

namespace App\Patterns\Strategy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 * Contexto do padrão Strategy para filtros
 * 
 * Gerencia e aplica diferentes estratégias de filtro dinamicamente.
 * Este padrão permite trocar algoritmos de filtragem em tempo de execução,
 * facilitando a combinação e reutilização de filtros.
 * 
 * Benefícios:
 * - Flexibilidade: Combinação dinâmica de filtros
 * - Reutilização: Filtros podem ser reutilizados em diferentes contextos
 * - Testabilidade: Cada estratégia pode ser testada isoladamente
 * - Manutenibilidade: Fácil adição de novos filtros
 */
class FilterContext
{
    /**
     * Estratégias de filtro registradas
     */
    private array $strategies = [];

    /**
     * Filtros ativos
     */
    private array $activeFilters = [];

    /**
     * Configurações do contexto
     */
    private array $config = [
        'log_filters' => true,
        'validate_values' => true,
        'cache_results' => false,
    ];

    /**
     * Registra uma estratégia de filtro
     * 
     * @param FilterStrategyInterface $strategy
     * @return void
     */
    public function registerStrategy(FilterStrategyInterface $strategy): void
    {
        $this->strategies[$strategy->getName()] = $strategy;
        
        if ($this->config['log_filters']) {
            Log::info('FilterContext: Estratégia registrada', [
                'strategy' => $strategy->getName(),
                'description' => $strategy->getDescription()
            ]);
        }
    }

    /**
     * Remove uma estratégia de filtro
     * 
     * @param string $strategyName
     * @return void
     */
    public function unregisterStrategy(string $strategyName): void
    {
        if (isset($this->strategies[$strategyName])) {
            unset($this->strategies[$strategyName]);
            
            // Remove dos filtros ativos se estiver lá
            unset($this->activeFilters[$strategyName]);
            
            if ($this->config['log_filters']) {
                Log::info('FilterContext: Estratégia removida', [
                    'strategy' => $strategyName
                ]);
            }
        }
    }

    /**
     * Adiciona um filtro ativo
     * 
     * @param string $strategyName
     * @param mixed $value
     * @return self
     * @throws \InvalidArgumentException
     */
    public function addFilter(string $strategyName, $value): self
    {
        if (!isset($this->strategies[$strategyName])) {
            throw new \InvalidArgumentException("Estratégia não encontrada: {$strategyName}");
        }

        $strategy = $this->strategies[$strategyName];

        // Valida o valor se a validação estiver habilitada
        if ($this->config['validate_values'] && !$strategy->isValidValue($value)) {
            throw new \InvalidArgumentException("Valor inválido para filtro {$strategyName}: " . json_encode($value));
        }

        $this->activeFilters[$strategyName] = $value;

        if ($this->config['log_filters']) {
            Log::debug('FilterContext: Filtro adicionado', [
                'strategy' => $strategyName,
                'value' => $value
            ]);
        }

        return $this;
    }

    /**
     * Remove um filtro ativo
     * 
     * @param string $strategyName
     * @return self
     */
    public function removeFilter(string $strategyName): self
    {
        if (isset($this->activeFilters[$strategyName])) {
            unset($this->activeFilters[$strategyName]);
            
            if ($this->config['log_filters']) {
                Log::debug('FilterContext: Filtro removido', [
                    'strategy' => $strategyName
                ]);
            }
        }

        return $this;
    }

    /**
     * Limpa todos os filtros ativos
     * 
     * @return self
     */
    public function clearFilters(): self
    {
        $count = count($this->activeFilters);
        $this->activeFilters = [];

        if ($this->config['log_filters']) {
            Log::debug('FilterContext: Todos os filtros removidos', [
                'count' => $count
            ]);
        }

        return $this;
    }

    /**
     * Aplica todos os filtros ativos à query
     * 
     * @param Builder $query
     * @return Builder
     */
    public function applyFilters(Builder $query): Builder
    {
        $appliedCount = 0;
        $startTime = microtime(true);

        foreach ($this->activeFilters as $strategyName => $value) {
            try {
                if (isset($this->strategies[$strategyName])) {
                    $strategy = $this->strategies[$strategyName];
                    $query = $strategy->apply($query, $value);
                    $appliedCount++;
                }
            } catch (\Exception $e) {
                Log::error('FilterContext: Erro ao aplicar filtro', [
                    'strategy' => $strategyName,
                    'value' => $value,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $executionTime = microtime(true) - $startTime;

        if ($this->config['log_filters']) {
            Log::info('FilterContext: Filtros aplicados', [
                'applied_count' => $appliedCount,
                'total_active' => count($this->activeFilters),
                'execution_time' => $executionTime
            ]);
        }

        return $query;
    }

    /**
     * Aplica um filtro específico à query
     * 
     * @param Builder $query
     * @param string $strategyName
     * @param mixed $value
     * @return Builder
     * @throws \InvalidArgumentException
     */
    public function applySingleFilter(Builder $query, string $strategyName, $value): Builder
    {
        if (!isset($this->strategies[$strategyName])) {
            throw new \InvalidArgumentException("Estratégia não encontrada: {$strategyName}");
        }

        $strategy = $this->strategies[$strategyName];

        if ($this->config['validate_values'] && !$strategy->isValidValue($value)) {
            throw new \InvalidArgumentException("Valor inválido para filtro {$strategyName}: " . json_encode($value));
        }

        return $strategy->apply($query, $value);
    }

    /**
     * Obtém as estratégias registradas
     * 
     * @return array
     */
    public function getRegisteredStrategies(): array
    {
        return array_map(function($strategy) {
            return [
                'name' => $strategy->getName(),
                'description' => $strategy->getDescription(),
                'possible_values' => $strategy->getPossibleValues(),
                'class' => get_class($strategy)
            ];
        }, $this->strategies);
    }

    /**
     * Obtém os filtros ativos
     * 
     * @return array
     */
    public function getActiveFilters(): array
    {
        return $this->activeFilters;
    }

    /**
     * Verifica se um filtro está ativo
     * 
     * @param string $strategyName
     * @return bool
     */
    public function hasActiveFilter(string $strategyName): bool
    {
        return isset($this->activeFilters[$strategyName]);
    }

    /**
     * Obtém o valor de um filtro ativo
     * 
     * @param string $strategyName
     * @return mixed|null
     */
    public function getFilterValue(string $strategyName)
    {
        return $this->activeFilters[$strategyName] ?? null;
    }

    /**
     * Configura o comportamento do contexto
     * 
     * @param array $config
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        
        if ($this->config['log_filters']) {
            Log::info('FilterContext: Configuração atualizada', [
                'config' => $this->config
            ]);
        }
    }

    /**
     * Obtém estatísticas do contexto
     * 
     * @return array
     */
    public function getStats(): array
    {
        return [
            'registered_strategies' => count($this->strategies),
            'active_filters' => count($this->activeFilters),
            'strategy_names' => array_keys($this->strategies),
            'active_filter_names' => array_keys($this->activeFilters),
            'config' => $this->config
        ];
    }

    /**
     * Cria uma cópia do contexto com os mesmos filtros
     * 
     * @return self
     */
    public function clone(): self
    {
        $clone = new self();
        $clone->strategies = $this->strategies;
        $clone->activeFilters = $this->activeFilters;
        $clone->config = $this->config;
        
        return $clone;
    }

    /**
     * Exporta a configuração atual dos filtros
     * 
     * @return array
     */
    public function exportFilters(): array
    {
        return [
            'active_filters' => $this->activeFilters,
            'timestamp' => now()->toISOString(),
            'strategies_count' => count($this->strategies)
        ];
    }

    /**
     * Importa configuração de filtros
     * 
     * @param array $filterConfig
     * @return self
     */
    public function importFilters(array $filterConfig): self
    {
        if (isset($filterConfig['active_filters'])) {
            $this->activeFilters = [];
            
            foreach ($filterConfig['active_filters'] as $strategyName => $value) {
                try {
                    $this->addFilter($strategyName, $value);
                } catch (\Exception $e) {
                    Log::warning('FilterContext: Erro ao importar filtro', [
                        'strategy' => $strategyName,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $this;
    }
}
