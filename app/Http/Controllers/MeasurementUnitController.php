<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

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
            'nombre'          => 'required|string|max:100',
            'estado'          => 'required|in:Activo,Inactivo',
            'codigo_dian'     => 'required|string|max:10|unique:measurement_units,codigo_dian',
            'descripcion'     => 'nullable|string',
            'tipo_aplicacion' => 'required|in:Producto,Servicio',
        ]);

        $unit = MeasurementUnit::create($request->all());
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
            'nombre'          => 'sometimes|string|max:100',
            'estado'          => 'sometimes|in:Activo,Inactivo',
            'codigo_dian'     => 'sometimes|string|max:10|unique:measurement_units,codigo_dian,' . $measurementUnit->id,
            'descripcion'     => 'nullable|string',
            'tipo_aplicacion' => 'sometimes|in:Producto,Servicio',
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
