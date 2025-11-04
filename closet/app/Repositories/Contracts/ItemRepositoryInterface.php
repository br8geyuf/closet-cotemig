<?php

namespace App\Repositories\Contracts;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Buscar itens do usuário
     */
    public function findByUser($userId);

    /**
     * Buscar itens por categoria
     */
    public function findByCategory($categoryId);

    /**
     * Buscar itens favoritos do usuário
     */
    public function findFavoritesByUser($userId);

    /**
     * Buscar itens por filtros avançados
     */
    public function findByFilters(array $filters, $userId);

    /**
     * Buscar itens por temporada
     */
    public function findBySeason($season, $userId);

    /**
     * Buscar itens por ocasião
     */
    public function findByOccasion($occasion, $userId);

    /**
     * Buscar itens mais usados
     */
    public function findMostUsed($userId, $limit = 10);

    /**
     * Buscar itens menos usados
     */
    public function findLeastUsed($userId, $limit = 10);

    /**
     * Buscar itens mais recentes
     */
    public function findRecentByUser($userId, $limit = 5);
}
