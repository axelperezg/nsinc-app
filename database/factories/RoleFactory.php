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
            'description' => 'Institution User',
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'super_admin',
            'description' => 'Super Administrator',
        ]);
    }

    public function sectorCoordinator(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'sector_coordinator',
            'description' => 'Sector Coordinator',
        ]);
    }

    public function dgncUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'dgnc_user',
            'description' => 'DGNC User',
        ]);
    }
}
