<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use Illuminate\Http\Request;


class InvoiceDetailController extends Controller
{
    public function index()
    {
        $invoice_details = InvoiceDetail::included()->filter()->sort()->getOrPaginate();
        return response()->json($invoice_details);
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|text',
            'cantidad' => 'required|numeric',
            'precio_unitario' => 'required|numeric',
            'valor_total' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'porcentaje_iva' => 'required|numeric',
            'valor_iva' => 'required|numeric',
            'unidad_medida' => 'required|string|max:20',
            'codigo_producto' => 'required|string|max:50',
            'observacion' => 'nullable|text',
        ]);
        $invoice_detail = InvoiceDetail::create($request->all());
        return response()->json($invoice_detail);
    }

    public function show($id)
    {
        $invoice_detail = InvoiceDetail::findOrFail($id);
        return response()->json($invoice_detail);
    }

    public function update(Request $request, InvoiceDetail $invoice_detail)
    {
        $request->validate([
            'descripcion' => 'sometimes|required|text',
            'cantidad' => 'sometimes|required|numeric',
            'precio_unitario' => 'sometimes|required|numeric',
            'talor_total' => 'sometimes|required|numeric',
            'descuento' => 'nullable|numeric',
            'porcentaje_iva' => 'sometimes|required|numeric',
            'valor_iva' => 'sometimes|required|numeric',
            'unidad_medida' => 'sometimes|required|string|max:20',
            'codigo_producto' => 'sometimes|required|string|max:50',
            'observacion' => 'nullable|text',
        ]);

         // Actualiza solo los campos que vienen en el request
    $invoice_detail->update($request->only(array_keys($request->all())));

    //Actualiza el campo pero siempretenemos que poner o validar nit, razon_social y tipo_documento
    //$company->update($request->all()); // Linea del Repositorio del Instrucor

    return response()->json($invoice_detail);

    }

    public function destroy($id)
    {
        $invoice_detail = InvoiceDetail::findOrFail($id);
        $invoice_detail->delete();
        return response()->json(null, 204);
    }
}
