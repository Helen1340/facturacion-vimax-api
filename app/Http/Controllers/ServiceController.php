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
        $service = Service::included()->filter()->sort()->paginate();

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
            'measurement_unit_id' => ['required', 'integer'], // FK not enforced yet
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:150'],
            'codigo_servicio' => ['required', 'string', 'max:50', 'unique:services,codigo_servicio'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ]);

        $service = Service::create($validated);
        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::findorfail($id);

        return response()->json($service, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'measurement_unit_id' => ['sometimes', 'integer'],
            'nombre' => ['sometimes', 'string', 'max:150'],
            'descripcion' => ['sometimes', 'string', 'max:150'],
            'codigo_servicio' => ['sometimes', 'string', 'max:50', 'unique:services,codigo_servicio,' . $service->id],
            'precio_unitario' => ['sometimes', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
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
