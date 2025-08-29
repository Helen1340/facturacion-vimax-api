<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::included()->filter()->sort()->getOrPaginate();

        return response()->json($roles);
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
                'nombre' => ['required', Rule::in(['administrador', 'facturador', 'contador', 'cliente'])],
                'descripcion' => ['nullable', 'string', 'max:255'],
                'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            ]);

            $role = Role::create($validated);

            return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
            $validated = $request->validate([
                'nombre' => ['sometimes', Rule::in(['administrador', 'facturador', 'contador', 'cliente'])],
                'descripcion' => ['sometimes', 'string', 'max:255'],
                'estado' => ['sometimes', Rule::in(['activo', 'inactivo'])],
            ]);

            $role->update($validated);

            return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json($role);
    }
}
