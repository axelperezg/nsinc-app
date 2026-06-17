<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpirationDate;
use Carbon\Carbon;

class ExpirationDateSeeder extends Seeder
{
    public function run(): void
    {
        $y = Carbon::now()->year;

        $dates = [
            // ── REGISTRO ─────────────────────────────────────────────────────
            [
                'anio'           => $y,
                'concept'        => 'Registro',
                'target_role'    => 'institution_user',
                'fecha_inicio'      => Carbon::create($y, 1, 2),
                'fecha_diaPrevio'   => Carbon::create($y, 9, 25),
                'fecha_limite'      => Carbon::create($y, 9, 30),
                'fecha_restrictiva' => Carbon::create($y, 9, 30),
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector el registro de su estrategia y programa anual.',
            ],
            [
                'anio'           => $y,
                'concept'        => 'Registro',
                'target_role'    => 'sector_coordinator',
                'fecha_inicio'      => Carbon::create($y, 10, 1),
                'fecha_diaPrevio'   => Carbon::create($y, 10, 10),
                'fecha_limite'      => Carbon::create($y, 10, 15),
                'fecha_restrictiva' => Carbon::create($y, 10, 15),
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las estrategias y programas registrados por las dependencias.',
            ],

            // ── MODIFICACIÓN ─────────────────────────────────────────────────
            [
                'anio'           => $y,
                'concept'        => 'Modificación',
                'target_role'    => 'institution_user',
                'fecha_inicio'      => Carbon::create($y, 10, 16),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 2, 20),
                'fecha_limite'      => Carbon::create($y + 1, 2, 28),
                'fecha_restrictiva' => Carbon::create($y + 1, 2, 28),
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las modificaciones a su estrategia y programa anual.',
            ],
            [
                'anio'           => $y,
                'concept'        => 'Modificación',
                'target_role'    => 'sector_coordinator',
                'fecha_inicio'      => Carbon::create($y + 1, 3, 1),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 3, 10),
                'fecha_limite'      => Carbon::create($y + 1, 3, 15),
                'fecha_restrictiva' => Carbon::create($y + 1, 3, 15),
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las modificaciones a las estrategias y programas.',
            ],

            // ── OBSERVACIÓN / SOLVENTACIÓN ───────────────────────────────────
            [
                'anio'           => $y,
                'concept'        => 'Observación',
                'target_role'    => 'institution_user',
                'fecha_inicio'      => Carbon::create($y, 10, 16),
                'fecha_diaPrevio'   => Carbon::create($y, 12, 1),
                'fecha_limite'      => Carbon::create($y, 12, 15),
                'fecha_restrictiva' => Carbon::create($y, 12, 15),
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las Solventaciones a su estrategia y programa anual.',
            ],
            [
                'anio'           => $y,
                'concept'        => 'Observación',
                'target_role'    => 'sector_coordinator',
                'fecha_inicio'      => Carbon::create($y, 12, 16),
                'fecha_diaPrevio'   => Carbon::create($y, 12, 18),
                'fecha_limite'      => Carbon::create($y, 12, 20),
                'fecha_restrictiva' => Carbon::create($y, 12, 20),
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las Solventaciones de las estrategias observadas.',
            ],

            // ── CANCELACIÓN ──────────────────────────────────────────────────
            [
                'anio'           => $y,
                'concept'        => 'Cancelación',
                'target_role'    => 'institution_user',
                'fecha_inicio'      => Carbon::create($y, 10, 16),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 11, 20),
                'fecha_limite'      => Carbon::create($y + 1, 11, 30),
                'fecha_restrictiva' => Carbon::create($y + 1, 11, 30),
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector las cancelaciones a su estrategia y programa anual.',
            ],
            [
                'anio'           => $y,
                'concept'        => 'Cancelación',
                'target_role'    => 'sector_coordinator',
                'fecha_inicio'      => Carbon::create($y + 1, 12, 1),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 12, 10),
                'fecha_limite'      => Carbon::create($y + 1, 12, 15),
                'fecha_restrictiva' => Carbon::create($y + 1, 12, 15),
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las cancelaciones de estrategias.',
            ],

            // ── AÑO SIGUIENTE — REGISTRO ─────────────────────────────────────
            [
                'anio'           => $y + 1,
                'concept'        => 'Registro',
                'target_role'    => 'institution_user',
                'fecha_inicio'      => Carbon::create($y + 1, 1, 2),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 9, 25),
                'fecha_limite'      => Carbon::create($y + 1, 9, 30),
                'fecha_restrictiva' => Carbon::create($y + 1, 9, 30),
                'description' => 'Plazo para que las Dependencias presenten a su Coordinadora de Sector el registro de su estrategia y programa anual.',
            ],
            [
                'anio'           => $y + 1,
                'concept'        => 'Registro',
                'target_role'    => 'sector_coordinator',
                'fecha_inicio'      => Carbon::create($y + 1, 10, 1),
                'fecha_diaPrevio'   => Carbon::create($y + 1, 10, 10),
                'fecha_limite'      => Carbon::create($y + 1, 10, 15),
                'fecha_restrictiva' => Carbon::create($y + 1, 10, 15),
                'description' => 'Plazo para que las Coordinadoras de Sector presenten a la DGNC las estrategias y programas registrados por las dependencias.',
            ],
        ];

        foreach ($dates as $date) {
            ExpirationDate::updateOrCreate(
                [
                    'anio'        => $date['anio'],
                    'concept'     => $date['concept'],
                    'target_role' => $date['target_role'],
                ],
                $date
            );
        }

        $this->command->info("Fechas de vencimiento creadas/actualizadas para {$y} y " . ($y + 1));
    }
}
