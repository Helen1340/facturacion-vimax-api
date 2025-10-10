<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    // lista con filtros, relaciones y paginación
    public function index()
    {
        $taxes = Tax::included()->filter()->sort()->getOrPaginate();
        return response()->json($taxes);
    }

    // crear un nuevo impuesto
    public function store(Request $request)
    {
        $request->validate([
            'tax_code'       => 'required|string|max:50',   // Código único del tributo
            'name'           => 'required|string|max:100',  // Nombre del tributo
            'description'    => 'nullable|string',          // Descripción del tributo
            'type'           => 'required|string|max:50',   // Tipo: impuesto, retención, contribución
            'percentage'     => 'nullable|numeric|between:0,999.99', // Porcentaje aplicado si aplica
            'fixed_value'    => 'nullable|numeric|min:0',   // Valor fijo si aplica
            'application_type'=> 'required|string|in:Percentage,FixedValue,Retention', // Tipo de aplicación
            'min_value'      => 'nullable|numeric|min:0',   // Valor mínimo aplicable
            'max_value'      => 'nullable|numeric|min:0',   // Valor máximo aplicable
            'status'         => 'required|in:Active,Inactive', // Estado del tributo
        ]);

        $tax = Tax::create($request->all());
        return response()->json($tax);
    }

    // mostrar un impuesto por id
    public function show($id)
    {
        $tax = Tax::findOrFail($id);
        return response()->json($tax);
    }

    // actualizar un impuesto
    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'tax_code'       => 'sometimes|string|max:50',
            'name'           => 'sometimes|string|max:100',
            'description'    => 'sometimes|nullable|string',
            'type'           => 'sometimes|string|max:50',
            'percentage'     => 'sometimes|numeric|between:0,999.99',
            'fixed_value'    => 'sometimes|numeric|min:0',
            'application_type'=> 'sometimes|string|in:Percentage,FixedValue,Retention',
            'min_value'      => 'sometimes|numeric|min:0',
            'max_value'      => 'sometimes|numeric|min:0',
            'status'         => 'sometimes|in:Active,Inactive',
        ]);

        // Actualiza solo los campos presentes en la request
        $tax->update($request->only(array_keys($request->all())));

        return response()->json($tax);
    }

    // eliminar un impuesto
    public function destroy(Tax $tax)
    {
        $tax->delete();
        return response()->json(null, 204);
    }
}
