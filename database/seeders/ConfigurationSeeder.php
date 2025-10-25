<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'key' => 'widget.expiration_dates.enabled',
                'value' => '1', // Activado por defecto
                'type' => 'boolean',
                'group' => 'widgets',
                'label' => 'Widget de Fechas de Vencimiento',
                'description' => 'Mostrar el widget de fechas de vencimiento en la lista de estrategias para usuarios de institución.',
            ],
            [
                'key' => 'pdf.logo_path',
                'value' => null,
                'type' => 'string',
                'group' => 'pdf',
                'label' => 'Logo Izquierdo para PDFs',
                'description' => 'Logo que se mostrará en la parte superior izquierda de los PDFs de estrategias. Se recomienda usar formato PNG o JPG con fondo transparente.',
            ],
            [
                'key' => 'pdf.logo_right_path',
                'value' => null,
                'type' => 'string',
                'group' => 'pdf',
                'label' => 'Logo Derecho para PDFs',
                'description' => 'Logo que se mostrará en la parte superior derecha de los PDFs de estrategias. Se recomienda usar formato PNG o JPG con fondo transparente.',
            ],
            // Aquí se pueden agregar más configuraciones en el futuro
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        $this->command->info('✅ Configuraciones creadas correctamente.');
    }
}
