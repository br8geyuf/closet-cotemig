# 🔧 Guia de Solução de Problemas - Closet Fashion 

## ❌ Erro: "Data truncated for column 'gender'" ao editar perfil

### 📋 Descrição do Problema

Este erro ocorre porque a coluna `gender` na tabela `user_profiles` está configurada com valores em português (`masculino`, `feminino`, etc.), mas o formulário envia valores em inglês (`male`, `female`, etc.).

### ✅ Solução Rápida

**Opção 1: Executar a migration de correção**

```bash
php artisan migrate
```

Isso executará automaticamente a migration `2025_10_07_000000_fix_gender_column_in_user_profiles.php` que corrige a estrutura da coluna.

**Opção 2: Executar o script SQL manualmente**

Se preferir, execute o arquivo `fix-gender-column.sql` diretamente no MySQL:

```bash
mysql -u seu_usuario -p seu_banco_de_dados < fix-gender-column.sql
```

Ou copie e cole o conteúdo do arquivo diretamente no phpMyAdmin ou outro cliente MySQL.

**Opção 3: Comandos SQL diretos**

Execute no MySQL:

```sql
UPDATE user_profiles SET gender = 'male' WHERE gender = 'masculino';
UPDATE user_profiles SET gender = 'female' WHERE gender = 'feminino';
UPDATE user_profiles SET gender = 'other' WHERE gender = 'outro';
UPDATE user_profiles SET gender = 'prefer_not_to_say' WHERE gender = 'prefiro_nao_dizer';

ALTER TABLE user_profiles MODIFY COLUMN gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL;
```

---

## ❌ Erro: "Failed to open stream: No such file or directory" em storage/framework/sessions

### 📋 Descrição do Problema

Este erro ocorre quando os diretórios necessários do Laravel não existem na estrutura do projeto. Isso é comum quando o projeto é extraído de um arquivo ZIP, pois o Git não versiona diretórios vazios.

### ✅ Solução Rápida (Windows)

**Opção 1: Usar o script automático**

1. Abra o **Prompt de Comando** ou **PowerShell** como Administrador
2. Navegue até a pasta do projeto:
   ```cmd
   cd C:\Users\Guilherme\Downloads\closet-fashion-gof
   ```
3. Execute o script de correção:
   ```cmd
   fix-storage.bat
   ```

**Opção 2: Comandos manuais**

Execute os seguintes comandos no terminal dentro da pasta do projeto:

```cmd
REM Criar diretórios
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\framework\cache\data
mkdir storage\logs
mkdir storage\app\public\avatars
mkdir bootstrap\cache

REM Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

REM Criar link simbólico
php artisan storage:link
```

### ✅ Solução Rápida (Linux/Mac)

Execute no terminal:

```bash
# Criar estrutura de diretórios
mkdir -p storage/framework/{sessions,views,cache/data}
mkdir -p storage/{logs,app/public/avatars}
mkdir -p bootstrap/cache

# Ajustar permissões
chmod -R 775 storage bootstrap/cache

# Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Criar link simbólico
php artisan storage:link
```

---

## 🖼️ Problema: Imagens de avatar não aparecem

### Causa

O link simbólico entre `storage/app/public` e `public/storage` não foi criado.

### Solução

Execute no terminal:

```bash
php artisan storage:link
```

Isso criará um link simbólico que permite o acesso público aos arquivos de storage.

---

## 🗄️ Problema: Erro de conexão com banco de dados

### Solução

1. Verifique o arquivo `.env` na raiz do projeto
2. Configure as credenciais do banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=closet_fashion
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

3. Crie o banco de dados no MySQL:

```sql
CREATE DATABASE closet_fashion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Execute as migrações:

```bash
php artisan migrate --seed
```

---

## 🔑 Problema: "No application encryption key has been set"

### Solução

Execute no terminal:

```bash
php artisan key:generate
```

Isso gerará uma chave de criptografia única para sua aplicação.

---

## 📦 Problema: Dependências não instaladas

### Solução

Execute no terminal:

```bash
# Instalar dependências PHP
composer install

# Instalar dependências JavaScript (se necessário)
npm install
```

---

## 🚀 Checklist Completo de Instalação

Siga esta ordem para garantir que tudo funcione:

1. ✅ Extrair o projeto
2. ✅ Copiar `.env.example` para `.env`
3. ✅ Configurar credenciais do banco de dados no `.env`
4. ✅ Executar `composer install`
5. ✅ Executar `php artisan key:generate`
6. ✅ Criar estrutura de diretórios (usar `fix-storage.bat` no Windows)
7. ✅ Executar `php artisan migrate --seed`
8. ✅ Executar `php artisan storage:link`
9. ✅ Executar `php artisan serve`
10. ✅ Acessar `http://127.0.0.1:8000`

---

## 📞 Suporte Adicional

Se o problema persistir:

1. Verifique os logs em `storage/logs/laravel.log`
2. Certifique-se de que o PHP 8.2+ está instalado
3. Verifique se todas as extensões PHP necessárias estão habilitadas:
   - `php-mbstring`
   - `php-xml`
   - `php-pdo`
   - `php-mysql`
   - `php-gd` (para manipulação de imagens)

---

## 🔍 Comandos Úteis de Diagnóstico

```bash
# Verificar versão do PHP
php -v

# Verificar extensões PHP instaladas
php -m

# Verificar configuração do Laravel
php artisan about

# Limpar todos os caches
php artisan optimize:clear

# Recriar autoload do Composer
composer dump-autoload
```

---

## ⚠️ Nota Importante sobre Permissões (Windows)

No Windows, certifique-se de executar o terminal como **Administrador** ao criar links simbólicos com `php artisan storage:link`.

Se ainda assim não funcionar, você pode copiar manualmente a pasta:

```cmd
xcopy /E /I storage\app\public public\storage
```

---

