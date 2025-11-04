<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;
    protected $user2;
    protected $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $this->conversation = Conversation::create();
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user2->id,
        ]);
    }

    /**
     * Testar se um usuário autenticado pode visualizar a lista de chats.
     */
    public function test_authenticated_user_can_view_chat_index()
    {
        $response = $this->actingAs($this->user1)
            ->get(route('chat.index'));

        $response->assertStatus(200);
        $response->assertViewIs('chat.index');
    }

    /**
     * Testar se um usuário autenticado pode visualizar uma conversa específica.
     */
    public function test_authenticated_user_can_view_conversation()
    {
        $response = $this->actingAs($this->user1)
            ->get(route('chat.show', $this->conversation->id));

        $response->assertStatus(200);
        $response->assertViewIs('chat.show');
    }

    /**
     * Testar se um usuário não autenticado é redirecionado para login.
     */
    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get(route('chat.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Testar se um usuário não participante não pode acessar a conversa.
     */
    public function test_non_participant_user_cannot_view_conversation()
    {
        $user3 = User::factory()->create();

        $response = $this->actingAs($user3)
            ->get(route('chat.show', $this->conversation->id));

        $response->assertStatus(403);
    }

    /**
     * Testar envio de mensagem.
     */
    public function test_authenticated_user_can_send_message()
    {
        $response = $this->actingAs($this->user1)
            ->postJson(route('chat.send', $this->conversation->id), [
                'content' => 'Olá, como vai?',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
            'content' => 'Olá, como vai?',
        ]);
    }

    /**
     * Testar validação de mensagem vazia.
     */
    public function test_empty_message_is_rejected()
    {
        $response = $this->actingAs($this->user1)
            ->postJson(route('chat.send', $this->conversation->id), [
                'content' => '',
            ]);

        $response->assertStatus(422);
    }

    /**
     * Testar criação de nova conversa.
     */
    public function test_user_can_create_conversation()
    {
        $user3 = User::factory()->create();

        $response = $this->actingAs($this->user1)
            ->post(route('chat.create'), [
                'user_id' => $user3->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('conversation_participants', [
            'user_id' => $this->user1->id,
        ]);
        $this->assertDatabaseHas('conversation_participants', [
            'user_id' => $user3->id,
        ]);
    }

    /**
     * Testar marcação de mensagem como lida.
     */
    public function test_message_can_be_marked_as_read()
    {
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
            'sender_type' => User::class,
            'content' => 'Teste',
        ]);

        $response = $this->actingAs($this->user2)
            ->postJson(route('chat.mark-read', $message->id));

        $response->assertStatus(200);

        $this->assertNotNull($message->fresh()->read_at);
    }

    /**
     * Testar obtenção de contagem de mensagens não lidas.
     */
    public function test_unread_count_can_be_retrieved()
    {
        Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
            'sender_type' => User::class,
            'content' => 'Mensagem não lida',
        ]);

        $response = $this->actingAs($this->user2)
            ->getJson(route('chat.unread-count'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['unread_count']);
    }

    /**
     * Testar deletação de conversa.
     */
    public function test_user_can_delete_conversation()
    {
        $response = $this->actingAs($this->user1)
            ->deleteJson(route('chat.delete', $this->conversation->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
        ]);
    }
}

