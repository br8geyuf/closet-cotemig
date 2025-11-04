@echo off
REM Script para corrigir estrutura de diretorios do Laravel no Windows

echo ========================================
echo  Correcao de Estrutura do Laravel
echo ========================================
echo.

REM Criar diretorios necessarios
echo [1/5] Criando estrutura de diretorios...
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\cache\data" mkdir "storage\framework\cache\data"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\app\public\avatars" mkdir "storage\app\public\avatars"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Criar arquivos .gitignore
echo [2/5] Criando arquivos .gitignore...

echo * > storage\framework\sessions\.gitignore
echo !.gitignore >> storage\framework\sessions\.gitignore

echo * > storage\framework\views\.gitignore
echo !.gitignore >> storage\framework\views\.gitignore

echo * > storage\framework\cache\.gitignore
echo !data/ >> storage\framework\cache\.gitignore
echo !.gitignore >> storage\framework\cache\.gitignore

echo * > storage\framework\cache\data\.gitignore
echo !.gitignore >> storage\framework\cache\data\.gitignore

echo * > storage\logs\.gitignore
echo !.gitignore >> storage\logs\.gitignore

REM Limpar caches
echo [3/5] Limpando caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

REM Executar migrations pendentes (corrige coluna gender)
echo [4/5] Executando migrations pendentes...
php artisan migrate --force

REM Criar link simbolico
echo [5/5] Criando link simbolico para storage...
php artisan storage:link

echo.
echo ========================================
echo  Correcao concluida com sucesso!
echo ========================================
echo.
echo Agora voce pode acessar o projeto em: http://127.0.0.1:8000
echo.
pause
