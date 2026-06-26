<?php

namespace App\Helpers;

use App\Models\ExpirationDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpirationDateHelper
{
    /**
     * Verifica si una acción está permitida según las fechas de vencimiento.
     *
     * Cuando se proporciona $userRole, el método busca primero un registro
     * específico para ese rol; si no existe, cae en el registro genérico
     * (target_role IS NULL). Esto permite tener plazos distintos por rol.
     */
    public static function canPerformAction(
        string $concept,
        ?int $year = null,
        ?int $institutionId = null,
        ?string $userRole = null,
        ?int $coordinatorUserId = null
    ): array {
        $year = $year ?? now()->year;
        $today = Carbon::today();

        // 1. Buscar registro específico para el rol del usuario
        $expiration = null;

        if ($userRole) {
            $expiration = ExpirationDate::where('concept', $concept)
                ->where('anio', $year)
                ->where('target_role', $userRole)
                ->first();
        }

        // 2. Si no hay registro específico, caer en el genérico (sin rol)
        if (!$expiration) {
            $expiration = ExpirationDate::where('concept', $concept)
                ->where('anio', $year)
                ->whereNull('target_role')
                ->first();
        }

        if (!$expiration) {
            Log::warning("No se encontró fecha de vencimiento para {$concept} año {$year}" . ($userRole ? " rol {$userRole}" : ''));
            return [
                'allowed' => true,
                'message' => "No hay fechas de vencimiento configuradas para {$concept} en el año {$year}. Se permite la acción por defecto.",
                'level' => 'info',
                'expiration' => null,
            ];
        }

        // 3. Verificar exención por institución
        if ($institutionId !== null && $expiration->isInstitutionExempted($institutionId)) {
            return [
                'allowed' => true,
                'message' => "Institución exenta de la validación de fechas para {$concept}.",
                'level' => 'info',
                'expiration' => $expiration,
            ];
        }

        // 4. Verificar exención por coordinadora de sector
        if ($coordinatorUserId !== null && $expiration->isCoordinatorExempted($coordinatorUserId)) {
            return [
                'allowed' => true,
                'message' => "Coordinadora de sector exenta de la validación de fechas para {$concept}.",
                'level' => 'info',
                'expiration' => $expiration,
            ];
        }

        // 5. Verificar restricción estricta (fecha_restrictiva)
        if ($today->isAfter($expiration->fecha_restrictiva)) {
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept}. La fecha restrictiva ({$expiration->fecha_restrictiva->format('d/m/Y')}) ha vencido.",
                'level' => 'danger',
                'expiration' => $expiration,
            ];
        }

        // 6. Verificar fecha límite
        if ($today->isAfter($expiration->fecha_limite)) {
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept}. La fecha límite ({$expiration->fecha_limite->format('d/m/Y')}) ha vencido.",
                'level' => 'danger',
                'expiration' => $expiration,
            ];
        }

        // 7. Verificar día previo (advertencia)
        if ($today->isSameDay($expiration->fecha_diaPrevio) || $today->isAfter($expiration->fecha_diaPrevio)) {
            $diasRestantes = $today->diffInDays($expiration->fecha_limite, false);
            return [
                'allowed' => true,
                'message' => "¡Atención! Quedan {$diasRestantes} día(s) para realizar {$concept}. Fecha límite: {$expiration->fecha_limite->format('d/m/Y')}",
                'level' => 'warning',
                'expiration' => $expiration,
            ];
        }

        // 8. Verificar si estamos antes de la fecha de inicio
        if ($today->isBefore($expiration->fecha_inicio)) {
            $diasParaInicio = $today->diffInDays($expiration->fecha_inicio);
            return [
                'allowed' => false,
                'message' => "No se puede realizar {$concept} aún. El período inicia el {$expiration->fecha_inicio->format('d/m/Y')} (faltan {$diasParaInicio} días).",
                'level' => 'info',
                'expiration' => $expiration,
            ];
        }

        // 9. Todo está bien
        $diasRestantes = $today->diffInDays($expiration->fecha_limite);
        return [
            'allowed' => true,
            'message' => "Puede realizar {$concept}. Fecha límite: {$expiration->fecha_limite->format('d/m/Y')} ({$diasRestantes} días restantes)",
            'level' => 'success',
            'expiration' => $expiration,
        ];
    }

    /**
     * Obtiene el estado de todas las fechas de vencimiento para un año.
     * Pasar $userRole permite encontrar fechas configuradas por rol específico.
     */
    public static function getAllExpirationStatuses(?int $year = null, ?string $userRole = null): array
    {
        $year = $year ?? now()->year;
        $concepts = ['Registro', 'Modificación', 'Observación'];
        $statuses = [];

        foreach ($concepts as $concept) {
            $statuses[$concept] = self::canPerformAction($concept, $year, null, $userRole);
        }

        return $statuses;
    }

    /**
     * Valida si una estrategia se puede crear/modificar según su concepto, año y rol del usuario.
     */
    public static function validateEstrategyConcept(
        string $concepto,
        int $year,
        ?int $institutionId = null,
        ?string $userRole = null,
        ?int $coordinatorUserId = null
    ): array {
        $conceptMap = [
            'Registro'    => 'Registro',
            'Modificación' => 'Modificación',
            'Modificacion' => 'Modificación',
            'Solventación' => 'Observación',
            'Solventacion' => 'Observación',
            'Cancelación'  => 'Cancelación',
            'Cancelacion'  => 'Cancelación',
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

        return self::canPerformAction($expirationConcept, $year, $institutionId, $userRole, $coordinatorUserId);
    }

    /**
     * Obtiene un mensaje formateado para mostrar en notificaciones de Filament
     */
    public static function getFormattedMessage(array $validation): string
    {
        $icon = match($validation['level']) {
            'danger'  => '🚫',
            'warning' => '⚠️',
            'success' => '✅',
            'info'    => 'ℹ️',
            default   => '📋',
        };

        return $icon . ' ' . $validation['message'];
    }
}
