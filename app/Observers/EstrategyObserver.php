<?php

namespace App\Observers;

use App\Models\Estrategy;
use App\Models\PlanNacionalDesarrollo;

class EstrategyObserver
{
    /**
     * Handle the Estrategy "saving" event.
     * Se ejecuta antes de crear o actualizar una estrategia
     */
    public function saving(Estrategy $estrategy): void
    {
        // Generar snapshot de los ejes seleccionados
        $this->generateEjesSnapshot($estrategy);
    }

    /**
     * Genera un snapshot de los ejes del PND seleccionados
     * Guarda el texto completo de cada eje para preservar el contexto histórico
     */
    protected function generateEjesSnapshot(Estrategy $estrategy): void
    {
        // Solo generar si hay ejes seleccionados y un PND asociado
        if (empty($estrategy->ejes_plan_nacional) || empty($estrategy->plan_nacional_desarrollo_id)) {
            $estrategy->ejes_plan_nacional_snapshot = null;

            return;
        }

        // Obtener el PND
        $pnd = PlanNacionalDesarrollo::find($estrategy->plan_nacional_desarrollo_id);

        if (! $pnd) {
            $estrategy->ejes_plan_nacional_snapshot = null;

            return;
        }

        $snapshot = [];
        $ejesSeleccionados = $estrategy->ejes_plan_nacional;

        // Procesar ejes generales
        foreach ($pnd->ejes_generales ?? [] as $eje) {
            $key = $eje['key'] ?? null;
            if ($key && ! empty($ejesSeleccionados[$key])) {
                $snapshot[] = [
                    'key' => $key,
                    'label' => $eje['label'] ?? '',
                    'description' => $eje['description'] ?? '',
                    'tipo' => 'general',
                ];
            }
        }

        // Procesar ejes transversales
        foreach ($pnd->ejes_transversales ?? [] as $eje) {
            $key = $eje['key'] ?? null;
            if ($key && ! empty($ejesSeleccionados[$key])) {
                $snapshot[] = [
                    'key' => $key,
                    'label' => $eje['label'] ?? '',
                    'description' => $eje['description'] ?? '',
                    'tipo' => 'transversal',
                ];
            }
        }

        // Agregar metadatos del PND para referencia
        if (! empty($snapshot)) {
            $estrategy->ejes_plan_nacional_snapshot = [
                'pnd_nombre' => $pnd->nombre,
                'pnd_periodo' => $pnd->fecha_inicio->format('d/m/Y').' - '.$pnd->fecha_fin->format('d/m/Y'),
                'nombre_ejes_generales' => $pnd->nombre_ejes_generales,
                'nombre_ejes_transversales' => $pnd->nombre_ejes_transversales,
                'fecha_snapshot' => now()->toIso8601String(),
                'ejes' => $snapshot,
            ];
        } else {
            $estrategy->ejes_plan_nacional_snapshot = null;
        }
    }
}
