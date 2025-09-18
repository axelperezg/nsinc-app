<?php

namespace App\Filament\Resources\RamoResource\Pages;

use App\Filament\Resources\RamoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRamos extends ListRecords
{
    protected static string $resource = RamoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
