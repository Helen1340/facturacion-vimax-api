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
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|string|max:50',
            'porcentaje_base' => 'required|numeric|between:0,999.99',
            'estado' => 'required|in:Activo,Inactivo',
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
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'sometimes|nullable|string',
            'tipo' => 'sometimes|string|max:50',
            'porcentaje_base' => 'sometimes|numeric|between:0,999.99',
            'estado' => 'sometimes|in:Activo,Inactivo',
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
