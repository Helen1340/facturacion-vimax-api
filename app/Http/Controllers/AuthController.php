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
            $role = Role::where('role_name', 'administrador')->first();
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
                'user' => $user->only(['id', 'first_name', 'email', 'company_id', 'role_id', 'document_number']),
                'company' => $company->only(['id', 'business_name', 'nit', 'email']),
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

    //completar el pre-registro de usuario y empresa

    public function completeRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Datos de la empresa - ✅ Cambiar a sometimes para validar solo si está presente
            'business_name' => 'sometimes|required|string|max:150',
            'nit' => 'sometimes|required|string|max:50',
            'trade_name' => 'nullable|string|max:150',
            'address' => 'sometimes|required|string|max:150',
            'city' => 'sometimes|required|string|max:100',
            'department' => 'sometimes|required|string|max:100',
            'country' => 'sometimes|required|string|max:50',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:100',
            'tax_regime' => 'sometimes|required|string|max:50',
            'ciiu_code' => 'nullable|string|max:10',
            'imagen' => 'nullable|image|max:10240', // ✅ Validación de imagen

            // Representante legal
            'legal_representative_name' => 'nullable|string|max:150',
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS',
            'legal_representative_document_number' => 'nullable|string|max:20',

            // Datos del usuario (opcionales)
            'first_name' => 'nullable|string|max:100',
            'document_type' => 'nullable|in:NIT,CC,CE,PAS',
            'document_number' => 'nullable|string|max:50',
            'user_address' => 'nullable|string|max:150',
            'user_country' => 'nullable|string|max:100',
            'user_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = \App\Models\User::find(Auth::id());

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $company = $user->company;

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la empresa asociada al usuario'
                ], 404);
            }

            // ✅ Preparar datos de actualización (solo los campos enviados)
            $companyData = [];

            // Agregar solo los campos que vienen en el request
            if ($request->has('business_name')) $companyData['business_name'] = $request->business_name;
            if ($request->has('nit')) $companyData['nit'] = $request->nit;
            if ($request->has('trade_name')) $companyData['trade_name'] = $request->trade_name;
            if ($request->has('address')) $companyData['address'] = $request->address;
            if ($request->has('city')) $companyData['city'] = $request->city;
            if ($request->has('department')) $companyData['department'] = $request->department;
            if ($request->has('country')) $companyData['country'] = $request->country;
            if ($request->has('phone')) $companyData['phone'] = $request->phone;
            if ($request->has('email')) $companyData['email'] = $request->email;
            if ($request->has('tax_regime')) $companyData['tax_regime'] = $request->tax_regime;
            if ($request->has('ciiu_code')) $companyData['ciiu_code'] = $request->ciiu_code;
            if ($request->has('legal_representative_name')) $companyData['legal_representative_name'] = $request->legal_representative_name;
            if ($request->has('legal_representative_document_type')) $companyData['legal_representative_document_type'] = $request->legal_representative_document_type;
            if ($request->has('legal_representative_document_number')) $companyData['legal_representative_document_number'] = $request->legal_representative_document_number;

            // ✅ Subir imagen a Cloudinary si existe
            if ($request->hasFile('imagen')) {
                $upload = cloudinary()->uploadApi()->upload(
                    $request->file('imagen')->getRealPath(),
                    ['folder' => 'company']
                );
                $companyData['logo_url'] = $upload['secure_url'];
            }

            // ✅ Actualizar empresa solo si hay datos
            if (!empty($companyData)) {
                $company->update($companyData);
            }

            // ✅ Actualizar usuario (solo los campos enviados)
            $userData = [];

            if ($request->has('first_name')) $userData['first_name'] = $request->first_name;
            if ($request->has('document_type')) $userData['document_type'] = $request->document_type;
            if ($request->has('document_number')) $userData['document_number'] = $request->document_number;
            if ($request->has('user_address')) $userData['address'] = $request->user_address;
            if ($request->has('user_country')) $userData['country'] = $request->user_country;
            if ($request->has('user_phone')) $userData['phone'] = $request->user_phone;
            if ($request->has('description')) $userData['description'] = $request->description;

            if (!empty($userData)) {
                $user->update($userData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro completado exitosamente',
                'data' => [
                    'company' => $company->fresh(), // Recargar datos actualizados
                    'user' => $user->fresh()
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al completar el preregistro',
                'error' => $e->getMessage(),
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
            'user' => $user->only(['id', 'first_name', 'email', 'company_id', 'role_id', 'document_number']),
            'company' => $company ? $company->only(['id', 'business_name', 'nit', 'email']) : null,
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
            'user' => $user->only([
                'id',
                'first_name',
                'document_type',
                'document_number',
                'address',
                'country',
                'description',
                'email',
                'phone',
                'status',
                'last_access',
            ]),
            'company' => $company ? $company->only([
                'id',
                'business_name',                    // Mapea a business_name (columna)
                'nit',                              // Mapea a nit (columna)
                'trade_name',
                'address',
                'city',
                'department',
                'country',
                'phone',
                'email',                            // Mapea a email (columna)
                'tax_regime',
                'ciiu_code',
                'logo_url',
                'legal_representative_name',
                'legal_representative_document_type',
                'legal_representative_document_number',
            ]) : null,
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
