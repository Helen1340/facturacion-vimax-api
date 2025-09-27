<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    // Listar todas las compañías
    public function index()
    {
        $companies=Company::included()->filter()->sort()->getOrPaginate();

        return response()->json($companies);
    }

    // Mostrar una compañía específica
    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    // Crear una nueva compañía
    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:150',
            'nit' => 'required|string|max:50|unique:companies,nit',
            'nombre_comercial' => 'nullable|string|max:150',
            'direccion' => 'required|string|max:150',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'pais' => 'required|string|max:50',
            'telefono' => 'required|string|max:20',
            'correo_empresa' => 'required|string|email|max:100|unique:companies,correo_electronico',
            'regimen' => 'required|string|max:50',
            'logo_url' => 'nullable|url|max:1000', //  campo opcional
            'codigo_ciiu' => 'nullable|string|max:10',
            'representante_nombre' => 'nullable|string|max:150',
            'representante_tipo_documento' => 'nullable|in:CC,CE,NIT,PAS',
            'representante_numero_documento' => 'nullable|string|max:20',

        ]);

        $company = Company::create($request->all());
        return response()->json($company, 201);
    }

    // Actualizar una compañía existente
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'razon_social' => 'sometimes|required|string|max:150',
    'nit' => 'sometimes|required|string|max:50|unique:companies,nit',
    'nombre_comercial' => 'nullable|string|max:150',
    'direccion' => 'sometimes|required|string|max:150',
    'ciudad' => 'sometimes|required|string|max:100',
    'departamento' => 'sometimes|required|string|max:100',
    'pais' => 'sometimes|required|string|max:50',
    'telefono' => 'sometimes|required|string|max:20',
    'correo_empresa' => 'sometimes|required|string|email|max:100|unique:companies,correo_electronico',
    'regimen' => 'sometimes|required|string|max:50',
    'logo_url' => 'nullable|url|max:1000',
    'codigo_ciiu' => 'nullable|string|max:10',
    'representante_nombre' => 'sometimes|required|string|max:150',
    'representante_tipo_documento' => 'sometimes|required|in:CC,CE,NIT,PAS',
     'representante_numero_documento' => 'sometimes|required|string|max:20',

        ]);

        $company->update($request->all());
        return response()->json($company);
    }

    // Eliminar una compañía
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully']);
    }
}

