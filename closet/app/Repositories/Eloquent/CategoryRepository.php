<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function findByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->orderBy('sort_order')
                          ->get();
    }

    public function findDefault()
    {
        return $this->model->where('is_default', true)
                          ->orderBy('sort_order')
                          ->get();
    }

    public function findOrderedByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->ordered()
                          ->get();
    }

    public function createDefaultCategories($userId)
    {
        $defaultCategories = [
            ['name' => 'Camisetas', 'color' => '#FF6B6B', 'icon' => 'shirt', 'sort_order' => 1],
            ['name' => 'Calças', 'color' => '#4ECDC4', 'icon' => 'pants', 'sort_order' => 2],
            ['name' => 'Vestidos', 'color' => '#45B7D1', 'icon' => 'dress', 'sort_order' => 3],
            ['name' => 'Sapatos', 'color' => '#96CEB4', 'icon' => 'shoe', 'sort_order' => 4],
            ['name' => 'Acessórios', 'color' => '#FFEAA7', 'icon' => 'accessory', 'sort_order' => 5],
            ['name' => 'Casacos', 'color' => '#DDA0DD', 'icon' => 'jacket', 'sort_order' => 6],
        ];

        $categories = [];
        foreach ($defaultCategories as $category) {
            $category['user_id'] = $userId;
            $category['is_default'] = true;
            $categories[] = $this->create($category);
        }

        return $categories;
    }
}

