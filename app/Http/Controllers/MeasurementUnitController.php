<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementUnitController extends Controller
{
    // lista con filtros, relaciones y paginación
    public function index()
    {
        $units = MeasurementUnit::included()->filter()->sort()->getOrPaginate();
        return response()->json($units);
    }

    // crear una nueva unidad de medida
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',          // Nombre
            'status'           => 'required|in:Active,Inactive',       // Estado
            'dian_code'        => 'required|string|max:10|unique:measurement_units,dian_code', // Código DIAN
            'description'      => 'nullable|string',                   // Descripción
            'application_type' => 'required|in:Product,Service',       // Tipo de aplicación
        ]);

        $data = $request->all();
        // Asignar automáticamente la empresa del usuario logueado
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin empresa asociada'
            ], 401);
        }
        $data['company_id'] = $user->company_id;
        
        $unit = MeasurementUnit::create($data);
        return response()->json($unit, 201);
    }

    // mostrar una unidad de medida por id
    public function show($id)
    {
        $unit = MeasurementUnit::findOrFail($id);
        return response()->json($unit);
    }

    // actualizar una unidad de medida
    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        $request->validate([
            'name'             => 'sometimes|string|max:100',          // Nombre
            'status'           => 'sometimes|in:Active,Inactive',       // Estado
            'dian_code'        => 'sometimes|string|max:10|unique:measurement_units,dian_code,' . $measurementUnit->id, // Código DIAN
            'description'      => 'nullable|string',                   // Descripción
            'application_type' => 'sometimes|in:Product,Service',       // Tipo de aplicación
        ]);

        $measurementUnit->update($request->only(array_keys($request->all())));

        return response()->json($measurementUnit);
    }

    // eliminar una unidad de medida
    public function destroy(MeasurementUnit $measurementUnit)
    {
        $measurementUnit->delete();
        return response()->json(null, 204);
    }
}
