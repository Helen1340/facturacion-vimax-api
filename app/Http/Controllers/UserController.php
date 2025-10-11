<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $user = User::included()->filter()->sort()->getOrPaginate();

        return response()->json($user);
    }



    public function store(Request $request)
    {
        $authUser = $request->user();

        // Si el usuario autenticado pertenece a una empresa, se usa esa empresa.
        $companyId = $authUser ? $authUser->company_id : $request->input('company_id');

        $validated = $request->validate([
        
            'role_id' => ['nullable', 'integer'],
            'first_name' => ['required', 'string', 'max:100'],
            'document_type' => ['nullable', Rule::in(['NIT', 'CC', 'CE'])],
            'document_number' => ['required', 'string', 'max:50', Rule::unique('users', 'document_number')],
            'address' => ['nullable', 'string', 'max:150'],
            'country' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:250'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'last_access' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8'], 

        ]);

        $user = User::create([
            ...$validated,
            'company_id' => $companyId,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data' => $user,
        ], 201);
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
            
        'company_id' => ['sometimes', 'integer', 'exists:companies,id'], // CORREGIDO: nullable -> sometimes
            'role_id' => ['sometimes', 'integer'], // CORREGIDO: nullable -> sometimes
            'first_name' => ['sometimes', 'string', 'max:100'], 
            'document_type' => ['sometimes', Rule::in(['NIT', 'CC', 'CE'])],
            'document_number' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'document_number')->ignore($user->id)],
            'address' => ['sometimes', 'string', 'max:150'], 
            'country' => ['sometimes', 'string', 'max:100'], 
            'description' => ['sometimes', 'string', 'max:250'], 
            'email' => ['sometimes', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'string', 'max:20'], 
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])], 
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
