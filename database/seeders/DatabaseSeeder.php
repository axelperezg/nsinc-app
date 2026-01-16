<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles y permisos (base del sistema)
            RoleSeeder::class,
            
            // 2. Sectores (necesarios para instituciones y usuarios)
            SectorSeeder::class,

            // 3. Naturalezas jurídicas (necesarias para instituciones)
            JuridicalNatureSeeder::class,

            // 4. Ramo Seeder
            RamoSeeder::class,

            // 5. Plan Nacional de Desarrollo (configuración de ejes para estrategias)
            PlanNacionalDesarrolloSeeder::class,

            // 6. Configuraciones del sistema
            ConfigurationSeeder::class,
            
            // 4. Fechas de vencimiento
            ExpirationDateSeeder::class,
            
            // 5. Instituciones (necesita sectores)
            InstitutionSeeder::class,
            
            // 6. Usuarios (necesita roles, sectores e instituciones)
            UserSeeder::class,
            
            // 7. Estrategias (necesita usuarios e instituciones)
            // EstrategySeeder::class, // Descomentar si se necesita
        ]);

        $this->command->info('✅ Todos los seeders se ejecutaron correctamente.');
    }
}
