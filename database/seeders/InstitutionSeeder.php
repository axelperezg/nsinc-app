<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;
use App\Models\Sector;
use App\Models\JuridicalNature;
use App\Models\Ramo;

class InstitutionSeeder extends Seeder
{
    public function run()
    {
        // Obtener o crear sectores básicos
        $sectorGobierno = Sector::firstOrCreate(['name' => 'Gobierno Federal'], ['acronym' => 'GF']);
        $sectorEducacion = Sector::firstOrCreate(['name' => 'Educación'], ['acronym' => 'EDU']);
        $sectorSalud = Sector::firstOrCreate(['name' => 'Salud'], ['acronym' => 'SAL']);
        $sectorEconomia = Sector::firstOrCreate(['name' => 'Economía y Finanzas'], ['acronym' => 'ECON']);

        // Obtener o crear naturalezas jurídicas
        $naturalezaGubernamental = JuridicalNature::firstOrCreate(['name' => 'Órgano Desconcentrado'], ['description' => 'Órgano administrativo dependiente de una secretaría']);
        $naturalezaEducativa = JuridicalNature::firstOrCreate(['name' => 'Organismo Público Descentralizado'], ['description' => 'Organismo con autonomía técnica y de gestión']);
        $naturalezaSalud = JuridicalNature::firstOrCreate(['name' => 'Instituto Nacional'], ['description' => 'Institución especializada en un área específica']);

        // Obtener o crear ramos
        $ramoEducacion = Ramo::firstOrCreate(['name' => 'Educación Pública']);
        $ramoSalud = Ramo::firstOrCreate(['name' => 'Salud']);
        $ramoEconomia = Ramo::firstOrCreate(['name' => 'Hacienda y Crédito Público']);

        // Crear instituciones de prueba
        $institutions = [
            [
                'name' => 'Secretaría de Educación Pública',
                'acronym' => 'SEP',
                'code' => 'SEP001',
                'sector_id' => $sectorEducacion->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'ramo_id' => $ramoEducacion->id,
                'isSector' => true,
                'control' => 'Normal',
            ],
            [
                'name' => 'Instituto Nacional de Salud Pública',
                'acronym' => 'INSP',
                'code' => 'INSP001',
                'sector_id' => $sectorSalud->id,
                'juridical_nature_id' => $naturalezaSalud->id,
                'ramo_id' => $ramoSalud->id,
                'isSector' => false,
                'control' => 'Normal',
            ],
            [
                'name' => 'Secretaría de Hacienda y Crédito Público',
                'acronym' => 'SHCP',
                'code' => 'SHCP001',
                'sector_id' => $sectorEconomia->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'ramo_id' => $ramoEconomia->id,
                'isSector' => true,
                'control' => 'Normal',
            ],
            [
                'name' => 'Universidad Nacional Autónoma de México',
                'acronym' => 'UNAM',
                'code' => 'UNAM001',
                'sector_id' => $sectorEducacion->id,
                'juridical_nature_id' => $naturalezaEducativa->id,
                'ramo_id' => $ramoEducacion->id,
                'isSector' => false,
                'control' => 'Normal',
            ],
            [
                'name' => 'Instituto Mexicano del Seguro Social',
                'acronym' => 'IMSS',
                'code' => 'IMSS001',
                'sector_id' => $sectorSalud->id,
                'juridical_nature_id' => $naturalezaSalud->id,
                'ramo_id' => $ramoSalud->id,
                'isSector' => false,
                'control' => 'Normal',
            ],
            [
                'name' => 'Banco de México',
                'acronym' => 'BANXICO',
                'code' => 'BANXICO001',
                'sector_id' => $sectorEconomia->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'ramo_id' => $ramoEconomia->id,
                'isSector' => false,
                'control' => 'Normal',
            ],
            [
                'name' => 'Instituto Politécnico Nacional',
                'acronym' => 'IPN',
                'code' => 'IPN001',
                'sector_id' => $sectorEducacion->id,
                'juridical_nature_id' => $naturalezaEducativa->id,
                'ramo_id' => $ramoEducacion->id,
                'isSector' => false,
                'control' => 'Normal',
            ],
            [
                'name' => 'Secretaría de Salud',
                'acronym' => 'SSA',
                'code' => 'SSA001',
                'sector_id' => $sectorSalud->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'ramo_id' => $ramoSalud->id,
                'isSector' => true,
                'control' => 'Normal',
            ],
        ];

        foreach ($institutions as $institutionData) {
            Institution::firstOrCreate(
                ['code' => $institutionData['code']],
                $institutionData
            );
        }

        $this->command->info('Se crearon ' . count($institutions) . ' instituciones de prueba exitosamente.');
        $this->command->info('Instituciones creadas:');
        
        foreach ($institutions as $institutionData) {
            $this->command->info('- ' . $institutionData['name'] . ' (' . $institutionData['acronym'] . ')');
        }
    }
}
