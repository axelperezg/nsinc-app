<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Responsable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponsableFactory extends Factory
{
    protected $model = Responsable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'institution_id' => Institution::factory(),
            'charge' => $this->faker->jobTitle(),
        ];
    }
}
