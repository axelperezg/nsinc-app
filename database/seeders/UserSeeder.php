<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Sector;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear super admin principal
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            // Super Admin - Manuel Axel Pérez García
            User::firstOrCreate(
                ['email' => 'maperezg@segob.gob.mx'],
                [
                    'name' => 'Manuel Axel Pérez García',
                    'password' => Hash::make('password'),
                    'role_id' => $superAdminRole->id,
                    'institution_id' => null, // Super admin no tiene institución específica
                ]
            );

            $this->command->info('Usuario Super Admin creado: maperezg@segob.gob.mx / password');

            // Super Admin adicional para pruebas
            User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                    'role_id' => $superAdminRole->id,
                    'institution_id' => null,
                ]
            );

            $this->command->info('Usuario Super Admin de prueba creado: admin@admin.com / password');
        }

        // Crear un usuario de institución por cada institución registrada
        $institutionUserRole = Role::where('name', 'institution_user')->first();

        if ($institutionUserRole) {
            Institution::all()->each(function (Institution $institution) use ($institutionUserRole) {
                $slug  = Str::slug($institution->acronym);
                $email = "{$slug}@{$slug}.gob.mx";

                User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'           => 'Usuario ' . $institution->acronym,
                        'password'       => Hash::make('password'),
                        'role_id'        => $institutionUserRole->id,
                        'institution_id' => $institution->id,
                    ]
                );

                $this->command->info("Usuario {$institution->acronym} creado: {$email} / password");
            });
        }

        // Crear un coordinador de sector por cada sector registrado
        $sectorCoordinatorRole = Role::where('name', 'sector_coordinator')->first();

        if ($sectorCoordinatorRole) {
            Sector::all()->each(function (Sector $sector) use ($sectorCoordinatorRole) {
                $slug  = Str::slug($sector->acronym);
                $email = "{$slug}@{$slug}.gob.mx";

                User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'      => 'Usuario ' . $sector->acronym,
                        'password'  => Hash::make('password'),
                        'sector_id' => $sector->id,
                        'role_id'   => $sectorCoordinatorRole->id,
                    ]
                );

                $this->command->info("Coordinador {$sector->acronym} creado: {$email} / password");
            });
        }

        // Crear usuario DGNC
        $dgncUserRole = Role::where('name', 'dgnc_user')->first();
        
        if ($dgncUserRole) {
            User::firstOrCreate(
                ['email' => 'usuario.dgnc@test.com'],
                [
                    'name' => 'Usuario DGNC',
                    'password' => Hash::make('password'),
                    'role_id' => $dgncUserRole->id,
                ]
            );
            
            $this->command->info('Usuario DGNC creado: usuario.dgnc@test.com / password');
        }

        $this->command->info('Usuarios de prueba creados exitosamente.');
    }
}
