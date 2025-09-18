<?php

namespace App\Filament\Resources\RamoResource\Pages;

use App\Filament\Resources\RamoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRamo extends EditRecord
{
    protected static string $resource = RamoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
