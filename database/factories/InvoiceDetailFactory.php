<?php

namespace Database\Factories;
use App\Models\InvoiceDetail;
use App\Models\ElectronicInvoice;
use App\Models\Product;
use App\Models\Service;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Elegir aleatoriamente entre Product o Service
        $itemClass = $this->faker->randomElement([
            Product::class,
            Service::class,
        ]);

        // Tomar un item existente, o crearlo en memoria si no hay
        $item = $itemClass::inRandomOrder()->first() ?? $itemClass::factory()->make();

        // Cantidad y precio
        $cantidad = $this->faker->numberBetween(1, 20);
        $precio_unitario = $item->precio_unitario ?? ($item->price ?? $this->faker->randomFloat(2, 500, 500000));

        // Cálculos
        $subtotal = round($cantidad * $precio_unitario, 2);

        // Simular IVA (19%, 5% o exento)
        $tasaIva = $this->faker->randomElement([0, 5, 19]);
        $valor_impuesto = $tasaIva > 0 ? round(($subtotal * $tasaIva) / 100, 2) : 0;

        // Descuento opcional
        $descuento = $this->faker->boolean(20) ? round($subtotal * 0.05, 2) : 0; // 20% de probabilidad de descuento

        // Total con impuestos y descuentos
        $valor_total = $subtotal - $descuento + $valor_impuesto;

        return [
            'electronic_invoice_id' => ElectronicInvoice::inRandomOrder()->first()?->id ?? ElectronicInvoice::factory(),
            'descripcion'           => $item->nombre ?? $this->faker->sentence(4),
            'cantidad'              => $cantidad,
            'precio_unitario'       => $precio_unitario,
            'subtotal'              => $subtotal,
            'descuento'             => $descuento > 0 ? $descuento : null,
            'impuestos_aplicados'   => $tasaIva > 0 ? "IVA {$tasaIva}%" : "Exento",
            'valor_impuesto'        => $valor_impuesto > 0 ? $valor_impuesto : null,
            'valor_total'           => $valor_total,
            'item_id'               => $item->id,
            'item_type'             => $itemClass,
        ];
    }
}