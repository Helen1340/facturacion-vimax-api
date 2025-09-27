<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;

class AuthController extends Controller
{
    /**
     * Registro de empresa y usuario administrador
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razon_social' => 'required|string|max:150',
    'nit' => 'required|string|max:50|unique:companies,nit',
    'correo_empresa' => 'required|string|email|max:100|unique:companies,correo_electronico',

    // Representante (opcionales en registro inicial)
    'representante_nombre' => 'nullable|string|max:150',
    'representante_tipo_documento' => 'nullable|in:CC,CE,NIT,PAS',
    'representante_numero_documento' => 'nullable|string|max:20',

    // Usuario administrador
    'nombre' => 'required|string|max:100',
'correo_electronico' => 'required|string|email|max:150|unique:users,correo_electronico',
'tipo_documento' => 'required|in:CC,CE,NIT,PAS',
'numero_documento' => 'required|string|max:20|unique:users,numero_documento',
'password' => 'required|string|min:8|confirmed',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Crear empresa con defaults para campos opcionales
            $company = Company::create([
                'razon_social' => $request->razon_social,
                'nit' => $request->nit,
                'correo_electronico' => $request->correo_empresa,
                'representante_nombre' => $request->representante_nombre,
                'representante_tipo_documento' => $request->representante_tipo_documento,
                'representante_numero_documento' => $request->representante_numero_documento,
                // defaults
                'nombre_comercial' => $request->nombre_comercial ?? '',
                'direccion' => $request->direccion ?? 'Sin definir',
                'ciudad' => $request->ciudad ?? 'Sin definir',
                'departamento' => $request->departamento ?? 'Sin definir',
                'pais' => $request->pais ?? 'Sin definir',
                'telefono' => $request->telefono ?? 'Sin definir',
                'regimen' => $request->regimen ?? 'Sin definir',
                'logo_url' => $request->logo_url ?? '',
                'codigo_ciiu' => $request->codigo_ciiu ?? '',
            ]);

            // Verificar que exista el rol administrador
            $role = Role::where('nombre', 'administrador')->first();
            if (!$role) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Error: rol "administrador" no encontrado. Crea el rol antes.'
                ], 500);
            }
            $roleId = $role->id;

            // Crear usuario administrador
            $user = User::create([
                
    'nombre' => $request->nombre,
    'correo_electronico' => $request->correo_electronico,
    'tipo_documento' => $request->tipo_documento,
    'numero_documento' => $request->numero_documento,
    'password' => Hash::make($request->password),
    'company_id' => $company->id,
    'role_id' => $roleId,
]);

            

            DB::commit();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Empresa y usuario administrador registrados correctamente',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->only(['id','nombre','correo_electronico','company_id','role_id','numero_documento']),
                'company' => $company->only(['id','razon_social','nit','correo_electronico']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // En producción preferible loggear $e->getMessage() y devolver mensaje genérico
            return response()->json([
                'message' => 'Error al registrar empresa y usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo_electronico' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('correo_electronico', $request->correo_electronico)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $company = $user->company;
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only(['id','nombre','correo_electronico','company_id','role_id','numero_documento']),
            'company' => $company ? $company->only(['id','razon_social','nit','correo_electronico']) : null,
        ], 200);
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $company = $user->company;

        return response()->json([
            'user' => $user->only(['id','nombre','correo_electronico','company_id','role_id','numero_documento']),
            'company' => $company ? $company->only(['id','razon_social','nit','correo_electronico']) : null,
        ], 200);
    }

    /**
     * Logout: revoca el token actual
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $current = $user->currentAccessToken();

        if ($current) {
            $current->delete();
            return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
        }

        return response()->json(['message' => 'No se encontró token activo'], 400);
    }
}
