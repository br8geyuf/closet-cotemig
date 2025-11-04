<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use App\Models\Store;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $companies = Company::all();
        $categories = Category::all();
        $stores = Store::all();

        if ($users->isEmpty() || $companies->isEmpty() || $categories->isEmpty() || $stores->isEmpty()) {
            $this->call(UserSeeder::class);
            $this->call(CompanySeeder::class);
            $this->call(CategorySeeder::class);
            $this->call(StoreSeeder::class);
            $users = User::all();
            $companies = Company::all();
            $categories = Category::all();
            $stores = Store::all();
        }

        Item::factory(20)->create([
            'user_id' => $users->random()->id,
            'company_id' => $companies->random()->id,
            'category_id' => $categories->random()->id,
            'store_id' => $stores->random()->id,
        ]);
    }
}

