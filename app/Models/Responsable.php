<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsable extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'charge',
        'institution_id',
    ];

    public function estrategies()
    {
        return $this->hasMany(Estrategy::class);
    }

    /**
     * Obtiene la institución a la que pertenece este responsable
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
