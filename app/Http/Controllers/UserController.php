<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FirebaseNotificationService;
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
        $authUser = $request->user();
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

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $oldStatus = $user->status; //  Guardar status anterior
        $isClient = optional($user->role)->role_name === 'cliente';
        $authUser = $request->user();
        $authRole = optional($authUser)->role ? optional($authUser->role)->role_name : null;
        $isAdmin = in_array(strtolower((string)$authRole), ['admin', 'administrador', 'superadmin']);
        $rules = [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'role_id' => ['sometimes', 'integer'],
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
            'current_password' => [($isClient || $isAdmin) ? 'nullable' : 'required', 'string'],
            'password' => ['sometimes', 'string', 'min:8'],
        ];
        $validated = $request->validate($rules);

        if (!$isClient && !$isAdmin) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 422);
            }
        }
        
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        unset($validated['current_password']);

        $user->update($validated);

        // 👇 Si cambió el status, enviar notificación
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
            $firebaseService = new FirebaseNotificationService();
            $firebaseService->sendUserStatusNotification($user->id, $validated['status']);
        }

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $user;
    }
}