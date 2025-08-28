<?php

namespace App\Http\Controllers;
use App\Models\CreditDebitNote;

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
            'motivo'           => 'required|string|max:250',
            'tipo_documento'   => 'required|in:debito,credito',
            'descripcion'      => 'required|string|max:250',
            'numero_nota'      => 'required|string|max:50|unique:credit_debit_notes,numero_nota', // numero_nota debe ser único
            'estado'           => 'required|in:aceptada,rechazada,pendiente',
            'fecha_emision'    => 'required|date',
            'valor_total'      => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/', // Decimal(18,2) con hasta 2 decimales
        ]);

        $credit_debit_note = CreditDebitNote::create($request->all());
        return response()->json($credit_debit_note, 201);
    }

    public function show($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        return response()->json($credit_debit_note);
    }

    public function update(Request $request, CreditDebitNote $credit_debit_note)
    {
        $request->validate([
            'motivo'           => 'sometimes|required|string|max:250',
            'tipo_documento'   => 'sometimes|required|in:debito,credito',
            'descripcion'      => 'sometimes|required|string|max:250',
            'numero_nota'      => 'sometimes|required|string|max:50|unique:credit_debit_notes,numero_nota,' . $credit_debit_note->id, // unique ignorando el ID actual
            'estado'           => 'sometimes|required|in:aceptada,rechazada,pendiente',
            'fecha_emision'    => 'sometimes|required|date',
            'valor_total'      => 'sometimes|required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $credit_debit_note->update($request->only(array_keys($request->all())));

        return response()->json($credit_debit_note);
    }

    public function destroy($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        $credit_debit_note->delete();
        return response()->json(null, 204);
    }
}
