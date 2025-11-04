<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    /**
     * Obter o primeiro usuário do chat.
     */
    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Obter o segundo usuário do chat.
     */
    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Obter todas as mensagens do chat.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Obter o outro usuário da conversa (não o usuário logado).
     */
    public function getOtherUser(User $user): User
    {
        return $this->user_one_id === $user->id ? $this->userTwo : $this->userOne;
    }

    /**
     * Obter a última mensagem do chat.
     */
    public function getLastMessage(): ?Message
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Contar mensagens não lidas para um usuário específico.
     */
    public function getUnreadCount(User $user): int
    {
        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}

