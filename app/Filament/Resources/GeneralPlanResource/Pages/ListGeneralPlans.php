<?php

namespace App\Filament\Resources\GeneralPlanResource\Pages;

use App\Filament\Resources\GeneralPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralPlans extends ListRecords
{
    protected static string $resource = GeneralPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
