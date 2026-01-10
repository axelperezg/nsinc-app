<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JuridicalNature;

class JuridicalNatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si hay registros existentes
        $existingCount = JuridicalNature::count();
        $wasTruncated = false;
        
        if ($existingCount > 0) {
            // Si hay registros, truncar la tabla para borrar todos y reiniciar el auto-increment
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            JuridicalNature::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $wasTruncated = true;
            
            $this->command->info("🗑️  Tabla juridical_natures truncada ({$existingCount} registros eliminados). IDs reiniciados desde 1.");
        } else {
            $this->command->info('📝 No hay registros existentes. Se crearán nuevos registros.');
        }
        
        $juridicalNatures = [
            [
                'name' => 'Dependencia',
                'description' => 'Dependencias del Poder Ejecutivo Federal',
            ],
            [
                'name' => 'Órgano Desconcentrado',
                'description' => 'Órganos administrativos desconcentrados de las dependencias',
            ],
            [
                'name' => 'Organismos',
                'description' => 'Organismos públicos descentralizados y autónomos',
            ],
            [
                'name' => 'Empresas de Participación Estatal',
                'description' => 'Empresas en las que el Estado tiene participación accionaria',
            ],
            [
                'name' => 'Fideicomiso',
                'description' => 'Fideicomisos públicos constituidos por el Estado',
            ],
        ];

        $created = 0;
        $skipped = 0;
        
        foreach ($juridicalNatures as $index => $natureData) {
            // Si se truncó, usar create (tabla está vacía, no hay riesgo de duplicados)
            // Si no se truncó, usar firstOrCreate para evitar duplicados
            if ($wasTruncated) {
                $nature = JuridicalNature::create([
                    'name' => $natureData['name'],
                    'description' => $natureData['description'],
                ]);
                $created++;
                $this->command->info("✅ Creado [ID: {$nature->id}]: {$natureData['name']}");
            } else {
                $nature = JuridicalNature::firstOrCreate(
                    ['name' => $natureData['name']],
                    [
                        'name' => $natureData['name'],
                        'description' => $natureData['description'],
                    ]
                );
                
                if ($nature->wasRecentlyCreated) {
                    $created++;
                    $this->command->info("✅ Creado [ID: {$nature->id}]: {$natureData['name']}");
                } else {
                    $skipped++;
                    $this->command->line("⏭️  Ya existe [ID: {$nature->id}]: {$natureData['name']}");
                }
            }
        }

        $this->command->info("\n📊 Resumen:");
        $this->command->info("   - Naturalezas jurídicas creadas: {$created}");
        if ($skipped > 0) {
            $this->command->info("   - Naturalezas jurídicas existentes (omitidas): {$skipped}");
        }
        $this->command->info("   - Total procesados: " . count($juridicalNatures));
        
        if ($wasTruncated && $created > 0) {
            $this->command->info("   - IDs iniciales: desde 1 hasta {$created}");
        }
    }
}
