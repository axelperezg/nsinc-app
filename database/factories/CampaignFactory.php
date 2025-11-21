<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\CampaignType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'temaEspecifco' => $this->faker->sentence(),
            'objetivoComuicacion' => $this->faker->paragraph(),
            'campaign_type_id' => CampaignType::factory(),
            'institution_id' => Institution::factory(),
            'estrategy_id' => Estrategy::factory(),
            'coemisores' => $this->faker->company(),
            'sexo' => ['Mujeres', 'Hombres'],
            'edad' => ['18-24', '25-34'],
            'poblacion' => ['Urbana'],
            'nse' => ['C+', 'C-'],
            'caracEspecific' => $this->faker->sentence(),
            'tv_oficial' => $this->faker->boolean(),
            'radio_oficial' => $this->faker->boolean(),
            'tv_comercial' => $this->faker->boolean(),
            'radio_comercial' => $this->faker->boolean(),
            'televisoras' => $this->faker->randomFloat(6, 0, 100000),
            'radiodifusoras' => $this->faker->randomFloat(6, 0, 100000),
            'cine' => $this->faker->randomFloat(6, 0, 50000),
            'decdmx' => $this->faker->randomFloat(6, 0, 30000),
            'deedos' => $this->faker->randomFloat(6, 0, 30000),
            'deextr' => $this->faker->randomFloat(6, 0, 20000),
            'revistas' => $this->faker->randomFloat(6, 0, 25000),
            'mediosComplementarios' => $this->faker->randomFloat(6, 0, 40000),
            'mediosDigitales' => $this->faker->randomFloat(6, 0, 60000),
            'mediosDigitalesInternet' => $this->faker->randomFloat(6, 0, 50000),
            'preEstudios' => $this->faker->randomFloat(6, 0, 15000),
            'postEstudios' => $this->faker->randomFloat(6, 0, 15000),
            'disenio' => $this->faker->randomFloat(6, 0, 20000),
            'produccion' => $this->faker->randomFloat(6, 0, 35000),
            'preProduccion' => $this->faker->randomFloat(6, 0, 25000),
            'postProduccion' => $this->faker->randomFloat(6, 0, 25000),
            'copiado' => $this->faker->randomFloat(6, 0, 10000),
        ];
    }
}
