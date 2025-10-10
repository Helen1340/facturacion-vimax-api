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
        // 1. Obtener todos los productos y los impuestos de tipo 'IVA'
        $products = Product::all();
        $ivaTaxes = Tax::where('type', 'IVA')->get();

        // 2. Validar que existan datos antes de continuar
        if ($products->isEmpty() || $ivaTaxes->isEmpty()) {
            echo " No se encontraron productos o impuestos IVA.\n";
            return;
        }

        // 3. Asignar a cada producto un impuesto IVA aleatorio
        foreach ($products as $product) {
            DB::table('product_tax')->insert([
                'product_id' => $product->id,
                'tax_id' => $ivaTaxes->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Seeder de relación producto-impuesto ejecutado correctamente.\n";
    }
}
