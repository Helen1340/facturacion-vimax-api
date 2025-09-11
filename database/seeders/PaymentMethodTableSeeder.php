<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodTableSeeder extends Seeder
{
    public function run(): void
    {
        // Lista de métodos oficiales DIAN
        $metodos = [
            ['nombre' => 'Efectivo',               'codigo_dian' => '10',  'descripcion' => 'Pago en efectivo'],
            ['nombre' => 'Cheque',                 'codigo_dian' => '20',  'descripcion' => 'Pago mediante cheque'],
            ['nombre' => 'Transferencia bancaria', 'codigo_dian' => '30',  'descripcion' => 'Transferencia entre cuentas bancarias'],
            ['nombre' => 'Consignación bancaria',  'codigo_dian' => '31',  'descripcion' => 'Depósito o consignación en cuenta bancaria'],
            ['nombre' => 'Tarjeta de débito',      'codigo_dian' => '41',  'descripcion' => 'Pago con tarjeta débito'],
            ['nombre' => 'Tarjeta de crédito',     'codigo_dian' => '42',  'descripcion' => 'Pago con tarjeta de crédito'],
           
        ];

        foreach ($metodos as $metodo) {
            PaymentMethod::updateOrCreate(
                ['codigo_dian' => $metodo['codigo_dian']],  // criterio único
                [
                    'nombre' => $metodo['nombre'],
                    'descripcion' => $metodo['descripcion'],
                ]);
            }

            PaymentMethod::factory()->count(43)->create();
        
    }
}
