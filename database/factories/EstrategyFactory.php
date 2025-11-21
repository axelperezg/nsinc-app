<?php

namespace Database\Factories;

use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\JuridicalNature;
use App\Models\Responsable;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstrategyFactory extends Factory
{
    protected $model = Estrategy::class;

    public function definition(): array
    {
        $institution = Institution::factory()->create();
        $juridicalNature = JuridicalNature::factory()->create();
        $responsable = Responsable::factory()->create(['institution_id' => $institution->id]);

        return [
            'anio' => now()->year,
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'juridical_nature_id' => $juridicalNature->id,
            'juridical_nature_name' => $juridicalNature->name,
            'mision' => $this->faker->paragraph(),
            'vision' => $this->faker->paragraph(),
            'objetivo_institucional' => $this->faker->paragraph(),
            'objetivo_estrategia' => $this->faker->paragraph(),
            'fecha_elaboracion' => $this->faker->date(),
            'estado_estrategia' => 'Creada',
            'concepto' => 'Registro',
            'fecha_envio_dgnc' => null,
            'presupuesto' => $this->faker->randomFloat(2, 100000, 10000000),
            'responsable_id' => $responsable->id,
            'responsable_name' => $responsable->name,
            'NombreSectorResponsable' => $this->faker->name(),
            'ejes_plan_nacional' => ['eje_general_1_gobernanza'],
            'justificacion_estudios' => $this->faker->paragraph(),
        ];
    }

    public function creada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Creada',
        ]);
    }

    public function enviadaCS(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Enviada a CS',
        ]);
    }

    public function aceptadaCS(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Aceptada CS',
        ]);
    }

    public function rechazadaCS(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Rechazada CS',
        ]);
    }

    public function enviadaDGNC(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Enviada a DGNC',
            'fecha_envio_dgnc' => $this->faker->date(),
        ]);
    }

    public function autorizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Autorizada',
            'fecha_envio_dgnc' => $this->faker->date(),
        ]);
    }

    public function rechazadaDGNC(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Rechazada DGNC',
            'fecha_envio_dgnc' => $this->faker->date(),
        ]);
    }

    public function observadaDGNC(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_estrategia' => 'Observada DGNC',
            'fecha_envio_dgnc' => $this->faker->date(),
        ]);
    }

    public function modificacion(): static
    {
        return $this->state(fn (array $attributes) => [
            'concepto' => 'Modificacion',
        ]);
    }

    public function solventacion(): static
    {
        return $this->state(fn (array $attributes) => [
            'concepto' => 'Solventacion',
        ]);
    }

    public function cancelacion(): static
    {
        return $this->state(fn (array $attributes) => [
            'concepto' => 'Cancelacion',
        ]);
    }
}
