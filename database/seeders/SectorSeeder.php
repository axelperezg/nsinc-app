<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sector;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si hay registros existentes
        $existingCount = Sector::count();
        $wasTruncated = false;
        
        if ($existingCount > 0) {
            // Si hay registros, truncar la tabla para borrar todos y reiniciar el auto-increment
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Sector::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $wasTruncated = true;
            
            $this->command->info("🗑️  Tabla sectors truncada ({$existingCount} registros eliminados). IDs reiniciados desde 1.");
        } else {
            $this->command->info('📝 No hay registros existentes. Se crearán nuevos registros.');
        }
        
        $sectors = [
            [
                'name' => 'COMISIÓN EJECUTIVA DE ATENCIÓN A VICTIMAS',
                'acronym' => 'CEAV',
                'ResponsableSector' => 'Martha Yuriria Rodríguez Estrada',
            ],
            [
                'name' => 'INSTITUTO DE SEGURIDAD Y SERVICIOS SOCIALES DE LOS TRABAJADORES DEL ESTADO',
                'acronym' => 'ISSSTE',
                'ResponsableSector' => 'Jorge Aguilera Cercado',
            ],
            [
                'name' => 'INSTITUTO MEXICANO DE LA RADIO',
                'acronym' => 'IMER',
                'ResponsableSector' => 'Lic. Laura Franco Hernández',
            ],
            [
                'name' => 'INSTITUTO MEXICANO DEL SEGURO SOCIAL',
                'acronym' => 'IMSS',
                'ResponsableSector' => 'Lic. Amadeo Díaz Moguel',
            ],
            [
                'name' => 'INSTITUTO NACIONAL DE LOS PUEBLOS INDÍGENAS',
                'acronym' => 'INPI',
                'ResponsableSector' => 'C. Marsel Paulina Bermúdez Gaona',
            ],
            [
                'name' => 'INSTITUTO NACIONAL PARA LA EVALUACIÓN DE LA EDUCACIÓN',
                'acronym' => 'INEE',
                'ResponsableSector' => 'Mtra. Rebeca Reynoso Angulo',
            ],
            [
                'name' => 'PRESIDENCIA DE LA REPUBLICA',
                'acronym' => 'PRESIDENCIA',
                'ResponsableSector' => 'Lic. Azucena Pimentel Mendoza',
            ],
            [
                'name' => 'PROCURADURIA DE LA DEFENSA DEL CONTRIBUYENTE',
                'acronym' => 'PRODECON',
                'ResponsableSector' => 'Lic. Eduardo Camacho Coronado',
            ],
            [
                'name' => 'SECRETARIA ANTICORRUPCIÓN Y BUEN GOBIERNO',
                'acronym' => 'BUEN GOBIERNO',
                'ResponsableSector' => 'Lic. Lucy Elena Sánchez Díaz',
            ],
            [
                'name' => 'SECRETARIA DE AGRICULTURA Y DESARROLLO RURAL',
                'acronym' => 'SADER',
                'ResponsableSector' => 'Nancy Beatriz Mejía Herrera',
            ],
            [
                'name' => 'SECRETARIA DE BIENESTAR',
                'acronym' => 'BIENESTAR',
                'ResponsableSector' => 'Lic. Lidia Arce Navarijo',
            ],
            [
                'name' => 'SECRETARÍA DE CIENCIA, HUMANIDADES, TECNOLOGÍA E INNOVACIÓN',
                'acronym' => 'SECIHTI',
                'ResponsableSector' => 'Sandra Arcos Reyes',
            ],
            [
                'name' => 'SECRETARIA DE CULTURA',
                'acronym' => 'CULTURA',
                'ResponsableSector' => 'Gloria Edlin Castro Navarrete',
            ],
            [
                'name' => 'SECRETARIA DE DESARROLLO AGRARIO, TERRITORIAL Y URBANO',
                'acronym' => 'SEDATU',
                'ResponsableSector' => 'Nayeli Gómez Castillo',
            ],
            [
                'name' => 'SECRETARIA DE ECONOMÍA',
                'acronym' => 'SE',
                'ResponsableSector' => 'Lic. Héctor Montaut Casas',
            ],
            [
                'name' => 'SECRETARIA DE EDUCACIÓN PÚBLICA',
                'acronym' => 'SEP',
                'ResponsableSector' => 'Lic. Miguel Ángel Pineda Baltazar',
            ],
            [
                'name' => 'SECRETARIA DE ENERGIA',
                'acronym' => 'SENER',
                'ResponsableSector' => 'Lic. Pamela Hamui Abadi',
            ],
            [
                'name' => 'SECRETARIA DE GOBERNACIÓN',
                'acronym' => 'SEGOB',
                'ResponsableSector' => 'Lic. Manuel Durán Aguirre',
            ],
            [
                'name' => 'SECRETARÍA DE HACIENDA Y CRÉDITO PÚBLICO',
                'acronym' => 'SHCP',
                'ResponsableSector' => 'Wilhem Friedrich Hagelsieb Garza',
            ],
            [
                'name' => 'SECRETARÍA DE INFRAESTRUCTURA, COMUNICACIONES Y TRANSPORTES',
                'acronym' => 'SICT',
                'ResponsableSector' => 'Lic. Wendy Vanessa Roa Coronado',
            ],
            [
                'name' => 'SECRETARIA DE LA DEFENSA NACIONAL',
                'acronym' => 'SEDENA',
                'ResponsableSector' => 'General Brigadier E.M. Enrique Mejía',
            ],
            [
                'name' => 'SECRETARIA DE LAS MUJERES',
                'acronym' => 'MUJERES',
                'ResponsableSector' => 'Lic. Paulina Daniela Romero López',
            ],
            [
                'name' => 'SECRETARIA DE MARINA',
                'acronym' => 'SEMAR',
                'ResponsableSector' => 'Contralmirante Rafael Antonio Lagune',
            ],
            [
                'name' => 'SECRETARIA DE MEDIO AMBIENTE Y RECURSOS NATURALES',
                'acronym' => 'SEMARNAT',
                'ResponsableSector' => 'José Manuel Gutiérrez Minera',
            ],
            [
                'name' => 'SECRETARIA DE RELACIONES EXTERIORES',
                'acronym' => 'SRE',
                'ResponsableSector' => 'Lic. Daniela Zapata Zalce',
            ],
            [
                'name' => 'SECRETARIA DE SALUD',
                'acronym' => 'SS',
                'ResponsableSector' => 'Lic. Carlos Álvaro Mateos Beltrán',
            ],
            [
                'name' => 'SECRETARIA DE TURISMO',
                'acronym' => 'SECTUR',
                'ResponsableSector' => 'Lic. Octavio Ortega Velio Mejía',
            ],
            [
                'name' => 'SERVICIOS DE SALUD DEL INSTITUTO MEXICANO DEL SEGURO SOCIAL PARA EL BIENESTAR',
                'acronym' => 'IMSS-BIENESTAR',
                'ResponsableSector' => 'Mtra. Vianey Berenice Fernández Mu',
            ],
            [
                'name' => 'SECRETARIA DEL TRABAJO Y PREVISION SOCIAL',
                'acronym' => 'STPS',
                'ResponsableSector' => 'Lic. Diego Camacho Aquiahuatl',
            ],
            [
                'name' => 'SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA',
                'acronym' => 'SSPC',
                'ResponsableSector' => 'Mtra. Tania Guadalupe Aguilar Diaz',
            ],
        ];

        $created = 0;
        $skipped = 0;
        
        foreach ($sectors as $index => $sectorData) {
            // Si se truncó, usar create (tabla está vacía, no hay riesgo de duplicados)
            // Si no se truncó, usar firstOrCreate para evitar duplicados
            if ($wasTruncated) {
                $sector = Sector::create([
                    'name' => $sectorData['name'],
                    'acronym' => $sectorData['acronym'],
                    'ResponsableSector' => $sectorData['ResponsableSector'],
                ]);
                $created++;
                $this->command->info("✅ Creado [ID: {$sector->id}]: {$sectorData['name']} ({$sectorData['acronym']})");
            } else {
                $sector = Sector::firstOrCreate(
                    ['acronym' => $sectorData['acronym']],
                    [
                        'name' => $sectorData['name'],
                        'ResponsableSector' => $sectorData['ResponsableSector'],
                    ]
                );
                
                if ($sector->wasRecentlyCreated) {
                    $created++;
                    $this->command->info("✅ Creado [ID: {$sector->id}]: {$sectorData['name']} ({$sectorData['acronym']})");
                } else {
                    $skipped++;
                    $this->command->line("⏭️  Ya existe [ID: {$sector->id}]: {$sectorData['name']} ({$sectorData['acronym']})");
                }
            }
        }

        $this->command->info("\n📊 Resumen:");
        $this->command->info("   - Sectores creados: {$created}");
        if ($skipped > 0) {
            $this->command->info("   - Sectores existentes (omitidos): {$skipped}");
        }
        $this->command->info("   - Total procesados: " . count($sectors));
        
        if ($wasTruncated && $created > 0) {
            $this->command->info("   - IDs iniciales: desde 1 hasta {$created}");
        }
    }
}
