<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::included()->filter()->sort()->getOrPaginate();

        return response()->json($service, 200);
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
        $validated = $request->validate([
            'measurement_unit_id' => ['required', 'integer', 'exists:measurement_units,id'], // unidad de medida (FK)
            'name'                => ['required', 'string', 'max:150'], // nombre del servicio
            'description'         => ['nullable', 'string', 'max:150'], // descripción
            'service_code'        => ['required', 'string', 'max:50', 'unique:services,service_code'], // código del servicio
            'unit_price'          => ['required', 'numeric', 'min:0'], // precio unitario
            'status'              => ['required', 'in:Active,Inactive'], // estado
        ]);

        $service = Service::create($validated);
        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);

        return response()->json($service, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'measurement_unit_id' => ['sometimes', 'integer', 'exists:measurement_units,id'], // unidad de medida (FK)
            'name'                => ['sometimes', 'string', 'max:150'], // nombre del servicio
            'description'         => ['nullable', 'string', 'max:150'], // descripción
            'service_code'        => ['sometimes', 'string', 'max:50', 'unique:services,service_code,' . $service->id], // código del servicio
            'unit_price'          => ['sometimes', 'numeric', 'min:0'], // precio unitario
            'status'              => ['sometimes', 'in:Active,Inactive'], // estado
        ]);

        $service->update($validated);
        return response()->json($service, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json($service, 204);
    }
}
