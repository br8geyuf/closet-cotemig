<?php

namespace App\Repositories\Eloquent;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

    public function findByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
                          ->with(['user', 'store'])
                          ->get();
    }

    public function findFavoritesByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->where('is_favorite', true)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findByFilters(array $filters, $userId)
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand'])) {
            $query->where('brand', 'like', '%' . $filters['brand'] . '%');
        }

        if (isset($filters['color'])) {
            $query->whereJsonContains('colors', $filters['color']);
        }

        if (isset($filters['season'])) {
            $query->where(function($q) use ($filters) {
                $q->where('season', $filters['season'])
                  ->orWhere('season', 'todas');
            });
        }

        if (isset($filters['occasion'])) {
            $query->where(function($q) use ($filters) {
                $q->where('occasion', $filters['occasion'])
                  ->orWhere('occasion', 'todas');
            });
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['price_min'])) {
            $query->where('purchase_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('purchase_price', '<=', $filters['price_max']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        return $query->with(['category', 'store'])->get();
    }

    public function findBySeason($season, $userId)
    {
        return $this->model->where('user_id', $userId)
                          ->bySeason($season)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findByOccasion($occasion, $userId)
    {
        return $this->model->where('user_id', $userId)
                          ->byOccasion($occasion)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findMostUsed($userId, $limit = 10)
    {
        return $this->model->where('user_id', $userId)
                          ->orderBy('usage_count', 'desc')
                          ->limit($limit)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findLeastUsed($userId, $limit = 10)
    {
        return $this->model->where('user_id', $userId)
                          ->orderBy('usage_count', 'asc')
                          ->limit($limit)
                          ->with(['category', 'store'])
                          ->get();
    }

    public function findRecentByUser($userId, $limit = 5)
    {
        return $this->model->where('user_id', $userId)
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->with(['category', 'store'])
                          ->get();
    }
}
