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

        // Obtener la primera empresa para la excepción
        $specialCompany = Company::first();

        // Excepción: 5 facturas con 2 ítems (producto + servicio)
        if ($specialCompany) {
            $userIds = $specialCompany->users()->pluck('id');
            $specialInvoices = $allInvoices->whereIn('user_id', $userIds)->take(5);

            foreach ($specialInvoices as $invoice) {
                // Producto
                $product = $products->random();
                $quantityProd = $faker->numberBetween(1, 3);
                $this->createDetail($invoice, $product, $quantityProd, Product::class, $taxRate);

                // Servicio
                $service = $services->random();
                $quantityServ = $faker->numberBetween(1, 2);
                $this->createDetail($invoice, $service, $quantityServ, Service::class, $taxRate);
            }
        }

        // Resto de facturas: solo un ítem
        $regularInvoices = $allInvoices->diff($specialInvoices ?? []);
        foreach ($regularInvoices as $invoice) {
            $isProduct = $faker->boolean();
            $item = $isProduct ? $products->random() : $services->random();
            $itemType = $isProduct ? Product::class : Service::class;
            $quantity = $faker->numberBetween(1, 5);

            $this->createDetail($invoice, $item, $quantity, $itemType, $taxRate);
        }
    }

    /**
     * Inserta el detalle de la factura con estructura UBL 2.1 (DIAN)
     */
    private function createDetail($invoice, $item, $quantity, $itemType, $taxRate): void
    {
        $faker = Faker::create('es_CO');

        // Usar nombres correctos de campos según tus modelos
        $unitPrice = $item->unit_price ?? $item->precio_unitario ?? $faker->randomFloat(2, 5000, 200000);

        $lineExtensionAmount = $unitPrice * $quantity; // Subtotal sin impuestos
        $discountAmount = $faker->boolean(20) ? $lineExtensionAmount * $faker->randomFloat(2, 0.05, 0.15) : 0;
        $taxAmount = ($lineExtensionAmount - $discountAmount) * $taxRate;
        $totalLineAmount = $lineExtensionAmount - $discountAmount + $taxAmount;

        DB::table('invoice_details')->insert([
            'electronic_invoice_id' => $invoice->id,
            'item_type' => $itemType,
            'item_id' => $item->id,
            'description' => $item->name ?? $item->nombre ?? 'Item sin descripción',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_extension_amount' => $lineExtensionAmount,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_line_amount' => $totalLineAmount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
