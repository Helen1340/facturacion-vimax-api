<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicDocument;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class RadianEventTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        
        // Obtener la mitad de los documentos electrónicos para generar eventos
        $electronicDocuments = ElectronicDocument::inRandomOrder()
            ->take(ceil(ElectronicDocument::count() / 2))
            ->get();

        foreach ($electronicDocuments as $document) {

            //Primer evento: Acuse de Recibo (código DIAN 030)
            DB::table('radian_events')->insert([
                'electronic_document_id' => $document->id,
                'event_code' => '030', // Código DIAN del evento
                'event_name' => 'Acuse de Recibo de Factura', // Nombre o tipo del evento
                'event_date' => now(), // Fecha y hora del evento
                'event_uuid' => Str::uuid(), // Identificador único del evento
                'response_xml' => '<RespuestaDian><Estado>Aprobado</Estado></RespuestaDian>', // XML de respuesta
                'dian_status' => 'accepted', // Estado validado por la DIAN
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //Segundo evento opcional: Recibo del Bien o Prestación del Servicio (código DIAN 031)
            if ($faker->boolean(50)) { // 50% de probabilidad
                DB::table('radian_events')->insert([
                    'electronic_document_id' => $document->id,
                    'event_code' => '031',
                    'event_name' => 'Recibo de Bien o Prestación del Servicio',
                    'event_date' => now(),
                    'event_uuid' => Str::uuid(),
                    'response_xml' => '<RespuestaDian><Estado>Aprobado</Estado></RespuestaDian>',
                    'dian_status' => 'accepted',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
