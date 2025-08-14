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
                'CodigoProductoServicio' => 1001,
                'CostoUnitario' => 2500.50,
                'Tipo' => 'Producto',
                'Nombre' => 'Laptop Dell Inspiron',
                'Descripcion' => 'Computador portátil de alto rendimiento con procesador Intel Core i7',
                'UsuarioCreacion' => 'admin',
                'PorcentajeIva' => 19.00,
                'AplicaImpuesto' => true,
                'Estado' => true
            ],
            [
                'CodigoProductoServicio' => 2001,
                'CostoUnitario' => 150.00,
                'Tipo' => 'Servicio',
                'Nombre' => 'Mantenimiento de computadoras',
                'Descripcion' => 'Servicio técnico especializado para equipos de cómputo',
                'UsuarioCreacion' => 'admin',
                'PorcentajeIva' => 0.00,
                'AplicaImpuesto' => false,
                'Estado' => true
            ],
            [
                'CodigoProductoServicio' => 1002,
                'CostoUnitario' => 3500.00,
                'Tipo' => 'Producto',
                'Nombre' => 'Impresora HP LaserJet Pro',
                'Descripcion' => 'Impresora láser de alta velocidad y bajo consumo',
                'UsuarioCreacion' => 'admin',
                'PorcentajeIva' => 19.00,
                'AplicaImpuesto' => true,
                'Estado' => true
            ],
            [
                'CodigoProductoServicio' => 3001,
                'CostoUnitario' => 500.00,
                'Tipo' => 'Producto',
                'Nombre' => 'Teclado mecánico Logitech',
                'Descripcion' => 'Teclado mecánico retroiluminado con switches de alta durabilidad',
                'UsuarioCreacion' => 'admin',
                'PorcentajeIva' => 19.00,
                'AplicaImpuesto' => true,
                'Estado' => true
            ],
            [
                'CodigoProductoServicio' => 2002,
                'CostoUnitario' => 80.00,
                'Tipo' => 'Servicio',
                'Nombre' => 'Instalación de software',
                'Descripcion' => 'Servicio de instalación y configuración de software en equipos de cómputo',
                'UsuarioCreacion' => 'admin',
                'PorcentajeIva' => 0.00,
                'AplicaImpuesto' => false,
                'Estado' => true
            ],
        ]);
    }
}
