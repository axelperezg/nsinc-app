<?php

namespace App\Filament\Resources\EstrategyResource\Pages;

use App\Filament\Resources\EstrategyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEstrategy extends CreateRecord
{
    protected static string $resource = EstrategyResource::class;

    /**
     * Redirigir a la lista después de crear la estrategia
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
