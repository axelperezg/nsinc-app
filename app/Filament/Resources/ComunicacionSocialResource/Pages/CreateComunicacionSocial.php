<?php

namespace App\Filament\Resources\ComunicacionSocialResource\Pages;

use App\Filament\Resources\ComunicacionSocialResource;
use App\Helpers\ExpirationDateHelper;
use App\Models\StrategyDraft;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateComunicacionSocial extends CreateRecord
{
    protected static string $resource = ComunicacionSocialResource::class;

    protected static string $view = 'filament.resources.estrategy-resource.pages.create-estrategy';

    public ?StrategyDraft $currentDraft = null;

    public function mount(): void
    {
        parent::mount();

        $year = $this->getYearForCreation();
        $validation = ExpirationDateHelper::validateEstrategyConcept('Registro', $year, Auth::user()?->institution_id, 'institution_user');

        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede crear estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        if ($validation['level'] === 'warning') {
            Notification::make()
                ->title('Advertencia de fecha límite')
                ->body($validation['message'])
                ->warning()
                ->duration(10000)
                ->send();
        }

        $this->loadDraft($year);
    }

    protected function getYearForCreation(): int
    {
        $year = request()->get('year');

        if (!$year) {
            $year = request()->get('tableFilters.anio.anio');
        }

        if (!$year) {
            $year = now()->year;
        }

        return (int) $year;
    }

    protected function loadDraft(int $year): void
    {
        $draft = StrategyDraft::where('user_id', Auth::id())
            ->where('year', $year)
            ->latest('last_saved_at')
            ->first();

        if ($draft) {
            $this->currentDraft = $draft;
            $this->form->fill($draft->draft_data);

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
                            $this->redirect($this->getResource()::getUrl('create'));
                        }),
                ])
                ->send();
        }
    }

    public function saveDraft(): void
    {
        try {
            $formState = $this->form->getState();
            $year = $this->getYearForCreation();

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
        } catch (\Exception $e) {
            Log::error('Error al guardar borrador: ' . $e->getMessage());
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partida_presupuestal'] = '36101';
        
        // Asegurar que fecha_elaboracion tenga un valor
        if (!isset($data['fecha_elaboracion']) || empty($data['fecha_elaboracion'])) {
            $data['fecha_elaboracion'] = now()->toDateString();
        }
        
        return $data;
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        $year = $data['anio'] ?? now()->year;
        $concepto = $data['concepto'] ?? 'Registro';
        $institutionId = $data['institution_id'] ?? Auth::user()->institution_id;

        $existing = ComunicacionSocialResource::findDuplicateUniqueConcept(
            (int) $institutionId,
            (int) $year,
            '36101',
            $concepto
        );

        if ($existing) {
            $label = $concepto === 'Registro' ? 'un Registro' : 'una Cancelación';
            Notification::make()
                ->title('Duplicado detectado — acción cancelada')
                ->body("Se identificó que se iba a duplicar una estrategia. Ya existe {$label} de Comunicación Social para esta institución en el año {$year}.")
                ->warning()
                ->persistent()
                ->send();

            $this->halt();
        }

        $validation = ExpirationDateHelper::validateEstrategyConcept($concepto, $year, $institutionId, 'institution_user');

        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede crear estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        if ($this->currentDraft) {
            $this->currentDraft->delete();
            $this->currentDraft = null;
        }

        $year = $this->record->anio;
        $concepto = $this->record->concepto;
        $validation = ExpirationDateHelper::validateEstrategyConcept($concepto, $year, $this->record->institution_id, 'institution_user');

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->extraAttributes([
                'wire:loading.attr' => 'disabled',
                'wire:loading.class' => 'opacity-50 cursor-not-allowed',
            ]);
    }
}
