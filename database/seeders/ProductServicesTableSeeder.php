<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_services')->insert([
            [
                'codigo_producto_servicio' => 1001,
                'costo_unitario' => 2500.50,
                'tipo' => 'Producto',
                'nombre' => 'Laptop Dell Inspiron',
                'descripcion' => 'Computador portátil de alto rendimiento con procesador Intel Core i7',
                'usuario_creacion' => 'admin',
                'porcentaje_iva' => 19.00,
                'aplica_impuesto' => true,
                'estado' => true
            ],
            [
                'codigo_producto_servicio' => 2001,
                'costo_unitario' => 150.00,
                'tipo' => 'Servicio',
                'nombre' => 'Mantenimiento de computadoras',
                'descripcion' => 'Servicio técnico especializado para equipos de cómputo',
                'usuario_creacion' => 'admin',
                'porcentaje_iva' => 0.00,
                'aplica_impuesto' => false,
                'estado' => true
            ],
            [
                'codigo_producto_servicio' => 1002,
                'costo_unitario' => 3500.00,
                'tipo' => 'Producto',
                'nombre' => 'Impresora HP LaserJet Pro',
                'descripcion' => 'Impresora láser de alta velocidad y bajo consumo',
                'usuario_creacion' => 'admin',
                'porcentaje_iva' => 19.00,
                'aplica_impuesto' => true,
                'estado' => true
            ],
            [
                'codigo_producto_servicio' => 3001,
                'costo_unitario' => 500.00,
                'tipo' => 'Producto',
                'nombre' => 'Teclado mecánico Logitech',
                'descripcion' => 'Teclado mecánico retroiluminado con switches de alta durabilidad',
                'usuario_creacion' => 'admin',
                'porcentaje_iva' => 19.00,
                'aplica_impuesto' => true,
                'estado' => true
            ],
            [
                'codigo_producto_servicio' => 2002,
                'costo_unitario' => 80.00,
                'tipo' => 'Servicio',
                'nombre' => 'Instalación de software',
                'descripcion' => 'Servicio de instalación y configuración de software en equipos de cómputo',
                'usuario_creacion' => 'admin',
                'porcentaje_iva' => 0.00,
                'aplica_impuesto' => false,
                'estado' => true
            ],
        ]);
    }
}
