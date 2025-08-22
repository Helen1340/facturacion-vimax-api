<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MeasurementUnit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasurementUnit>
 */
class MeasurementUnitFactory extends Factory
{
    protected $model = MeasurementUnit::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word, // Nombre de la unidad
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
            'codigo_dian' => strtoupper($this->faker->unique()->bothify('??###')), // Código tipo DIAN
            'descripcion' => $this->faker->sentence(6), // Descripción corta
            'tipo_aplicacion' => $this->faker->randomElement(['Producto', 'Servicio']),
        ];
    }
}

