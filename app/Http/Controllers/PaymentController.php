<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // lista con filtros, relaciones y paginación
    public function index()
    {
        $payments = Payment::included()->filter()->sort()->getOrPaginate();
        return response()->json($payments);
    }

    // crear un nuevo pago
    public function store(Request $request)
    {
    $request->validate([
        'electronic_invoice_id' => 'required|exists:electronic_invoices,id', // FK factura
            'payment_method_id'     => 'required|exists:payment_methods,id',     // FK método
            'payment_date'          => 'required|date',                         // fecha de pago
            'amount_paid'           => 'required|numeric|min:0',                // valor pagado
            'currency'              => 'required|string|max:3',                 // moneda
            'payment_reference'     => 'nullable|string|max:255',               // referencia
    ]);

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
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
            'electronic_invoice_id' => 'sometimes|exists:electronic_invoices,id',
            'payment_method_id'     => 'sometimes|exists:payment_methods,id',
            'payment_date'          => 'sometimes|date',
            'amount_paid'           => 'sometimes|numeric|min:0',
            'currency'              => 'sometimes|string|max:3',
            'payment_reference'     => 'nullable|string|max:255',
        ]);

        $payment->update($request->only(array_keys($request->all())));

        return response()->json($payment);
    }

    // eliminar un pago
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }
}
