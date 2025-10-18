<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /**
     * Obtiene la fecha de vencimiento para un concepto y año específico
     *
     * @param string $concept
     * @param int|null $year
     * @return ExpirationDate|null
     */
    public static function getByConceptAndYear(string $concept, ?int $year = null): ?self
    {
        $year = $year ?? now()->year;

        return static::where('concept', $concept)
            ->where('anio', $year)
            ->first();
    }

    /**
     * Verifica si la fecha actual está dentro del período permitido
     *
     * @return bool
     */
    public function isWithinPeriod(): bool
    {
        $today = Carbon::today();
        return $today->isBetween($this->fecha_inicio, $this->fecha_limite);
    }

    /**
     * Verifica si la fecha actual está después de la fecha restrictiva
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return Carbon::today()->isAfter($this->fecha_restrictiva);
    }

    /**
     * Verifica si estamos en el período de advertencia (después de fecha_diaPrevio)
     *
     * @return bool
     */
    public function isInWarningPeriod(): bool
    {
        $today = Carbon::today();
        return $today->isSameDay($this->fecha_diaPrevio) ||
               ($today->isAfter($this->fecha_diaPrevio) && $today->isBefore($this->fecha_limite));
    }

    /**
     * Obtiene los días restantes hasta la fecha límite
     *
     * @return int
     */
    public function getDaysRemaining(): int
    {
        return Carbon::today()->diffInDays($this->fecha_limite, false);
    }

    /**
     * Verifica si la fecha actual está antes del período permitido
     *
     * @return bool
     */
    public function isBeforePeriod(): bool
    {
        return Carbon::today()->isBefore($this->fecha_inicio);
    }

    /**
     * Obtiene el estado actual de esta fecha de vencimiento
     *
     * @return string 'not_started'|'active'|'warning'|'expired'|'restricted'
     */
    public function getStatus(): string
    {
        $today = Carbon::today();

        if ($today->isAfter($this->fecha_restrictiva)) {
            return 'restricted';
        }

        if ($today->isAfter($this->fecha_limite)) {
            return 'expired';
        }

        if ($this->isInWarningPeriod()) {
            return 'warning';
        }

        if ($today->isBefore($this->fecha_inicio)) {
            return 'not_started';
        }

        return 'active';
    }

    /**
     * Obtiene todas las fechas de vencimiento para un año específico
     *
     * @param int|null $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForYear(?int $year = null)
    {
        $year = $year ?? now()->year;

        return static::where('anio', $year)
            ->orderBy('concept')
            ->get();
    }
}
