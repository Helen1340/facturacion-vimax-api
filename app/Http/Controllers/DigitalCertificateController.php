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
        $digitalCertificate = DigitalCertificate::create($request->all());

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
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalCertificate $digitalCertificate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalCertificate $digitalCertificate)
    {
        $validatedData = $request->validate([
            'NIT'               => 'sometimes|nullable|exists:empresas,NIT',
            'Nombre_Certificado'=> 'sometimes|string|max:225',
            'Ruta_Certificado'  => 'sometimes|string',
            'Contrasena'        => 'sometimes|string|max:225',
            'Fecha_Inicio'      => 'sometimes|date',
            'Fecha_Fin'         => [
                'sometimes',
                'date',
                Rule::when(
                    $request->has('Fecha_Inicio'),
                    ['after_or_equal:Fecha_Inicio']
                ),
            ],
            'Estado'            => 'sometimes|in:Vigente,Vencido,Revocado',
            'Proveedor'         => 'sometimes|string|max:100',
        ]);

        $digitalCertificate->update($validatedData);

        return $digitalCertificate;
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
