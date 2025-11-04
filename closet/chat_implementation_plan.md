# Plano de Implementação do Recurso de Chat

Este documento detalha o plano para integrar um recurso de chat em tempo real ao projeto Laravel `Closet Fashion GOF`.

## 1. Visão Geral

O objetivo é adicionar um sistema de mensagens privadas entre usuários, permitindo conversas em tempo real.

## 2. Arquitetura Proposta

### Backend (Laravel)

*   **Modelos**: Criação de `Chat` (para representar uma conversa entre dois ou mais usuários) e `Message` (para cada mensagem enviada).
*   **Migrações**: Tabelas `chats` e `messages` no banco de dados.
*   **Controladores**: `ChatController` para gerenciar a lógica de criação de chats, envio e recuperação de mensagens.
*   **Rotas**: Rotas API para as funcionalidades do chat.
*   **Broadcasting**: Utilização de Laravel Echo e WebSockets (via Pusher ou Laravel WebSockets) para comunicação em tempo real.

### Frontend (Blade, JavaScript, CSS)

*   **Componentes Vue.js/React (opcional, mas recomendado)**: Para uma interface de chat reativa e dinâmica.
*   **Blade Templates**: Integração dos componentes JavaScript nas views Blade.
*   **JavaScript**: Lógica para enviar e receber mensagens, atualizar a interface do usuário em tempo real.
*   **CSS**: Estilização da interface do chat.

## 3. Detalhes da Implementação

### 3.1. Banco de Dados

#### Tabela `chats`

| Coluna       | Tipo       | Descrição                               |
| :----------- | :--------- | :-------------------------------------- |
| `id`         | `bigIncrements` | Chave primária                        |
| `user_one_id`| `foreignId`| ID do primeiro usuário na conversa    |
| `user_two_id`| `foreignId`| ID do segundo usuário na conversa     |
| `created_at` | `timestamp`| Timestamp de criação                  |
| `updated_at` | `timestamp`| Timestamp da última atualização       |

#### Tabela `messages`

| Coluna       | Tipo       | Descrição                               |
| :----------- | :--------- | :-------------------------------------- |
| `id`         | `bigIncrements` | Chave primária                        |
| `chat_id`    | `foreignId`| ID do chat ao qual a mensagem pertence|
| `user_id`    | `foreignId`| ID do usuário que enviou a mensagem   |
| `content`    | `text`     | Conteúdo da mensagem                  |
| `read_at`    | `timestamp`| Timestamp de leitura (opcional)       |
| `created_at` | `timestamp`| Timestamp de criação                  |
| `updated_at` | `timestamp`| Timestamp da última atualização       |

### 3.2. Backend (Laravel)

1.  **Instalação de Pacotes**: Instalar `pusher/pusher-php-server` e `laravel/ui` (se não estiverem instalados) para WebSockets e scaffolding de autenticação/frontend.
2.  **Configuração de Broadcasting**: Configurar o `config/app.php` e `.env` para usar o driver `pusher`.
3.  **Modelos**: Criar `app/Models/Chat.php` e `app/Models/Message.php` com seus respectivos relacionamentos.
4.  **Migrações**: Criar os arquivos de migração para as tabelas `chats` e `messages`.
5.  **Controlador**: Criar `app/Http/Controllers/ChatController.php` com métodos para:
    *   `index()`: Listar chats do usuário logado.
    *   `show(Chat $chat)`: Exibir mensagens de um chat específico.
    *   `store(Request $request)`: Criar um novo chat ou enviar uma mensagem.
6.  **Eventos e Listeners**: Criar um evento `MessageSent` para broadcast de mensagens em tempo real.
7.  **Rotas**: Definir rotas em `routes/web.php` ou `routes/api.php` para as ações do `ChatController`.

### 3.3. Frontend

1.  **Instalação de Pacotes**: Instalar `laravel-echo` e `pusher-js` via npm/yarn.
2.  **Configuração de Echo**: Configurar `resources/js/bootstrap.js` para Laravel Echo.
3.  **Componentes Vue/React**: Desenvolver componentes para:
    *   Lista de chats.
    *   Janela de chat com campo de entrada de mensagem e exibição de mensagens.
    *   Integração com Laravel Echo para receber mensagens em tempo real.
4.  **Blade Views**: Criar `resources/views/chat/index.blade.php` e `resources/views/chat/show.blade.php` para exibir a interface do chat.
5.  **Estilização**: Utilizar Tailwind CSS e/ou CSS customizado para estilizar os componentes.

## 4. Próximos Passos

*   Implementar as migrações e modelos.
*   Configurar o broadcasting.
*   Desenvolver o `ChatController`.
*   Criar as rotas.
*   Desenvolver o frontend com JavaScript e Blade templates.
