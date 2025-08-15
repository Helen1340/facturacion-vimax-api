<?php

namespace App\Http\Controllers;

use App\Models\CreditDebitNote;
use Illuminate\Http\Request;

class CreditDebitNoteController extends Controller
{
    // Lista con filtros, relaciones y paginación
    public function index()
    {
        $credit_debit_notes = CreditDebitNote::included()->filter()->sort()->getOrPaginate();
        return response()->json($credit_debit_notes);
    }

    // Crear una nueva nota crédito / débito
    public function store(Request $request)
    {
        $request->validate([
            'motivo'       => 'required|string|max:255',
            'tipoNota'     => 'required|in:debito,Credito',
            'descripcion'  => 'required|string',
            'valor_total'   => 'required|numeric|between:0,9999999999999.99',
            'cufe_nota'     => 'required|string|max:100',
            'xml_firmado'   => 'required|string',
            'estado_dian'   => 'required|in:aceptada,rechazada,pendiente',
            'fecha_emision' => 'nullable|date',
            'moneda'       => 'required|string|size:3',
        ]);

        $credit_debit_note = CreditDebitNote::create($request->all());

        return response()->json($credit_debit_note);
    }

    // Mostrar una nota crédito / débito por id
    public function show($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        return response()->json($credit_debit_note);
    }

    // Actualizar una nota crédito / débito
    public function update(Request $request, CreditDebitNote $credit_debit_note)
    {
        $request->validate([
            'motivo'       => 'sometimes|string|max:255',
            'tipo_nota'     => 'sometimes|in:debito,Credito',
            'descripcion'  => 'sometimes|string',
            'valor_total'   => 'sometimes|numeric|between:0,9999999999999.99',
            'cufe_nota'     => 'sometimes|string|max:100',
            'xml_firmado'   => 'sometimes|string',
            'estado_dian'   => 'sometimes|in:aceptada,rechazada,pendiente',
            'fecha_emision' => 'sometimes|nullable|date',
            'moneda'       => 'sometimes|string|size:3',
        ]);

        // Actualiza solo los campos presentes en la petición
        $credit_debit_note->update($request->only(array_keys($request->all())));

        return response()->json($credit_debit_note);
    }

    // Eliminar una nota crédito / débito
    public function destroy($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        $credit_debit_note->delete();
        return response()->json(null, 204);
    }
}

