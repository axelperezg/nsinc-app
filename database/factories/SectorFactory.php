<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name.' Sector',
            // El prefijo de 'name' no garantiza unicidad (varios nombres pueden
            // compartir las mismas 3 primeras letras), por lo que se agrega un
            // sufijo único para evitar colisiones con la restricción unique() de la columna.
            'acronym' => strtoupper(substr($name, 0, 3)).$this->faker->unique()->numerify('####'),
        ];
    }
}
