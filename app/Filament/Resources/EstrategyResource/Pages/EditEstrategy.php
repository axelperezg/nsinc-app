<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditEstrategy extends EditRecord
{
    protected static string $resource = EstrategyResource::class;

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
