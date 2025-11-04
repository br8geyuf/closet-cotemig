<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'title',
        'content',
        'memory_date',
        'location',
        'occasion',
        'photos',
        'tags',
        'rating',
        'is_favorite',
    ];

    protected $casts = [
        'memory_date' => 'date',
        'photos' => 'array',
        'tags' => 'array',
        'is_favorite' => 'boolean',
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
     * Scopes
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeByOccasion($query, $occasion)
    {
        return $query->where('occasion', $occasion);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('memory_date', 'desc');
    }
}
