<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Sector;
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

        // Crear usuario de institución (SEP)
        $institutionUserRole = Role::where('name', 'institution_user')->first();
        $institutionSEP = \App\Models\Institution::where('acronym', 'SEP')->first();

        if ($institutionUserRole && $institutionSEP) {
            User::firstOrCreate(
                ['email' => 'sep@admin.com'],
                [
                    'name' => 'Usuario SEP',
                    'password' => Hash::make('password'),
                    'role_id' => $institutionUserRole->id,
                    'institution_id' => $institutionSEP->id,
                ]
            );

            $this->command->info('Usuario SEP creado: sep@admin.com / password');
        }

        // Crear usuario básico (UNAM)
        $institutionUserRole = Role::where('name', 'institution_user')->first();
        $institutionUNAM = \App\Models\Institution::where('acronym', 'UNAM')->first();
        
        if ($institutionUserRole && $institutionUNAM) {
            User::firstOrCreate(
                ['email' => 'unam@user.com'],
                [
                    'name' => 'Usuario UNAM',
                    'password' => Hash::make('password'),
                    'role_id' => $institutionUserRole->id,
                    'institution_id' => $institutionUNAM->id,
                ]
            );
            
            $this->command->info('Usuario UNAM creado: unam@user.com / password');
        }

        // Crear usuario básico (IMSS)
        if ($institutionUserRole) {
            $institutionIMSS = \App\Models\Institution::where('acronym', 'IMSS')->first();
            
            if ($institutionIMSS) {
                User::firstOrCreate(
                    ['email' => 'imss@user.com'],
                    [
                        'name' => 'Usuario IMSS',
                        'password' => Hash::make('password'),
                        'role_id' => $institutionUserRole->id,
                        'institution_id' => $institutionIMSS->id,
                    ]
                );
                
                $this->command->info('Usuario IMSS creado: imss@user.com / password');
            }
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
