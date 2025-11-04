<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Buscar usuário por email
     */
    public function findByEmail($email);

    /**
     * Buscar usuário com perfil
     */
    public function findWithProfile($id);

    /**
     * Criar usuário com perfil
     */
    public function createWithProfile(array $userData, array $profileData = []);
}

