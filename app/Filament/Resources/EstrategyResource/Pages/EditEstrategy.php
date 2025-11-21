<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use App\Helpers\ExpirationDateHelper;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditEstrategy extends EditRecord
{
    protected static string $resource = EstrategyResource::class;

    /**
     * Hook que se ejecuta al montar la página
     */
    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Validar fecha de vencimiento basado en el concepto de la estrategia
        $validation = ExpirationDateHelper::validateEstrategyConcept(
            $this->record->concepto,
            $this->record->anio
        );

        // Si no está permitido editar, redirigir
        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede editar estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            // Redirigir a la lista
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        // Si hay advertencia, mostrarla
        if ($validation['level'] === 'warning') {
            Notification::make()
                ->title('Advertencia de fecha límite')
                ->body($validation['message'])
                ->warning()
                ->duration(10000)
                ->send();
        }
    }

    /**
     * Hook que se ejecuta antes de guardar los cambios
     */
    protected function beforeSave(): void
    {
        // Validar nuevamente antes de guardar
        $validation = ExpirationDateHelper::validateEstrategyConcept(
            $this->record->concepto,
            $this->record->anio
        );

        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede guardar estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            // Detener el proceso de guardado
            $this->halt();
        }
    }

    /**
     * Hook que se ejecuta después de guardar
     */
    protected function afterSave(): void
    {
        $validation = ExpirationDateHelper::validateEstrategyConcept(
            $this->record->concepto,
            $this->record->anio
        );

        $message = "La estrategia ha sido actualizada exitosamente.";
        if ($validation['expiration']) {
            $diasRestantes = $validation['expiration']->getDaysRemaining();
            $message .= " Recuerde que tiene hasta el {$validation['expiration']->fecha_limite->format('d/m/Y')} ({$diasRestantes} días) para completar este proceso.";
        }

        Notification::make()
            ->title('Estrategia actualizada')
            ->body($message)
            ->success()
            ->duration(10000)
            ->send();
    }

    /**
     * Redirigir a la lista después de editar la estrategia
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Solo super administradores pueden eliminar
        if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin') {
            $actions[] = Actions\DeleteAction::make();
        }

        return $actions;
    }
}
