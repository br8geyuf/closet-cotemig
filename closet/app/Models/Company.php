<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'description',
        'website',
        'logo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 🔗 Relacionamentos
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /**
     * 🔗 Adicionado: relação com usuários
     * Define que uma empresa pode ter vários usuários associados.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
    }

    /**
     * 🎭 Accessor: devolve CNPJ com máscara
     */
    public function getCnpjMaskedAttribute()
    {
        if (!$this->cnpj) {
            return null;
        }

        return preg_replace(
            "/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/",
            "$1.$2.$3/$4-$5",
            $this->cnpj
        );
    }
}
