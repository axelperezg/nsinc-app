<?php

namespace Database\Seeders;

use App\Models\PlanNacionalDesarrollo;
use Illuminate\Database\Seeder;

class PlanNacionalDesarrolloSeeder extends Seeder
{
    public function run(): void
    {
        // ── PND 2025-2030 (activo) ──────────────────────────────────────────
        $this->command->info('Creando Plan Nacional de Desarrollo 2025-2030...');

        PlanNacionalDesarrollo::firstOrCreate(
            ['nombre' => 'Plan Nacional de Desarrollo 2025-2030'],
            [
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2030-12-31',
                'activo' => true,
                'sin_plan_publicado' => false,
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

        // ── Período sin PND publicado: enero–octubre 2031 ──────────────────
        $this->command->info('Creando período sin Plan Nacional publicado (ene–oct 2031)...');

        PlanNacionalDesarrollo::firstOrCreate(
            ['nombre' => 'Sin Plan Nacional de Desarrollo publicado (2031)'],
            [
                'fecha_inicio' => '2031-01-01',
                'fecha_fin' => '2031-10-31',
                'activo' => false,
                'sin_plan_publicado' => true,
                'nombre_ejes_generales' => 'Ejes Generales',
                'nombre_ejes_transversales' => 'Ejes Transversales',
                'descripcion' => 'El Plan Nacional de Desarrollo 2031-2035 aún no ha sido publicado. '
                    .'Los campos de ejes estratégicos se habilitarán una vez que el plan sea publicado.',
                'ejes_generales' => null,
                'ejes_transversales' => null,
            ]
        );

        $this->command->info('✅ Período sin PND publicado (ene–oct 2031) creado correctamente.');

        // ── PND 2031-2035 (a partir del 1 de noviembre de 2031) ────────────
        $this->command->info('Creando Plan Nacional de Desarrollo 2031-2035...');

        PlanNacionalDesarrollo::firstOrCreate(
            ['nombre' => 'Plan Nacional de Desarrollo 2031-2035'],
            [
                'fecha_inicio' => '2031-11-01',
                'fecha_fin' => '2035-12-31',
                'activo' => false,
                'sin_plan_publicado' => false,
                'nombre_ejes_generales' => 'Ejes Generales',
                'nombre_ejes_transversales' => 'Ejes Transversales',
                'descripcion' => 'Plan Nacional de Desarrollo del gobierno federal 2031-2035',
                'ejes_generales' => [],
                'ejes_transversales' => [],
            ]
        );

        $this->command->info('✅ Plan Nacional de Desarrollo 2031-2035 creado correctamente.');
    }
}
