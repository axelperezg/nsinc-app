<?php

namespace App\Http\Controllers;

use App\Models\Estrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class EstrategyController extends Controller
{
    public function evaluar(Request $request, Estrategy $estrategy)
    {
        // Verificar que el usuario sea coordinador de sector
        $user = Auth::user();
        if (!$user || $user->role->name !== 'sector_coordinator') {
            abort(403, 'No tienes permisos para evaluar estrategias.');
        }

        // Verificar que la estrategia esté en estado 'Enviado a CS'
        if ($estrategy->estado_estrategia !== 'Enviado a CS') {
            abort(400, 'Solo se pueden evaluar estrategias en estado "Enviado a CS".');
        }

        // Verificar que la estrategia pertenezca al sector del coordinador
        if ($estrategy->institution->sector_id !== $user->sector_id) {
            abort(403, 'No puedes evaluar estrategias de otros sectores.');
        }

        $nuevoEstado = $request->input('estado');
        
        // Validar que el estado sea válido
        if (!in_array($nuevoEstado, ['Aceptada CS', 'Rechazada CS'])) {
            abort(400, 'Estado no válido.');
        }

        // Actualizar el estado de la estrategia
        $estrategy->update(['estado_estrategia' => $nuevoEstado]);

        // Mostrar notificación de éxito
        if ($nuevoEstado === 'Aceptada CS') {
            FilamentNotification::make()
                ->title('Estrategia Aceptada')
                ->body('La estrategia ha sido aceptada exitosamente.')
                ->success()
                ->send();
        } else {
            FilamentNotification::make()
                ->title('Estrategia Rechazada')
                ->body('La estrategia ha sido rechazada.')
                ->danger()
                ->send();
        }

        // Redirigir de vuelta a la lista de estrategias
        return redirect()->route('filament.admin.resources.estrategies.index');
    }
}
