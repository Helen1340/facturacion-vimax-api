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
                'Id_DetalleFactura' => 1,
                'Descripcion' => 'Laptop Dell Inspiron 15',
                'Cantidad' => 1,
                'PrecioUnitario' => 1200.00,
                'ValorTotal' => 1200.00,
                'Descuento' => null, // Sin descuento
                'PorcentajeIVA' => 19.00,
                'ValorIVA' => 228.00,
                'UnidadMedida' => 'Unidad',
                'CodigoProducto' => 'LAPT-DELL-001',
                'Observacion' => 'Incluye cargador original',
                'product_service_id' => 1, // Suponiendo que el producto existe con el ID 1
            ],
            // Registro 2: Un producto con descuento
            [
                'Id_DetalleFactura' => 2,
                'Descripcion' => 'Mouse inalámbrico Logitech',
                'Cantidad' => 2,
                'PrecioUnitario' => 25.50,
                'ValorTotal' => 45.90, // (25.50 * 2) - 10% de descuento
                'Descuento' => 5.10, // 10% de 51.00
                'PorcentajeIVA' => 19.00,
                'ValorIVA' => 8.72,
                'UnidadMedida' => 'Unidad',
                'CodigoProducto' => 'MOUSE-LOGI-002',
                'Observacion' => 'Oferta del mes',
                'product_service_id' => 2, // Suponiendo que el producto existe con el ID 2
            ],
            // Registro 3: Un servicio en lugar de un producto físico
            [
                'Id_DetalleFactura' => 3,
                'Descripcion' => 'Servicio de mantenimiento web',
                'Cantidad' => 5, // Horas de servicio
                'PrecioUnitario' => 50.00,
                'ValorTotal' => 250.00,
                'Descuento' => null,
                'PorcentajeIVA' => 19.00,
                'ValorIVA' => 47.50,
                'UnidadMedida' => 'Horas',
                'CodigoProducto' => 'SERV-WEB-003',
                'Observacion' => 'Mantenimiento del sitio web principal',
                'product_service_id' => 3, // Suponiendo que el servicio existe con el ID 3
            ],
            // Registro 4: Múltiples unidades de un mismo artículo
            [
                'Id_DetalleFactura' => 4,
                'Descripcion' => 'Monitor Samsung 24 pulgadas',
                'Cantidad' => 3,
                'PrecioUnitario' => 150.00,
                'ValorTotal' => 450.00,
                'Descuento' => null,
                'PorcentajeIVA' => 19.00,
                'ValorIVA' => 85.50,
                'UnidadMedida' => 'Unidad',
                'CodigoProducto' => 'MONI-SAMS-004',
                'Observacion' => null, // Sin observación
                'product_service_id' => 4, // Suponiendo que el producto existe con el ID 4
            ],
            // Registro 5: Un producto con un valor de IVA del 0%
            [
                'Id_DetalleFactura' => 5,
                'Descripcion' => 'Libro "El principito"',
                'Cantidad' => 1,
                'PrecioUnitario' => 20.00,
                'ValorTotal' => 20.00,
                'Descuento' => null,
                'PorcentajeIVA' => 0.00, // IVA del 0% para libros
                'ValorIVA' => 0.00,
                'UnidadMedida' => 'Unidad',
                'CodigoProducto' => 'LIBRO-PRIN-005',
                'Observacion' => null,
                'product_service_id' => 5, // Suponiendo que el producto existe con el ID 5
            ],
        ]);
    }
}
