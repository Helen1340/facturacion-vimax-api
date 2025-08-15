<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::included()->filter()->sort()->paginate();

        return response()->json($taxes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $validatedData = $request->validate([
                'codigo_dian'      => 'required|string|max:20|unique:taxes,codigo_dian',
                'nombre'           => 'required|string|max:100',
                'descripcion'      => 'nullable|string',
                'tipo_aplicacion'  => 'required|in:trasladado,retenido',
                'porcentaje_base'  => 'required|numeric|min:0|max:100',
                'estado'           => 'boolean',
            ]);

            $taxe = Tax::create($validatedData);

            return response()->json($taxe);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $taxe = Tax::findOrFail($id);

        return response()->json($taxe);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
            $validatedData = $request->validate([
                'codigo_dian'      => 'sometimes|string|max:20|unique:taxes,codigo_dian,' . $tax->id,
                'nombre'           => 'sometimes|string|max:100',
                'descripcion'      => 'sometimes|nullable|string',
                'tipo_aplicacion'  => 'sometimes|in:trasladado,retenido',
                'porcentaje_base'  => 'sometimes|numeric|min:0|max:100',
                'estado'           => 'sometimes|boolean',
            ]);

            $tax->update($validatedData);

            return response()->json($tax);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return $tax;
    }
}
