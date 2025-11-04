<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obter a conversa à qual a mensagem pertence.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Obter o remetente da mensagem (polimórfico).
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Marcar a mensagem como lida.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Verificar se a mensagem foi lida.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Obter o usuário remetente se for um usuário.
     */
    public function getUser(): ?User
    {
        return $this->sender_type === 'App\Models\User' ? $this->sender : null;
    }

    /**
     * Obter a empresa remetente se for uma empresa.
     */
    public function getCompany(): ?Company
    {
        return $this->sender_type === 'App\Models\Company' ? $this->sender : null;
    }
}

