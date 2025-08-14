<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // Listar roles
    public function index()
    {
        $roles = Role::included()->filter()->sort()->getOrPaginate();
        return response()->json($roles);
    }

    // Crear nuevo rol
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:roles,name',
            'descripcion' => 'nullable|max:255',
        ]);

        $role = Role::create($request->all());
        return response()->json($role, 201);
    }

    // Mostrar rol por id
    public function show($id)
    {
        $role = Role::included()->findOrFail($id);
        return response()->json($role);
    }

    // Actualizar rol
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nombre' => 'sometimes|max:255|unique:roles,name,' . $role->id,
            'descripcion' => 'sometimes|max:255',
        ]);

        $role->update($request->only(array_keys($request->all())));

        return response()->json($role);
    }

    // Eliminar rol
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(null, 204);
    }
}
