<?php

namespace App\Filament\Resources\JuridicalNatureResource\Pages;

use App\Filament\Resources\JuridicalNatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJuridicalNature extends EditRecord
{
    protected static string $resource = JuridicalNatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
