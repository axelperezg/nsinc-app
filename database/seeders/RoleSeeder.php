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
            'view_all_sectors' => 'Ver todos los sectores',
            'manage_all_sectors' => 'Gestionar todos los sectores',
            'view_sector_strategies' => 'Ver estrategias del sector',
            'approve_sector_strategies' => 'Aprobar estrategias del sector',
            'view_own_sector' => 'Ver sector propio',
            'view_all_strategies' => 'Ver todas las estrategias',
            'authorize_strategies' => 'Autorizar estrategias',
        ];

        foreach ($permissions as $name => $display_name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $display_name]
            );
        }

        // Asignar permisos a roles (usando sync para evitar duplicados)
        $superAdminRole->permissions()->sync(Permission::all()->pluck('id'));
        $institutionUserRole->permissions()->sync(
            Permission::where('name', 'view_own_institution')->pluck('id')
        );

        $sectorCoordinatorRole->permissions()->sync(
            Permission::whereIn('name', ['view_sector_strategies', 'approve_sector_strategies', 'view_own_sector'])->pluck('id')
        );

        $dgncUserRole->permissions()->sync(
            Permission::whereIn('name', ['view_all_strategies', 'authorize_strategies', 'view_all_institutions'])->pluck('id')
        );
    }
}
