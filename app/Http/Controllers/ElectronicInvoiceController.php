<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectronicInvoice;

class ElectronicInvoiceController extends Controller
{
    public function index()
    {
        // Listar facturas electrónicas con filtros, orden y paginación
        $invoices = ElectronicInvoice::included()->filter()->sort()->getOrPaginate();

        return response()->json($invoices);
    }

    

    
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'numero_factura' => 'required|string|max:20|unique:electronic_invoices',
            'fecha_emision'  => 'required|date',
            'estado_interno' => 'required|string|max:50',
            'observacion'    => 'nullable|string|max:255',
        ]);

        $invoice = ElectronicInvoice::create($request->all());

        return response()->json($invoice);
    }

    
    public function show($id)
    {
        //$invoice = ElectronicInvoice::included()->findOrFail($id);
        //return response()->json($invoice); //como nos enseno el instructor 

        // Mostrar una factura con sus detalles y producto/servicio
        $invoice = ElectronicInvoice::with('invoiceDetails.item')->findOrFail($id);
        return response()->json($invoice);
    }

   
    public function update(Request $request, ElectronicInvoice $electronicInvoice)
    {
        $request->validate([
            'numero_factura' => 'sometimes|string|max:20|unique:electronic_invoices,numero_factura,' . $electronicInvoice->id,
            'fecha_emision'  => 'sometimes|date',
            'estado_interno' => 'sometimes|string|max:50',
            'observacion'    => 'nullable|string|max:255',
        ]);

        $electronicInvoice->update($request->all());

        return response()->json($electronicInvoice);
    }

    
    public function destroy(ElectronicInvoice $electronicInvoice)
    {
        $electronicInvoice->delete();
        return response()->json($electronicInvoice);
    }
}
