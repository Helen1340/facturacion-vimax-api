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
            'tipo_documento' => 'required|in:NIT,CC,CE',
            'direccion' => 'required|string|max:150',
            'municipio' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'pais' => 'required|string|max:50',
            'telefono' => 'required|string|max:20',
            'correo_electronico' => 'required|email|unique:companies,correo_electronico',
            'regimen' => 'required|string|max:50',
            'logo_url' => 'nullable|string',
            'nombre_comercial' => 'nullable|string|max:150',
            'codigo_ciiu' => 'nullable|string|max:10',
            'numero_documento' => 'required|string|max:20|unique:companies,numero_documento',
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
            'tipo_documento' => 'sometimes|required|in:NIT,CC,CE',
            'direccion' => 'sometimes|required|string|max:150',
            'municipio' => 'sometimes|required|string|max:100',
            'departamento' => 'sometimes|required|string|max:100',
            'pais' => 'sometimes|required|string|max:50',
            'telefono' => 'sometimes|required|string|max:20',
            'correo_electronico' => 'sometimes|required|email|unique:companies,correo_electronico,' . $company->id,
            'regimen' => 'sometimes|required|string|max:50',
            'logo_url' => 'nullable|string',
            'nombre_comercial' => 'nullable|string|max:150',
            'codigo_ciiu' => 'nullable|string|max:10',
            'numero_documento' => 'sometimes|required|string|max:20|unique:companies,numero_documento,' . $company->id,
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

