<?php

namespace App\Filament\Resources\TranversalPlanResource\Pages;

use App\Filament\Resources\TranversalPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTranversalPlans extends ListRecords
{
    protected static string $resource = TranversalPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
