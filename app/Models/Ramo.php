<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ramo extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Obtiene las instituciones que pertenecen a este ramo
     */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
