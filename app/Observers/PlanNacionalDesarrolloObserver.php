<?php

namespace App\Observers;

use App\Models\PlanNacionalDesarrollo;
use Illuminate\Support\Facades\Cache;

class PlanNacionalDesarrolloObserver
{
    /**
     * Handle the PlanNacionalDesarrollo "saving" event.
     * Se ejecuta antes de crear o actualizar un PND
     */
    public function saving(PlanNacionalDesarrollo $pnd): void
    {
        // Si se activa este PND, desactivar todos los demás
        if ($pnd->activo && $pnd->isDirty('activo')) {
            PlanNacionalDesarrollo::where('id', '!=', $pnd->id ?? 0)
                ->update(['activo' => false]);

            // Limpiar cache
            Cache::forget('pnd_active');
        }
    }

    /**
     * Handle the PlanNacionalDesarrollo "saved" event.
     * Se ejecuta después de crear o actualizar un PND
     */
    public function saved(PlanNacionalDesarrollo $pnd): void
    {
        // Limpiar cache cuando se guarda cualquier PND
        Cache::forget('pnd_active');
    }

    /**
     * Handle the PlanNacionalDesarrollo "deleting" event.
     * Se ejecuta antes de eliminar un PND
     */
    public function deleting(PlanNacionalDesarrollo $pnd): void
    {
        // Prevenir eliminación si tiene estrategias asociadas
        $count = $pnd->estrategies()->count();

        if ($count > 0) {
            throw new \Exception(
                "No se puede eliminar este PND porque está siendo utilizado por {$count} estrategia(s). ".
                'Considera desactivarlo en lugar de eliminarlo.'
            );
        }
    }

    /**
     * Handle the PlanNacionalDesarrollo "deleted" event.
     */
    public function deleted(PlanNacionalDesarrollo $pnd): void
    {
        // Limpiar cache cuando se elimina un PND
        Cache::forget('pnd_active');
    }
}
