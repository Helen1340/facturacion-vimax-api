<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasurementUnitTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar la tabla para evitar duplicados
        DB::table('measurement_units')->delete();

        // 2. Insertar unidades oficiales DIAN
        DB::table('measurement_units')->insert([
            // === PRODUCTOS ===
            ['nombre' => 'Unidad',     'estado' => 'Activo', 'codigo_dian' => 'UND', 'descripcion' => 'Unidad de producto', 'tipo_aplicacion' => 'Producto'],
            ['nombre' => 'Kilogramo',  'estado' => 'Activo', 'codigo_dian' => 'KGM', 'descripcion' => 'Peso en kilogramos', 'tipo_aplicacion' => 'Producto'],
            ['nombre' => 'Gramo',      'estado' => 'Activo', 'codigo_dian' => 'GRM', 'descripcion' => 'Peso en gramos', 'tipo_aplicacion' => 'Producto'],
            ['nombre' => 'Litro',      'estado' => 'Activo', 'codigo_dian' => 'LTR', 'descripcion' => 'Volumen en litros', 'tipo_aplicacion' => 'Producto'],
            ['nombre' => 'Mililitro',  'estado' => 'Activo', 'codigo_dian' => 'MLT', 'descripcion' => 'Volumen en mililitros', 'tipo_aplicacion' => 'Producto'],
            ['nombre' => 'Caja',       'estado' => 'Activo', 'codigo_dian' => 'BX',  'descripcion' => 'Caja de productos', 'tipo_aplicacion' => 'Producto'],

            // === SERVICIOS ===
            ['nombre' => 'Hora',       'estado' => 'Activo', 'codigo_dian' => 'HUR', 'descripcion' => 'Tiempo en horas', 'tipo_aplicacion' => 'Servicio'],
            ['nombre' => 'Día',        'estado' => 'Activo', 'codigo_dian' => 'DAY', 'descripcion' => 'Tiempo en días', 'tipo_aplicacion' => 'Servicio'],
            ['nombre' => 'Mes',        'estado' => 'Activo', 'codigo_dian' => 'MON', 'descripcion' => 'Tiempo en meses', 'tipo_aplicacion' => 'Servicio'],
            ['nombre' => 'Servicio',   'estado' => 'Activo', 'codigo_dian' => 'E48', 'descripcion' => 'Prestación de servicios', 'tipo_aplicacion' => 'Servicio'],
            ['nombre' => 'Contrato',   'estado' => 'Activo', 'codigo_dian' => 'CNT', 'descripcion' => 'Contrato de servicios', 'tipo_aplicacion' => 'Servicio'],
        ]);

        // 3. Solo en desarrollo: unidades extra ficticias
       // if (app()->environment('local')) {
           // MeasurementUnit::factory()->count(39)->create();
        
    }
}
