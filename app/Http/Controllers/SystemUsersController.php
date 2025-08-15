<?php

namespace App\Http\Controllers;

use App\Models\SystemUser;
use App\Models\SystemUsers;
use Illuminate\Http\Request;

class SystemUsersController extends Controller
{
    // Lista con filtros, relaciones y paginación
    public function index()
    {
        $system_users = SystemUsers::included()->filter()->sort()->getOrPaginate();
        return response()->json($system_users);
    }

    // Crear un nuevo usuario del sistema
    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'rol' => 'required|in:Admin,Facturador',
            'contrasena' => 'required|string|max:225|min:8',
            'correo_electronico' => 'required|email|max:100|unique:system_users,correo_electronico',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|boolean',
            'ultimo_acceso' => 'nullable|date',
            'numero_identificacion' => 'required|string|max:15|unique:system_users,numero_identificacion'
        ]);
        $system_user = SystemUsers::create($request->all());

        return response()->json($system_user);
    }

    // Mostrar un usuario del sistema por ID
    public function show($id)
    {
        $system_user = SystemUsers::findOrFail($id);
        return response()->json($system_user);
    }

    // Actualizar usuario
    public function update(Request $request, SystemUsers $system_user)
    {
        $request->validate([
            'nombre_completo' => 'sometimes|string|max:100',
            'rol' => 'sometimes|in:Admin,Facturador',
            'contrasena' => 'sometimes|string|max:225|min:8',
            'correo_electronico' => 'sometimes|email|max:100|unique:system_users,correo_electronico,' . $system_user->id,
            'telefono' => 'sometimes|nullable|string|max:20',
            'estado' => 'sometimes|boolean',
            'ultimo_acceso' => 'sometimes|date',
            'numero_identificacion' => 'sometimes|string|max:15|unique:system_users,numero_identificacion,' . $system_user->id
        ]);

        // Actualiza solo los campos que vienen en el request
    $system_user->update($request->only(array_keys($request->all())));

    //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
    //$company->update($request->all()); // Linea del Repositorio del Instrucor
        return response()->json($system_user);
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $system_user = SystemUsers::findOrFail($id);
        $system_user->delete();
        return response()->json(null, 204);
    }
}
