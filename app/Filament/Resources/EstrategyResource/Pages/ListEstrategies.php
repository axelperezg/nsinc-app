<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use App\Filament\Widgets\ExpirationDatesWidget;
use App\Models\Estrategy;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListEstrategies extends ListRecords
{
    protected static string $resource = EstrategyResource::class;

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
                ->exists();
        }

        // Solo mostrar el botón "Crear Estrategia" si:
        // 1. NO existe estrategia para el año filtrado
        // 2. El usuario tiene una institución asignada
        // 3. El usuario tiene los permisos adecuados (institution_user o institution_admin)
        $canCreate = !$existeEstrategia
            && $user
            && $user->institution_id
            && $user->role
            && in_array($user->role->name, ['institution_user', 'institution_admin']);

        if ($canCreate) {
            $actions[] = Actions\CreateAction::make('create_' . $anio)
                ->label('Crear Estrategia')
                ->url(fn () => static::getResource()::getUrl('create', ['year' => $anio]))
                // Hacer que el botón sea reactivo al año seleccionado en el filtro
                ->extraAttributes([
                    'wire:key' => 'create-estrategy-' . $anio . '-' . ($user->institution_id ?? 'no-inst'),
                ]);
        }

        return $actions;
    }

    // Tabs deshabilitados - se usan los filtros de la tabla en su lugar
    /*
    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->badge($this->getCountForTab(null)),
            
            'enviadas_dgnc' => Tab::make('Enviadas a DGNC')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado_estrategia', 'Enviada a DGNC'))
                ->badge($this->getCountForTab('Enviada a DGNC')),
            
            'autorizadas' => Tab::make('Autorizadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado_estrategia', 'Autorizada'))
                ->badge($this->getCountForTab('Autorizada')),
            
            'observadas' => Tab::make('Observadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado_estrategia', 'Observada DGNC'))
                ->badge($this->getCountForTab('Observada DGNC')),
            
            'canceladas' => Tab::make('Canceladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado_estrategia', 'like', '%Cancelada%'))
                ->badge($this->getCountForTab('estado_estrategia', '%Cancelada%')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'todas';
    }

    private function getCountForTab($estado = null, $pattern = null)
    {
        $user = Auth::user();
        $baseQuery = Estrategy::query();
        
        // Aplicar filtros de usuario
        if ($user && $user->role) {
            switch ($user->role->name) {
                case 'super_admin':
                case 'dgnc_user':
                    break;
                case 'sector_coordinator':
                    if ($user->sector_id) {
                        $baseQuery->whereHas('institution', function ($q) use ($user) {
                            $q->where('sector_id', $user->sector_id);
                        });
                    }
                    break;
                default:
                    if ($user->institution_id) {
                        $baseQuery->where('institution_id', $user->institution_id);
                    }
                    break;
            }
        }
        
        // Aplicar filtro de estado si se proporciona
        if ($estado !== null) {
            if ($pattern !== null) {
                $baseQuery->where('estado_estrategia', 'like', $pattern);
            } else {
                $baseQuery->where('estado_estrategia', $estado);
            }
        }
        
        return $baseQuery->count();
    }
    */

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
