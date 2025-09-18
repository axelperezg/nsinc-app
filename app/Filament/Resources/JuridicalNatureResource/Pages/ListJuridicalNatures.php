<?php

namespace App\Filament\Resources\JuridicalNatureResource\Pages;

use App\Filament\Resources\JuridicalNatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJuridicalNatures extends ListRecords
{
    protected static string $resource = JuridicalNatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
