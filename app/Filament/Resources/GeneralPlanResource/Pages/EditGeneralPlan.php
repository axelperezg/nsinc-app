<?php

namespace App\Filament\Resources\GeneralPlanResource\Pages;

use App\Filament\Resources\GeneralPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralPlan extends EditRecord
{
    protected static string $resource = GeneralPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
