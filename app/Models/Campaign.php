<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'temaEspecifco',
        'objetivoComuicacion',
        'campaign_type_id',
        'institution_id',
        'coemisores',
        'sexo',
        'edad',
        'poblacion',
        'nse',
        'caracEspecific',
        'tv_oficial',
        'radio_oficial',
        'tv_comercial',
        'radio_comercial',
        'televisoras',
        'radiodifusoras',
        'cine',
        'decdmx',
        'deedos',
        'deextr',
        'revistas',
        'mediosComplementarios',
        'mediosDigitales',
        'preEstudios',
        'postEstudios',
        'disenio',
        'produccion',
        'preProduccion',
        'postProduccion',
        'copiado',
    ];

    protected $casts = [
        'tv_oficial' => 'boolean',
        'radio_oficial' => 'boolean',
        'tv_comercial' => 'boolean',
        'radio_comercial' => 'boolean',
        'televisoras' => 'decimal:6',
        'radiodifusoras' => 'decimal:6',
        'cine' => 'decimal:6',
        'decdmx' => 'decimal:6',
        'deedos' => 'decimal:6',
        'deextr' => 'decimal:6',
        'revistas' => 'decimal:6',
        'mediosComplementarios' => 'decimal:6',
        'mediosDigitales' => 'decimal:6',
        'preEstudios' => 'decimal:6',
        'postEstudios' => 'decimal:6',
        'disenio' => 'decimal:6',
        'produccion' => 'decimal:6',
        'preProduccion' => 'decimal:6',
        'postProduccion' => 'decimal:6',
        'copiado' => 'decimal:6',
        'sexo' => 'array',
        'edad' => 'array',
        'poblacion' => 'array',
        'nse' => 'array',
    ];

    public function campaignType()
    {
        return $this->belongsTo(CampaignType::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function estrategy()
    {
        return $this->belongsTo(Estrategy::class);
    }

    public function versions()
    {
        return $this->hasMany(Version::class);
    }

    // Arrays de opciones para los selects
    public static function getSexoOptions(): array
    {
        return [
            'Mujeres' => 'Mujeres',
            'Hombres' => 'Hombres',
        ];
    }

    public static function getEdadOptions(): array
    {
        return [
            '18-24' => '18-24',
            '25-34' => '25-34',
            '35-44' => '35-44',
            '45-54' => '45-54',
            '55-64' => '55-64',
            '65+' => '65+',
        ];
    }

    public static function getPoblacionOptions(): array
    {
        return [
            'Urbana' => 'Urbana',
            'Rural' => 'Rural',
        ];
    }

    public static function getNseOptions(): array
    {
        return [
            'AB' => 'AB',
            'C+' => 'C+',
            'C-' => 'C-',
            'D+' => 'D+',
            'D' => 'D',
            'E' => 'E',
        ];
    }
}
