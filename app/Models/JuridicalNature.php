<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JuridicalNature extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Obtiene las instituciones que tienen esta naturaleza jurídica
     */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
