<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fake()->numberBetween(1, 3),
            'nombre' => fake()->name(),
            'tipo_documento' => fake()->randomElement(['NIT', 'CC', 'CE']),
            'numero_documento' => fake()->numerify('##########'),
            'direccion' => fake()->address(),
            'pais' => fake()->country(),
            'descripcion' => fake()->sentence(6),
            'contrasena' => Hash::make('password'),
            'correo_electronico' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('3#########'),
            'estado' => fake()->randomElement(['Activo', 'Inactivo']),
            'ultimo_acceso' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
