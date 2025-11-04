<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Maria Fashion',
            'email' => 'maria@closet.com',
        ]);

        User::factory()->create([
            'name' => 'Lucas Brechó',
            'email' => 'lucas@closet.com',
        ]);

        User::factory()->create([
            'name' => 'Ana Promoções',
            'email' => 'ana@closet.com',
        ]);
    }
}
