<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'dni_ruc',
        'nombre_completo',
        'nro_celular',
        'correo',
        'empresa',
        'direccion',
    ];

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }

    public function scopePorDniRuc(Builder $query, string $term = ''): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where('dni_ruc', 'like', '%'.$term.'%');
    }
}
