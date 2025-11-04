<?php

namespace App\Repositories\Eloquent;

use App\Patterns\Singleton\DatabaseConnection;
use Illuminate\Support\Facades\Log;

/**
 * Repositório que demonstra o uso do padrão Singleton
 * 
 * Esta classe serve como exemplo de como utilizar o DatabaseConnection
 * Singleton em repositórios para operações de banco de dados.
 */
class SingletonAwareRepository extends BaseRepository
{
    /**
     * Instância do Singleton DatabaseConnection
     */
    protected DatabaseConnection $dbConnection;

    /**
     * Construtor que injeta o Singleton
     */
    public function __construct($model)
    {
        parent::__construct($model);
        
        // Obtém a instância única do DatabaseConnection
        $this->dbConnection = DatabaseConnection::getInstance();
        
        Log::info('SingletonAwareRepository: Inicializado com DatabaseConnection Singleton');
    }

    /**
     * Exemplo de método que usa o Singleton para operações customizadas
     * 
     * @param array $criteria
     * @return array
     */
    public function findWithCustomQuery(array $criteria): array
    {
        $tableName = $this->model->getTable();
        
        // Constrói query dinâmica
        $whereClause = '';
        $bindings = [];
        
        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "{$field} = ?";
                $bindings[] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        $query = "SELECT * FROM {$tableName} {$whereClause}";
        
        // Usa o Singleton para executar a query
        $results = $this->dbConnection->query($query, $bindings);
        
        Log::info('SingletonAwareRepository: Query customizada executada', [
            'table' => $tableName,
            'criteria' => $criteria,
            'results_count' => count($results)
        ]);
        
        return $results;
    }

    /**
     * Método para operações em lote usando transações
     * 
     * @param array $operations
     * @return bool
     */
    public function batchOperations(array $operations): bool
    {
        try {
            // Inicia transação usando o Singleton
            $this->dbConnection->beginTransaction();
            
            foreach ($operations as $operation) {
                switch ($operation['type']) {
                    case 'insert':
                        $this->dbConnection->insert($operation['query'], $operation['bindings'] ?? []);
                        break;
                    case 'update':
                        $this->dbConnection->update($operation['query'], $operation['bindings'] ?? []);
                        break;
                    case 'delete':
                        $this->dbConnection->delete($operation['query'], $operation['bindings'] ?? []);
                        break;
                }
            }
            
            // Confirma todas as operações
            $this->dbConnection->commit();
            
            Log::info('SingletonAwareRepository: Operações em lote executadas com sucesso', [
                'operations_count' => count($operations)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            // Desfaz todas as operações em caso de erro
            $this->dbConnection->rollback();
            
            Log::error('SingletonAwareRepository: Erro nas operações em lote', [
                'error' => $e->getMessage(),
                'operations_count' => count($operations)
            ]);
            
            return false;
        }
    }

    /**
     * Obtém estatísticas de uso do banco de dados
     * 
     * @return array
     */
    public function getDatabaseStats(): array
    {
        return $this->dbConnection->getStats();
    }

    /**
     * Testa a conexão com o banco
     * 
     * @return bool
     */
    public function testDatabaseConnection(): bool
    {
        return $this->dbConnection->testConnection();
    }

    /**
     * Exemplo de método que demonstra reutilização da mesma instância
     * 
     * @return array
     */
    public function demonstrateSingletonReuse(): array
    {
        // Obtém uma nova "instância" (que será a mesma devido ao Singleton)
        $anotherConnection = DatabaseConnection::getInstance();
        
        // Verifica se é a mesma instância
        $isSameInstance = $this->dbConnection === $anotherConnection;
        
        // Obtém estatísticas de ambas as "instâncias"
        $stats1 = $this->dbConnection->getStats();
        $stats2 = $anotherConnection->getStats();
        
        Log::info('SingletonAwareRepository: Demonstração de reutilização do Singleton', [
            'is_same_instance' => $isSameInstance,
            'stats_are_equal' => $stats1 === $stats2
        ]);
        
        return [
            'is_same_instance' => $isSameInstance,
            'stats_from_first' => $stats1,
            'stats_from_second' => $stats2,
            'stats_are_equal' => $stats1 === $stats2
        ];
    }
}
