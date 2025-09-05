<?php

namespace Database\Factories;

use App\Models\CreditDebitNote;
use App\Models\DianNumbering;
use App\Models\ElectronicDocument;
use App\Models\ElectronicInvoice;
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
        'electronic_invoice_id' =>ElectronicInvoice::inRandomOrder()->first()?->id,
        'dian_numbering_id'     =>DianNumbering::inRandomOrder()->first()?->id,
        'credit_debit_note_id'  => $this->faker->optional()->randomElement(
                                      CreditDebitNote::pluck('id')->toArray()
                                   ),

        // CUFE y CUDE: hash alfanumérico largo, como exige la DIAN
        'cufe' => strtoupper($this->faker->unique()->regexify('[A-Z0-9]{96}')),
        'cude' => strtoupper($this->faker->unique()->regexify('[A-Z0-9]{96}')),

        // XML básico con etiquetas válidas
        'xml_documento' => '<Factura><Emisor>Empresa XYZ</Emisor><Receptor>Cliente ABC</Receptor><Total>' 
                           . $this->faker->randomFloat(2, 50000, 2000000) 
                           . '</Total></Factura>',

        'estado_dian' => $this->faker->randomElement(['Enviado', 'Aceptado', 'Rechazado', 'Pendiente']),
        'fecha_validacion' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),

        // Firma y hash simulados
        'firma_digital' => 'FIRMA-' . strtoupper($this->faker->bothify('??##??##??')),
        'hash_documento' => strtoupper(hash('sha256', $this->faker->uuid)),

        'descripcion' => $this->faker->randomElement([
            'Factura electrónica emitida y validada con la DIAN',
            'Nota crédito asociada a devolución',
            'Nota débito por ajuste en valor',
        ]),

        'ambiente' => $this->faker->randomElement(['Pruebas', 'Producción']),
        'tipo_documento' => $this->faker->randomElement(['Factura', 'Nota Crédito', 'Nota Débito']),

        // Simulación de QR y CDR como códigos únicos
        'qr_codigo' => 'QR-' . strtoupper($this->faker->bothify('??###??###')),
        'cdr' => 'CDR-' . strtoupper($this->faker->bothify('??###??###')),

        'modo_emision' => $this->faker->randomElement(['normal', 'en contingencia']),

    ];
}
}