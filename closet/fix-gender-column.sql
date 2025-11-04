-- Script SQL para corrigir a coluna gender na tabela user_profiles
-- Execute este script diretamente no MySQL ou através do php artisan migrate

-- Passo 1: Atualizar valores existentes
UPDATE user_profiles SET gender = 'male' WHERE gender = 'masculino';
UPDATE user_profiles SET gender = 'female' WHERE gender = 'feminino';
UPDATE user_profiles SET gender = 'other' WHERE gender = 'outro';
UPDATE user_profiles SET gender = 'prefer_not_to_say' WHERE gender = 'prefiro_nao_dizer';

-- Passo 2: Modificar a estrutura da coluna
ALTER TABLE user_profiles MODIFY COLUMN gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL;
