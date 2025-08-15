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
            'Numero_Factura' => 'required|integer|exists:facturas_electronicas,NumeroFactura',
            'FechaPago' => 'nullable|date',
            'ValorPagado' => 'required|numeric',
            'Moneda' => 'required|string|max:3',
            'MedioPago' => 'required|string|max:50',
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
            'Numero_Factura' => 'sometimes|integer|exists:facturas_electronicas,NumeroFactura',
            'FechaPago' => 'sometimes|nullable|date',
            'ValorPagado' => 'sometimes|numeric',
            'Moneda' => 'sometimes|string|max:3',
            'MedioPago' => 'sometimes|string|max:50',
        ]);

        // Actualiza solo los campos que vienen en el request
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
