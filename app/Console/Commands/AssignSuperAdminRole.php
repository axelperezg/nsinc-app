<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignSuperAdminRole extends Command
{
    protected $signature = 'user:make-super-admin {email}';
    protected $description = 'Asigna el rol de super admin a un usuario';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->error("Rol super_admin no encontrado. Ejecuta primero el seeder de roles.");
            return 1;
        }

        $user->role()->associate($superAdminRole);
        $user->save();

        $this->info("Usuario {$user->name} ahora es Super Admin.");
        return 0;
    }
}
