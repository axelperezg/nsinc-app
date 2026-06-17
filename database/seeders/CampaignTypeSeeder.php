<?php

namespace Database\Seeders;

use App\Models\CampaignType;
use Illuminate\Database\Seeder;

class CampaignTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Cultura ciudadana y valores',
            'Culturales',
            'Prevención',
            'Rendición de cuentas',
            'Servicios de Gobierno',
        ];

        foreach ($types as $name) {
            CampaignType::firstOrCreate(
                ['name' => $name],
                ['description' => '']
            );
        }
    }
}
