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
            'company_name' => 'required|string|max:150', // Razón social de la empresa
            'identification_number' => 'required|string|max:50|unique:companies,nit', // NIT o número de identificación
            'trade_name' => 'nullable|string|max:150', // Nombre comercial
            'address' => 'required|string|max:150', // Dirección principal
            'city' => 'required|string|max:100', // Ciudad o municipio
            'department' => 'required|string|max:100', // Departamento
            'country' => 'required|string|max:50', // País
            'phone' => 'required|string|max:20', // Teléfono de contacto
            'email' => 'required|string|email|max:100|unique:companies,correo_electronico', // Correo electrónico oficial
            'tax_regime' => 'required|string|max:50', // Régimen tributario (común, simplificado, etc.)
            'logo_url' => 'nullable|url|max:1000', // URL del logotipo
            'ciiu_code' => 'nullable|string|max:10', // Código CIIU de actividad económica
            'legal_representative_name' => 'nullable|string|max:150', // Nombre del representante legal
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS', // Tipo de documento del representante
            'legal_representative_document_number' => 'nullable|string|max:20', // Número de documento del representante

        ]);

        $company = Company::create($request->all());
        return response()->json($company, 201);
    }

    // Actualizar una compañía existente
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'company_name' => 'sometimes|required|string|max:150',
            'identification_number' => 'sometimes|required|string|max:50|unique:companies,nit,' . $id,
            'trade_name' => 'nullable|string|max:150',
            'address' => 'sometimes|required|string|max:150',
            'city' => 'sometimes|required|string|max:100',
            'department' => 'sometimes|required|string|max:100',
            'country' => 'sometimes|required|string|max:50',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|string|email|max:100|unique:companies,correo_electronico,' . $id,
            'tax_regime' => 'sometimes|required|string|max:50',
            'logo_url' => 'nullable|url|max:1000',
            'ciiu_code' => 'nullable|string|max:10',
            'legal_representative_name' => 'nullable|string|max:150',
            'legal_representative_document_type' => 'nullable|in:CC,CE,NIT,PAS',
            'legal_representative_document_number' => 'nullable|string|max:20',
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

