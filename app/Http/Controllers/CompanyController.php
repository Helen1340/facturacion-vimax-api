<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\Rule; // Necesario para la regla unique en update

class CompanyController extends Controller
{
    public function index()
    {
        $roles = Company::included()->filter()->sort()->getOrPaginate();

        return response()->json($roles);
    }

    // Crear una nueva compaÃ±Ã­a
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Nombres de campos que vienen del JSON
            'company_name' => 'required|string|max:150', 
            'identification_number' => 'required|string|max:50|unique:companies,nit', // Valida contra la columna 'nit'
            'trade_name' => 'nullable|string|max:150', 
            'address' => 'required|string|max:150', 
            'city' => 'required|string|max:100', 
            'department' => 'required|string|max:100', 
            'country' => 'required|string|max:50', 
            'phone' => 'required|string|max:20', 
            'email' => 'required|string|email|max:100|unique:companies,email', // Asume que la columna es 'email' (según tu migración)
            'tax_regime' => 'required|string|max:50', 
            'logo_url' => 'nullable|url|max:1000', 
            'ciiu_code' => 'nullable|string|max:10', 
            'legal_representative_name' => 'nullable|string|max:150', 
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS', 
            'legal_representative_document_number' => 'nullable|string|max:20', 
        ]);

        $company = Company::create($validatedData);
        return response()->json($company, 201);
    }
      public function show($id)
    {
        $company = Company::findOrFail($id);

        return response()->json($company);
    }

    // Actualizar una compaÃ±Ã­a existente
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        // La validaciÃ³n requiere el objeto $company para Rule::ignore(), asÃ­ que usamos el cÃ³digo de abajo
        $validatedData = $request->validate([
            
            'company_name' => 'sometimes|string|max:150',
            
            'identification_number' => [
                'sometimes', 
                'string', 
                'max:50', 
                Rule::unique('companies', 'nit')->ignore($company->id)
            ],
            'trade_name' => 'nullable|string|max:150',
            'address' => 'sometimes|string|max:150',
            'city' => 'sometimes|string|max:100',
            'department' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:50',
            'phone' => 'sometimes|string|max:20',
            // *** 3: Usar Rule::unique para email y mapear a 'email' ***
            'email' => [
                'sometimes', 
                'string', 
                'email', 
                'max:100', 
                Rule::unique('companies', 'email')->ignore($company->id)
            ],
            'tax_regime' => 'sometimes|string|max:50',
            'logo_url' => 'nullable|url|max:1000',
            'ciiu_code' => 'nullable|string|max:10',
            'legal_representative_name' => 'nullable|string|max:150',
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS',
            'legal_representative_document_number' => 'nullable|string|max:20',
        ]);
        
    

        // Solo actualiza los campos mapeados y validados
        $company->update($validatedData); 
        return response()->json($company);
    }

  
    
    public function destroy($id)
    {
        // 1. Encontrar la empresa por ID. Falla con 404 si no existe.
        $company = Company::findOrFail($id); 
        
        // 2. Eliminar el registro.
        $company->delete();
        
        // 3. Devolver una respuesta 204 (No Content)
        return response()->json(null, 204); 
    }
} 