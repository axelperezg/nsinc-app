<?php

namespace Database\Seeders;

use App\Models\PlanNacionalDesarrollo;
use Illuminate\Database\Seeder;

class PlanNacionalDesarrolloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando Plan Nacional de Desarrollo 2025-2030...');

        PlanNacionalDesarrollo::firstOrCreate(
            ['nombre' => 'Plan Nacional de Desarrollo 2025-2030'],
            [
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2030-12-31',
                'activo' => true,
                'nombre_ejes_generales' => 'Ejes Generales',
                'nombre_ejes_transversales' => 'Ejes Transversales',
                'descripcion' => 'Plan Nacional de Desarrollo del gobierno federal 2025-2030',
                'ejes_generales' => [
                    [
                        'key' => 'eje_general_1_gobernanza',
                        'label' => 'Eje General 1: Gobernanza con justicia y participación ciudadana',
                        'description' => 'Fortalecimiento democrático',
                        'orden' => 1,
                    ],
                    [
                        'key' => 'eje_general_2_desarrollo',
                        'label' => 'Eje General 2: Desarrollo con bienestar y humanismo',
                        'description' => 'Bienestar social',
                        'orden' => 2,
                    ],
                    [
                        'key' => 'eje_general_3_economia',
                        'label' => 'Eje General 3: Economía moral y trabajo',
                        'description' => 'Desarrollo económico',
                        'orden' => 3,
                    ],
                    [
                        'key' => 'eje_general_4_sustentable',
                        'label' => 'Eje General 4: Desarrollo sustentable',
                        'description' => 'Medio ambiente',
                        'orden' => 4,
                    ],
                ],
                'ejes_transversales' => [
                    [
                        'key' => 'eje_transversal_1_igualdad',
                        'label' => 'Eje Transversal 1: Igualdad sustantiva y derechos de las mujeres',
                        'description' => 'Igualdad de género',
                        'orden' => 1,
                    ],
                    [
                        'key' => 'eje_transversal_2_innovacion',
                        'label' => 'Eje Transversal 2: Innovación pública para el desarrollo tecnológico nacional',
                        'description' => 'Innovación tecnológica',
                        'orden' => 2,
                    ],
                    [
                        'key' => 'eje_transversal_3_derechos',
                        'label' => 'Eje Transversal 3: Derechos de los pueblos y comunidades indígenas y afromexicanas',
                        'description' => 'Pueblos originarios',
                        'orden' => 3,
                    ],
                ],
            ]
        );

        $this->command->info('✅ Plan Nacional de Desarrollo 2025-2030 creado correctamente.');
    }
}
