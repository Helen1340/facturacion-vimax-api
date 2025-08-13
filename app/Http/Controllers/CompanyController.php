<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{

    // Listar con filtros, relaciones y paginación
    public function index()
    {
        $companies = Company::included()->filter()->sort()->getOrPaginate();

        return response()->json($companies);
    }

     // Crear nueva empresa
    public function store(Request $request)
    {
        $request->validate([
            'nit' => 'required|unique:companies,nit|max:20',
            'razon_social' => 'required|max:255',
            'tipo_documento' => 'required|in:NIT,CC,CE,TI',
            'direccion' => 'nullable|max:150',
            'municipio' => 'nullable|max:100',
            'departamento' => 'nullable|max:100',
            'pais' => 'nullable|max:50',
            'telefono' => 'nullable|max:20',
            'correo_electronico' => 'nullable|email|max:100',
            'regimen' => 'nullable|max:50',
            'logo' => 'nullable|max:100',
            'codigo_ciiu' => 'nullable|max:10',
        ]);

        $company = Company::create($request->all());

        return response()->json($company, 201);
    }

    // Mostrar empresa por id
    public function show($id)
    {
        $company = Company::included()->findOrFail($id);

        return response()->json($company);
    }
    
    // Actualizar empresa
    public function update(Request $request, Company $company)
    {
        $request->validate([
        'nit' => 'sometimes|unique:companies,nit,' . $company->id . '|max:20',
        'razon_social' => 'sometimes|max:255',
        'tipo_documento' => 'sometimes|in:NIT,CC,CE,TI',
        'direccion' => 'sometimes|max:150',
        'municipio' => 'sometimes|max:100',
        'departamento' => 'sometimes|max:100',
        'pais' => 'sometimes|max:50',
        'telefono' => 'sometimes|max:20',
        'correo_electronico' => 'sometimes|email|max:100',
        'regimen' => 'sometimes|max:50',
        'logo' => 'sometimes|max:100',
        'codigo_ciiu' => 'sometimes|max:10',
        ]);

        // Actualiza solo los campos que vienen en el request
        $company->update($request->only(array_keys($request->all())));
        
        //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
         //$company->update($request->all()); // Linea del Repositorio del Instrucor

        return response()->json($company);
    }

    // Eliminar empresa
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }
}


    