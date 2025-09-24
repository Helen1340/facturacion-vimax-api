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
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'role_id' => ['nullable', 'integer'],
            'nombre' => ['required', 'string', 'max:100'],

            // validación que coincide con el enum
            'tipo_documento' => ['nullable', Rule::in(['NIT', 'CC', 'CE'])],
            'numero_documento' => ['required', 'string', 'max:50', 'unique:users,numero_documento'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'pais' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:250'],

            // corregido a 150
            'correo_electronico' => ['required', 'email', 'max:150', 'unique:users,correo_electronico'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
            'ultimo_acceso' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8'],
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
            'role_id' => ['sometimes', 'integer'], // Validar existencia cuando roles esté disponible
            'nombre' => ['sometimes', 'string', 'max:100'],
            'tipo_documento' => ['sometimes', 'string', 'max:20'],
            'numero_documento' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('users', 'numero_documento')->ignore($user->id),
            ],
            'direccion' => ['sometimes', 'string', 'max:255'],
            'pais' => ['sometimes', 'string', 'max:100'],
            'descripcion' => ['sometimes', 'string'],
            'correo_electronico' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'correo_electronico')->ignore($user->id),
            ],
            'telefono' => ['sometimes', 'string', 'max:20'],
            'estado' => ['sometimes', Rule::in(['Activo', 'Inactivo'])],
            'ultimo_acceso' => ['sometimes', 'date'],
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
