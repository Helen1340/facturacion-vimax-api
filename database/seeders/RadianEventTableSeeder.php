<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicDocument;
use Faker\Factory as Faker;

class RadianEventTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        
        // Obtener la mitad de los documentos electrónicos que ya existen en la base de datos
        $electronicDocuments = ElectronicDocument::inRandomOrder()->take(ceil(ElectronicDocument::count() / 2))->get();

        foreach ($electronicDocuments as $document) {
            // Evento de Acuse de Recibo
            DB::table('radian_events')->insert([
                'electronic_document_id' => $document->id,
                'codigo' => '030', // Código DIAN para Acuse de Recibo
                'fecha_evento' => now(),
                'tipo_evento' => 'Acuse de Recibo de Factura',
                'xml_respuesta' => '<RespuestaDian><Estado>Aprobado</Estado></RespuestaDian>',
                'estado_dian' => 'Validado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear un segundo evento para algunas de las facturas
            if ($faker->boolean(50)) { // 50% de probabilidad
                 DB::table('radian_events')->insert([
                    'electronic_document_id' => $document->id,
                    'codigo' => '031', // Código DIAN para Recibo del Bien o Prestación del Servicio
                    'fecha_evento' => now(),
                    'tipo_evento' => 'Recibo de Mercancías o Servicios',
                    'xml_respuesta' => '<RespuestaDian><Estado>Aprobado</Estado></RespuestaDian>',
                    'estado_dian' => 'Validado',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}