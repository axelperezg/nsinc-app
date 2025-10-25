<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Estrategy extends Model
{
    protected $fillable = [
        'anio',
        'institution_id',
        'institution_name',
        'juridical_nature_id',
        'juridical_nature_name',
        'mision',
        'vision',
        'objetivo_institucional',
        'objetivo_estrategia',
        'fecha_elaboracion',
        'estado_estrategia',
        'concepto',
        'oficio_dgnc',
        'estrategia_original_id',
        'fecha_envio_dgnc',
        'presupuesto',
        'responsable_id',
        'responsable_name',
        'NombreSectorResponsable',
        'ejes_plan_nacional',
        'justificacion_estudios',
    ];

    protected $casts = [
        'fecha_elaboracion' => 'date',
        'fecha_envio_dgnc' => 'date',
        'presupuesto' => 'decimal:2',
        'ejes_plan_nacional' => 'array',
    ];

    // Arrays de opciones para los selects
    public static function getEstadosOptions(): array
    {
        return [
            'Creada' => 'Creada',
            'Enviada a CS' => 'Enviada a CS',
            'Aceptada CS' => 'Aceptada CS',
            'Enviada a DGNC' => 'Enviada a DGNC',
            'Autorizada' => 'Autorizada',
            'Rechazada CS' => 'Rechazada CS',
            'Rechazada DGNC' => 'Rechazada DGNC',
            'Observada DGNC' => 'Observada DGNC',
        ];
    }

    public static function getConceptosOptions(): array
    {
        return [
            'Registro' => 'Registro',
            'Modificacion' => 'Modificación',
            'Solventacion' => 'Solventación',
            'Cancelacion' => 'Cancelación',
        ];
    }

    public static function getEjesGeneralesOptions(): array
    {
        return [
            'eje_general_1_gobernanza' => 'Eje General 1: Gobernanza con justicia y participación ciudadana',
            'eje_general_2_desarrollo' => 'Eje General 2: Desarrollo con bienestar y humanismo',
            'eje_general_3_economia' => 'Eje General 3: Economía moral y trabajo',
            'eje_general_4_sustentable' => 'Eje General 4: Desarrollo sustentable',
        ];
    }

    public static function getEjesTransversalesOptions(): array
    {
        return [
            'eje_transversal_1_igualdad' => 'Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres',
            'eje_transversal_2_innovacion' => 'Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional',
            'eje_transversal_3_derechos' => 'Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function juridicalNature()
    {
        return $this->belongsTo(JuridicalNature::class);
    }

    // Método para obtener los ejes seleccionados
    public function getEjesSeleccionadosAttribute()
    {
        return $this->ejes_plan_nacional ?? [];
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function estrategiaOriginal()
    {
        return $this->belongsTo(Estrategy::class, 'estrategia_original_id');
    }

    public function modificaciones()
    {
        return $this->hasMany(Estrategy::class, 'estrategia_original_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    /**
     * Obtiene los documentos de oficio DGNC de esta estrategia
     */
    public function oficioDgncDocuments()
    {
        return $this->hasMany(OficioDgncDocument::class);
    }

    /**
     * Mutador para sincronizar el nombre de la institución cuando se cambia el ID
     */
    public function setInstitutionIdAttribute($value)
    {
        $this->attributes['institution_id'] = $value;
        
        if ($value) {
            $institution = Institution::find($value);
            $this->attributes['institution_name'] = $institution ? $institution->name : null;
            
            // También sincronizar el responsable de la institución
            $responsable = Responsable::where('institution_id', $value)->first();
            if ($responsable) {
                $this->attributes['responsable_id'] = $responsable->id;
                $this->attributes['responsable_name'] = $responsable->name;
            } else {
                $this->attributes['responsable_id'] = null;
                $this->attributes['responsable_name'] = null;
            }
        } else {
            $this->attributes['institution_name'] = null;
            $this->attributes['responsable_id'] = null;
            $this->attributes['responsable_name'] = null;
        }
    }

    /**
     * Mutador para sincronizar el nombre de la naturaleza jurídica cuando se cambia el ID
     */
    public function setJuridicalNatureIdAttribute($value)
    {
        $this->attributes['juridical_nature_id'] = $value;
        
        if ($value) {
            $juridicalNature = JuridicalNature::find($value);
            $this->attributes['juridical_nature_name'] = $juridicalNature ? $juridicalNature->name : null;
        } else {
            $this->attributes['juridical_nature_name'] = null;
        }
    }

    /**
     * Mutador para sincronizar el nombre del responsable cuando se cambia el ID
     */
    public function setResponsableIdAttribute($value)
    {
        $this->attributes['responsable_id'] = $value;
        
        if ($value) {
            $responsable = Responsable::find($value);
            $this->attributes['responsable_name'] = $responsable ? $responsable->name : null;
        } else {
            $this->attributes['responsable_name'] = null;
        }
    }

    /**
     * Scope global para filtrar por institución del usuario
     */
    protected static function booted()
    {
        static::addGlobalScope('institution', function (Builder $builder) {
            $user = Auth::user();
            
            if ($user && $user->institution_id && $user->role && $user->role->name !== 'super_admin') {
                $builder->where('institution_id', $user->institution_id);
            }
            // Si es super admin, no se aplica filtro (ve todas las instituciones)
        });
    }

    /**
     * Scope para obtener estrategias filtradas por usuario
     */
    public function scopeForUser($query, $user = null)
    {
        $user = $user ?? Auth::user();
        
        if ($user && $user->institution_id && $user->role && $user->role->name !== 'super_admin') {
            $query->where('institution_id', $user->institution_id);
        }
        
        return $query;
    }

    /**
     * Verifica si esta estrategia es la última para su institución y año
     */
    public function isLatestForInstitutionAndYear(): bool
    {
        $latest = static::where('institution_id', $this->institution_id)
            ->where('anio', $this->anio)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latest && $latest->id === $this->id;
    }

    /**
     * Obtiene la última estrategia para una institución y año específicos
     */
    public static function getLatestForInstitutionAndYear(int $institutionId, int $year): ?self
    {
        return static::where('institution_id', $institutionId)
            ->where('anio', $year)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
