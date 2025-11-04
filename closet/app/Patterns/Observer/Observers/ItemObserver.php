<?php

namespace App\Patterns\Observer\Observers;

use App\Patterns\Observer\ObserverInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Observador para eventos relacionados a itens
 * 
 * Reage a eventos como criação, atualização e remoção de itens,
 * executando ações como notificações, atualizações de estatísticas, etc.
 */
class ItemObserver implements ObserverInterface
{
    public function getName(): string
    {
        return 'item_observer';
    }

    public function getInterestedEvents(): array
    {
        return [
            'item.created',
            'item.updated',
            'item.deleted',
            'item.favorited',
            'item.unfavorited',
            'item.worn',
            'item.sold'
        ];
    }

    public function update(string $event, array $data): void
    {
        Log::info('ItemObserver: Processando evento', [
            'event' => $event,
            'item_id' => $data['item_id'] ?? null
        ]);

        switch ($event) {
            case 'item.created':
                $this->handleItemCreated($data);
                break;
            
            case 'item.updated':
                $this->handleItemUpdated($data);
                break;
            
            case 'item.deleted':
                $this->handleItemDeleted($data);
                break;
            
            case 'item.favorited':
                $this->handleItemFavorited($data);
                break;
            
            case 'item.unfavorited':
                $this->handleItemUnfavorited($data);
                break;
            
            case 'item.worn':
                $this->handleItemWorn($data);
                break;
            
            case 'item.sold':
                $this->handleItemSold($data);
                break;
        }
    }

    /**
     * Processa criação de item
     */
    private function handleItemCreated(array $data): void
    {
        // Atualiza estatísticas do usuário
        $this->updateUserStats($data['user_id'], 'items_count', 1);
        
        // Verifica se é o primeiro item da categoria
        $this->checkFirstItemInCategory($data);
        
        // Envia notificação de boas-vindas se for o primeiro item
        $this->checkFirstItemEver($data);
        
        Log::info('ItemObserver: Item criado processado', [
            'item_id' => $data['item_id'],
            'category' => $data['category'] ?? 'unknown'
        ]);
    }

    /**
     * Processa atualização de item
     */
    private function handleItemUpdated(array $data): void
    {
        // Verifica se houve mudança de categoria
        if (isset($data['old_category']) && isset($data['new_category']) && 
            $data['old_category'] !== $data['new_category']) {
            $this->handleCategoryChange($data);
        }
        
        // Verifica se houve mudança de condição
        if (isset($data['old_condition']) && isset($data['new_condition']) && 
            $data['old_condition'] !== $data['new_condition']) {
            $this->handleConditionChange($data);
        }
        
        Log::info('ItemObserver: Item atualizado processado', [
            'item_id' => $data['item_id']
        ]);
    }

    /**
     * Processa remoção de item
     */
    private function handleItemDeleted(array $data): void
    {
        // Atualiza estatísticas do usuário
        $this->updateUserStats($data['user_id'], 'items_count', -1);
        
        // Remove das listas de favoritos se necessário
        $this->cleanupFavorites($data['item_id']);
        
        // Atualiza estatísticas da categoria
        $this->updateCategoryStats($data['category'], -1);
        
        Log::info('ItemObserver: Item removido processado', [
            'item_id' => $data['item_id']
        ]);
    }

    /**
     * Processa item favoritado
     */
    private function handleItemFavorited(array $data): void
    {
        // Atualiza contador de favoritos do usuário
        $this->updateUserStats($data['user_id'], 'favorites_count', 1);
        
        // Registra preferência por categoria
        $this->updateCategoryPreference($data['user_id'], $data['category'], 1);
        
        // Sugere itens similares
        $this->suggestSimilarItems($data);
        
        Log::info('ItemObserver: Item favoritado processado', [
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id']
        ]);
    }

    /**
     * Processa item desfavoritado
     */
    private function handleItemUnfavorited(array $data): void
    {
        // Atualiza contador de favoritos do usuário
        $this->updateUserStats($data['user_id'], 'favorites_count', -1);
        
        // Atualiza preferência por categoria
        $this->updateCategoryPreference($data['user_id'], $data['category'], -1);
        
        Log::info('ItemObserver: Item desfavoritado processado', [
            'item_id' => $data['item_id']
        ]);
    }

    /**
     * Processa item usado/vestido
     */
    private function handleItemWorn(array $data): void
    {
        // Atualiza contador de uso
        $this->updateItemUsage($data['item_id']);
        
        // Atualiza estatísticas de uso por categoria
        $this->updateCategoryUsage($data['category']);
        
        // Verifica se precisa de cuidados especiais
        $this->checkMaintenanceNeeds($data);
        
        Log::info('ItemObserver: Uso de item registrado', [
            'item_id' => $data['item_id'],
            'date' => $data['worn_date'] ?? now()
        ]);
    }

    /**
     * Processa item vendido
     */
    private function handleItemSold(array $data): void
    {
        // Atualiza estatísticas financeiras
        $this->updateFinancialStats($data);
        
        // Remove das listas ativas
        $this->deactivateItem($data['item_id']);
        
        // Sugere reposição se necessário
        $this->suggestReplacement($data);
        
        Log::info('ItemObserver: Item vendido processado', [
            'item_id' => $data['item_id'],
            'sale_price' => $data['sale_price'] ?? 0
        ]);
    }

    /**
     * Atualiza estatísticas do usuário
     */
    private function updateUserStats(int $userId, string $stat, int $increment): void
    {
        // Em uma implementação real, isso atualizaria o banco de dados
        Log::debug('ItemObserver: Atualizando estatística do usuário', [
            'user_id' => $userId,
            'stat' => $stat,
            'increment' => $increment
        ]);
    }

    /**
     * Verifica se é o primeiro item da categoria
     */
    private function checkFirstItemInCategory(array $data): void
    {
        // Lógica para verificar se é o primeiro item da categoria
        // e enviar notificação de conquista
        Log::debug('ItemObserver: Verificando primeiro item da categoria', [
            'category' => $data['category']
        ]);
    }

    /**
     * Verifica se é o primeiro item do usuário
     */
    private function checkFirstItemEver(array $data): void
    {
        // Lógica para verificar se é o primeiro item do usuário
        // e enviar email de boas-vindas
        Log::debug('ItemObserver: Verificando primeiro item do usuário', [
            'user_id' => $data['user_id']
        ]);
    }

    /**
     * Processa mudança de categoria
     */
    private function handleCategoryChange(array $data): void
    {
        $this->updateCategoryStats($data['old_category'], -1);
        $this->updateCategoryStats($data['new_category'], 1);
        
        Log::debug('ItemObserver: Mudança de categoria processada', [
            'from' => $data['old_category'],
            'to' => $data['new_category']
        ]);
    }

    /**
     * Processa mudança de condição
     */
    private function handleConditionChange(array $data): void
    {
        // Se a condição piorou, pode sugerir cuidados ou substituição
        $conditions = ['novo', 'usado_excelente', 'usado_bom', 'usado_regular', 'danificado'];
        $oldIndex = array_search($data['old_condition'], $conditions);
        $newIndex = array_search($data['new_condition'], $conditions);
        
        if ($newIndex > $oldIndex) {
            $this->suggestItemCare($data);
        }
        
        Log::debug('ItemObserver: Mudança de condição processada', [
            'from' => $data['old_condition'],
            'to' => $data['new_condition']
        ]);
    }

    /**
     * Limpa favoritos relacionados ao item removido
     */
    private function cleanupFavorites(int $itemId): void
    {
        Log::debug('ItemObserver: Limpando favoritos do item', [
            'item_id' => $itemId
        ]);
    }

    /**
     * Atualiza estatísticas da categoria
     */
    private function updateCategoryStats(string $category, int $increment): void
    {
        Log::debug('ItemObserver: Atualizando estatísticas da categoria', [
            'category' => $category,
            'increment' => $increment
        ]);
    }

    /**
     * Atualiza preferência por categoria
     */
    private function updateCategoryPreference(int $userId, string $category, int $increment): void
    {
        Log::debug('ItemObserver: Atualizando preferência de categoria', [
            'user_id' => $userId,
            'category' => $category,
            'increment' => $increment
        ]);
    }

    /**
     * Sugere itens similares
     */
    private function suggestSimilarItems(array $data): void
    {
        Log::debug('ItemObserver: Sugerindo itens similares', [
            'based_on_item' => $data['item_id']
        ]);
    }

    /**
     * Atualiza uso do item
     */
    private function updateItemUsage(int $itemId): void
    {
        Log::debug('ItemObserver: Atualizando uso do item', [
            'item_id' => $itemId
        ]);
    }

    /**
     * Atualiza uso da categoria
     */
    private function updateCategoryUsage(string $category): void
    {
        Log::debug('ItemObserver: Atualizando uso da categoria', [
            'category' => $category
        ]);
    }

    /**
     * Verifica necessidades de manutenção
     */
    private function checkMaintenanceNeeds(array $data): void
    {
        Log::debug('ItemObserver: Verificando necessidades de manutenção', [
            'item_id' => $data['item_id']
        ]);
    }

    /**
     * Atualiza estatísticas financeiras
     */
    private function updateFinancialStats(array $data): void
    {
        Log::debug('ItemObserver: Atualizando estatísticas financeiras', [
            'sale_price' => $data['sale_price'] ?? 0
        ]);
    }

    /**
     * Desativa item
     */
    private function deactivateItem(int $itemId): void
    {
        Log::debug('ItemObserver: Desativando item', [
            'item_id' => $itemId
        ]);
    }

    /**
     * Sugere substituição
     */
    private function suggestReplacement(array $data): void
    {
        Log::debug('ItemObserver: Sugerindo substituição', [
            'sold_item' => $data['item_id']
        ]);
    }

    /**
     * Sugere cuidados para o item
     */
    private function suggestItemCare(array $data): void
    {
        Log::debug('ItemObserver: Sugerindo cuidados para o item', [
            'item_id' => $data['item_id'],
            'condition' => $data['new_condition']
        ]);
    }
}
