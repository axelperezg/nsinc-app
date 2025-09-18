<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpirationDate extends Model
{
    protected $fillable = [
        'anio',
        'fecha_inicio',
        'fecha_diaPrevio',
        'fecha_limite',
        'fecha_restrictiva',
        'concept',
        'description',
    ];

    /**
     * Los atributos que deben ser convertidos a fechas
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_diaPrevio' => 'date',
        'fecha_limite' => 'date',
        'fecha_restrictiva' => 'date',
    ];
}
