<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunicationController extends Controller
{
    /**
     * Lista todas as conversas do usuário logado.
     */
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orWhereHas('companies', function ($query) use ($user) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })
        ->with(['messages' => function ($query) {
            $query->latest()->limit(1);
        }, 'users', 'companies'])
        ->latest('updated_at')
        ->paginate(15);

        return view('communication.index', compact('conversations'));
    }

    /**
     * Exibir uma conversa específica com suas mensagens.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Verificar se o usuário é participante da conversa
        if (!$conversation->hasUser($user)) {
            abort(403, 'Acesso não autorizado a esta conversa.');
        }

        // Marcar todas as mensagens como lidas
        $conversation->markAllAsRead($user);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $otherUsers = $conversation->users()
            ->where('user_id', '!=', $user->id)
            ->get();

        return view('communication.show', compact('conversation', 'messages', 'otherUsers'));
    }

    /**
     * Enviar uma nova mensagem em uma conversa.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Verificar se o usuário é participante da conversa
        if (!$conversation->hasUser($user)) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => User::class,
            'content' => $validated['content'],
        ]);

        // Atualizar o timestamp da conversa
        $conversation->touch();

        // Disparar o evento de mensagem enviada
        event(new \App\Events\MessageSent($message, $user));

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }

    /**
     * Criar uma nova conversa com um usuário específico.
     */
    public function createConversation(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|different:' . $user->id,
        ]);

        // Verificar se já existe uma conversa entre os dois usuários
        $existingConversation = Conversation::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('users', function ($query) use ($validated) {
            $query->where('user_id', $validated['user_id']);
        })
        ->first();

        if ($existingConversation) {
            return redirect()->route('communication.show', $existingConversation->id);
        }

        // Criar uma nova conversa
        $conversation = Conversation::create();

        // Adicionar os participantes
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $validated['user_id'],
        ]);

        return redirect()->route('communication.show', $conversation->id);
    }

    /**
     * Marcar uma mensagem como lida.
     */
    public function markMessageAsRead(Message $message)
    {
        $user = Auth::user();

        // Verificar se o usuário é participante da conversa
        if (!$message->conversation->hasUser($user)) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Deletar uma conversa (soft delete).
     */
    public function deleteConversation(Conversation $conversation)
    {
        $user = Auth::user();

        // Verificar se o usuário é participante da conversa
        if (!$conversation->hasUser($user)) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }

        // Remover o usuário da conversa
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Obter mensagens não lidas para o usuário.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        $unreadCount = Conversation::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with('messages')
        ->get()
        ->sum(function ($conversation) use ($user) {
            return $conversation->getUnreadCount($user);
        });

        return response()->json(['unread_count' => $unreadCount]);
    }

    // Métodos específicos para empresas, se necessário, podem ser mantidos ou adaptados.
    // Por enquanto, vamos focar na funcionalidade de chat para usuários.
    // Os métodos indexCompany, showCompany, sendCompany e getMessages (JSON) do CommunicationController original
    // podem ser revisados ou removidos se a lógica do ChatController for suficiente e mais robusta.
    // Para simplificar, vou remover os métodos indexCompany, showCompany, sendCompany e getMessages (JSON)
    // e manter apenas os métodos adaptados do ChatController.

    // O método 'send' do CommunicationController original será substituído por 'sendMessage'.
    // O método 'getMessages' do CommunicationController original será removido, pois a view 'show' já carrega as mensagens.
}

