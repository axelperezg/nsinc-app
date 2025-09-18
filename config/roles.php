<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roles del Sistema
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir los roles disponibles en el sistema y sus permisos
    | asociados.
    |
    */

    'roles' => [
        'super_admin' => [
            'name' => 'super_admin',
            'display_name' => 'Super Administrador',
            'description' => 'Acceso completo a todas las instituciones y funcionalidades',
            'permissions' => [
                'view_all_institutions',
                'manage_all_institutions',
                'view_own_institution',
                'manage_own_institution',
                'view_all_sectors',
                'manage_all_sectors',
            ],
        ],
        'institution_admin' => [
            'name' => 'institution_admin',
            'display_name' => 'Administrador de Institución',
            'description' => 'Administra solo su institución',
            'permissions' => [
                'view_own_institution',
                'manage_own_institution',
            ],
        ],
        'institution_user' => [
            'name' => 'institution_user',
            'display_name' => 'Usuario de Institución',
            'description' => 'Usuario básico de institución',
            'permissions' => [
                'view_own_institution',
            ],
        ],
        'sector_coordinator' => [
            'name' => 'sector_coordinator',
            'display_name' => 'Coordinadora de Sector',
            'description' => 'Coordina estrategias de un sector específico',
            'permissions' => [
                'view_sector_strategies',
                'approve_sector_strategies',
                'view_own_sector',
            ],
        ],
        'dgnc_user' => [
            'name' => 'dgnc_user',
            'display_name' => 'Usuario DGNC',
            'description' => 'Usuario de la Dirección General de Normatividad de Comunicación',
            'permissions' => [
                'view_all_strategies',
                'authorize_strategies',
                'view_all_institutions',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permisos del Sistema
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir todos los permisos disponibles en el sistema.
    |
    */

    'permissions' => [
        'view_all_institutions' => [
            'name' => 'view_all_institutions',
            'display_name' => 'Ver todas las instituciones',
            'description' => 'Permite ver información de todas las instituciones',
        ],
        'manage_all_institutions' => [
            'name' => 'manage_all_institutions',
            'display_name' => 'Gestionar todas las instituciones',
            'description' => 'Permite gestionar todas las instituciones',
        ],
        'view_own_institution' => [
            'name' => 'view_own_institution',
            'display_name' => 'Ver institución propia',
            'description' => 'Permite ver información de la institución propia',
        ],
        'manage_own_institution' => [
            'name' => 'manage_own_institution',
            'display_name' => 'Gestionar institución propia',
            'description' => 'Permite gestionar la institución propia',
        ],
        'view_all_sectors' => [
            'name' => 'view_all_sectors',
            'display_name' => 'Ver todos los sectores',
            'description' => 'Permite ver información de todos los sectores',
        ],
        'manage_all_sectors' => [
            'name' => 'manage_all_sectors',
            'display_name' => 'Gestionar todos los sectores',
            'description' => 'Permite gestionar todos los sectores',
        ],
        'view_sector_strategies' => [
            'name' => 'view_sector_strategies',
            'display_name' => 'Ver estrategias del sector',
            'description' => 'Permite ver estrategias del sector asignado',
        ],
        'approve_sector_strategies' => [
            'name' => 'approve_sector_strategies',
            'display_name' => 'Aprobar estrategias del sector',
            'description' => 'Permite aprobar estrategias del sector asignado',
        ],
        'view_own_sector' => [
            'name' => 'view_own_sector',
            'display_name' => 'Ver sector propio',
            'description' => 'Permite ver información del sector propio',
        ],
        'view_all_strategies' => [
            'name' => 'view_all_strategies',
            'display_name' => 'Ver todas las estrategias',
            'description' => 'Permite ver todas las estrategias del sistema',
        ],
        'authorize_strategies' => [
            'name' => 'authorize_strategies',
            'display_name' => 'Autorizar estrategias',
            'description' => 'Permite autorizar estrategias aprobadas por CS',
        ],
    ],
];
