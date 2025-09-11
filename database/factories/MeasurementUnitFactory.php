<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MeasurementUnit;

class MeasurementUnitFactory extends Factory
{
    protected $model = MeasurementUnit::class;

    public function definition(): array
    {
        return [
            'nombre' => ucfirst($this->faker->unique()->word),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
            'codigo_dian' => 'X' . strtoupper($this->faker->unique()->bothify('??###')), // evita choque con oficiales
            'descripcion' => $this->faker->sentence(6),
            'tipo_aplicacion' => $this->faker->randomElement(['Producto', 'Servicio']),
        ];
    }
}
