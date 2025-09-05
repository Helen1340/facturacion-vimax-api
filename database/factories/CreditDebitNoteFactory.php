<?php

namespace Database\Factories;
use App\Models\ElectronicInvoice;

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

    // Elegir una factura existente aleatoria
    $factura = ElectronicInvoice::inRandomOrder()->first() ??ElectronicInvoice::factory()->create();
    $fecha_emision = $this->faker->dateTimeBetween($factura->fecha_emision, 'now');
    $tipo_documento = $this->faker->randomElement(['credito', 'debito']);
    $numero_nota = $tipo_documento === 'credito' 
        ? 'NC-' . $this->faker->unique()->numerify('########') 
        : 'ND-' . $this->faker->unique()->numerify('########');
    $valor_total = $tipo_documento === 'credito' 
        ? $this->faker->randomFloat(2, 1000, $factura->total_factura) 
        : $this->faker->randomFloat(2, 1000, 5000000);

    return [
        'electronic_invoice_id' => $factura->id,
        'motivo' => $this->faker->sentence(6),
        'tipo_documento' => $tipo_documento,
        'descripcion' => $this->faker->sentence(10),
        'numero_nota' => $numero_nota,
        'estado' => $this->faker->randomElement(['aceptada', 'rechazada', 'pendiente']),
        'fecha_emision' => $fecha_emision->format('Y-m-d'),
        'valor_total' => $valor_total,
    ];
}
    }