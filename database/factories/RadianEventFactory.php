<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RadianEvent>
 */
class RadianEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'codigo' => $this->faker->regexify('[A-Z0-9]{10}'), // Código aleatorio (10 caracteres alfanuméricos)
                'fecha_evento' => $this->faker->dateTimeBetween('-1 year', 'now'), // Fecha de evento en el último año
                'tipo_evento' => $this->faker->randomElement(['Recibido', 'Aceptado', 'Rechazado', 'Validado', 'Notificado']), // Tipo de evento simulado
                'xml_respuesta' => '<xml><respuesta>' . $this->faker->sentence . '</respuesta></xml>', // Respuesta en XML simulada
                'estado_dian' => $this->faker->randomElement(['Enviado', 'Procesado', 'Rechazado', 'Pendiente']), // Estado en la DIAN
        ];
    }
}
