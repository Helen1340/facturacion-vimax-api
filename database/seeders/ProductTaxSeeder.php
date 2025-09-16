<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Tax;

class ProductTaxSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener todos los productos y los impuestos de tipo IVA
        $products = Product::all();
        $ivaTaxes = Tax::where('tipo', 'IVA')->get();

        // 2. Recorrer cada producto
        foreach ($products as $product) {
            // Asignar un impuesto IVA aleatorio a cada producto
            // Insertar el par de IDs en la tabla pivot
            DB::table('product_tax')->insert([
                'product_id' => $product->id,
                'tax_id' => $ivaTaxes->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}