<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpirationDate;
use Carbon\Carbon;

class ExpirationDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar fechas existentes (opcional - comentar si no desea eliminar)
        // ExpirationDate::truncate();

        $currentYear = Carbon::now()->year;

        // Fechas de vencimiento para el año actual (2025)
        $expirationDates2025 = [
            // REGISTRO - Dependencias a Coordinadora de Sector
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 1, 2),  // 2 de enero
                'fecha_diaPrevio' => Carbon::create($currentYear, 9, 25), // 25 de septiembre
                'fecha_limite' => Carbon::create($currentYear, 9, 30),    // 30 de septiembre
                'fecha_restrictiva' => Carbon::create($currentYear, 9, 30),
                'concept' => 'Registro',
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector el registro de su estrategia y programa anual.',
            ],
            // REGISTRO - Coordinadora de Sector a DGNC
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 10, 1),  // 1 de octubre
                'fecha_diaPrevio' => Carbon::create($currentYear, 10, 10), // 10 de octubre
                'fecha_limite' => Carbon::create($currentYear, 10, 15),    // 15 de octubre
                'fecha_restrictiva' => Carbon::create($currentYear, 10, 15),
                'concept' => 'Registro',
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las estrategias y programas registrados por las dependencias.',
            ],
            // MODIFICACIÓN - Dependencias a Coordinadora de Sector
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 10, 16),  // 16 de octubre
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 2, 20), // 20 de febrero del año siguiente
                'fecha_limite' => Carbon::create($currentYear + 1, 2, 28),    // 28 de febrero del año siguiente
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 2, 28),
                'concept' => 'Modificación',
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las modificaciones a su estrategia y programa anual.',
            ],
            // MODIFICACIÓN - Coordinadora de Sector a DGNC
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear + 1, 3, 1),  // 1 de marzo del año siguiente
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 3, 10), // 10 de marzo del año siguiente
                'fecha_limite' => Carbon::create($currentYear + 1, 3, 15),    // 15 de marzo del año siguiente
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 3, 15),
                'concept' => 'Modificación',
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las modificaciones a las estrategias y programas.',
            ],
            // SOLVENTACIÓN/OBSERVACIÓN - Dependencias a Coordinadora de Sector
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 10, 16),  // 16 de octubre
                'fecha_diaPrevio' => Carbon::create($currentYear, 12, 1), // 1 de diciembre
                'fecha_limite' => Carbon::create($currentYear, 12, 15),    // 15 de diciembre
                'fecha_restrictiva' => Carbon::create($currentYear, 12, 15),
                'concept' => 'Observación',
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las Solventaciones a su estrategia y programa anual.',
            ],
            // SOLVENTACIÓN/OBSERVACIÓN - Coordinadora de Sector a DGNC
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 12, 16),  // 16 de diciembre
                'fecha_diaPrevio' => Carbon::create($currentYear, 12, 18), // 18 de diciembre
                'fecha_limite' => Carbon::create($currentYear, 12, 20),    // 20 de diciembre
                'fecha_restrictiva' => Carbon::create($currentYear, 12, 20),
                'concept' => 'Observación',
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las Solventaciones de las estrategias observadas.',
            ],
            // CANCELACIÓN - Dependencias a Coordinadora de Sector
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear, 10, 16),  // 16 de octubre
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 11, 20), // 20 de noviembre del año siguiente
                'fecha_limite' => Carbon::create($currentYear + 1, 11, 30),    // 30 de noviembre del año siguiente
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 11, 30),
                'concept' => 'Cancelación',
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las cancelaciones a su estrategia y programa anual.',
            ],
            // CANCELACIÓN - Coordinadora de Sector a DGNC
            [
                'anio' => $currentYear,
                'fecha_inicio' => Carbon::create($currentYear + 1, 12, 1),  // 1 de diciembre del año siguiente
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 12, 10), // 10 de diciembre del año siguiente
                'fecha_limite' => Carbon::create($currentYear + 1, 12, 15),    // 15 de diciembre del año siguiente
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 12, 15),
                'concept' => 'Cancelación',
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las cancelaciones de estrategias.',
            ],
        ];

        // Fechas de vencimiento para el año siguiente (2026)
        $expirationDates2026 = [
            // REGISTRO - Dependencias a Coordinadora de Sector
            [
                'anio' => $currentYear + 1,
                'fecha_inicio' => Carbon::create($currentYear + 1, 1, 2),  // 2 de enero 2026
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 9, 25), // 25 de septiembre 2026
                'fecha_limite' => Carbon::create($currentYear + 1, 9, 30),    // 30 de septiembre 2026
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 9, 30),
                'concept' => 'Registro',
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector el registro de su estrategia y programa anual.',
            ],
            // REGISTRO - Coordinadora de Sector a DGNC
            [
                'anio' => $currentYear + 1,
                'fecha_inicio' => Carbon::create($currentYear + 1, 10, 1),  // 1 de octubre 2026
                'fecha_diaPrevio' => Carbon::create($currentYear + 1, 10, 10), // 10 de octubre 2026
                'fecha_limite' => Carbon::create($currentYear + 1, 10, 15),    // 15 de octubre 2026
                'fecha_restrictiva' => Carbon::create($currentYear + 1, 10, 15),
                'concept' => 'Registro',
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las estrategias y programas registrados por las dependencias.',
            ],
        ];

        // Insertar o actualizar fechas de vencimiento
        foreach ($expirationDates2025 as $date) {
            ExpirationDate::updateOrCreate(
                [
                    'anio' => $date['anio'],
                    'concept' => $date['concept'],
                    'description' => $date['description'],
                ],
                $date
            );
        }

        foreach ($expirationDates2026 as $date) {
            ExpirationDate::updateOrCreate(
                [
                    'anio' => $date['anio'],
                    'concept' => $date['concept'],
                    'description' => $date['description'],
                ],
                $date
            );
        }

        $this->command->info('Fechas de vencimiento creadas/actualizadas exitosamente para ' . $currentYear . ' y ' . ($currentYear + 1));
    }
}
