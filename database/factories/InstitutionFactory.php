<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Sector;
use App\Models\JuridicalNature;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
    protected $model = Institution::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        return [
            'name' => $name,
            'acronym' => strtoupper(substr($name, 0, 3)),
            'code' => strtoupper($this->faker->bothify('??###')),
            'sector_id' => Sector::factory(),
            'juridical_nature_id' => JuridicalNature::factory(),
            'isSector' => false,
            'control' => $this->faker->randomElement(['Federal', 'Estatal', 'Municipal']),
        ];
    }
}
