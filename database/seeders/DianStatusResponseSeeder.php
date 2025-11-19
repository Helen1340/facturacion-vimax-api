<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DianStatusResponse;

class DianStatusResponseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        DianStatusResponse::create([
            'electronic_document_id' => 1,
            'status_code' => '200',
            'status_description' => 'Documento recibido correctamente por la DIAN',
            'status_message' => 'La factura fue validada exitosamente y está disponible para consulta en el sistema de la DIAN.',
            'response_xml' => '<ApplicationResponse>...</ApplicationResponse>',
            'protocol_number' => 'PRT-000123456',
            'received_at' => now(),
        ]);

        DianStatusResponse::create([
            'electronic_document_id' => 2,
            'status_code' => '400',
            'status_description' => 'Error en validación del XML',
            'status_message' => 'El documento no cumple con el esquema XML exigido por la DIAN.',
            'response_xml' => '<ApplicationResponse>Error en estructura XML</ApplicationResponse>',
            'protocol_number' => 'PRT-000123457',
            'received_at' => now(),
        ]);
        */
    }
}
