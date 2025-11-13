<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
// Importar la clase Rule para la validación de unicidad en el método update
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; 

class TaxController extends Controller
{
    // lista con filtros, relaciones y paginaciÃ³n
    public function index()
    {
        $taxes = Tax::included()->filter()->sort()->getOrPaginate();
        return response()->json($taxes);
    }

    // crear un nuevo impuesto
    public function store(Request $request)
    {
        $request->validate([
            // *** CORRECCIÓN CLAVE 1: Agregamos unique:taxes para evitar el 500 por duplicado ***
            'tax_code'       => 'required|string|max:50|unique:taxes', 
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string',
            'type'           => 'required|string|max:50',
            'percentage'     => 'nullable|numeric|between:0,999.99',
            'fixed_value'    => 'nullable|numeric|min:0',
            // Aseguramos que los valores coincidan con los de tu migración (sin caracteres corruptos)
            'application_type'=> 'required|string|in:Porcentaje,ValorFijo,Retencion', 
            'min_value'      => 'nullable|numeric|min:0',
            'max_value'      => 'nullable|numeric|min:0',
            'status'         => 'required|in:Activo,Inactivo', 
        ]);

        $data = $request->all();
        // Asignar automáticamente la empresa del usuario logueado
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin empresa asociada'
            ], 401);
        }
        $data['company_id'] = $user->company_id;
        
        $tax = Tax::create($data);
        
        // Buena práctica: Usar 201 Created para una respuesta de creación exitosa
        return response()->json($tax, 201); 
    }

    // mostrar un impuesto por id
    public function show($id)
    {
        $tax = Tax::findOrFail($id);
        return response()->json($tax);
    }

    // actualizar un impuesto
    public function update(Request $request, Tax $tax)
    {
        $validatedData = $request->validate([
            // *** CORRECCIÓN CLAVE 2: Usar Rule::unique para ignorar el ID actual y permitir UPDATE ***
            'tax_code'       => [
                'sometimes', 
                'string', 
                'max:50', 
                Rule::unique('taxes')->ignore($tax->id)
            ],
            'name'           => 'sometimes|string|max:100',
            'description'    => 'sometimes|nullable|string',
            'type'           => 'sometimes|string|max:50',
            'percentage'     => 'sometimes|nullable|numeric|between:0,999.99',
            'fixed_value'    => 'sometimes|nullable|numeric|min:0',
            'application_type'=> 'sometimes|string|in:Porcentaje,ValorFijo,Retencion',
            'min_value'      => 'sometimes|nullable|numeric|min:0',
            'max_value'      => 'sometimes|nullable|numeric|min:0',
            'status'         => 'sometimes|in:Activo,Inactivo',
        ]);

        // *** CORRECCIÓN CLAVE 3: Usar $request->validated() para una actualización más segura ***
        // Solo actualizamos los campos que pasaron la validación
        $tax->update($validatedData);

        return response()->json($tax);
    }

    // eliminar un impuesto
    public function destroy(Tax $tax)
    {
        $tax->delete();
        return response()->json(null, 204);
    }
}