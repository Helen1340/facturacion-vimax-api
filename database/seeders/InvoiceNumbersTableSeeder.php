<?php

namespace Database\Seeders;

use App\Models\InvoiceNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceNumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InvoiceNumber::insert([
            [
                'NIT' => 900123456,
                'tipo_documento' => 'Factura',
                'prefijo' => 'FAC',
                'numero_inicial' => 1000,
                'numero_final' => 2000,
                'fecha_resolucion' => '2024-01-15',
                'numero_resolucion' => '12345',
                'fecha_inicio' => '2024-01-16',
                'fecha_fin' => '2025-01-15',
                'estado_actual' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NIT' => 901234567,
                'tipo_documento' => 'notaCredito',
                'prefijo' => 'NC',
                'numero_inicial' => 500,
                'numero_final' => 1500,
                'fecha_resolucion' => '2024-02-10',
                'numero_resolucion' => '56789',
                'fecha_inicio' => '2024-02-11',
                'fecha_fin' => '2025-02-10',
                'estado_actual' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NIT' => 902345678,
                'tipo_documento' => 'Factura',
                'prefijo' => 'F2024',
                'numero_inicial' => 200,
                'numero_final' => 1200,
                'fecha_resolucion' => '2024-03-05',
                'numero_resolucion' => '98765',
                'fecha_inicio' => '2024-03-06',
                'fecha_fin' => '2025-03-05',
                'estado_actual' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NIT' => 903456789,
                'tipo_documento' => 'notaCredito',
                'prefijo' => 'NCR',
                'numero_inicial' => 300,
                'numero_final' => 800,
                'fecha_resolucion' => '2024-04-20',
                'numero_resolucion' => '54321',
                'fecha_inicio' => '2024-04-21',
                'fecha_fin' => '2025-04-20',
                'estado_actual' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NIT' => 904567890,
                'tipo_documento' => 'Factura',
                'prefijo' => 'VENTA',
                'numero_inicial' => 50,
                'numero_final' => 500,
                'fecha_resolucion' => '2024-05-01',
                'numero_resolucion' => '11223',
                'fecha_inicio' => '2024-05-02',
                'fecha_fin' => '2025-05-01',
                'estado_actual' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
