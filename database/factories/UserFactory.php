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
            // Escoger una empresa existente en lugar de crear una nueva
            'company_id' => $this->faker->numberBetween(1, 3), // ajusta a la cantidad real de empresas en tu DB
            'role_id' => $this->faker->numberBetween(1, 4),    // roles existentes (ej: admin, contador, facturador, cliente)

            // Datos personales básicos
            'nombre' => $this->faker->name(),
            'tipo_documento' => $this->faker->randomElement(['NIT', 'CC', 'CE']),
            'numero_documento' => $this->faker->unique()->numerify(
                $this->faker->randomElement([
                    '#########',     // CC
                    '##########',    // CC o CE
                    '########-#',    // NIT con dígito de verificación
                ])
            ),

            // Ubicación realista
            'direccion' => $this->faker->streetAddress(),
            'pais' => 'Colombia',
            'descripcion' => $this->faker->sentence(6),

            // Seguridad
            'contrasena' => Hash::make('123456'), // contraseña genérica encriptada
            'correo_electronico' => $this->faker->unique()->safeEmail(),
            'telefono' => '3' . $this->faker->numerify('########'),

            // Estado
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
            'ultimo_acceso' => $this->faker->dateTimeBetween('-1 year', 'now'),
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
