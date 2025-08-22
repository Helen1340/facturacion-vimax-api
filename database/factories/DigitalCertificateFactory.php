<?php

namespace Database\Factories;

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
        return [
            'nombre_certificado' => 'cert_' . $this->faker->bothify('######') . '.p12', // Nombre del archivo de certificado
            'ruta_certificado' => '/storage/certificados/' . $this->faker->uuid . '.p12', // Ruta simulada en el servidor
            'numero_serial' => strtoupper($this->faker->bothify('??##??##??##??##')), // Serial en formato alfanumérico
            'contrasena' => $this->faker->password(12, 20), // Contraseña aleatoria de 12 a 20 caracteres
            'fecha_inicio' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'), // Fecha de inicio aleatoria
            'fecha_fin' => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'), // Fecha de vencimiento
            'estado' => $this->faker->randomElement(['Vigente', 'Vencido', 'Revocado']), // Estado del certificado
            'entidad_emisora' => $this->faker->randomElement(['Certicámara', 'Andes SCD', 'GSE', 'Autoridad de Certificación Digital']), // Emisores de certificados
        ];
    }
}
