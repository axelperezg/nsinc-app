<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ramo;

class RamoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si hay registros existentes
        $existingCount = Ramo::count();
        $wasTruncated = false;
        
        if ($existingCount > 0) {
            // Si hay registros, truncar la tabla para borrar todos y reiniciar el auto-increment
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Ramo::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $wasTruncated = true;
            
            $this->command->info("🗑️  Tabla ramos truncada ({$existingCount} registros eliminados). IDs reiniciados desde 1.");
        } else {
            $this->command->info('📝 No hay registros existentes. Se crearán nuevos registros.');
        }
        
        $ramos = [
            [
                'name' => '02 PRESIDENCIA DE LA REPÚBLICA',
            ],
            [
                'name' => '04 GOBERNACIÓN',
            ],
            [
                'name' => '05 RELACIONES EXTERIORES',
            ],
            [
                'name' => '06 HACIENDA Y CRÉDITO PÚBLICO',
            ],
            [
                'name' => '07 DEFENSA NACIONAL',
            ],
            [
                'name' => '08 AGRICULTURA Y DESARROLLO RURAL',
            ],
            [
                'name' => '09 INFRAESTRUCTURA, COMUNICACIONES Y TRANSPORTES',
            ],
            [
                'name' => '10 ECONOMÍA',
            ],
            [
                'name' => '11 EDUCACIÓN PÚBLICA',
            ],
            [
                'name' => '12 SALUD',
            ],
            [
                'name' => '13 MARINA',
            ],
            [
                'name' => '14 TRABAJO Y PREVISIÓN SOCIAL',
            ],
            [
                'name' => '16 MEDIO AMBIENTE Y RECURSOS NATURALES',
            ],
            [
                'name' => '18 ENERGÍA',
            ],
            [
                'name' => '20 BIENESTAR',
            ],
            [
                'name' => '21 TURISMO',
            ],
            [
                'name' => '27 FUNCIÓN PÚBLICA',
            ],
            [
                'name' => '36 SEGURIDAD',
            ],
            [
                'name' => '38 CONSEJO',
            ],
            [
                'name' => '47 ENTIDADES',
            ],
            [
                'name' => '48 CULTURA',
            ],
            [
                'name' => '50 INSTITUTO',
            ],
            [
                'name' => '51 INSTITUTO',
            ],
            [
                'name' => '52 PETRÓLEOS MEXICANOS',
            ],
        ];

        $created = 0;
        $skipped = 0;
        
        foreach ($ramos as $index => $ramoData) {
            // Si se truncó, usar create (tabla está vacía, no hay riesgo de duplicados)
            // Si no se truncó, usar firstOrCreate para evitar duplicados
            if ($wasTruncated) {
                $ramo = Ramo::create([
                    'name' => $ramoData['name'],
                ]);
                $created++;
                $this->command->info("✅ Creado [ID: {$ramo->id}]: {$ramoData['name']}");
            } else {
                $ramo = Ramo::firstOrCreate(
                    ['name' => $ramoData['name']]
                );
                
                if ($ramo->wasRecentlyCreated) {
                    $created++;
                    $this->command->info("✅ Creado [ID: {$ramo->id}]: {$ramoData['name']}");
                } else {
                    $skipped++;
                    $this->command->line("⏭️  Ya existe [ID: {$ramo->id}]: {$ramoData['name']}");
                }
            }
        }

        $this->command->info("\n📊 Resumen:");
        $this->command->info("   - Ramos creados: {$created}");
        if ($skipped > 0) {
            $this->command->info("   - Ramos existentes (omitidos): {$skipped}");
        }
        $this->command->info("   - Total procesados: " . count($ramos));
        
        if ($wasTruncated && $created > 0) {
            $this->command->info("   - IDs iniciales: desde 1 hasta {$created}");
        }
    }
}
