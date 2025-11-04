# Documentação da Funcionalidade de Chat

Este documento descreve a implementação e o uso da funcionalidade de chat integrada ao projeto Laravel `Closet Fashion GOF`.

## 1. Visão Geral

A funcionalidade de chat permite que usuários autenticados troquem mensagens em tempo real. Ela foi desenvolvida aproveitando as estruturas existentes de `Conversation`, `Message` e `ConversationParticipant`, adicionando lógica de negócio e interface de usuário para uma experiência completa de chat.

## 2. Modelos e Migrações

Os modelos `Conversation`, `Message` e `ConversationParticipant` foram aprimorados e/ou utilizados para gerenciar as conversas e mensagens.

### 2.1. Modelo `Conversation` (`app/Models/Conversation.php`)

O modelo `Conversation` representa uma conversa entre dois ou mais participantes. Ele foi estendido com os seguintes métodos:

*   `messages()`: Relacionamento HasMany para obter todas as mensagens da conversa.
*   `participants()`: Relacionamento HasMany para obter os participantes da conversa.
*   `users()`: Relacionamento BelongsToMany para obter os usuários participantes.
*   `companies()`: Relacionamento BelongsToMany para obter as empresas participantes.
*   `getLastMessage()`: Retorna a última mensagem da conversa.
*   `getUnreadCount(User $user)`: Retorna o número de mensagens não lidas para um usuário específico na conversa.
*   `markAllAsRead(User $user)`: Marca todas as mensagens não lidas de um usuário na conversa como lidas.
*   `hasUser(User $user)`: Verifica se um usuário é participante da conversa.
*   `hasCompany(Company $company)`: Verifica se uma empresa é participante da conversa.
*   `getDisplayName()`: Retorna o nome de exibição da conversa (título ou nomes dos participantes).

### 2.2. Modelo `Message` (`app/Models/Message.php`)

O modelo `Message` representa uma mensagem individual dentro de uma conversa. Ele foi estendido com os seguintes atributos e métodos:

*   `$fillable`: Adicionado `read_at`.
*   `$casts`: Adicionado `read_at`, `created_at` e `updated_at` como `datetime`.
*   `conversation()`: Relacionamento BelongsTo para obter a conversa à qual a mensagem pertence.
*   `sender()`: Relacionamento polimórfico para obter o remetente da mensagem (User ou Company).
*   `markAsRead()`: Marca a mensagem como lida.
*   `isRead()`: Verifica se a mensagem foi lida.
*   `getUser()`: Retorna o usuário remetente se for um usuário.
*   `getCompany()`: Retorna a empresa remetente se for uma empresa.

### 2.3. Migrações

As seguintes migrações foram criadas para suportar a funcionalidade de chat:

*   `2025_10_20_000001_create_chats_table.php`: Cria a tabela `chats` com `user_one_id`, `user_two_id` e timestamps.
*   `2025_10_20_000002_create_messages_table.php`: Cria a tabela `messages` com `chat_id`, `user_id`, `content`, `read_at` e timestamps.

**Nota**: As tabelas `conversations` e `conversation_participants` já existiam no projeto original e foram aproveitadas. As novas migrações `chats` e `messages` foram criadas para uma estrutura de chat mais simples e direta entre dois usuários, caso a estrutura `conversation` existente não fosse adequada para este propósito. No entanto, a implementação do `ChatController` foi adaptada para usar a estrutura `Conversation` existente, tornando as migrações `chats` e `messages` redundantes no contexto atual. O código do `ChatController` foi ajustado para usar `Conversation` e `ConversationParticipant`.

## 3. Controlador

### `ChatController` (`app/Http/Controllers/ChatController.php`)

Este controlador gerencia toda a lógica de negócio relacionada ao chat.

*   `index()`: Exibe a lista de conversas do usuário logado, incluindo a última mensagem e o contador de mensagens não lidas.
*   `show(Conversation $conversation)`: Exibe uma conversa específica, marcando todas as mensagens como lidas para o usuário autenticado.
*   `sendMessage(Request $request, Conversation $conversation)`: Envia uma nova mensagem para uma conversa.
*   `createConversation(Request $request)`: Cria uma nova conversa entre o usuário logado e outro usuário.
*   `markMessageAsRead(Message $message)`: Marca uma mensagem específica como lida.
*   `deleteConversation(Conversation $conversation)`: Remove o usuário logado de uma conversa (soft delete).
*   `getUnreadCount()`: Retorna a contagem total de mensagens não lidas para o usuário logado.

## 4. Rotas

As seguintes rotas foram adicionadas ao arquivo `routes/web.php` dentro do middleware `auth`:

```php
Route::prefix("chat")->name("chat.")->group(function () {
    Route::get("/",[ChatController::class,"index"])->name("index");
    Route::get("/{conversation}",[ChatController::class,"show"])->name("show");
    Route::post("/{conversation}/send",[ChatController::class,"sendMessage"])->name("send");
    Route::post("/create",[ChatController::class,"createConversation"])->name("create");
    Route::post("/{message}/read",[ChatController::class,"markMessageAsRead"])->name("mark-read");
    Route::delete("/{conversation}",[ChatController::class,"deleteConversation"])->name("delete");
    Route::get("/unread/count",[ChatController::class,"getUnreadCount"])->name("unread-count");
});
```

## 5. Views (Frontend)

Duas views Blade foram criadas para a interface do chat:

### 5.1. `chat/index.blade.php`

Esta view exibe uma lista de todas as conversas do usuário. Inclui:

*   Um sidebar com a lista de conversas, mostrando o nome do outro participante, a última mensagem e um contador de mensagens não lidas.
*   Um link para iniciar uma nova conversa (redireciona para a busca de usuários).
*   Um contador global de mensagens não lidas que é atualizado periodicamente via JavaScript.

### 5.2. `chat/show.blade.php`

Esta view exibe as mensagens de uma conversa específica. Inclui:

*   O cabeçalho da conversa com o nome de exibição e um botão para deletar a conversa.
*   A área de exibição de mensagens, com mensagens formatadas para remetente e destinatário.
*   Um campo de entrada de texto e um botão para enviar novas mensagens (com lógica JavaScript para envio via AJAX).
*   Funcionalidade de rolagem automática para a última mensagem.
*   Atualização automática da página a cada 5 segundos para novas mensagens (em uma implementação mais avançada, WebSockets seriam usados para tempo real sem recarregar a página).

## 6. Testes

Um arquivo de teste (`tests/Feature/ChatTest.php`) foi criado para garantir a funcionalidade do chat, cobrindo os seguintes cenários:

*   Visualização da lista de chats por usuário autenticado.
*   Visualização de conversa específica por usuário autenticado.
*   Redirecionamento de usuário não autenticado.
*   Restrição de acesso a conversas para usuários não participantes.
*   Envio de mensagens.
*   Validação de mensagens vazias.
*   Criação de novas conversas.
*   Marcação de mensagens como lidas.
*   Obtenção da contagem de mensagens não lidas.
*   Deleção de conversas.

## 7. Próximos Passos e Melhorias (Opcional)

Para uma experiência de chat mais robusta e em tempo real, as seguintes melhorias podem ser consideradas:

*   **WebSockets**: Implementar Laravel Echo com Pusher ou Laravel WebSockets para notificação e atualização de mensagens em tempo real, eliminando a necessidade de `location.reload()` no frontend.
*   **Notificações**: Adicionar notificações visuais e sonoras para novas mensagens.
*   **Emoji e Anexos**: Suporte a emojis e envio de arquivos/imagens nas mensagens.
*   **Grupos de Chat**: Estender a funcionalidade para permitir conversas em grupo.
*   **Interface de Usuário**: Melhorar a interface do usuário com componentes mais interativos e animações.

## 8. Instalação e Configuração

Para que a funcionalidade de chat esteja disponível, siga os passos de instalação do projeto original e, em seguida, execute as migrações (se as tabelas `chats` e `messages` forem necessárias para alguma outra funcionalidade ou se a estrutura `Conversation` for substituída) e configure o broadcasting para WebSockets (se for implementado).

```bash
php artisan migrate
```

Se você optar por usar WebSockets com Pusher, certifique-se de configurar suas credenciais no arquivo `.env`:

```dotenv
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=YOUR_APP_ID
PUSHER_APP_KEY=YOUR_APP_KEY
PUSHER_APP_SECRET=YOUR_APP_SECRET
PUSHER_APP_CLUSTER=YOUR_APP_CLUSTER
```

E instale as dependências JavaScript:

```bash
npm install && npm run dev
```

## 9. Uso

Após a instalação e configuração, os usuários autenticados poderão acessar a funcionalidade de chat através da rota `/chat` ou de links que apontem para `route("chat.index")`.
