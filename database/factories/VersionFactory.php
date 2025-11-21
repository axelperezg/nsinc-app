<?php

namespace Database\Factories;

use App\Models\Version;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class VersionFactory extends Factory
{
    protected $model = Version::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'fechaInicio' => $this->faker->date(),
            'fechaFinal' => $this->faker->date(),
            'campaign_id' => Campaign::factory(),
        ];
    }
}
