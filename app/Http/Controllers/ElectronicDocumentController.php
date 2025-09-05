<?php

namespace App\Http\Controllers;

use App\Models\ElectronicDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ElectronicDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $electronicDocument = ElectronicDocument::included()->filter()->sort()->getOrPaginate();

        return response()->json($electronicDocument);
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

        $validated = $request->validate([
                'ElectronicInvoice_id' => 'required|exists:electronic_invoices,id',
                'DianNumbering_id'     => 'required|exists:dian_numberings,id',
                'CreditDebitNote_id'   => 'nullable|exists:credit_debit_notes,id',
                'cufe'                 => 'required|string|max:255|unique:electronic_documents,cufe',
                'cude'                 => 'required|string|max:50',
                'xml_documento'        => 'required|string',
                'estado_dian'          => 'required|string|max:50',
                'fecha_validacion'     => 'nullable|date',
                'firma_digital'        => 'nullable|string',
                'hash_documento'       => 'nullable|string|max:255',
                'descripcion'          => 'nullable|string',
                'ambiente'             => ['required', Rule::in(['Pruebas', 'Producción'])],
                'tipo_documento'       => 'required|string|max:50',
                'qr_codigo'            => 'nullable|string',
                'cdr'                  => 'nullable|string',
                'modo_emision'         => ['required', Rule::in(['normal', 'en contingencia'])],
        ]);

        $electronicDocument = ElectronicDocument::create($validated);


        return response()->json($electronicDocument);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $electronicDocument = ElectronicDocument::findOrFail($id);

        return response()->json($electronicDocument);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectronicDocument $electronicDocument)
    {
        $validated = $request->validate([
                'ElectronicInvoice_id' => 'sometimes|required|exists:electronic_invoices,id',
                'DianNumbering_id'     => 'sometimes|required|exists:dian_numberings,id',
                'CreditDebitNote_id'   => 'nullable|exists:credit_debit_notes,id',
                'cufe'                 => 'sometimes|required|string|max:255|unique:electronic_documents,cufe,' . $electronicDocument->id,
                'cude'                 => 'sometimes|required|string|max:50',
                'xml_documento'        => 'sometimes|required|string',
                'estado_dian'          => 'sometimes|required|string|max:50',
                'fecha_validacion'     => 'nullable|date',
                'firma_digital'        => 'nullable|string',
                'hash_documento'       => 'nullable|string|max:255',
                'descripcion'          => 'nullable|string',
                'ambiente'             => ['sometimes', Rule::in(['Pruebas', 'Producción'])],
                'tipo_documento'       => 'sometimes|required|string|max:50',
                'qr_codigo'            => 'nullable|string',
                'cdr'                  => 'nullable|string',
                'modo_emision'         => ['sometimes', Rule::in(['normal', 'en contingencia'])],
        ]);

        $electronicDocument->update($validated);

        return response()->json($electronicDocument);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectronicDocument $electronicDocument)
    {
        $electronicDocument->delete();

        return response()->json($electronicDocument);
    }
}
