<?php

namespace App\Filament\Resources\PromocionPublicidadResource\Pages;

use App\Filament\Resources\PromocionPublicidadResource;
use App\Helpers\ExpirationDateHelper;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPromocionPublicidad extends EditRecord
{
    protected static string $resource = PromocionPublicidadResource::class;

    /**
     * Flag para indicar si se debe redirigirir después de guardar
     */
    public bool $shouldRedirect = true;

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
     * Redirigir a la lista después de editar la estrategia (solo si shouldRedirect es true)
     */
    protected function getRedirectUrl(): ?string
    {
        if ($this->shouldRedirect) {
            return $this->getResource()::getUrl('index');
        }

        // Si no debe redirigir, retornar null para quedarse en la misma página
        return null;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Botón "Guardar cambios" para usuarios de institución
        if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'institution_user' &&
            $this->record && in_array($this->record->estado_estrategia, ['Creada', 'Rechazada CS', 'Rechazada DGNC'])) {

            $actions[] = Actions\Action::make('guardar_sin_salir')
                ->label('Guardar Sin Salir')
                ->icon('heroicon-o-bookmark')
                ->color('success')
                ->action(function () {
                    // Desactivar redirección
                    $this->shouldRedirect = false;

                    try {
                        // Guardar el formulario usando el método save() de Filament
                        $this->save();

                        Notification::make()
                            ->title('Cambios Guardados')
                            ->body('Los cambios han sido guardados exitosamente. Puedes continuar editando.')
                            ->success()
                            ->send();
                    } finally {
                        // Reactivar redirección para el siguiente guardado normal
                        $this->shouldRedirect = true;
                    }
                })
                ->keyBindings(['mod+s']);
        }

        // Solo super administradores pueden eliminar
        if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'super_admin') {
            $actions[] = Actions\DeleteAction::make();
        }

        return $actions;
    }

    /**
     * Sobrescribir el título de notificación de guardado
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cambios guardados';
    }
}
