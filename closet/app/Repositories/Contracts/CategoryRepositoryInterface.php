<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Buscar categorias do usuário
     */
    public function findByUser($userId);

    /**
     * Buscar categorias padrão
     */
    public function findDefault();

    /**
     * Buscar categorias ordenadas
     */
    public function findOrderedByUser($userId);

    /**
     * Criar categorias padrão para usuário
     */
    public function createDefaultCategories($userId);
}

