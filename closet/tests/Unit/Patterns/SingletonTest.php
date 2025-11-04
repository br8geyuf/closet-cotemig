<?php

namespace Tests\Unit\Patterns;

use Tests\TestCase;
use App\Patterns\Singleton\DatabaseConnection;

/**
 * Testes para o padrão Singleton
 */
class SingletonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset da instância para cada teste
        DatabaseConnection::resetInstance();
    }

    protected function tearDown(): void
    {
        // Limpa a instância após cada teste
        DatabaseConnection::resetInstance();
        parent::tearDown();
    }

    /**
     * Testa se o Singleton retorna sempre a mesma instância
     */
    public function test_singleton_returns_same_instance()
    {
        $instance1 = DatabaseConnection::getInstance();
        $instance2 = DatabaseConnection::getInstance();

        $this->assertSame($instance1, $instance2);
        $this->assertEquals(spl_object_id($instance1), spl_object_id($instance2));
    }

    /**
     * Testa se a conexão é inicializada corretamente
     */
    public function test_connection_is_initialized()
    {
        $db = DatabaseConnection::getInstance();
        $connection = $db->getConnection();

        $this->assertNotNull($connection);
        $this->assertInstanceOf(\Illuminate\Database\Connection::class, $connection);
    }

    /**
     * Testa se as estatísticas são coletadas
     */
    public function test_stats_are_collected()
    {
        $db = DatabaseConnection::getInstance();
        $stats = $db->getStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('queries_executed', $stats);
        $this->assertArrayHasKey('connection_time', $stats);
        $this->assertArrayHasKey('driver', $stats);
    }

    /**
     * Testa se o teste de conexão funciona
     */
    public function test_connection_test_works()
    {
        $db = DatabaseConnection::getInstance();
        $result = $db->testConnection();

        $this->assertTrue($result);
    }

    /**
     * Testa se queries são executadas e contabilizadas
     */
    public function test_queries_are_tracked()
    {
        $db = DatabaseConnection::getInstance();
        
        $initialStats = $db->getStats();
        $initialCount = $initialStats['queries_executed'];

        // Executa uma query simples
        $db->query('SELECT 1 as test');

        $finalStats = $db->getStats();
        $finalCount = $finalStats['queries_executed'];

        $this->assertEquals($initialCount + 1, $finalCount);
        $this->assertNotNull($finalStats['last_query_time']);
    }

    /**
     * Testa transações
     */
    public function test_transactions_work()
    {
        $db = DatabaseConnection::getInstance();

        // Não deve lançar exceção
        $db->beginTransaction();
        $db->commit();

        $db->beginTransaction();
        $db->rollback();

        $this->assertTrue(true); // Se chegou até aqui, funcionou
    }

    /**
     * Testa reset da instância
     */
    public function test_instance_can_be_reset()
    {
        $instance1 = DatabaseConnection::getInstance();
        $id1 = spl_object_id($instance1);

        DatabaseConnection::resetInstance();

        $instance2 = DatabaseConnection::getInstance();
        $id2 = spl_object_id($instance2);

        $this->assertNotEquals($id1, $id2);
    }

    /**
     * Testa se clonagem é impedida
     */
    public function test_cloning_is_prevented()
    {
        $instance = DatabaseConnection::getInstance();

       $this->expectException(\Error::class);      $clone = clone $instance;
    }

    /**
     * Testa se deserialização é impedida
     */
    public function test_unserialization_is_prevented()
    {
        $instance = DatabaseConnection::getInstance();
        $serialized = serialize($instance);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Serialization of \'PDO\' is not allowed');
        
        unserialize($serialized);
    }
}
