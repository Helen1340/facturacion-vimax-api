<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // lista con filtros, relaciones y paginacion
    public function index()
    {
        $payments = Payment::included()->filter()->sort()->getOrPaginate();
        return response()->json($payments);
    }

    // crear un nuevo pago
    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago' => 'nullable|date',
            'valor_pagado' => 'required|numeric',
            'moneda' => 'required|string|max:3',
            'medio_pago' => 'required|string|max:50',
        ]);

        $payment = Payment::create($request->all());
        return response()->json($payment);
    }

    // mostrar un pago por id
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
    }

    // actualizar un pago
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'fecha_pago' => 'sometimes|nullable|date',
            'valor_pagado' => 'sometimes|numeric',
            'moneda' => 'sometimes|string|max:3',
            'medio_pago' => 'sometimes|string|max:50',
        ]);

        // Actualiza solo los campos que vienen en el request
        $payment->update($request->only(array_keys($request->all())));
        //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
        //$paymet->update($request->all()); // Linea del Repositorio del Instrucor
        return response()->json($payment);
    }

    // eliminar un pago
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(null, 204);
    }
}
