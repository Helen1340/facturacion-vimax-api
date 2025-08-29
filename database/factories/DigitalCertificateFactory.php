<?php

namespace Database\Factories;
use App\Models\Company;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DigitalCertificate>
 */
class DigitalCertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return  [
            'company_id' => Company::factory(), // Esto vincula a una empresa creada por el CompanyFactory
            'nombre_certificado' => 'cert_' . $this->faker->bothify('######') . '.p12',
            'ruta_certificado' => '/storage/certificados/' . $this->faker->uuid . '.p12',
            'numero_serial' => strtoupper($this->faker->bothify('??##??##??##??##')),
            'contrasena' => $this->faker->password(12, 20),
            'fecha_inicio' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'fecha_fin' => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'estado' => $this->faker->randomElement(['Vigente', 'Vencido', 'Revocado']),
            'entidad_emisora' => $this->faker->randomElement(['Certicámara', 'Andes SCD', 'GSE', 'Autoridad de Certificación Digital']),
        ];
    
    }
}
