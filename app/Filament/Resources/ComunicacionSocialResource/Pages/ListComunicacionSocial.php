<?php

namespace App\Filament\Resources\ComunicacionSocialResource\Pages;

use App\Filament\Resources\Base\Pages\BaseListEstrategies;
use App\Filament\Resources\ComunicacionSocialResource;

class ListComunicacionSocial extends BaseListEstrategies
{
    protected static string $resource = ComunicacionSocialResource::class;

    protected static string $partidaPresupuestal = '36101';
}
