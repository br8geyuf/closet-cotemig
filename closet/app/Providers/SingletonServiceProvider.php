<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Patterns\Singleton\DatabaseConnection;

/**
 * Service Provider para registrar o padrão Singleton
 * 
 * Este provider garante que o Singleton seja registrado corretamente
 * no container de injeção de dependência do Laravel.
 */
class SingletonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registra o DatabaseConnection como singleton no container
        $this->app->singleton(DatabaseConnection::class, function ($app) {
            return DatabaseConnection::getInstance();
        });

        // Registra um alias para facilitar o uso
        $this->app->alias(DatabaseConnection::class, 'database.singleton');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Aqui podemos adicionar configurações adicionais se necessário
        // Por exemplo, registrar listeners ou middleware
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            DatabaseConnection::class,
            'database.singleton',
        ];
    }
}
