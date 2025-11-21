<?php

namespace Database\Factories;

use App\Models\Responsable;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponsableFactory extends Factory
{
    protected $model = Responsable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'institution_id' => Institution::factory(),
            'cargo' => $this->faker->jobTitle(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
        ];
    }
}
