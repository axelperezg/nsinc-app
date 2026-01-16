<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class PlanNacionalDesarrollo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plan_nacional_desarrollo';

    protected $fillable = [
        'nombre',
        'periodo_inicio',
        'periodo_fin',
        'activo',
        'nombre_ejes_generales',
        'nombre_ejes_transversales',
        'ejes_generales',
        'ejes_transversales',
        'descripcion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'periodo_inicio' => 'integer',
        'periodo_fin' => 'integer',
        'ejes_generales' => 'array',
        'ejes_transversales' => 'array',
    ];

    /**
     * Relación con estrategias
     */
    public function estrategies()
    {
        return $this->hasMany(Estrategy::class);
    }

    /**
     * Obtener el PND activo (con cache)
     */
    public static function getActive(): ?self
    {
        return Cache::remember('pnd_active', 3600, function () {
            return self::where('activo', true)->first();
        });
    }

    /**
     * Obtener PND por año específico
     */
    public static function getForYear(int $year): ?self
    {
        return self::where('periodo_inicio', '<=', $year)
            ->where('periodo_fin', '>=', $year)
            ->first();
    }

    /**
     * Obtener label de un eje por su key (para PDFs)
     */
    public function getEjeLabel(string $key): ?string
    {
        $allEjes = array_merge($this->ejes_generales ?? [], $this->ejes_transversales ?? []);

        foreach ($allEjes as $eje) {
            if (isset($eje['key']) && $eje['key'] === $key) {
                return $eje['label'] ?? null;
            }
        }

        return null;
    }

    /**
     * Obtener todos los ejes (generales + transversales) como array plano
     */
    public function getAllEjesFlattened(): array
    {
        $ejesGenerales = collect($this->ejes_generales ?? [])->pluck('label', 'key')->toArray();
        $ejesTransversales = collect($this->ejes_transversales ?? [])->pluck('label', 'key')->toArray();

        return array_merge($ejesGenerales, $ejesTransversales);
    }

    /**
     * Scope para obtener solo PNDs activos
     */
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }
}
