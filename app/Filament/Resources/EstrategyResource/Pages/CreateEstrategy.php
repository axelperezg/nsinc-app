<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use App\Helpers\ExpirationDateHelper;
use App\Models\StrategyDraft;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateEstrategy extends CreateRecord
{
    protected static string $resource = EstrategyResource::class;

    // Vista personalizada con auto-guardado
    protected static string $view = 'filament.resources.estrategy-resource.pages.create-estrategy';

    // Propiedad para almacenar el borrador actual
    public ?StrategyDraft $currentDraft = null;

    /**
     * Hook que se ejecuta al montar la página
     */
    public function mount(): void
    {
        parent::mount();

        // Obtener el año de la URL, sesión, o año actual
        $year = $this->getYearForCreation();

        // Validar fecha de vencimiento para "Registro"
        $validation = ExpirationDateHelper::validateEstrategyConcept('Registro', $year);

        // Si no está permitido, redirigir y mostrar notificación
        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede crear estrategia')
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

        // Cargar borrador si existe
        $this->loadDraft($year);
    }

    /**
     * Obtener el año para crear la estrategia
     */
    protected function getYearForCreation(): int
    {
        // 1. Intentar obtener de parámetro URL directo
        $year = request()->get('year');

        // 2. Si no, intentar del filtro de tabla (si viene de la lista)
        if (!$year) {
            $year = request()->get('tableFilters.anio.anio');
        }

        // 3. Si no, usar el año actual
        if (!$year) {
            $year = now()->year;
        }

        return (int) $year;
    }

    /**
     * Cargar borrador guardado previamente
     */
    protected function loadDraft(int $year): void
    {
        $draft = StrategyDraft::where('user_id', Auth::id())
            ->where('year', $year)
            ->latest('last_saved_at')
            ->first();

        if ($draft) {
            $this->currentDraft = $draft;

            // Llenar el formulario con los datos del borrador
            $this->form->fill($draft->draft_data);

            // Mostrar notificación al usuario
            Notification::make()
                ->title('Borrador recuperado')
                ->body("Se ha recuperado tu borrador guardado. Última modificación: {$draft->last_saved_at->diffForHumans()}")
                ->info()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('eliminar')
                        ->button()
                        ->color('danger')
                        ->label('Eliminar borrador')
                        ->action(function () use ($draft) {
                            $draft->delete();
                            $this->currentDraft = null;
                            Notification::make()
                                ->title('Borrador eliminado')
                                ->success()
                                ->send();
                            // Recargar la página para limpiar el formulario
                            $this->redirect($this->getResource()::getUrl('create'));
                        }),
                ])
                ->send();
        }
    }

    /**
     * Método público para guardar borrador (llamado desde Livewire)
     */
    public function saveDraft(): void
    {
        try {
            $formState = $this->form->getState();
            $year = $this->getYearForCreation();

            // Crear o actualizar el borrador
            $this->currentDraft = StrategyDraft::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'year' => $year,
                ],
                [
                    'draft_data' => $formState,
                    'last_saved_at' => now(),
                ]
            );

            // Notificación silenciosa (opcional)
            // Se puede mostrar un indicador visual en lugar de notificación
        } catch (\Exception $e) {
            // Manejar errores silenciosamente para no interrumpir al usuario
            Log::error('Error al guardar borrador: ' . $e->getMessage());
        }
    }

    /**
     * Hook que se ejecuta antes de guardar el registro
     */
    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        $year = $data['anio'] ?? now()->year;
        $concepto = $data['concepto'] ?? 'Registro';

        // Validar nuevamente antes de guardar
        $validation = ExpirationDateHelper::validateEstrategyConcept($concepto, $year);

        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede crear estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            // Detener el proceso de creación
            $this->halt();
        }
    }

    /**
     * Hook que se ejecuta después de crear el registro
     */
    protected function afterCreate(): void
    {
        // Eliminar el borrador si existe
        if ($this->currentDraft) {
            $this->currentDraft->delete();
            $this->currentDraft = null;
        }

        // Mostrar notificación de éxito con información de fecha
        $year = $this->record->anio;
        $concepto = $this->record->concepto;

        $validation = ExpirationDateHelper::validateEstrategyConcept($concepto, $year);

        $message = "La estrategia ha sido creada exitosamente.";
        if ($validation['expiration']) {
            $diasRestantes = $validation['expiration']->getDaysRemaining();
            $message .= " Recuerde que tiene hasta el {$validation['expiration']->fecha_limite->format('d/m/Y')} ({$diasRestantes} días) para completar este proceso.";
        }

        Notification::make()
            ->title('Estrategia creada')
            ->body($message)
            ->success()
            ->duration(10000)
            ->send();
    }

    /**
     * Redirigir a la lista después de crear la estrategia
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Acciones del header
     */
    protected function getHeaderActions(): array
    {
        return [
            // Sin acciones por ahora
        ];
    }

    /**
     * Personalizar las acciones del formulario
     * Ocultar el botón "Crear y Crear otro"
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
