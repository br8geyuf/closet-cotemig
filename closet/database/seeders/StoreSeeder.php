<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Zara',
                'description' => 'Moda contemporânea para homens, mulheres e crianças',
                'website' => 'https://www.zara.com.br',
                'phone' => '(11) 3000-0000',
                'address' => 'Shopping Iguatemi',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '04094-000',
                'type' => 'ambas',
                'social_media' => [
                    'instagram' => '@zara',
                    'facebook' => 'ZaraBrasil'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'H&M',
                'description' => 'Moda acessível e sustentável',
                'website' => 'https://www2.hm.com/pt_br',
                'phone' => '(11) 3001-0000',
                'address' => 'Shopping Morumbi',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '05650-000',
                'type' => 'ambas',
                'social_media' => [
                    'instagram' => '@hm',
                    'facebook' => 'HMBrasil'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Renner',
                'description' => 'Moda brasileira para toda a família',
                'website' => 'https://www.lojasrenner.com.br',
                'phone' => '(51) 3165-4000',
                'address' => 'Rua Gonçalves Dias, 100',
                'city' => 'Porto Alegre',
                'state' => 'RS',
                'zip_code' => '90130-060',
                'type' => 'ambas',
                'social_media' => [
                    'instagram' => '@lojasrenner',
                    'facebook' => 'LojasRenner'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Shein',
                'description' => 'Moda online internacional',
                'website' => 'https://br.shein.com',
                'type' => 'online',
                'social_media' => [
                    'instagram' => '@shein_brasil',
                    'facebook' => 'SheinBrasil'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
