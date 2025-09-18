<?php

namespace App\Filament\Resources\ExpirationDateResource\Pages;

use App\Filament\Resources\ExpirationDateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpirationDates extends ListRecords
{
    protected static string $resource = ExpirationDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
