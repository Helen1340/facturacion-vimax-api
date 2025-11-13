<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/services?status=Active&included=measurementUnit,taxes
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Filtrar por status si se envía (por defecto solo activos si no se especifica)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Por defecto, solo servicios activos para facilitar uso en facturas
            $query->where('status', 'Active');
        }

        // Aplicar includes, filtros y ordenamiento
        $services = $query->included()->filter()->sort()->getOrPaginate();

        return response()->json($services, 200);
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

        // Asignar automáticamente la empresa del usuario logueado
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin empresa asociada'
            ], 401);
        }
        $validated['company_id'] = $user->company_id;
        
        $service = Service::create($validated);
        return response()->json($service, 201);
    }

    /**
     * Obtener solo servicios activos (útil para facturas)
     * GET /api/services/active
     */
    public function active(Request $request)
    {
        $services = Service::where('status', 'Active')
            ->with(['measurementUnit', 'taxes'])
            ->select('id', 'service_code', 'name', 'description', 'unit_price', 'measurement_unit_id', 'status')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
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
