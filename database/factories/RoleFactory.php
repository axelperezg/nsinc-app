<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => 'institution_user',
            'display_name' => 'Institution User',
            'description' => 'Institution User',
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
            'description' => 'Super Administrator',
        ]);
    }

    public function sectorCoordinator(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'sector_coordinator',
            'display_name' => 'Sector Coordinator',
            'description' => 'Sector Coordinator',
        ]);
    }

    public function dgncUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'dgnc_user',
            'display_name' => 'DGNC User',
            'description' => 'DGNC User',
        ]);
    }
}
