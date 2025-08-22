<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RadianEventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('radian_events')->insert([
            // Evento 1:
            [
                'codigo'                 => '030', // Código de acuse de recibo de la factura electrónica
                'fecha_evento'           => '2024-08-10 10:00:00',
                'tipo_evento'            => 'AcuseRecibo',
                'xml_respuesta'          => '<DianResponse>...</DianResponse>', // XML de respuesta de la DIAN
                'estado_dian'            => 'OK',
            ],
            // Evento 2:
            [
                'codigo'                 => '032', // Código de aceptación expresa
                'fecha_evento'           => '2024-08-11 15:30:00',
                'tipo_evento'            => 'AceptacionExpresa',
                'xml_respuesta'          => '<DianResponse>...</DianResponse>',
                'estado_dian'            => 'OK',
            ],
            // Evento 3:
            [
                'codigo'                 => '031', // Código de reclamo
                'fecha_evento'           => '2024-08-12 09:00:00',
                'tipo_evento'            => 'Reclamo',
                'xml_respuesta'          => '<DianResponse>...</DianResponse>',
                'estado_dian'            => 'Rechazado',
            ],
            // Evento 4:
            [
                'codigo'                 => '030',
                'fecha_evento'           => '2024-08-13 11:45:00',
                'tipo_evento'            => 'AcuseRecibo',
                'xml_respuesta'          => '<DianResponse>...</DianResponse>',
                'estado_dian'            => 'OK',
            ],
            // Evento 5:
            [
                'codigo'                 => '033', // Código de aceptación tácita
                'fecha_evento'           => '2024-08-20 10:00:00',
                'tipo_evento'            => 'AceptacionTacita',
                'xml_respuesta'          => '<DianResponse>...</DianResponse>',
                'estado_dian'            => 'OK',
            ],
        ]);
    }
}
