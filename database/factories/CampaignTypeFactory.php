<?php

namespace Database\Factories;

use App\Models\CampaignType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignTypeFactory extends Factory
{
    protected $model = CampaignType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Institucional',
                'Social',
                'Comercial',
                'Política',
                'Educativa',
            ]),
        ];
    }
}
