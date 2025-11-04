<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de autenticação
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'company' => [ // 👈 guard exclusivo para empresas
            'driver' => 'session',
            'provider' => 'companies',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers (de onde os usuários/empresas vêm)
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'companies' => [ // 👈 provider exclusivo para empresas
            'driver' => 'eloquent',
            'model' => App\Models\Company::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuração de reset de senhas
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets', // 🔹 usar nome padrão
            'expire' => 60,
            'throttle' => 60,
        ],

        'companies' => [
            'provider' => 'companies',
            'table' => 'company_password_resets', // 🔹 separa da tabela dos users
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tempo antes de expirar a sessão de confirmação da senha
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];
