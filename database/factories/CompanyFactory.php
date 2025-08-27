<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'razon_social' => $this->faker->company,
            'tipo_documento' => $this->faker->randomElement(['NIT', 'CC', 'CE']),
            'direccion' => $this->faker->streetAddress,
            'municipio' => $this->faker->city,
            'departamento' => $this->faker->state,
            'pais' => 'Colombia', // O $this->faker->country si lo prefieres
            'telefono' => $this->faker->phoneNumber,
            'correo_electronico' => $this->faker->unique()->safeEmail,
            'regimen' => $this->faker->word,
            'logo_url' => $this->faker->imageUrl(),
            'nombre_comercial' => $this->faker->companySuffix,
            'codigo_ciiu' => $this->faker->regexify('[0-9]{4}'),
            'numero_documento' => $this->faker->unique()->randomNumber(9),
    
        ];
    }
}
