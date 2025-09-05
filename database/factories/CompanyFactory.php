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
        // Generar NIT realista de Colombia
    $nit = $this->faker->unique()->numerify('#########') . '-' . $this->faker->numberBetween(0, 9);

    // Código CIIU entre 4 dígitos (sectores económicos)
    $codigo_ciiu = $this->faker->numberBetween(1000, 9999);

    return [
        'razon_social' => $this->faker->company,
        'tipo_documento' => 'NIT', // La mayoría de las empresas usan NIT
        'numero_documento' => $nit,
        'direccion' => $this->faker->streetAddress,
        'municipio' => $this->faker->city,
        'departamento' => $this->faker->state,
        'pais' => 'Colombia',
        'telefono' => $this->faker->numerify('3#########'), // Formato celular colombiano
        'correo_electronico' => $this->faker->unique()->companyEmail,
        'regimen' => $this->faker->randomElement(['Simplificado', 'Común']),
        'logo_url' => $this->faker->imageUrl(200, 200, 'business', true),
        'nombre_comercial' => $this->faker->company,
        'codigo_ciiu' => $codigo_ciiu,
    ];
}
}