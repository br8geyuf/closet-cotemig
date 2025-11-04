<?php

namespace App\Repositories\Eloquent;

use App\Models\Budget;
use App\Repositories\Contracts\BudgetRepositoryInterface;

class BudgetRepository extends BaseRepository implements BudgetRepositoryInterface
{
    public function __construct(Budget $model)
    {
        parent::__construct($model);
    }

    public function findByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findActiveByUser($userId)
    {
        return $this->model->where('user_id', $userId)->active()->get();
    }

    public function findOverBudget($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->whereRaw('spent_amount > total_amount')
                          ->get();
    }

    public function updateSpentAmount($budgetId, $amount)
    {
        $budget = $this->findOrFail($budgetId);
        $budget->increment('spent_amount', $amount);
        return $budget;
    }
}

