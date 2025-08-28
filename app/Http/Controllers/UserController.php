<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::included()->filter()->sort()->paginate();

        return response()->json($user);
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
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'role_id' => ['nullable', 'integer'], // Validar existencia cuando roles esté disponible
            'nombre' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['nullable', 'string', 'max:20'],
            'numero_documento' => ['required', 'string', 'max:50', 'unique:users,numero_documento'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'pais' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'correo_electronico' => ['required', 'email', 'max:150', 'unique:users,correo_electronico'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
            'ultimo_acceso' => ['nullable', 'date'],
            'contrasena' => ['required', 'string', 'min:8'],
        ]);

        // se usa hash:make para guardar la contrasena encriptada
        $user = User::create([
            ...$validated,
            'contrasena' => Hash::make($validated['contrasena']),
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
            'contrasena' => ['sometimes', 'string', 'min:8'],
        ]);

        // Si se envía la contrasena, se encripta
        if (isset($validated['contrasena'])) {
            $validated['contrasena'] = Hash::make($validated['contrasena']);
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
