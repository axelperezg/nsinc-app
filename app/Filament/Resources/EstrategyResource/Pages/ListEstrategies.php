<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use App\Models\Estrategy;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListEstrategies extends ListRecords
{
    protected static string $resource = EstrategyResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Obtener el año del filtro actual o usar el año actual
        $anio = request()->get('tableFilters.anio.anio', now()->year);
        
        // Verificar si ya existe una estrategia para este año
        $estrategiaExistente = Estrategy::where('anio', $anio)->first();

        // Solo mostrar el botón "Crear Estrategia" si NO existe ninguna estrategia para el año filtrado
        if (!$estrategiaExistente) {
            $actions[] = Actions\CreateAction::make();
        }

        return $actions;
    }

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
}
