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
        $invoiceNumber = InvoiceNumber::create($request->all());

        return response()->json($invoiceNumber);
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
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceNumber $invoiceNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceNumber $invoiceNumber)
    {
        $request->validate([
            'nit' => 'required|integer|gt:0',
            'tipo_documento' => 'required|in:Factura,NotaCredito',
            'prefijo' => 'required|string|max:10',
            'numero_inicial' => 'required|integer|gt:0',
            'numero_final' => 'required|integer|gt:0',
            'fecha_resolucion' => 'required|date',
            'numero_resolucion' => 'required|string|max:50',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'estado_actual' => 'required|boolean'
        ]);

        $invoiceNumber->update($request->all());

        return response()->json([
            'message' => 'Numeración DIAN actualizada con éxito.',
            'data' => $invoiceNumber
        ], 200);
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
