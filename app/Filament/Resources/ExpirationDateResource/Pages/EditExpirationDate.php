<?php

namespace App\Filament\Resources\ExpirationDateResource\Pages;

use App\Filament\Resources\ExpirationDateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpirationDate extends EditRecord
{
    protected static string $resource = ExpirationDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
