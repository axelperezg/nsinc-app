<?php

namespace App\Filament\Widgets;

use App\Models\Estrategy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class EstrategyOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        $query = Estrategy::query();
        
        if ($user && $user->role && $user->role->name !== 'super_admin' && $user->institution_id) {
            $query->where('institution_id', $user->institution_id);
        }

        $totalEstrategias = $query->count();
        $estrategiasActivas = (clone $query)->where('estado_estrategia', 'Autorizada')->count();
        $estrategiasPendientes = (clone $query)->whereIn('estado_estrategia', ['Creada', 'Enviada a CS'])->count();

        return [
            Stat::make('Total Estrategias', $totalEstrategias)
                ->description($user && $user->role && $user->role->name === 'super_admin' ? 'Todas las instituciones' : 'Tu institución')
                ->color('primary'),
            
            Stat::make('Estrategias Activas', $estrategiasActivas)
                ->color('success'),
            
            Stat::make('Estrategias Pendientes', $estrategiasPendientes)
                ->color('warning'),
        ];
    }
}
