<?php

namespace App\Repositories\Contracts;

interface BudgetRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUser($userId);
    public function findActiveByUser($userId);
    public function findOverBudget($userId);
    public function updateSpentAmount($budgetId, $amount);
}

