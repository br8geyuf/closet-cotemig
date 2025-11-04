<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'company_id',
    ];

    /**
     * A conversa deste participante
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Usuário participante (opcional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Empresa participante (opcional)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
