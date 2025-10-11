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
            // Validaciones de la Empresa
            'business_name' => 'required|string|max:150',
            'nit' => 'required|string|max:50|unique:companies,nit',
            'company_email' => 'required|string|email|max:100|unique:companies,email', // Corregido: 'email' de empresa

            // Representante (opcionales en registro inicial)
            'legal_representative_name' => 'nullable|string|max:150',
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS',
            'legal_representative_document_number' => 'nullable|string|max:20',

            // Validaciones del Usuario administrador
            'first_name' => 'required|string|max:100',
            'user_email' => 'required|string|email|max:150|unique:users,email', // Corregido: 'email' de usuario
            'document_type' => 'required|in:CC,CE,NIT,PAS',
            'document_number' => 'required|string|max:20|unique:users,document_number',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Crear empresa con defaults para campos opcionales
            $company = Company::create([
                // Corregido: Usar 'business_name' y 'company_email' del request
                'business_name' => $request->business_name,
                'nit' => $request->nit,
                'email' => $request->company_email, // Corregido: Mapeo
                'legal_representative_name' => $request->legal_representative_name, // Corregido: Mapeo
                'legal_representative_document_type' => $request->legal_representative_document_type, // Corregido: Mapeo
                'legal_representative_document_number' => $request->legal_representative_document_number, // Corregido: Mapeo
                // defaults
                'trade_name' => $request->trade_name ?? '',
                'address' => $request->address ?? 'Sin definir',
                'city' => $request->city ?? 'Sin definir',
                'department' => $request->department ?? 'Sin definir',
                'country' => $request->country ?? 'Sin definir',
                'phone' => $request->phone ?? 'Sin definir',
                'tax_regime' => $request->tax_regime ?? 'Sin definir',
                'logo_url' => $request->logo_url ?? '',
                'ciiu_code' => $request->ciiu_code ?? '',
            ]);

            // Verificar que exista el rol administrador
            $role = Role::where('role_name', 'administrator')->first();
            if (!$role) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Error: rol "administrador" no encontrado. Crea el rol antes.'
                ], 500);
            }
            $roleId = $role->id;

            // Crear usuario administrador
            $user = User::create([
                // Corregido: Usar los nombres de campos del request
                'first_name' => $request->first_name,
                'email' => $request->user_email, // Corregido: Mapeo
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
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
                'user' => $user->only(['id','first_name','email','company_id','role_id','document_number']),
                'company' => $company->only(['id','business_name','nit','email']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // En producciÃ³n preferible loggear $e->getMessage() y devolver mensaje generico
            return response()->json([
                'message' => 'Error al registrar empresa y usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Login de usuario
     
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Corregido: El campo del request para login es 'email', no 'correo_electronico'
        $user = User::where('email', $request->email)->first(); 

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $company = $user->company;
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only(['id','first_name','email','company_id','role_id','document_number']),
            'company' => $company ? $company->only(['id','business_name','nit','email']) : null,
        ], 200);
    }
    

    //Obtener usuario autenticado
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $company = $user->company;

        return response()->json([
            'user' => $user->only(['id','first_name','email','company_id','role_id','document_number']),
            'company' => $company ? $company->only(['id','business_name','nit','email']) : null,
        ], 200);
    }

    // Logout: revoca el token actual
    
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

        return response()->json(['message' => 'No se encontro token activo'], 400);
    }
}