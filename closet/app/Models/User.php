<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\{
    Clothing, Post, Item, Category, Favorite, Budget, Memory,
    Company, Promotion, CartItem, Review, Wishlist, RecentView,
    UserProfile, UserPoints, PointTransaction
};

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    // Perfil e Empresa
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    // Itens, Categorias, Posts etc.
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function memories()
    {
        return $this->hasMany(Memory::class);
    }

    public function clothes()
    {
        return $this->hasMany(Clothing::class);
    }

    public function itemsForSale()
    {
        return $this->hasMany(Item::class)->where('for_sale', true);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Promoções (via empresa)
    public function promotions()
    {
        return $this->hasManyThrough(
            Promotion::class,
            Company::class,
            'user_id',
            'company_id',
            'id',
            'id'
        );
    }

    // Seguidores / Seguindo
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    // Relacionamentos extras
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
        return $this->hasMany(Wishlist::class);
    }

    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }

    public function points()
    {
        return $this->hasOne(UserPoints::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS PERSONALIZADOS
    |--------------------------------------------------------------------------
    */

    protected $appends = [
        'avatar_url',
        'badge',
        'followers_count',
    ];

    public function getAvatarUrlAttribute()
    {
        if ($this->profile && $this->profile->avatar) {
            return asset('storage/avatars/' . $this->profile->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&size=200&bold=true';
    }

    public function getBadgeAttribute()
    {
        if ($this->promotions()->where('active', true)->exists()) {
            return 'Promoção';
        }

        if (auth()->check() && auth()->user()->following()->where('followed_id', $this->id)->exists()) {
            return 'Seguindo';
        }

        return '';
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NOTIFICAÇÃO
    |--------------------------------------------------------------------------
    */

    /**
     * 🔔 Notifica o usuário quando ganha um novo seguidor
     */
    public function notifyNewFollower(User $follower)
    {
        $this->notify(new \App\Notifications\NewFollower($follower));
    }

    /**
     * 🛍️ Notifica seguidores quando um novo item é adicionado
     */
    public function notifyNewItem(User $creator, $item)
    {
        $this->notify(new \App\Notifications\NewItemAdded($creator, $item));
    }
}
