<?php

namespace App\Http\Controllers;
use App\Models\DianNumbering;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DianNumberingController extends Controller
{
    public function index()
    {
        $loggedUser = Auth::user();
        $dian_numberings = DianNumbering::where('company_id', $loggedUser->company_id)
            ->included()->filter()->sort()->getOrPaginate();
        return response()->json($dian_numberings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type'        => 'required|in:Factura,NotaCredito,NotaDebito',
            'document_type_code'   => 'nullable|string|max:10',
            'prefix'               => 'required|string|max:10',
            'start_number'         => 'required|numeric|min:0',
            'end_number'           => 'required|numeric|min:' . ($request->input('start_number') ?? 0),
            'resolution_date'      => 'required|date',
            'resolution_number'    => 'required|string|max:50',
            'validity_start_date'  => 'required|date',
            'validity_end_date'    => 'required|date|after_or_equal:validity_start_date',
            'current_status'       => 'required|in:Activo,Inactivo',
            'environment'          => 'nullable|in:Pruebas,Producción,Produccion',
            'description'          => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        $dian_numbering = DianNumbering::create($data);
        return response()->json($dian_numbering, 201);
    }

    public function show($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        $loggedUser = Auth::user();
        if (!$loggedUser || $dian_numbering->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        return response()->json($dian_numbering);
    }

    public function update(Request $request, DianNumbering $dianNumbering)
    {
        $loggedUser = Auth::user();
        if (!$loggedUser || $dianNumbering->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'document_type'        => 'sometimes|in:Factura,NotaCredito,NotaDebito',
            'document_type_code'   => 'sometimes|string|max:10',
            'prefix'               => 'sometimes|string|max:10',
            'start_number'         => 'sometimes|numeric|min:0',
            'end_number'           => 'sometimes|numeric|min:' . ($request->input('start_number') ?? 0),
            'resolution_date'      => 'sometimes|date',
            'resolution_number'    => 'sometimes|string|max:50',
            'validity_start_date'  => 'sometimes|date',
            'validity_end_date'    => 'sometimes|date|after_or_equal:validity_start_date',
            'current_status'       => 'sometimes|in:Activo,Inactivo',
            'environment'          => 'sometimes|in:Pruebas,Producción,Produccion',
            'description'          => 'sometimes|string|max:255',
        ]);

        $dianNumbering->update($validated);
        return response()->json($dianNumbering);
    }

    public function destroy($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        $loggedUser = Auth::user();
        if (!$loggedUser || $dian_numbering->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        $dian_numbering->delete();
        return response()->json(null, 204);
    }
}
