<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    /**
     * Buscar todos os registros
     */
    public function all();

    /**
     * Buscar registro por ID
     */
    public function find($id);

    /**
     * Buscar registro por ID ou falhar
     */
    public function findOrFail($id);

    /**
     * Criar novo registro
     */
    public function create(array $data);

    /**
     * Atualizar registro
     */
    public function update($id, array $data);

    /**
     * Deletar registro
     */
    public function delete($id);

    /**
     * Buscar por critérios
     */
    public function findBy(array $criteria);

    /**
     * Buscar primeiro registro por critérios
     */
    public function findOneBy(array $criteria);

    /**
     * Contar registros
     */
    public function count();

    /**
     * Paginação
     */
    public function paginate($perPage = 15);
}

