<?php

namespace App\Helpers;

use App\Models\ExpirationDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpirationDateHelper
{
    /**
     * Verifica si una acción está permitida según las fechas de vencimiento
     *
     * @param string $concept El concepto a validar ('Registro', 'Modificación', 'Observación')
     * @param int|null $year El año para el cual validar (null = año actual)
     * @return array ['allowed' => bool, 'message' => string, 'level' => string, 'expiration' => ExpirationDate|null]
     */
    public static function canPerformAction(string $concept, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $today = Carbon::today();

        // Buscar la fecha de vencimiento para el concepto y año
        $expiration = ExpirationDate::where('concept', $concept)
            ->where('anio', $year)
            ->first();

        if (!$expiration) {
            Log::warning("No se encontró fecha de vencimiento para {$concept} año {$year}");
            return [
                'allowed' => true,
                'message' => "No hay fechas de vencimiento configuradas para {$concept} en el año {$year}. Se permite la acción por defecto.",
                'level' => 'info',
                'expiration' => null,
            ];
        }

        // Verificar restricción estricta (fecha_restrictiva)
        if ($today->isAfter($expiration->fecha_restrictiva)) {
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept}. La fecha restrictiva ({$expiration->fecha_restrictiva->format('d/m/Y')}) ha vencido.",
                'level' => 'danger',
                'expiration' => $expiration,
            ];
        }

        // Verificar fecha límite
        if ($today->isAfter($expiration->fecha_limite)) {
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept}. La fecha límite ({$expiration->fecha_limite->format('d/m/Y')}) ha vencido.",
                'level' => 'danger',
                'expiration' => $expiration,
            ];
        }

        // Verificar día previo (advertencia)
        if ($today->isSameDay($expiration->fecha_diaPrevio) || $today->isAfter($expiration->fecha_diaPrevio)) {
            $diasRestantes = $today->diffInDays($expiration->fecha_limite, false);
            return [
                'allowed' => true,
                'message' => "¡Atención! Quedan {$diasRestantes} día(s) para realizar {$concept}. Fecha límite: {$expiration->fecha_limite->format('d/m/Y')}",
                'level' => 'warning',
                'expiration' => $expiration,
            ];
        }

        // Verificar si estamos antes de la fecha de inicio
        if ($today->isBefore($expiration->fecha_inicio)) {
            $diasParaInicio = $today->diffInDays($expiration->fecha_inicio);
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept} aún. El período inicia el {$expiration->fecha_inicio->format('d/m/Y')} (faltan {$diasParaInicio} días).",
                'level' => 'info',
                'expiration' => $expiration,
            ];
        }

        // Todo está bien
        $diasRestantes = $today->diffInDays($expiration->fecha_limite);
        return [
            'allowed' => true,
            'message' => "Puede realizar {$concept}. Fecha límite: {$expiration->fecha_limite->format('d/m/Y')} ({$diasRestantes} días restantes)",
            'level' => 'success',
            'expiration' => $expiration,
        ];
    }

    /**
     * Obtiene el estado de todas las fechas de vencimiento para un año
     *
     * @param int|null $year
     * @return array
     */
    public static function getAllExpirationStatuses(?int $year = null): array
    {
        $year = $year ?? now()->year;
        $concepts = ['Registro', 'Modificación', 'Observación'];
        $statuses = [];

        foreach ($concepts as $concept) {
            $statuses[$concept] = self::canPerformAction($concept, $year);
        }

        return $statuses;
    }

    /**
     * Valida si una estrategia se puede crear según su concepto y año
     *
     * @param string $concepto
     * @param int $year
     * @return array
     */
    public static function validateEstrategyConcept(string $concepto, int $year): array
    {
        // Mapear concepto de estrategia a concepto de fecha de vencimiento
        $conceptMap = [
            'Registro' => 'Registro',
            'Modificación' => 'Modificación',
            'Modificacion' => 'Modificación', // Sin tilde
            'Solventación' => 'Observación',
            'Solventacion' => 'Observación', // Sin tilde
            'Cancelación' => 'Modificación', // Cancelación usa las mismas fechas que Modificación
            'Cancelacion' => 'Modificación', // Sin tilde
        ];

        $expirationConcept = $conceptMap[$concepto] ?? null;

        if (!$expirationConcept) {
            return [
                'allowed' => true,
                'message' => "Concepto '{$concepto}' no requiere validación de fechas.",
                'level' => 'info',
                'expiration' => null,
            ];
        }

        return self::canPerformAction($expirationConcept, $year);
    }

    /**
     * Obtiene un mensaje formateado para mostrar en notificaciones de Filament
     *
     * @param array $validation
     * @return string
     */
    public static function getFormattedMessage(array $validation): string
    {
        $icon = match($validation['level']) {
            'danger' => '🚫',
            'warning' => '⚠️',
            'success' => '✅',
            'info' => 'ℹ️',
            default => '📋',
        };

        return $icon . ' ' . $validation['message'];
    }
}
