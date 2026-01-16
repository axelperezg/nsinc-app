<?php

namespace App\Filament\Resources\PlanNacionalDesarrolloResource\Pages;

use App\Filament\Resources\PlanNacionalDesarrolloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanNacionalDesarrollo extends EditRecord
{
    protected static string $resource = PlanNacionalDesarrolloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
