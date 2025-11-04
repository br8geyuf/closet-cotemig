<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false; // Não usar timestamps padrão

    protected $fillable = [
        "user_id",
        "company_id",
        "event_type",
        "payload",
    ];

    protected $casts = [
        "payload" => "array",
        "created_at" => "datetime",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

