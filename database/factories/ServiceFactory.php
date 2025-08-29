<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        return [
            'measurementunit_id' => $this->faker->numberBetween(1, 10), // Ajusta según los ids válidos
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(6),
            'codigo_servicio' => $this->faker->unique()->bothify('SVC-####'),
            'precio_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }
}
