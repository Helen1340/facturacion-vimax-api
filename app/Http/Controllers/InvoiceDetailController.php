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
            'Id_DetalleFactura' => 'required|integer',
            'Descripcion' => 'required|text',
            'Cantidad' => 'required|numeric',
            'PrecioUnitario' => 'required|numeric',
            'ValorTotal' => 'required|numeric',
            'Descuento' => 'nullable|numeric',
            'PorcentajeIVA' => 'required|numeric',
            'ValorIVA' => 'required|numeric',
            'UnidadMedida' => 'required|string|max:20',
            'CodigoProducto' => 'required|string|max:50',
            'Observacion' => 'nullable|text',
            'product_service_id' => 'nullable|exists:product_services,id',
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
            'Id_DetalleFactura' => 'sometimes|integer',
            'Descripcion' => 'sometimes|required|text',
            'Cantidad' => 'sometimes|required|numeric',
            'PrecioUnitario' => 'sometimes|required|numeric',
            'ValorTotal' => 'sometimes|required|numeric',
            'Descuento' => 'nullable|numeric',
            'PorcentajeIVA' => 'sometimes|required|numeric',
            'ValorIVA' => 'sometimes|required|numeric',
            'UnidadMedida' => 'sometimes|required|string|max:20',
            'CodigoProducto' => 'sometimes|required|string|max:50',
            'Observacion' => 'nullable|text',
            'product_service_id' => 'nullable|exists:product_services,id',
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
