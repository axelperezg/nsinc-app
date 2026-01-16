<?php

namespace App\Filament\Resources\PlanNacionalDesarrolloResource\Pages;

use App\Filament\Resources\PlanNacionalDesarrolloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanNacionalDesarrollos extends ListRecords
{
    protected static string $resource = PlanNacionalDesarrolloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
