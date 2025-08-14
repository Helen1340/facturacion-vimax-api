<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectronicInvoice;

class ElectronicInvoiceController extends Controller
{
    public function index()
    {
        
        $electronicInvoices = ElectronicInvoice::included()->filter()->sort()->getOrPaginate();
            return response()->json($electronicInvoices);
    }

    // Crear una nueva factura electrónica.
     
    public function store(Request $request)
    {
        // Validar los datos que vienen en la request
        $request->validate([
            'user_id'        => 'required|exists:users,id', // El usuario debe existir
            'customer_id'    => 'required|exists:customers,id', // El cliente debe existir
            'numero_factura' => 'required|unique:electronic_invoices,numero_factura|max:50', // Número de factura único
            'fecha_emision'  => 'required|date',
            'hora_emision'   => 'required',
            'moneda'         => 'required|max:3',
            'medio_pago'     => 'required|max:50',
            'subtotal'       => 'required|numeric',
            'total_impuesto' => 'required|numeric',
            'total'          => 'required|numeric',
            'cufe'           => 'required|unique:electronic_invoices,cufe|size:96',
            'xml_firmado'    => 'required',
            // Campos opcionales: 'codigo_qr', 'cdr', 'estado_dian', 'modo_emision', 'estado_interno'
        ]);

        // Crear la factura electrónica en la base de datos
        $electronicInvoice = ElectronicInvoice::create($request->all());

        return response()->json($electronicInvoice, 201);
    }

    //Mostrar una factura electrónica específica.
     public function show($id)
    {
        $electronicInvoice = ElectronicInvoice::included()->findOrFail($id);

        return response()->json($electronicInvoice);
    }

    //Actualizar una factura electrónica existente.
    
    public function update(Request $request, ElectronicInvoice $electronicInvoice)
    {
        
        $request->validate([
            'user_id'        => 'sometimes|required|exists:users,id',
            'customer_id'    => 'sometimes|required|exists:customers,id',
            'numero_factura' => 'sometimes|required|max:50|unique:electronic_invoices,numero_factura,' . $electronicInvoice->id,
            'fecha_emision'  => 'sometimes|required|date',
            'hora_emision'   => 'sometimes|required',
            'moneda'         => 'sometimes|required|max:3',
            'medio_pago'     => 'sometimes|required|max:50',
            'subtotal'       => 'sometimes|required|numeric',
            'total_impuesto' => 'sometimes|required|numeric',
            'total'          => 'sometimes|required|numeric',
            'cufe'           => 'sometimes|required|size:96|unique:electronic_invoices,cufe,' . $electronicInvoice->id,
            'xml_firmado'    => 'sometimes|required',
        ]);

        // Actualizar la factura con los datos enviados
        $electronicInvoice->update($request->all());

        return response()->json($electronicInvoice);
    }

    /**
     * Eliminar una factura electrónica.
     */
    public function destroy(ElectronicInvoice $electronicInvoice)
    {
        // Eliminar la factura de la base de datos
        $electronicInvoice->delete();


        return response()->json(null, 204);
    }

}
