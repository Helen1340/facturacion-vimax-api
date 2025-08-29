<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'measurement_unit_id' => $this->faker->numberBetween(1, 10), // Ajusta según los ids válidos
            'codigo_estandar' => $this->faker->optional()->bothify('EST-####'),
            'codigo_producto' => $this->faker->unique()->bothify('PRD-####'),
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(6),
            'precio_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }
}
