<?php

namespace App\Filament\Resources\Base\Pages;

use App\Exports\CampaignsExport;
use App\Filament\Widgets\ExpirationDatesWidget;
use App\Models\Estrategy;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

abstract class BaseListEstrategies extends ListRecords
{
    /**
     * Partida presupuestal: '36101' para Comunicación Social, '36201' para Promoción y Publicidad
     * Las clases hijas deben definir este valor
     */
    protected static string $partidaPresupuestal;

    /**
     * Hook que se ejecuta cuando se actualizan los filtros
     */
    public function updatedTableFilters(): void
    {
        // Disparar evento para que los widgets se actualicen
        $this->dispatch('filtersUpdated', year: $this->getFilteredYear());

        // Resetear el cache de acciones y forzar re-renderizado
        $this->cachedHeaderActions = [];
    }

    /**
     * Obtener las acciones del header (se ejecuta cada vez que se actualiza)
     */
    protected function getHeaderActions(): array
    {
        $actions = [];

        // Obtener el año del filtro actual o usar el año actual
        $anio = $this->getFilteredYear();
        $user = Auth::user();

        // Verificar si ya existe una estrategia para este año y esta institución
        $existeEstrategia = false;

        if ($user && $user->institution_id) {
            $existeEstrategia = Estrategy::where('anio', $anio)
                ->where('institution_id', $user->institution_id)
                ->where('partida_presupuestal', static::$partidaPresupuestal)
                ->exists();
        }

        // Solo mostrar el botón "Crear Estrategia" si:
        // 1. NO existe estrategia para el año filtrado
        // 2. El usuario tiene una institución asignada
        // 3. El usuario tiene los permisos adecuados (institution_user)
        $canCreate = !$existeEstrategia
            && $user
            && $user->institution_id
            && $user->role
            && $user->role->name === 'institution_user';

        if ($canCreate) {
            $actions[] = Actions\CreateAction::make('create_' . $anio)
                ->label('Crear Estrategia')
                ->url(fn () => static::getResource()::getUrl('create', ['year' => $anio]))
                // Hacer que el botón sea reactivo al año seleccionado en el filtro
                ->extraAttributes([
                    'wire:key' => 'create-estrategia-' . static::$partidaPresupuestal . '-' . $anio . '-' . ($user->institution_id ?? 'no-inst'),
                ]);
        }

        // Botón de exportar campañas a Excel
        $partida = static::$partidaPresupuestal;
        $nombrePartida = $partida === '36101' ? 'comunicacion_social' : 'promocion_publicidad';

        $actions[] = Actions\Action::make('exportar_campanas_excel')
            ->label('Exportar Campañas a Excel')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () use ($partida, $nombrePartida) {
                return Excel::download(
                    new CampaignsExport($partida),
                    'campanas_' . $nombrePartida . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                );
            })
            ->tooltip('Descargar campañas de esta partida en formato Excel');

        return $actions;
    }

    /**
     * Widgets que se muestran en la parte superior de la página
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ExpirationDatesWidget::make([
                'filterYear' => $this->getFilteredYear(),
            ]),
        ];
    }

    /**
     * Obtener el año filtrado actual
     */
    protected function getFilteredYear(): int
    {
        // Obtener los filtros de la tabla
        $tableFilters = $this->tableFilters;

        // Si existe el filtro de año, usarlo
        if (isset($tableFilters['anio']['anio']) && !empty($tableFilters['anio']['anio'])) {
            return (int) $tableFilters['anio']['anio'];
        }

        // Si no hay filtro, usar el año actual
        return now()->year;
    }
}
