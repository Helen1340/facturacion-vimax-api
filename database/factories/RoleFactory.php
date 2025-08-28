<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['administrador', 'facturador', 'contador', 'cliente']),
            'descripcion' => $this->faker->sentence(8),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
