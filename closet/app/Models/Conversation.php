<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    /**
     * Mensagens da conversa
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Participantes da conversa (pivot table)
     * Cada participante pode ser user ou company
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Usuários participantes
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('company_id')
                    ->withTimestamps();
    }

    /**
     * Empresas participantes
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'conversation_participants')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }

    /**
     * Obter a última mensagem da conversa.
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
            ->where(function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->orWhere('sender_type', '!=', 'App\Models\User');
            })
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Marcar todas as mensagens da conversa como lidas para um usuário.
     */
    public function markAllAsRead(User $user): void
    {
        $this->messages()
            ->where(function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->orWhere('sender_type', '!=', 'App\Models\User');
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Verificar se um usuário é participante da conversa.
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Verificar se uma empresa é participante da conversa.
     */
    public function hasCompany(Company $company): bool
    {
        return $this->companies()->where('company_id', $company->id)->exists();
    }

    /**
     * Obter o nome da conversa (título ou nomes dos participantes).
     */
    public function getDisplayName(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $participants = [];
        foreach ($this->users as $user) {
            $participants[] = $user->name;
        }
        foreach ($this->companies as $company) {
            $participants[] = $company->name;
        }

        return implode(', ', $participants) ?: 'Conversa';
    }
}

