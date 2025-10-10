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
            'name'       => 'required|string|max:100', // nombre
            'dian_code'  => 'required|string|max:10|unique:payment_methods,dian_code', // código DIAN
            'description'=> 'nullable|string|max:250', // descripción
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
            'name'       => 'sometimes|string|max:100', // nombre
            'dian_code'  => 'sometimes|string|max:10|unique:payment_methods,dian_code,' . $paymentMethod->id, // código DIAN
            'description'=> 'nullable|string|max:250', // descripción
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
