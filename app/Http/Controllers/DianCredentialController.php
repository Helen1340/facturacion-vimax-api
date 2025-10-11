<?php

namespace App\Http\Controllers;

use App\Models\DianCredential;
use Illuminate\Http\Request;

class DianCredentialController extends Controller
{
    public function index()
    {
        $credentials = DianCredential::included()->filter()->sort()->getOrPaginate();
        return response()->json($credentials, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'ambiente' => ['required', 'in:pruebas,produccion'],
            'url_point' => ['required', 'string', 'max:255'],
            'usuario' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:150'],
            'estado' => ['in:Activo,Inactivo'],
        ]);

        $credential = DianCredential::create($validated);
        return response()->json($credential, 201);
    }

    public function show($id)
    {
        $credential = DianCredential::findOrFail($id);
        return response()->json($credential, 200);
    }

    public function update(Request $request, DianCredential $dianCredential)
    {
        $validated = $request->validate([
            'ambiente' => ['sometimes', 'in:pruebas,produccion'],
            'url_point' => ['sometimes', 'string', 'max:255'],
            'usuario' => ['sometimes', 'string', 'max:100'],
            'password' => ['sometimes', 'string', 'max:150'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
        ]);

        $dianCredential->update($validated);
        return response()->json($dianCredential, 200);
    }

    public function destroy(DianCredential $dianCredential)
    {
        $dianCredential->delete();
        return response()->json(null, 204);
    }
}
