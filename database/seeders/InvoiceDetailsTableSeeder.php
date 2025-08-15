<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_details')->insert([
            // Registro 1: Un ejemplo simple de un producto
            [
                'id_detalle_factura' => 1,
                'descripcion' => 'Laptop Dell Inspiron 15',
                'cantidad' => 1,
                'precio_unitario' => 1200.00,
                'valor_total' => 1200.00,
                'descuento' => null, // Sin descuento
                'porcentaje_iva' => 19.00,
                'valor_iva' => 228.00,
                'unidad_medida' => 'Unidad',
                'codigo_producto' => 'LAPT-DELL-001',
                'observacion' => 'Incluye cargador original',
            ],
            // Registro 2: Un producto con descuento
            [
                'id_detalle_factura' => 2,
                'descripcion' => 'Mouse inalámbrico Logitech',
                'cantidad' => 2,
                'precio_unitario' => 25.50,
                'valor_total' => 45.90, // (25.50 * 2) - 10% de descuento
                'descuento' => 5.10, // 10% de 51.00
                'porcentaje_iva' => 19.00,
                'valor_iva' => 8.72,
                'unidad_medida' => 'Unidad',
                'codigo_producto' => 'MOUSE-LOGI-002',
                'observacion' => 'Oferta del mes',
            ],
            // Registro 3: Un servicio en lugar de un producto físico
            [
                'id_detalle_factura' => 3,
                'descripcion' => 'Servicio de mantenimiento web',
                'cantidad' => 5, // Horas de servicio
                'precio_unitario' => 50.00,
                'valor_total' => 250.00,
                'descuento' => null,
                'porcentaje_iva' => 19.00,
                'valor_iva' => 47.50,
                'unidad_medida' => 'Horas',
                'codigo_producto' => 'SERV-WEB-003',
                'observacion' => 'Mantenimiento del sitio web principal',
            ],
            // Registro 4: Múltiples unidades de un mismo artículo
            [
                'id_detalle_factura' => 4,
                'descripcion' => 'Monitor Samsung 24 pulgadas',
                'cantidad' => 3,
                'precio_unitario' => 150.00,
                'valor_total' => 450.00,
                'descuento' => null,
                'porcentaje_iva' => 19.00,
                'valor_iva' => 85.50,
                'unidad_medida' => 'Unidad',
                'codigo_producto' => 'MONI-SAMS-004',
                'observacion' => null, // Sin observación
            ],
            // Registro 5: Un producto con un valor de IVA del 0%
            [
                'id_detalle_factura' => 5,
                'descripcion' => 'Libro "El principito"',
                'cantidad' => 1,
                'precio_unitario' => 20.00,
                'valor_total' => 20.00,
                'descuento' => null,
                'porcentaje_iva' => 0.00, // IVA del 0% para libros
                'valor_iva' => 0.00,
                'unidad_medida' => 'Unidad',
                'codigo_producto' => 'LIBRO-PRIN-005',
                'observacion' => null,
            ],
        ]);
    }
}
