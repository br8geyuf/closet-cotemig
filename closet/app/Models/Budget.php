<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'total_amount',
        'spent_amount',
        'period',
        'start_date',
        'end_date',
        'categories',
        'is_active',
        'notify_on_limit',
        'notification_threshold',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'categories' => 'array',
        'is_active' => 'boolean',
        'notify_on_limit' => 'boolean',
        'total_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'notification_threshold' => 'decimal:2',
    ];

    /**
     * Relacionamentos
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Métodos auxiliares
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->spent_amount;
    }

    public function getUsagePercentageAttribute()
    {
        return ($this->spent_amount / $this->total_amount) * 100;
    }

    public function isOverBudget()
    {
        return $this->spent_amount > $this->total_amount;
    }

    public function shouldNotify()
    {
        return $this->notify_on_limit && 
               $this->getUsagePercentageAttribute() >= $this->notification_threshold;
    }
}
