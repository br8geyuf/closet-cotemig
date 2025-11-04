<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * Relacionamentos
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Métodos auxiliares
     */
    public function addPoints($points, $description = null, $orderId = null)
    {
        $this->increment('balance', $points);
        
        PointTransaction::create([
            'user_id' => $this->user_id,
            'points_change' => $points,
            'type' => 'earn',
            'description' => $description,
            'order_id' => $orderId,
        ]);
    }

    public function redeemPoints($points, $description = null)
    {
        if ($this->balance >= $points) {
            $this->decrement('balance', $points);
            
            PointTransaction::create([
                'user_id' => $this->user_id,
                'points_change' => -$points,
                'type' => 'redeem',
                'description' => $description,
            ]);

            return true;
        }

        return false;
    }
}

