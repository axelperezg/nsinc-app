<?php

namespace App\Helpers;

use App\Models\User;

class PermissionHelper
{
    /**
     * Verifica si un usuario puede ver todas las instituciones
     */
    public static function canViewAllInstitutions(User $user): bool
    {
        return $user->role && $user->role->name === 'super_admin';
    }

    /**
     * Verifica si un usuario puede gestionar todas las instituciones
     */
    public static function canManageAllInstitutions(User $user): bool
    {
        return $user->role && $user->role->name === 'super_admin';
    }

    /**
     * Verifica si un usuario puede ver su institución
     */
    public static function canViewOwnInstitution(User $user): bool
    {
        return $user->role && $user->institution_id;
    }

    /**
     * Verifica si un usuario puede gestionar su institución
     */
    public static function canManageOwnInstitution(User $user): bool
    {
        return $user->role && in_array($user->role->name, ['super_admin', 'institution_admin']);
    }

    /**
     * Obtiene el ID de la institución del usuario (null si puede ver todas)
     */
    public static function getUserInstitutionId(User $user): ?int
    {
        return self::canViewAllInstitutions($user) ? null : $user->institution_id;
    }

    /**
     * Verifica si un usuario tiene un permiso específico
     */
    public static function hasPermission(User $user, string $permissionName): bool
    {
        return $user->role && $user->role->hasPermission($permissionName);
    }
}
