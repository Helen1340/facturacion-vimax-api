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
            'IdUsuario' => 'required|string|unique:system_users,IdUsuario',
            'NombreCompleto' => 'required|string|max:100',
            'Rol' => 'required|in:Admin,Facturador',
            'Contrasena' => 'required|string|max:225|min:8',
            'CorreoElectronico' => 'required|email|max:100|unique:system_users,CorreoElectronico',
            'Telefono' => 'nullable|string|max:20',
            'Estado' => 'required|boolean',
            'UltimoAcceso' => 'nullable|date',
            'NumeroIdentificacion' => 'required|string|max:15|unique:system_users,NumeroIdentificacion'
        ]);

        // Encriptar contraseña antes de guardar
        $data = $request->all();
        $data['Contrasena'] = bcrypt($data['Contrasena']);

        $system_user = SystemUsers::create($data);

        return response()->json($system_user, 201);
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
            'IdUsuario' => 'sometimes|string|unique:system_users,IdUsuario,' . $system_user->id,
            'NombreCompleto' => 'sometimes|string|max:100',
            'Rol' => 'sometimes|in:Admin,Facturador',
            'Contrasena' => 'sometimes|string|max:225|min:8',
            'CorreoElectronico' => 'sometimes|email|max:100|unique:system_users,CorreoElectronico,' . $system_user->id,
            'Telefono' => 'sometimes|nullable|string|max:20',
            'Estado' => 'sometimes|boolean',
            'UltimoAcceso' => 'sometimes|date',
            'NumeroIdentificacion' => 'sometimes|string|max:15|unique:system_users,NumeroIdentificacion,' . $system_user->id
        ]);

        $data = $request->only(array_keys($request->all()));

        if (isset($data['Contrasena'])) {
            $data['Contrasena'] = bcrypt($data['Contrasena']);
        }

        $system_user->update($data);

        return response()->json($system_user);
    }

    // Eliminar usuario
    public function destroy(SystemUsers $system_user)
    {
        $system_user->delete();
        return response()->json(null, 204);
    }
}
