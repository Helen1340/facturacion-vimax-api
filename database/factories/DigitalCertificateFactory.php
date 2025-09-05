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
        // Fechas de inicio y fin del certificado
    $fecha_inicio = $this->faker->dateTimeBetween('-2 years', 'now');
    $fecha_fin = (clone $fecha_inicio)->modify('+2 years');

    return [
        'company_id' => \App\Models\Company::inRandomOrder()->first()?->id, // Empresa existente
        'nombre_certificado' => 'certificado_' . $this->faker->bothify('####') . '.p12',
        'ruta_certificado' => '/storage/certificados/' . $this->faker->uuid . '.p12',
        'numero_serial' => strtoupper($this->faker->bothify('??##??##??##??##')), // Serial simulado
        'contrasena' => $this->faker->regexify('[A-Za-z0-9@#]{12,20}'), // Contraseña segura
        'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
        'fecha_fin' => $fecha_fin->format('Y-m-d'),
        'estado' => $this->faker->randomElement(['Vigente', 'Vencido', 'Revocado']),
        'entidad_emisora' => $this->faker->randomElement([
            'Certicámara S.A.',
            'Andes SCD',
            'GSE',
            'Camerfirma Colombia',
            'Autoridad de Certificación Digital'
        ]),
    ];
}
}