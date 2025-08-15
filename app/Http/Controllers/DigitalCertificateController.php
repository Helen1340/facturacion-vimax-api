<?php

namespace App\Http\Controllers;

use App\Models\DigitalCertificate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DigitalCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $digitalCertificate = DigitalCertificate::included()->filter()->sort()->paginate();

        return response()->json($digitalCertificate);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $validatedData = $request->validate([
                'nit'                => 'required|integer|exists:empresas,nit',
                'nombre_certificado' => 'required|string|max:225',
                'ruta_certificado'   => 'required|string',
                'contrasena'         => 'required|string|max:225',
                'fecha_inicio'       => 'required|date',
                'fecha_fin'          => 'required|date|after_or_equal:fecha_inicio',
                'estado'             => 'required|in:vigente,vencido,revocado',
                'proveedor'          => 'required|string|max:100',
            ]);

            $digitalCertificate = DigitalCertificate::create($validatedData);

            return response()->json($digitalCertificate);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $digitalCertificate = DigitalCertificate::findOrFail($id);

        return response()->json($digitalCertificate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalCertificate $digitalCertificate)
    {
            $validatedData = $request->validate([
                'nit'                => 'sometimes|integer|exists:empresas,nit',
                'nombre_certificado' => 'sometimes|string|max:225',
                'ruta_certificado'   => 'sometimes|string',
                'contrasena'         => 'sometimes|string|max:225',
                'fecha_inicio'       => 'sometimes|date',
                'fecha_fin'          => 'sometimes|date|after_or_equal:fecha_inicio',
                'estado'             => 'sometimes|in:vigente,vencido,revocado',
                'proveedor'          => 'sometimes|string|max:100',
            ]);

            $digitalCertificate->update($validatedData);

            return response()->json($digitalCertificate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalCertificate $digitalCertificate)
    {
        $digitalCertificate->delete();

        return response()->json(['message' => 'Digital Certificate deleted successfully.'], 204);
    }
}
