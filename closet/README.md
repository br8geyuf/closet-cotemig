# Closet Fashion GOF - Sua Plataforma de Moda Definitiva

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
    <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🌟 Sobre o Projeto

O **Closet Fashion GOF** é uma aplicação web robusta e moderna construída com **Laravel**, projetada para ser uma plataforma completa para entusiastas da moda, lojistas e criadores de conteúdo. A plataforma permite que os usuários gerenciem seus guarda-roupas, sigam outros usuários, criem posts, favoritem itens e muito mais.

Este projeto foi aprimorado para incluir uma funcionalidade de **edição de perfil de usuário** completa e visualmente atraente, permitindo uma personalização rica e uma experiência de usuário aprimorada.

---

## ✨ Funcionalidades Principais

| Funcionalidade          | Descrição                                                                                             |
| ----------------------- | ----------------------------------------------------------------------------------------------------- |
| 👤 **Gestão de Perfil**   | Edição completa do perfil, incluindo avatar, informações pessoais, biografia e alteração de senha.     |
| 🖼️ **Upload de Avatar**   | Sistema de upload de imagem de perfil com preview instantâneo e validação de arquivo.                  |
| 🤝 **Sistema Social**      | Siga e deixe de seguir outros usuários para acompanhar suas atividades e posts.                        |
| 💖 **Favoritos e Itens**  | Adicione itens ao seu "closet" e favorite peças de outros usuários.                                  |
| 📈 **Dashboard Pessoal**  | Um painel centralizado com estatísticas sobre suas atividades, seguidores e itens.                     |
| 🔍 **Busca Avançada**      | Pesquise por usuários com filtros e ordenação para encontrar perfis de interesse.                     |
| 🏢 **Perfil de Empresa**  | Funcionalidades específicas para empresas, incluindo gestão de promoções.                             |

---

## 🚀 Melhorias Implementadas

Nesta versão, o foco foi aprimorar a experiência do usuário através de uma reformulação completa da seção de perfil:

1.  **Botão "✏️ Editar Perfil"**: Adicionado um botão claro e acessível no dashboard e na página de perfil para facilitar o acesso à edição.

2.  **Página de Edição de Perfil (`edit.blade.php`)**: 
    *   **Design Moderno**: Uma interface completamente redesenhada, com um layout limpo, responsivo e visualmente agradável.
    *   **Upload de Avatar Aprimorado**: Componente de upload de foto com preview, overlay interativo e validação em tempo real (tamanho e tipo de arquivo).
    *   **Novos Campos de Perfil**: Adicionados campos para *Nome*, *Sobrenome*, *Telefone*, *Data de Nascimento*, *Gênero* e *Biografia*.
    *   **Contador de Caracteres**: Feedback em tempo real para o campo de biografia.
    *   **Alteração de Senha Segura**: Seção dedicada para alterar a senha com validação de senha atual e confirmação.
    *   **Feedback ao Usuário**: Mensagens de sucesso e erro estilizadas para uma comunicação clara.

3.  **Página de Visualização de Perfil (`show.blade.php`)**:
    *   **Design de Cartão de Visita**: Layout aprimorado que exibe as informações do usuário de forma organizada e elegante, incluindo a nova foto de perfil, biografia e detalhes adicionais.
    *   **Estatísticas Visuais**: Exibição clara de seguidores, seguindo e número de itens.

4.  **Backend Robusto**:
    *   O `UserController` foi refatorado para gerenciar a lógica de atualização de todas as novas informações, incluindo o upload de avatar e a atualização de dados no `UserProfile`.
    *   O model `User` foi atualizado para carregar a URL do avatar de forma mais inteligente, exibindo um avatar gerado a partir do nome do usuário caso nenhuma foto tenha sido enviada.

---

## 🛠️ Tecnologias Utilizadas

*   **Backend**: Laravel 10, PHP 8.2
*   **Frontend**: Blade Templates, Tailwind CSS (para estilos base), CSS customizado para as novas páginas.
*   **Banco de Dados**: MySQL (ou outro de sua preferência, configurável no `.env`)
*   **Servidor**: Apache/Nginx

---

## ⚙️ Instalação e Configuração

Siga os passos abaixo para configurar o ambiente de desenvolvimento local:

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/seu-usuario/closet-fashion-gof.git
    cd closet-fashion-gof
    ```

2.  **Instale as dependências do Composer:**
    ```bash
    composer install
    ```

3.  **Configure o arquivo de ambiente:**
    *   Copie o arquivo de exemplo: `cp .env.example .env`
    *   Gere a chave da aplicação: `php artisan key:generate`
    *   Configure as credenciais do seu banco de dados no arquivo `.env`.

4.  **Execute as migrações e seeders:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Crie o link simbólico para o armazenamento:**
    ```bash
    php artisan storage:link
    ```

6.  **Inicie o servidor de desenvolvimento:**
    ```bash
    php artisan serve
    ```

7.  Acesse a aplicação em `http://127.0.0.1:8000`.

---

## 📄 Licença

Este projeto é de código aberto e está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

