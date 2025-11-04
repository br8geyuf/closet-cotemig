<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat; // Importar o modelo Chat
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Inicia um novo chat ou retorna o chat existente com o usuário alvo.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function startChat(int $user_id)
    {
        // 1. Verificar se o usuário alvo existe
        $targetUser = User::find($user_id);

        if (!$targetUser) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $currentUser = Auth::user();

        // 2. Impedir que o usuário inicie um chat consigo mesmo
        if ($currentUser->id === $targetUser->id) {
            return response()->json(['error' => 'Você não pode iniciar um chat consigo mesmo.'], 400);
        }

        // 3. Buscar um chat existente entre os dois usuários
        $chat = Chat::where(function ($query) use ($currentUser, $targetUser) {
            $query->where('user_one_id', $currentUser->id)
                  ->where('user_two_id', $targetUser->id);
        })->orWhere(function ($query) use ($currentUser, $targetUser) {
            $query->where('user_one_id', $targetUser->id)
                  ->where('user_two_id', $currentUser->id);
        })->first();

        // 4. Se o chat não existir, criar um novo
        if (!$chat) {
            // Garante que o ID menor seja sempre o user_one_id para evitar duplicidade
            $ids = [$currentUser->id, $targetUser->id];
            sort($ids); 
            
            $chat = Chat::create([
                'user_one_id' => $ids[0],
                'user_two_id' => $ids[1],
            ]);
        }
        
        // 5. Retornar o ID do chat criado/encontrado
        return response()->json([
            'message' => 'Chat iniciado com sucesso.',
            'chat_id' => $chat->id,
            'target_user_id' => $targetUser->id,
            'target_user_name' => $targetUser->name,
        ], 200);
    }
}
