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
            'id_unidad_medida' => 'required|integer|unique:unit_of_measures,id_unidad_medida',
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
            'codio_dian' => 'required|string|max:10',
            'descripcion' => 'nullable|text',
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
            'id_unidad_medida' => 'sometimes|integer|unique:unit_of_measures,IdUnidadMedida,' . $unit_of_measure->IdUnidadMedida,
            'nombre' => 'sometimes|string|max:150',
            'estado' => 'sometimes|boolean',
            'codio_dian' => 'sometimes|string|max:50',
            'descripcion' => 'sometimes|nullable|string|max:150',
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
