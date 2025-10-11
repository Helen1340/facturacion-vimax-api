<?php

namespace App\Http\Controllers;
use App\Models\DianNumbering;

use Illuminate\Http\Request;

class DianNumberingController extends Controller
{
    public function index()
    {
        $dian_numberings = DianNumbering::included()->filter()->sort()->getOrPaginate();
        return response()->json($dian_numberings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id'           => 'required|exists:companies,id',                // ID de la compañía
            'document_type'        => 'required|in:Factura,NotaCredito,NotaDebito', // Tipo de documento DIAN
            'document_type_code'   => 'nullable|string|max:10',                       // Código oficial DIAN
            'prefix'               => 'required|string|max:10',                       // Prefijo de numeración
            'start_number'         => 'required|numeric|min:0',                       // Número inicial autorizado
            'end_number'           => 'required|numeric|min:' . ($request->input('start_number') ?? 0), // Número final autorizado
            'resolution_date'      => 'required|date',                                 // Fecha resolución DIAN
            'resolution_number'    => 'required|string|max:50',                        // Número resolución DIAN
            'validity_start_date'  => 'required|date',                                  // Fecha inicio vigencia
            'validity_end_date'    => 'required|date|after_or_equal:validity_start_date', // Fecha fin vigencia
            'current_status'       => 'required|in:Activo,Inactivo',                  // Estado actual
            'environment'          => 'nullable|in:Pruebas,Producción',               // Ambiente
            'description'          => 'nullable|string|max:255',                      // Descripción opcional
        ]);

        $dian_numbering = DianNumbering::create($request->all());
        return response()->json($dian_numbering, 201);
    }

    public function show($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        return response()->json($dian_numbering);
    }

    public function update(Request $request, DianNumbering $dianNumbering)
    {
    $request->validate([
        'company_id'           => 'sometimes|exists:companies,id',                // ID de la compañía
        'document_type'        => 'sometimes|in:Factura,NotaCredito,NotaDebito',
        'document_type_code'   => 'sometimes|string|max:10',
        'prefix'               => 'sometimes|string|max:10',
        'start_number'         => 'sometimes|numeric|min:0',
        'end_number'           => 'sometimes|numeric|min:' . ($request->input('start_number') ?? 0),
        'resolution_date'      => 'sometimes|date',
        'resolution_number'    => 'sometimes|string|max:50',
        'validity_start_date'  => 'sometimes|date',
        'validity_end_date'    => 'sometimes|date|after_or_equal:validity_start_date',
        'current_status'       => 'sometimes|in:Activo,Inactivo',
        'environment'          => 'sometimes|in:Pruebas,Producción',
        'description'          => 'sometimes|string|max:255',
    ]);

    $dianNumbering->update($request->only(array_keys($request->all())));

    return response()->json($dianNumbering);
}

    public function destroy($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        $dian_numbering->delete();
        return response()->json(null, 204);
    }
}
