<?php

namespace Database\Factories;

use App\Models\JuridicalNature;
use Illuminate\Database\Eloquent\Factories\Factory;

class JuridicalNatureFactory extends Factory
{
    protected $model = JuridicalNature::class;

    public function definition(): array
    {
        $names = [
            'Administración Pública Centralizada' => 'Dependencias del Poder Ejecutivo Federal',
            'Administración Pública Paraestatal' => 'Entidades paraestatales y descentralizadas',
            'Órgano Autónomo' => 'Organismos autónomos constitucionales',
            'Poder Legislativo' => 'Cámara de Diputados y Senadores',
            'Poder Judicial' => 'Poder Judicial de la Federación',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($names));

        return [
            'name' => $name,
            'description' => $names[$name],
        ];
    }
}
