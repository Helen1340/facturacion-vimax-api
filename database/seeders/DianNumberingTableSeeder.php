<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DianNumberingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dian_numberings')->insert([
            // Numeración 1
            [
                'tipo_documento'     => 'Factura',
                'prefijo'            => 'FV',
                'numero_inicio'      => 1,
                'numero_fin'         => 1000,
                'fecha_resolucion'   => '2023-01-01',
                'numero_resolucion'  => '18760000001',
                'fecha_inicio'       => '2023-01-01',
                'fecha_fin'          => '2024-12-31',
                'estado_actual'      => 'Activo',
            ],
            // Numeración 2
            [
                'tipo_documento'     => 'notaCredito',
                'prefijo'            => 'NC',
                'numero_inicio'      => 1,
                'numero_fin'         => 500,
                'fecha_resolucion'   => '2023-01-01',
                'numero_resolucion'  => '18760000002',
                'fecha_inicio'       => '2023-01-01',
                'fecha_fin'          => '2024-12-31',
                'estado_actual'      => 'Activo',
            ],
            // Numeración 3
            [
                'tipo_documento'     => 'Factura',
                'prefijo'            => 'FA',
                'numero_inicio'      => 1001,
                'numero_fin'         => 2000,
                'fecha_resolucion'   => '2022-06-01',
                'numero_resolucion'  => '18760000003',
                'fecha_inicio'       => '2022-06-01',
                'fecha_fin'          => '2023-05-31',
                'estado_actual'      => 'Inactivo',
            ],
            // Numeración 4
            [
                'tipo_documento'     => 'notaDebito',
                'prefijo'            => 'ND',
                'numero_inicio'      => 1,
                'numero_fin'         => 200,
                'fecha_resolucion'   => '2024-01-01',
                'numero_resolucion'  => '18760000004',
                'fecha_inicio'       => '2024-01-01',
                'fecha_fin'          => '2025-12-31',
                'estado_actual'      => 'Activo',
            ],
            // Numeración 5
            [
                'tipo_documento'     => 'Factura',
                'prefijo'            => 'FC',
                'numero_inicio'      => 1,
                'numero_fin'         => 1500,
                'fecha_resolucion'   => '2023-09-01',
                'numero_resolucion'  => '18760000005',
                'fecha_inicio'       => '2023-09-01',
                'fecha_fin'          => '2025-08-31',
                'estado_actual'      => 'Activo',
            ],
        ]);
    }
}
