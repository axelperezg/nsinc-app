<?php

namespace App\Filament\Resources\CampaignTypeResource\Pages;

use App\Filament\Resources\CampaignTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignType extends EditRecord
{
    protected static string $resource = CampaignTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
