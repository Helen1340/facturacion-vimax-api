<?php

namespace App\Http\Controllers;
use App\Models\DianNumbering;

use Illuminate\Http\Request;

class DianNumberingController extends Controller
{
    public function index()
    {
        $dian_numberings = DianNumbering::included()->filter()->sort()->getOrPaginate();
        return response()->json($dian_numberings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento'     => 'required|in:Factura,notaCredito,notaDebito',
            'prefijo'            => 'required|string|max:10',
            'numero_inicio'      => 'required|integer|min:0', // bigInteger en DB se mapea a integer en validación
            'numero_fin'         => 'required|integer|min:' . ($request->input('numero_inicio') ?? 0), // numero_fin debe ser mayor o igual que numero_inicio
            'fecha_resolucion'   => 'required|date',
            'numero_resolucion'  => 'required|string|max:50',
            'fecha_inicio'       => 'required|date',
            'fecha_fin'          => 'required|date|after_or_equal:fecha_inicio',
            'estado_actual'      => 'required|in:Activo,Inactivo',
        ]);

        $dian_numbering = DianNumbering::create($request->all());
        return response()->json($dian_numbering, 201);
    }

    public function show($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        return response()->json($dian_numbering);
    }

    public function update(Request $request, DianNumbering $dian_numbering)
    {
        $request->validate([
            'tipo_documento'     => 'sometimes|required|in:Factura,notaCredito,notaDebito',
            'prefijo'            => 'sometimes|required|string|max:10',
            'numero_inicio'      => 'sometimes|required|integer|min:0',
            'numero_fin'         => 'sometimes|required|integer|min:' . ($request->input('numero_inicio') ?? $dian_numbering->numero_inicio),
            'fecha_resolucion'   => 'sometimes|required|date',
            'numero_resolucion'  => 'sometimes|required|string|max:50',
            'fecha_inicio'       => 'sometimes|required|date',
            'fecha_fin'          => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'estado_actual'      => 'sometimes|required|in:Activo,Inactivo',
        ]);

        $dian_numbering->update($request->only(array_keys($request->all())));

        return response()->json($dian_numbering);
    }

    public function destroy($id)
    {
        $dian_numbering = DianNumbering::findOrFail($id);
        $dian_numbering->delete();
        return response()->json(null, 204);
    }
}
