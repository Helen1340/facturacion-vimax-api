<?php

namespace Database\Factories;

use App\Models\MeasurementUnit;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

       protected $model = Service::class;     
   

    public function definition(): array
    {
     
   
        return [
            'measurement_unit_id' => MeasurementUnit::inRandomOrder()->first()->id,
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(6),
            // Generamos un código seguro sin usar unique()
            'codigo_servicio' => 'SVC-' . $this->faker->numberBetween(10000, 99999),
            'precio_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }
}