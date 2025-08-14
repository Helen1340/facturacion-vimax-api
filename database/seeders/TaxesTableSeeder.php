<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'codigo_dian'      => '01', // IVA 19% según estándar DIAN
                'nombre'           => 'IVA',
                'descripcion'      => 'Impuesto al Valor Agregado (19%)',
                'tipo_aplicacion'  => 'trasladado',
                'porcentaje_base'  => 19.00,
                'estado'           => true,
            ],
            [
                'codigo_dian'      => '02', // IVA 5%
                'nombre'           => 'IVA',
                'descripcion'      => 'Impuesto al Valor Agregado (5%)',
                'tipo_aplicacion'  => 'trasladado',
                'porcentaje_base'  => 5.00,
                'estado'           => true,
            ],
            [
                'codigo_dian'      => '03', // ReteFuente
                'nombre'           => 'Retención en la Fuente',
                'descripcion'      => 'Retención a título de renta (15%)',
                'tipo_aplicacion'  => 'retenido',
                'porcentaje_base'  => 15.00,
                'estado'           => true,
            ],
            [
                'codigo_dian'      => '04', // ICA
                'nombre'           => 'ICA',
                'descripcion'      => 'Impuesto de Industria y Comercio (1%)',
                'tipo_aplicacion'  => 'retenido',
                'porcentaje_base'  => 1.00,
                'estado'           => true,
            ],
            [
                'codigo_dian'      => '05', // ReteIVA
                'nombre'           => 'Retención IVA',
                'descripcion'      => 'Retención del IVA (15%)',
                'tipo_aplicacion'  => 'retenido',
                'porcentaje_base'  => 15.00,
                'estado'           => true,
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
