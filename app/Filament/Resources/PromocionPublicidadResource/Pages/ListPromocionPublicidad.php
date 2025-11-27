<?php

namespace App\Filament\Resources\PromocionPublicidadResource\Pages;

use App\Filament\Resources\Base\Pages\BaseListEstrategies;
use App\Filament\Resources\PromocionPublicidadResource;

class ListPromocionPublicidad extends BaseListEstrategies
{
    protected static string $resource = PromocionPublicidadResource::class;

    protected static string $partidaPresupuestal = '36201';
}
