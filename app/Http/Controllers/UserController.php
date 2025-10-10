<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    
    public function index()
    {
        $user = User::included()->filter()->sort()->getOrPaginate();

        return response()->json($user);
    }

    
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'], // ID de la empresa a la que pertenece
            'role_id' => ['nullable', 'integer'], // ID del rol del usuario
            'name' => ['required', 'string', 'max:100'], // Nombre completo del usuario
            'document_type' => ['nullable', Rule::in(['NIT', 'CC', 'CE'])], // Tipo de documento
            'document_number' => ['required', 'string', 'max:50', 'unique:users,document_number'], // Número de documento
            'address' => ['nullable', 'string', 'max:150'], // Dirección del usuario
            'country' => ['nullable', 'string', 'max:100'], // País del usuario
            'description' => ['nullable', 'string', 'max:250'], // Información adicional
            'email' => ['required', 'email', 'max:150', 'unique:users,email'], // Correo electrónico
            'phone' => ['nullable', 'string', 'max:20'], // Teléfono
            'status' => ['nullable', Rule::in(['Activo', 'Inactivo'])], // Estado del usuario
            'last_access' => ['nullable', 'date'], // Último acceso
            'password' => ['required', 'string', 'min:8'], // Contraseña
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'role_id' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string', 'max:100'],
            'document_type' => ['sometimes', Rule::in(['NIT', 'CC', 'CE'])],
            'document_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('users', 'document_number')->ignore($user->id),
            ],
            'address' => ['sometimes', 'string', 'max:150'],
            'country' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'string', 'max:250'],
            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['sometimes', 'string', 'max:20'],
            'status' => ['sometimes', Rule::in(['Activo', 'Inactivo'])],
            'last_access' => ['sometimes', 'date'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        // Si se envía la password, se encripta
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $user;
    }
}
