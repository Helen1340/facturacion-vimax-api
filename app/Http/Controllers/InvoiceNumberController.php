<?php

namespace App\Http\Controllers;

use App\Models\InvoiceNumber;
use Illuminate\Http\Request;

class InvoiceNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoiceNumber = InvoiceNumber::included()->filter()->sort()->paginate();

        return response()->json($invoiceNumber);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $validatedData = $request->validate([
                'nit' => 'required|integer|gt:0',
                'tipo_documento' => 'required|in:Factura,NotaCredito',
                'prefijo' => 'required|string|max:10',
                'numero_inicial' => 'required|integer|gt:0',
                'numero_final' => 'required|integer|gt:0',
                'fecha_resolucion' => 'required|date',
                'numero_resolucion' => 'required|string|max:50',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'estado_actual' => 'required|boolean'
            ]);

            $invoiceNumber = InvoiceNumber::create($validatedData);

            return response()->json($invoiceNumber, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoiceNumber = InvoiceNumber::findOrFail($id);

        return response()->json($invoiceNumber);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceNumber $invoiceNumber)
    {
            $validatedData = $request->validate([
                'nit' => 'sometimes|integer|gt:0',
                'tipo_documento' => 'sometimes|in:Factura,NotaCredito',
                'prefijo' => 'sometimes|string|max:10',
                'numero_inicial' => 'sometimes|integer|gt:0',
                'numero_final' => 'sometimes|integer|gt:0',
                'fecha_resolucion' => 'sometimes|date',
                'numero_resolucion' => 'sometimes|string|max:50',
                'fecha_inicio' => 'sometimes|date',
                'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
                'estado_actual' => 'sometimes|boolean'
            ]);

            $invoiceNumber->update($validatedData);

            return response()->json($invoiceNumber);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceNumber $invoiceNumber)
    {
        $invoiceNumber->delete();

        return response()->json(['message' => 'Invoice Number deleted successfully.'], 204);
    }
}
