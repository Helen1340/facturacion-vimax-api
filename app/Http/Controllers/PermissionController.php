<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // Lista con filtros, relaciones y paginación
    public function index()
    {
        $permissions = Permission::included()->filter()->sort()->getOrPaginate();
        return response()->json($permissions);
    }

    // Crear un nuevo permiso
    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:100',
            'descripcion'=> 'nullable|string|max:150',
        ]);

        $permission = Permission::create($request->all());

        return response()->json($permission);
    }

    // Mostrar un permiso por id
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    // Actualizar un permiso
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'nombre'     => 'sometimes|string|max:100',
            'descripcion'=> 'sometimes|nullable|string|max:150',
        ]);

        // Actualiza solo los campos que vienen en el request
        $permission->update($request->only(array_keys($request->all())));

        return response()->json($permission);
    }

    // Eliminar un permiso
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}

