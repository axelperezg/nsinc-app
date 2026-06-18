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
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'sin_plan_publicado',
        'nombre_ejes_generales',
        'nombre_ejes_transversales',
        'ejes_generales',
        'ejes_transversales',
        'descripcion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'sin_plan_publicado' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
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
     * Obtener PND por fecha específica (incluye períodos sin plan publicado)
     */
    public static function getForDate(string|\DateTimeInterface $date): ?self
    {
        $date = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;

        return self::whereDate('fecha_inicio', '<=', $date)
            ->whereDate('fecha_fin', '>=', $date)
            ->first();
    }

    /**
     * Obtener PND por año específico (busca por rango de fechas)
     */
    public static function getForYear(int $year): ?self
    {
        return self::whereDate('fecha_inicio', '<=', "{$year}-12-31")
            ->whereDate('fecha_fin', '>=', "{$year}-01-01")
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
