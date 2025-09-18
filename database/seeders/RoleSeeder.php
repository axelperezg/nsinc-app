<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], [
            'display_name' => 'Super Administrador',
            'description' => 'Acceso completo a todas las instituciones y funcionalidades',
        ]);

        $institutionAdminRole = Role::firstOrCreate(['name' => 'institution_admin'], [
            'display_name' => 'Administrador de Institución',
            'description' => 'Administra solo su institución',
        ]);

        $institutionUserRole = Role::firstOrCreate(['name' => 'institution_user'], [
            'display_name' => 'Usuario de Institución',
            'description' => 'Usuario básico de institución',
        ]);

        $sectorCoordinatorRole = Role::firstOrCreate(['name' => 'sector_coordinator'], [
            'display_name' => 'Coordinadora de Sector',
            'description' => 'Coordina estrategias de un sector específico',
        ]);

        $dgncUserRole = Role::firstOrCreate(['name' => 'dgnc_user'], [
            'display_name' => 'Usuario DGNC',
            'description' => 'Usuario de la Dirección General de Normatividad de Comunicación',
        ]);

        // Crear permisos
        $permissions = [
            'view_all_institutions' => 'Ver todas las instituciones',
            'manage_all_institutions' => 'Gestionar todas las instituciones',
            'view_own_institution' => 'Ver institución propia',
            'manage_own_institution' => 'Gestionar institución propia',
        ];

        foreach ($permissions as $name => $display_name) {
            Permission::create([
                'name' => $name,
                'display_name' => $display_name
            ]);
        }

        // Asignar permisos a roles
        $superAdminRole->permissions()->attach(Permission::all());
        $institutionAdminRole->permissions()->attach(
            Permission::whereIn('name', ['view_own_institution', 'manage_own_institution'])->get()
        );
        $institutionUserRole->permissions()->attach(
            Permission::where('name', 'view_own_institution')->get()
        );

        $sectorCoordinatorRole->permissions()->attach(
            Permission::whereIn('name', ['view_sector_strategies', 'approve_sector_strategies', 'view_own_sector'])->get()
        );

        $dgncUserRole->permissions()->attach(
            Permission::whereIn('name', ['view_all_strategies', 'authorize_strategies', 'view_all_institutions'])->get()
        );
    }
}
