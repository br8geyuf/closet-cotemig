<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * Campos preenchíveis em massa.
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'category_id',
        'store_id',
        'name',
        'description',
        'brand',
        'size',
        'colors',
        'condition',
        'purchase_price',
        'purchase_date',
        'images',
        'tags',
        'usage_count',
        'last_used',
        'is_favorite',
        'season',
        'occasion',
    ];

    /**
     * Conversões automáticas de tipos.
     */
    protected $casts = [
        'colors' => 'array',
        'images' => 'array',
        'tags' => 'array',
        'purchase_date' => 'datetime:Y-m-d',
        'last_used' => 'datetime:Y-m-d',
        'is_favorite' => 'boolean',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Relações principais
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relações adicionais
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function memories()
    {
        return $this->hasMany(Memory::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        // pivot personalizado — garante compatibilidade com Laravel
        return $this->belongsToMany(Wishlist::class, 'wishlist_items')
                    ->withTimestamps();
    }

    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }

    /**
     * Scopes — filtros reutilizáveis
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeBySeason($query, $season)
    {
        return $query->where(function ($q) use ($season) {
            $q->where('season', $season)
              ->orWhere('season', 'todas');
        });
    }

    public function scopeByOccasion($query, $occasion)
    {
        return $query->where(function ($q) use ($occasion) {
            $q->where('occasion', $occasion)
              ->orWhere('occasion', 'todas');
        });
    }

    /**
     * Métodos auxiliares
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used' => now()]);
    }

    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }

    public function getFirstImageAttribute(): ?string
    {
        if (!is_array($this->images) || count($this->images) === 0) {
            return asset('images/default-item.png'); // imagem padrão
        }

        return asset('storage/' . $this->images[0]);
    }

    /**
     * Avaliações — média e contagem
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
