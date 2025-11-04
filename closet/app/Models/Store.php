<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'logo',
        'social_media',
        'type',
        'is_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamentos
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
