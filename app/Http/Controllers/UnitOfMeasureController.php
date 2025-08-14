<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;

class UnitOfMeasureController extends Controller
{
    // lista con filtros, relaciones y paginación
    public function index()
    {
        $unit_of_measures = UnitOfMeasure::included()->filter()->sort()->getOrPaginate();
        return response()->json($unit_of_measures);
    }

    // crear una nueva unidad de medida
    public function store(Request $request)
    {
        $request->validate([
            'IdUnidadMedida' => 'required|integer|unique:unit_of_measures,IdUnidadMedida',
            'Nombre' => 'required|string|max:150',
            'Estado' => 'required|boolean',
            'CodioDIAN' => 'required|string|max:50',
            'Descripcion' => 'nullable|string|max:150',
        ]);

        $unit_of_measure = UnitOfMeasure::create($request->all());
        return response()->json($unit_of_measure);
    }

    // mostrar una unidad de medida por id
    public function show($id)
    {
        $unit_of_measure = UnitOfMeasure::findOrFail($id);
        return response()->json($unit_of_measure);
    }

    // actualizar una unidad de medida
    public function update(Request $request, UnitOfMeasure $unit_of_measure)
    {
        $request->validate([
            'IdUnidadMedida' => 'sometimes|integer|unique:unit_of_measures,IdUnidadMedida,' . $unit_of_measure->IdUnidadMedida,
            'Nombre' => 'sometimes|string|max:150',
            'Estado' => 'sometimes|boolean',
            'CodioDIAN' => 'sometimes|string|max:50',
            'Descripcion' => 'sometimes|nullable|string|max:150',
        ]);

        // Actualiza solo los campos que vienen en el request
        $unit_of_measure->update($request->only(array_keys($request->all())));


         //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
        //$company->update($request->all()); // Linea del Repositorio del Instrucor


        return response()->json($unit_of_measure);
    }

    // eliminar una unidad de medida
    public function destroy(UnitOfMeasure $unit_of_measure)
    {
        $unit_of_measure->delete();
        return response()->json(null, 204);
    }
}
