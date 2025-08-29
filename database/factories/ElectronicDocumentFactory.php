<?php

namespace Database\Factories;
use App\Models\ElectronicDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ElectronicDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'electronic_invoice_id' => $this->faker->numberBetween(1, 5),
            'dian_numbering_id' => $this->faker->numberBetween(1, 3),
            'credit_debit_note_id' => $this->faker->optional()->numberBetween(1, 2),
            
            'cufe' => $this->faker->unique()->regexify('CUFE[0-9]{10}'),
            'cude' => $this->faker->unique()->regexify('DOC-[0-9]{3}'),
            'xml_documento' => '<xml>' . $this->faker->sentence(3) . '</xml>',
            'estado_dian' => $this->faker->randomElement(['Enviado', 'Aceptado', 'Rechazado', 'Pendiente']),
            'fecha_validacion' => $this->faker->date('Y-m-d'),
            'firma_digital' => 'FirmaDigital' . $this->faker->numerify('###'),
            'hash_documento' => 'HASH' . $this->faker->numerify('###'),
            'descripcion' => $this->faker->sentence(5),
            'ambiente' => $this->faker->randomElement(['Pruebas', 'Producción']),
            'tipo_documento' => $this->faker->randomElement(['Factura', 'Nota Crédito', 'Nota Débito']),
            'qr_codigo' => 'QRCODE' . $this->faker->numerify('###'),
            'cdr' => 'CDR' . $this->faker->numerify('###'),
            'modo_emision' => $this->faker->randomElement(['normal', 'en contingencia']),
        ];
    }
}
