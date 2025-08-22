<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    // lista con filtros, relaciones y paginación
    public function index()
    {
        $methods = PaymentMethod::included()->filter()->sort()->getOrPaginate();
        return response()->json($methods);
    }

    // crear un nuevo método de pago
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'codigo_dian' => 'required|string|max:10|unique:payment_methods,codigo_dian',
            'descripcion' => 'nullable|string|max:250',
        ]);

        $method = PaymentMethod::create($request->all());
        return response()->json($method, 201);
    }

    // mostrar un método de pago por id
    public function show($id)
    {
        $method = PaymentMethod::findOrFail($id);
        return response()->json($method);
    }

    // actualizar un método de pago
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'codigo_dian' => 'sometimes|string|max:10|unique:payment_methods,codigo_dian,' . $paymentMethod->id,
            'descripcion' => 'nullable|string|max:250',
        ]);

        $paymentMethod->update($request->only(array_keys($request->all())));

        return response()->json($paymentMethod);
    }

    // eliminar un método de pago
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return response()->json(null, 204);
    }
}
