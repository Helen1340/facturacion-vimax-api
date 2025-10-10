<?php

namespace App\Http\Controllers;
use App\Models\DigitalCertificate;

use Illuminate\Http\Request;

class DigitalCertificateController extends Controller
{
    /*  la función index devuelve una lista de certificados digitales
        con la posibilidad de incluir relaciones, filtrar y ordenar
        según los parámetros de la solicitud.
    */
    public function index()
    {
        $digital_certificates = DigitalCertificate::included()->filter()->sort()->getOrPaginate();
        return response()->json($digital_certificates);
    }

    /*  la función store crea un nuevo certificado digital
        validando los datos de la solicitud y guardándolos en la base de datos.
    */
    public function store(Request $request)
    {
        $request->validate([
            'company_id'            => 'required|exists:companies,id', // ID de la compañía propietaria del certificado
            'certificate_name'      => 'required|string|max:255',       // Nombre del certificado
            'certificate_path'      => 'required|string',               // Ruta del archivo del certificado
            'serial_number'         => 'required|string|max:100',       // Número de serie del certificado
            'password'              => 'required|string|max:150',       // Contraseña del certificado
            'start_date'            => 'required|date',                 // Fecha de inicio de vigencia
            'end_date'              => 'required|date|after_or_equal:start_date', // Fecha de fin de vigencia
            'status'                => 'required|in:Vigente,Vencido,Revocado', // Estado actual del certificado
            'issuer'                => 'required|string|max:100',       // Entidad emisora del certificado
            'certificate_type'      => 'sometimes|in:Producción,Pruebas', // Tipo de certificado (producción o pruebas)
            'signature_algorithm'   => 'sometimes|string|max:50',       // Algoritmo de firma digital
            'uuid'                  => 'sometimes|string|max:100',      // UUID del certificado (opcional)
            'description'           => 'sometimes|string|max:255',      // Descripción opcional del certificado
        ]);

        $digital_certificate = DigitalCertificate::create($request->all());
        return response()->json($digital_certificate, 201);
    }

    /*  la función show devuelve un certificado digital específico
        basado en su ID, lanzando un error 404 si no se encuentra.
    */
    public function show($id)
    {
        $digital_certificate = DigitalCertificate::findOrFail($id);
        return response()->json($digital_certificate);
    }

    /*  la función update actualiza un certificado digital existente
        validando los datos de la solicitud y actualizando solo los campos proporcionados.
    */
    public function update(Request $request, DigitalCertificate $digitalCertificate)
    {
        $request->validate([
            'company_id'            => 'sometimes|exists:companies,id', 
            'certificate_name'      => 'sometimes|required|string|max:255',
            'certificate_path'      => 'sometimes|required|string',
            'serial_number'         => 'sometimes|required|string|max:100',
            'password'              => 'sometimes|required|string|max:150',
            'start_date'            => 'sometimes|required|date',
            'end_date'              => 'sometimes|required|date|after_or_equal:start_date',
            'status'                => 'sometimes|required|in:Vigente,Vencido,Revocado',
            'issuer'                => 'sometimes|required|string|max:100',
            'certificate_type'      => 'sometimes|in:Producción,Pruebas',
            'signature_algorithm'   => 'sometimes|string|max:50',
            'uuid'                  => 'sometimes|string|max:100',
            'description'           => 'sometimes|string|max:255',
        ]);

        // Actualiza solo los campos que vienen en el request
        $digitalCertificate->update($request->only(array_keys($request->all())));

        return response()->json($digitalCertificate);
    }

    /*  la función destroy elimina un certificado digital específico
        basado en su ID, lanzando un error 404 si no se encuentra.
    */
    public function destroy($id)
    {
        $digital_certificate = DigitalCertificate::findOrFail($id);
        $digital_certificate->delete();
        return response()->json(null, 204);
    }
}
