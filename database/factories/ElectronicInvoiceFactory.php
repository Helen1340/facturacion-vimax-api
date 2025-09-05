<?php

namespace Database\Factories;

use App\Models\ElectronicInvoice;
use App\Models\User; 

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectronicInvoice>
 */
class ElectronicInvoiceFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

    // Calcular valores coherentes
        $sub_total = $this->faker->randomFloat(2, 50_000, 2_000_000);

        // IVA (19%, 5% o exento)
        $tasaIva = $this->faker->randomElement([0, 5, 19]);
        $valor_impuesto = $tasaIva > 0 ? round(($sub_total * $tasaIva) / 100, 2) : 0;

        // Descuento opcional
        $descuento_total = $this->faker->boolean(25) ? round($sub_total * 0.05, 2) : 0;

        // Total final
        $total_factura = $sub_total - $descuento_total + $valor_impuesto;

        return [
            'user_id'         => User::inRandomOrder()->first()?->id,
            'numero_factura'  => 'FV-' . now()->year . '-' . $this->faker->unique()->numerify('#####'),
            'fecha_emision'   => $this->faker->dateTimeBetween('-2 years', 'now'),
            'sub_total'       => $sub_total,
            'total_impuesto'  => $valor_impuesto,
            'total_factura'   => $total_factura,
            'estado_interno'  => $this->faker->randomElement(['borrador', 'emitida', 'anulada']),
            'descuento_total' => $descuento_total > 0 ? $descuento_total : null,
            'observacion'     => $this->faker->optional()->randomElement([
                                    'Pago en efectivo',
                                    'Pago con tarjeta',
                                    'Factura recurrente',
                                    'Factura ajustada por devolución',
                                ]),
        ];
    }
}