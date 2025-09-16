<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicInvoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\Company;
use Faker\Factory as Faker;

class InvoiceDetailSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $allInvoices = ElectronicInvoice::all();
        $products = Product::all();
        $services = Service::all();
        $taxRate = 0.19;

        // Obtener la primera empresa para la excepción de 5 facturas
        $specialCompany = Company::first();

        // 1. Manejar la excepción: 5 facturas para una empresa específica con 2 ítems
        if ($specialCompany) {
            // Primero, obtener los IDs de los usuarios de esa empresa
            $userIds = $specialCompany->users()->pluck('id');

            // Luego, usar esos IDs en el whereIn
            $specialInvoices = $allInvoices->whereIn('user_id', $userIds)->take(5);

            foreach ($specialInvoices as $invoice) {
                // Agregar un producto
                $product = $products->random();
                $cantidadProd = $faker->numberBetween(1, 3);
                $this->createDetail($invoice, $product, $cantidadProd, Product::class);

                // Agregar un servicio
                $service = $services->random();
                $cantidadServ = $faker->numberBetween(1, 2);
                $this->createDetail($invoice, $service, $cantidadServ, Service::class);
            }
        }
        
        // 2. Manejar el caso general: 1 ítem por factura para el resto
        $regularInvoices = $allInvoices->diff($specialInvoices ?? []);
        foreach ($regularInvoices as $invoice) {
            $isProduct = $faker->boolean();
            $item = $isProduct ? $products->random() : $services->random();
            $itemType = $isProduct ? Product::class : Service::class;
            $cantidad = $faker->numberBetween(1, 5);

            $this->createDetail($invoice, $item, $cantidad, $itemType);
        }
    }

    private function createDetail($invoice, $item, $cantidad, $itemType): void
    {
        $faker = Faker::create('es_CO');
        $precioUnitario = $item->precio_unitario; // Usar el nombre de columna correcto
        $subtotal = $precioUnitario * $cantidad;
        $descuento = $faker->boolean(20) ? $subtotal * $faker->randomFloat(2, 0.05, 0.15) : 0;
        $valorImpuesto = ($subtotal - $descuento) * 0.19;
        $valorTotal = $subtotal - $descuento + $valorImpuesto;

        DB::table('invoice_details')->insert([
            'electronic_invoice_id' => $invoice->id,
            'item_type' => $itemType,
            'item_id' => $item->id,
            'descripcion' => $item->nombre,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'valor_impuesto' => $valorImpuesto,
            'valor_total' => $valorTotal,
            'impuestos_aplicados' => json_encode(['IVA' => $valorImpuesto]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}