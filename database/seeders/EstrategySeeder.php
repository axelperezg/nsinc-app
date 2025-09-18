<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\JuridicalNature;
use App\Models\Responsable;

class EstrategySeeder extends Seeder
{
    public function run()
    {
        // Obtener o crear naturaleza jurídica
        $naturalezaGubernamental = JuridicalNature::firstOrCreate(['name' => 'Órgano Desconcentrado']);
        
        // Obtener o crear responsables
        $responsable1 = Responsable::firstOrCreate(['name' => 'Dr. Juan Pérez'], ['charge' => 'Director General']);
        $responsable2 = Responsable::firstOrCreate(['name' => 'Lic. María González'], ['charge' => 'Subdirectora']);
        $responsable3 = Responsable::firstOrCreate(['name' => 'Ing. Carlos Rodríguez'], ['charge' => 'Coordinador']);

        // Obtener instituciones
        $institutionSEP = Institution::where('acronym', 'SEP')->first();
        $institutionUNAM = Institution::where('acronym', 'UNAM')->first();
        $institutionIMSS = Institution::where('acronym', 'IMSS')->first();
        $institutionSHCP = Institution::where('acronym', 'SHCP')->first();

        // Crear estrategias de prueba
        $estrategies = [];

        if ($institutionSEP) {
            $estrategies[] = [
                'anio' => '2025',
                'institution_id' => $institutionSEP->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'mision' => 'Garantizar una educación de calidad para todos los mexicanos, promoviendo la equidad y la excelencia académica.',
                'vision' => 'Ser la institución líder en la transformación educativa de México, formando ciudadanos competentes y comprometidos con el desarrollo del país.',
                'objetivo_institucional' => 'Mejorar la calidad de la educación básica y media superior en todo el territorio nacional.',
                'objetivo_estrategia' => 'Implementar programas innovadores de enseñanza y aprendizaje que respondan a las necesidades del siglo XXI.',
                'fecha_elaboracion' => '2025-01-15',
                'estado_estrategia' => 'Autorizada',
                'concepto' => 'Registro',
                'fecha_envio_dgnc' => '2025-01-20',
                'presupuesto' => 15000000.00,
                'responsable_id' => $responsable1->id,
                'ejes_plan_nacional' => [
                    'eje_general_2_desarrollo' => true,
                    'eje_transversal_1_igualdad' => true,
                ],
            ];
        }

        if ($institutionUNAM) {
            $estrategies[] = [
                'anio' => '2025',
                'institution_id' => $institutionUNAM->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'mision' => 'Impartir educación superior para formar profesionistas útiles a la sociedad, organizar y realizar investigaciones y extender la cultura.',
                'vision' => 'Ser la institución de educación superior más importante de México y una de las mejores del mundo.',
                'objetivo_institucional' => 'Mantener la excelencia académica y la vanguardia en investigación científica y humanística.',
                'objetivo_estrategia' => 'Fortalecer la internacionalización y la innovación educativa en todos los niveles.',
                'fecha_elaboracion' => '2025-01-10',
                'estado_estrategia' => 'Enviada a CS',
                'concepto' => 'Modificacion',
                'fecha_envio_dgnc' => null,
                'presupuesto' => 25000000.00,
                'responsable_id' => $responsable2->id,
                'ejes_plan_nacional' => [
                    'eje_general_2_desarrollo' => true,
                    'eje_general_4_sustentable' => true,
                    'eje_transversal_2_innovacion' => true,
                ],
            ];
        }

        if ($institutionIMSS) {
            $estrategies[] = [
                'anio' => '2025',
                'institution_id' => $institutionIMSS->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'mision' => 'Proporcionar servicios de salud y seguridad social a la población derechohabiente.',
                'vision' => 'Ser la institución de seguridad social más importante de México, reconocida por la excelencia en la atención médica.',
                'objetivo_institucional' => 'Mejorar la calidad de los servicios de salud y ampliar la cobertura de atención.',
                'objetivo_estrategia' => 'Implementar tecnologías médicas avanzadas y mejorar la eficiencia operativa.',
                'fecha_elaboracion' => '2025-01-05',
                'estado_estrategia' => 'Creada',
                'concepto' => 'Registro',
                'fecha_envio_dgnc' => null,
                'presupuesto' => 35000000.00,
                'responsable_id' => $responsable3->id,
                'ejes_plan_nacional' => [
                    'eje_general_2_desarrollo' => true,
                    'eje_general_3_economia' => true,
                ],
            ];
        }

        if ($institutionSHCP) {
            $estrategies[] = [
                'anio' => '2025',
                'institution_id' => $institutionSHCP->id,
                'juridical_nature_id' => $naturalezaGubernamental->id,
                'mision' => 'Conducir la política económica del país para garantizar un crecimiento sostenido y sostenible.',
                'vision' => 'Ser la institución rectora de la política económica más confiable y eficiente de América Latina.',
                'objetivo_institucional' => 'Mantener la estabilidad macroeconómica y promover el desarrollo económico inclusivo.',
                'objetivo_estrategia' => 'Implementar políticas fiscales responsables y promover la transparencia financiera.',
                'fecha_elaboracion' => '2025-01-12',
                'estado_estrategia' => 'Aceptada a CS',
                'concepto' => 'Registro',
                'fecha_envio_dgnc' => '2025-01-18',
                'presupuesto' => 45000000.00,
                'responsable_id' => $responsable1->id,
                'ejes_plan_nacional' => [
                    'eje_general_3_economia' => true,
                    'eje_general_4_sustentable' => true,
                    'eje_transversal_2_innovacion' => true,
                ],
            ];
        }

        // Crear las estrategias
        foreach ($estrategies as $estrategyData) {
            Estrategy::firstOrCreate(
                [
                    'institution_id' => $estrategyData['institution_id'],
                    'anio' => $estrategyData['anio'],
                ],
                $estrategyData
            );
        }

        $this->command->info('Se crearon ' . count($estrategies) . ' estrategias de prueba exitosamente.');
        $this->command->info('Estrategias creadas:');
        
        foreach ($estrategies as $estrategyData) {
            $institution = Institution::find($estrategyData['institution_id']);
            $this->command->info('- ' . $institution->name . ' (' . $estrategyData['estado_estrategia'] . ')');
        }
    }
}
