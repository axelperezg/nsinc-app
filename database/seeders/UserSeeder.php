<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Verificar si el super admin ya existe
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $existingSuperAdmin = User::where('email', 'admin@admin.com')->first();
        
        if ($superAdminRole && !$existingSuperAdmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'institution_id' => null, // Super admin no tiene institución específica
            ]);
            
            $this->command->info('Usuario Super Admin creado: admin@admin.com / password');
        } else {
            $this->command->info('Usuario Super Admin ya existe: admin@admin.com / password');
        }

        // Crear usuario de institución (SEP)
        $institutionAdminRole = Role::where('name', 'institution_admin')->first();
        $institutionSEP = \App\Models\Institution::where('acronym', 'SEP')->first();
        
        if ($institutionAdminRole && $institutionSEP) {
            User::firstOrCreate(
                ['email' => 'sep@admin.com'],
                [
                    'name' => 'Admin SEP',
                    'password' => Hash::make('password'),
                    'role_id' => $institutionAdminRole->id,
                    'institution_id' => $institutionSEP->id,
                ]
            );
            
            $this->command->info('Usuario Admin SEP creado: sep@admin.com / password');
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

                // Crear coordinador de sector (para el sector Educación)
        $sectorCoordinatorRole = Role::where('name', 'sector_coordinator')->first();
        $sectorEducacion = \App\Models\Sector::where('name', 'Educación')->first();
        
        if ($sectorCoordinatorRole && $sectorEducacion) {
            User::firstOrCreate(
                ['email' => 'coord.educacion@test.com'],
                [
                    'name' => 'Coordinadora Sector Educación',
                    'password' => Hash::make('password'),
                    'sector_id' => $sectorEducacion->id,
                    'role_id' => $sectorCoordinatorRole->id,
                ]
            );
            
            $this->command->info('Coordinadora Sector Educación creada: coord.educacion@test.com / password');
        }

        // Crear coordinador de sector (para el sector Salud)
        $sectorSalud = \App\Models\Sector::where('name', 'Salud')->first();
        
        if ($sectorCoordinatorRole && $sectorSalud) {
            User::firstOrCreate(
                ['email' => 'coord.salud@test.com'],
                [
                    'name' => 'Coordinadora Sector Salud',
                    'password' => Hash::make('password'),
                    'sector_id' => $sectorSalud->id,
                    'role_id' => $sectorCoordinatorRole->id,
                ]
            );
            
            $this->command->info('Coordinadora Sector Salud creada: coord.salud@test.com / password');
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
