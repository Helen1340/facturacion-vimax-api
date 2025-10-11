<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectronicInvoice;
use Illuminate\Support\Facades\Auth;

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
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $validated = $request->validate([
            'invoice_number'        => 'required|string|max:20|unique:electronic_invoices,invoice_number',
            'issue_date'            => 'required|date',
            'internal_status'       => 'required|string|max:50',
            'observation'           => 'nullable|string|max:255',

            // --- Campos DIAN / UBL ---
            'ubl_version'           => 'nullable|string|max:10',
            'customization_id'      => 'nullable|string|max:50',
            'profile_id'            => 'nullable|string|max:50',
            'uuid'                  => 'nullable|string|max:100',
            'document_currency_code' => 'nullable|string|max:10',
            'invoice_type_code'     => 'nullable|string|max:10',

            // --- Totales principales ---
            'line_extension_amount' => 'nullable|numeric|min:0',
            'tax_exclusive_amount'  => 'nullable|numeric|min:0',
            'tax_inclusive_amount'  => 'nullable|numeric|min:0',
            'payable_amount'        => 'nullable|numeric|min:0',

            // --- Control de estado DIAN ---
            'dian_status'           => 'nullable|string|max:50',
            'sent_at'               => 'nullable|date',
            'received_at'           => 'nullable|date',

            // --- Información de pago ---
            'payment_means_code'    => 'nullable|string|max:10',
            'payment_terms'         => 'nullable|string|max:255',
            'payment_means_name'    => 'nullable|string|max:255',
        ]);

        $invoice = ElectronicInvoice::create([
            ...$validated,
            'user_id' => $authUser->id,
        ]);

        return response()->json([
            'message' => 'Factura creada exitosamente',
            'data' => $invoice,
        ], 201);
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
            // --- Relación y datos base ---
            'user_id'               => 'sometimes|exists:users,id',
            'invoice_number'        => 'sometimes|string|max:20|unique:electronic_invoices,invoice_number,' . $electronicInvoice->id,
            'issue_date'            => 'sometimes|date',
            'internal_status'       => 'sometimes|string|max:50',
            'observation'           => 'nullable|string|max:255',

            // --- Campos DIAN / UBL ---
            'ubl_version'           => 'nullable|string|max:10',
            'customization_id'      => 'nullable|string|max:50',
            'profile_id'            => 'nullable|string|max:50',
            'uuid'                  => 'nullable|string|max:100',
            'document_currency_code' => 'nullable|string|max:10',
            'invoice_type_code'     => 'nullable|string|max:10',

            // --- Totales principales ---
            'line_extension_amount' => 'nullable|numeric|min:0',
            'tax_exclusive_amount'  => 'nullable|numeric|min:0',
            'tax_inclusive_amount'  => 'nullable|numeric|min:0',
            'payable_amount'        => 'nullable|numeric|min:0',

            // --- Control de estado DIAN ---
            'dian_status'           => 'nullable|string|max:50',
            'sent_at'               => 'nullable|date',
            'received_at'           => 'nullable|date',

            // --- Información de pago ---
            'payment_means_code'    => 'nullable|string|max:10',
            'payment_terms'         => 'nullable|string|max:255',
            'payment_means_name'    => 'nullable|string|max:255',
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
