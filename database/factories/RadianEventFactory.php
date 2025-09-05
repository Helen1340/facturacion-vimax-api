<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ElectronicDocument;

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
        // Vincula a un documento electrónico ya existente
        'electronic_document_id' =>ElectronicDocument::inRandomOrder()->first()?->id ?? ElectronicDocument::factory(),

        // Código de evento único y aleatorio (10 caracteres alfanuméricos)
        'codigo' => strtoupper($this->faker->unique()->bothify('EVT-####??')),

        // Fecha del evento dentro del último año
        'fecha_evento' => $this->faker->dateTimeBetween('-1 year', 'now'),

        // Tipo de evento simulado según la DIAN
        'tipo_evento' => $this->faker->randomElement([
            'Recibido',    // Documento recibido por la DIAN
            'Aceptado',    // Documento aceptado
            'Rechazado',   // Documento rechazado
            'Validado',    // Validado para efectos fiscales
            'Notificado'   // Notificación generada al emisor
        ]),

        // Respuesta en XML simulada, con contenido más estructurado
        'xml_respuesta' => '<evento><codigo>' . strtoupper($this->faker->bothify('EVT-###??')) . '</codigo><descripcion>' . $this->faker->sentence(8) . '</descripcion></evento>',

        // Estado en la DIAN
        'estado_dian' => $this->faker->randomElement(['Enviado', 'Procesado', 'Rechazado', 'Pendiente']),
    ];
}
}