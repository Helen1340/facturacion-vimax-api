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
            'nombre_certificado' => 'required|string|max:255',
            'ruta_certificado'   => 'required|string',
            'numero_serial'      => 'required|string|max:100',
            'contrasena'         => 'required|string|max:150',
            'fecha_inicio'       => 'required|date',
            'fecha_fin'          => 'required|date|after_or_equal:fecha_inicio',
            'estado'             => 'required|in:Vigente,Vencido,Revocado',
            'entidad_emisora'    => 'required|string|max:100',
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
    public function update(Request $request, DigitalCertificate $digital_certificate)
    {
        $request->validate([
            'nombre_certificado' => 'sometimes|required|string|max:225',
            'ruta_certificado'   => 'sometimes|required|string',
            'numero_serial'      => 'sometimes|required|string|max:100',
            'contrasena'         => 'sometimes|required|string|max:150',
            'fecha_inicio'       => 'sometimes|required|date',
            'fecha_fin'          => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'estado'             => 'sometimes|required|in:Vigente,Vencido,Revocado',
            'entidad_emisora'    => 'sometimes|required|string|max:100',
        ]);

        // Actualiza solo los campos que vienen en el request
        $digital_certificate->update($request->only(array_keys($request->all())));

        return response()->json($digital_certificate);
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
