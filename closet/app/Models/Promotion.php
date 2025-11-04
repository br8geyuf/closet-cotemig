<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',          // ✅ adicionado
        'company_id',
        'title',
        'description',
        'type',
        'discount_percentage',
        'discount_amount',
        'minimum_purchase',
        'coupon_code',
        'start_date',
        'end_date',
        'is_active',
        'image',
        'terms_conditions',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
    ];

    /**
     * Relacionamentos
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // ✅ relacionamento com usuário
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
