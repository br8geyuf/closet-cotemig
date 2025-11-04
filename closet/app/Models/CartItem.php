<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'price_at_addition',
    ];

    protected $casts = [
        'price_at_addition' => 'decimal:2',
    ];

    /**
     * Relacionamentos
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Métodos auxiliares
     */
    public function getSubtotalAttribute()
    {
        return ($this->price_at_addition ?? 0) * $this->quantity;
    }
}

