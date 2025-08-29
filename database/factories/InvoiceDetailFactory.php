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

        // Crear el item (product o service)
        $item = $itemClass::factory()->create();

        // Cantidad y precio según item
        $cantidad = $this->faker->numberBetween(1, 10);
        $precio_unitario = $item->precio_unitario ?? ($item->price ?? $this->faker->randomFloat(2, 10, 500));
        $valor_total = round($precio_unitario * $cantidad, 2);

        return [
            'electronic_invoice_id' => ElectronicInvoice::factory(),
            'descripcion' => $this->faker->sentence(6),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'valor_total' => $valor_total,
            'subtotal' => $valor_total, // puedes ajustarlo si aplicas impuestos o descuento
            'descuento' => null,
            'impuestos_aplicados' => null,
            'valor_impuesto' => null,
            'item_id' => $item->id,
            'item_type' => $itemClass,
        ];



    }
}
