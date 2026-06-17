<?php

namespace App\Filament\Resources\PromocionPublicidadResource\Pages;

use App\Filament\Resources\PromocionPublicidadResource;
use App\Models\Estrategy;
use App\Models\Campaign;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SolventarPromocionPublicidad extends Page
{
    protected static string $resource = PromocionPublicidadResource::class;

    protected static string $view = 'filament.resources.estrategy-resource.pages.solventar-estrategy';

    public $estrategyOriginal;
    public $estrategyNueva;

    public function mount($record): void
    {
        $this->estrategyOriginal = Estrategy::with('campaigns.versions')->findOrFail($record);

        // Verificar que la estrategia esté en estado 'Observada DGNC'
        if ($this->estrategyOriginal->estado_estrategia !== 'Observada DGNC') {
            Notification::make()
                ->title('Error')
                ->body('Solo se pueden solventar estrategias observadas por DGNC.')
                ->danger()
                ->send();

            $this->redirect(PromocionPublicidadResource::getUrl('index'));
            return;
        }

        // Validar fechas de vencimiento para Observación (Solventación usa fechas de Observación)
        $validation = \App\Helpers\ExpirationDateHelper::validateEstrategyConcept(
            'Solventación',
            $this->estrategyOriginal->anio,
            $this->estrategyOriginal->institution_id,
            'institution_user'
        );

        if (!$validation['allowed']) {
            Notification::make()
                ->title('No se puede solventar estrategia')
                ->body($validation['message'])
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(PromocionPublicidadResource::getUrl('index'));
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

        // Crear la nueva estrategia
        $this->duplicarEstrategia();
    }

    protected function duplicarEstrategia(): void
    {
        try {
            // Verificar duplicados recientes (creados en los últimos 30 segundos)
            $recentDuplicate = Estrategy::withoutGlobalScopes()
                ->where('institution_id', $this->estrategyOriginal->institution_id)
                ->where('anio', $this->estrategyOriginal->anio)
                ->where('concepto', 'Solventacion')
                ->where('estrategia_original_id', $this->estrategyOriginal->id)
                ->where('created_at', '>=', now()->subSeconds(30))
                ->first();

            if ($recentDuplicate) {
                Notification::make()
                    ->title('Solventación ya existe')
                    ->body('Ya existe una solventación reciente de esta estrategia. Redirigiendo a la edición.')
                    ->info()
                    ->send();

                $this->redirect(PromocionPublicidadResource::getUrl('edit', ['record' => $recentDuplicate->id]));
                return;
            }

            DB::beginTransaction();

            // 1. Duplicar la estrategia principal
            $estrategyNueva = $this->estrategyOriginal->replicate();
            $estrategyNueva->concepto = 'Solventacion';
            $estrategyNueva->estado_estrategia = 'Creada';
            $estrategyNueva->fecha_elaboracion = now();
            $estrategyNueva->fecha_envio_dgnc = null;
            $estrategyNueva->estrategia_original_id = $this->estrategyOriginal->id;
            $estrategyNueva->created_at = now();
            $estrategyNueva->updated_at = now();
            $estrategyNueva->save();

            // 2. Duplicar las campañas
            foreach ($this->estrategyOriginal->campaigns as $campaignOriginal) {
                $campaignNueva = $campaignOriginal->replicate();
                $campaignNueva->estrategy_id = $estrategyNueva->id;
                $campaignNueva->created_at = now();
                $campaignNueva->updated_at = now();
                $campaignNueva->save();

                // 3. Duplicar las versiones de cada campaña
                foreach ($campaignOriginal->versions as $versionOriginal) {
                    $versionNueva = $versionOriginal->replicate();
                    $versionNueva->campaign_id = $campaignNueva->id;
                    $versionNueva->created_at = now();
                    $versionNueva->updated_at = now();
                    $versionNueva->save();
                }
            }

            DB::commit();

            $this->estrategyNueva = $estrategyNueva;

            Notification::make()
                ->title('Estrategia Solventada')
                ->body('Se ha creado exitosamente una solventación de la estrategia con todas sus campañas y versiones.')
                ->success()
                ->send();

            // Redirigir a la edición de la nueva estrategia
            $this->redirect(PromocionPublicidadResource::getUrl('edit', ['record' => $estrategyNueva->id]));

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error al solventar la estrategia: ' . $e->getMessage())
                ->danger()
                ->send();

            $this->redirect(PromocionPublicidadResource::getUrl('index'));
        }
    }

}
