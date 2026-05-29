<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $fillable = [
        'cliente_id',
        'dia',
        'hora_estimada',
        'direccion',
        'detalle',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'dia' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
