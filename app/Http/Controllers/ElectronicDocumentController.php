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
                'electronic_invoice_id' => 'required|exists:electronic_invoices,id', // FK a la factura electrónica
            'dian_numbering_id'     => 'required|exists:dian_numberings,id',      // FK a la numeración DIAN
            'credit_debit_note_id'  => 'nullable|exists:credit_debit_notes,id',   // FK a nota crédito/débito
            'cufe'                  => 'required|string|max:255|unique:electronic_documents,cufe', // Código Único de Factura Electrónica
            'cude'                  => 'required|string|max:50',                  // Código Único de Documento Electrónico
            'xml_document'          => 'required|string',                         // XML del documento electrónico
            'dian_status'           => 'required|string|max:50',                 // Estado del documento ante la DIAN
            'validation_date'       => 'nullable|date',                           // Fecha de validación del documento
            'digital_signature'     => 'nullable|string',                         // Firma digital del documento
            'document_hash'         => 'nullable|string|max:255',                // Hash del documento electrónico
            'description'           => 'nullable|string',                         // Descripción del documento
            'environment'           => ['required', Rule::in(['Pruebas', 'Producción'])], // Ambiente: Pruebas o Producción
            'document_type'         => 'required|string|max:50',                  // Tipo de documento (Factura, Nota Crédito, etc.)
            'qr_code'               => 'nullable|string',                         // Código QR del documento
            'cdr'                   => 'nullable|string',                         // Código de Respuesta de la DIAN
            'emission_mode'         => ['required', Rule::in(['normal', 'en contingencia'])], // Modo de emisión
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
            'electronic_invoice_id' => 'sometimes|required|exists:electronic_invoices,id', // FK a la factura electrónica
            'dian_numbering_id'     => 'sometimes|required|exists:dian_numberings,id',     // FK a la numeración DIAN
            'credit_debit_note_id'  => 'nullable|exists:credit_debit_notes,id',           // FK a nota crédito/débito
            'cufe'                  => 'sometimes|required|string|max:255|unique:electronic_documents,cufe,' . $electronicDocument->id, // CUFE
            'cude'                  => 'sometimes|required|string|max:50',                // CUDE
            'xml_document'          => 'sometimes|required|string',                        // XML del documento
            'dian_status'           => 'sometimes|required|string|max:50',                // Estado DIAN
            'validation_date'       => 'nullable|date',                                     // Fecha de validación
            'digital_signature'     => 'nullable|string',                                   // Firma digital
            'document_hash'         => 'nullable|string|max:255',                            // Hash
            'description'           => 'nullable|string',                                    // Descripción
            'environment'           => ['sometimes', Rule::in(['Pruebas', 'Producción'])], // Ambiente
            'document_type'         => 'sometimes|required|string|max:50',                  // Tipo de documento
            'qr_code'               => 'nullable|string',                                    // Código QR
            'cdr'                   => 'nullable|string',                                    // CDR
            'emission_mode'         => ['sometimes', Rule::in(['normal', 'en contingencia'])], // Modo de emisión
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
