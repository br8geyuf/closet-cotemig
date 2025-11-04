<?php

namespace App\Patterns\Observer\Observers;

use App\Patterns\Observer\ObserverInterface;
use Illuminate\Support\Facades\Log;

/**
 * Observador para eventos relacionados a orçamentos
 */
class BudgetObserver implements ObserverInterface
{
    public function getName(): string
    {
        return 'budget_observer';
    }

    public function getInterestedEvents(): array
    {
        return [
            'budget.created',
            'budget.updated',
            'budget.exceeded',
            'budget.warning',
            'purchase.made'
        ];
    }

    public function update(string $event, array $data): void
    {
        Log::info('BudgetObserver: Processando evento', [
            'event' => $event,
            'budget_id' => $data['budget_id'] ?? null
        ]);

        switch ($event) {
            case 'budget.exceeded':
                $this->handleBudgetExceeded($data);
                break;
            case 'budget.warning':
                $this->handleBudgetWarning($data);
                break;
            case 'purchase.made':
                $this->handlePurchaseMade($data);
                break;
        }
    }

    private function handleBudgetExceeded(array $data): void
    {
        // Enviar notificação de orçamento excedido
        Log::warning('BudgetObserver: Orçamento excedido', $data);
    }

    private function handleBudgetWarning(array $data): void
    {
        // Enviar aviso de aproximação do limite
        Log::info('BudgetObserver: Aviso de orçamento', $data);
    }

    private function handlePurchaseMade(array $data): void
    {
        // Atualizar gastos do orçamento
        Log::info('BudgetObserver: Compra registrada', $data);
    }
}
