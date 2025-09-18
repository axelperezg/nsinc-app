<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Estrategy;

class EstrategyPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Todos pueden ver la lista (filtrada según su rol)
    }

    //public function view(User $user, Estrategy $estrategy): bool
    //{
    //    // Super admin puede ver todo
    //    if ($user->role && $user->role->name === 'super_admin') {
    //        return true;
    //    }
    //    
    //    // Usuario normal solo ve su institución
    //    return $user->institution_id === $estrategy->institution_id;
    //}

    public function create(User $user): bool
    {
        return true; // Todos pueden crear
    }

    public function update(User $user, Estrategy $estrategy): bool
    {
        // Super admin puede editar todo
        if ($user->role && $user->role->name === 'super_admin') {
            return true;
        }
        
        // Usuario normal solo edita su institución
        return $user->institution_id === $estrategy->institution_id;
    }

    public function delete(User $user, Estrategy $estrategy): bool
    {
        // Solo super administradores pueden eliminar
        return $user->role && $user->role->name === 'super_admin';
    }
}
