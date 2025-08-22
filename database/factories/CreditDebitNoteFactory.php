<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CreditDebitNote>
 */
class CreditDebitNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'motivo' => $this->faker->sentence(6), // Motivo breve de la nota
            'tipo_documento' => $this->faker->randomElement(['debito', 'credito']), // Tipo de nota
            'descripcion' => $this->faker->sentence(10), // Descripción más detallada
            'numero_nota' => 'NC-' . $this->faker->numerify('########'), // Número de nota con prefijo (ej: NC-12345678)
            'estado' => $this->faker->randomElement(['aceptada', 'rechazada', 'pendiente']), // Estado de la nota
            'fecha_emision' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'), // Fecha de emisión dentro del último año
            'valor_total' => $this->faker->randomFloat(2, 1000, 5000000), // Valor entre $1.000 y $5.000.000
        ];
    }
}
