<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;
    protected $fillable = [
        'sector_id',
        'name',
        'acronym',
        'code',
        'juridical_nature_id',
        'isSector',
        'ramo_id',
        'control',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos específicos
     */
    protected $casts = [
        'isSector' => 'boolean',
    ];

    /**
     * Obtiene el sector al que pertenece esta institución
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Obtiene la naturaleza jurídica de esta institución
     */
    public function juridicalNature(): BelongsTo
    {
        return $this->belongsTo(JuridicalNature::class);
    }

    /**
     * Obtiene el ramo al que pertenece esta institución
     */
    public function ramo(): BelongsTo
    {
        return $this->belongsTo(Ramo::class);
    }

    /**
     * Obtiene los usuarios que pertenecen a esta institución
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Obtiene el responsable de esta institución
     */
    public function responsable(): HasMany
    {
        return $this->hasMany(Responsable::class);
    }
}
