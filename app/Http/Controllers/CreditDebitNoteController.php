<?php

namespace App\Http\Controllers;
use App\Models\CreditDebitNote;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;

class CreditDebitNoteController extends Controller
{
    public function index()
    {
        $credit_debit_notes = CreditDebitNote::included()->filter()->sort()->getOrPaginate();
        return response()->json($credit_debit_notes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'electronic_invoice_id' => 'required|exists:electronic_invoices,id', // FK a la factura electrónica
            'reason'                => 'required|string|max:250',                // Motivo de la nota
            'note_type'             => ['required', Rule::in(['debit','credit'])], // Tipo: débito o crédito
            'description'           => 'nullable|string|max:250',                 // Descripción
            'note_number'           => 'required|string|max:50|unique:credit_debit_notes,note_number', // Número de nota único
            'status'                => ['required', Rule::in(['accepted','rejected','pending'])], // Estado
            'issue_date'            => 'required|date',                             // Fecha de emisión
            'total_amount'          => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/', // Valor total decimal
        ]);

        $credit_debit_note = CreditDebitNote::create($request->all());
        return response()->json($credit_debit_note, 201);
    }

    public function show($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        return response()->json($credit_debit_note);
    }

    public function update(Request $request, CreditDebitNote $creditDebitNote)
    {
        $request->validate([
            'electronic_invoice_id' => 'sometimes|required|exists:electronic_invoices,id',
            'reason'                => 'sometimes|required|string|max:250',
            'note_type'             => ['sometimes','required', Rule::in(['debit','credit'])],
            'description'           => 'sometimes|nullable|string|max:250',
            'note_number'           => 'sometimes|required|string|max:50|unique:credit_debit_notes,note_number,' . $creditDebitNote->id,
            'status'                => ['sometimes','required', Rule::in(['accepted','rejected','pending'])],
            'issue_date'            => 'sometimes|required|date',
            'total_amount'          => 'sometimes|required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $creditDebitNote->update($request->only(array_keys($request->all())));

        return response()->json($creditDebitNote);
    }

    public function destroy($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        $credit_debit_note->delete();
        return response()->json(null, 204);
    }
}
