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
            'IdUsuario'    => 'required|integer|exists:system_users,id',
            'Motivo'       => 'required|string|max:255',
            'TipoNota'     => 'required|in:debito,Credito',
            'Descripcion'  => 'required|string',
            'ValorTotal'   => 'required|numeric|between:0,9999999999999.99',
            'CUFENota'     => 'required|string|max:100',
            'XMLFirmado'   => 'required|string',
            'EstadoDian'   => 'required|in:aceptada,rechazada,pendiente',
            'FechaEmision' => 'nullable|date',
            'Moneda'       => 'required|string|size:3',
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
            'IdUsuario'    => 'sometimes|integer|exists:system_users,id',
            'Motivo'       => 'sometimes|string|max:255',
            'TipoNota'     => 'sometimes|in:debito,Credito',
            'Descripcion'  => 'sometimes|string',
            'ValorTotal'   => 'sometimes|numeric|between:0,9999999999999.99',
            'CUFENota'     => 'sometimes|string|max:100',
            'XMLFirmado'   => 'sometimes|string',
            'EstadoDian'   => 'sometimes|in:aceptada,rechazada,pendiente',
            'FechaEmision' => 'sometimes|nullable|date',
            'Moneda'       => 'sometimes|string|size:3',
        ]);

        // Actualiza solo los campos presentes en la petición
        $credit_debit_note->update($request->only(array_keys($request->all())));

        return response()->json($credit_debit_note);
    }

    // Eliminar una nota crédito / débito
    public function destroy(CreditDebitNote $credit_debit_note)
    {
        $credit_debit_note->delete();
        return response()->json(null, 204);
    }
}

