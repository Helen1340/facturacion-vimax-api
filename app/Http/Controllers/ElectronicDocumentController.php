<?php

namespace App\Http\Controllers;

use App\Models\ElectronicDocument;
use App\Models\ElectronicInvoice;
use App\Models\DianNumbering;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ElectronicDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loggedUser = Auth::user();
        if (!$loggedUser || !$loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado o sin empresa asociada'], 401);
        }
        $electronicDocument = ElectronicDocument::whereHas('electronicInvoice.user', function ($q) use ($loggedUser) {
            $q->where('company_id', $loggedUser->company_id);
        })
            ->included()->filter()->sort()->getOrPaginate();

        return response()->json($electronicDocument);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'electronic_invoice_id' => 'required|exists:electronic_invoices,id',
            'dian_numbering_id'     => 'required|exists:dian_numberings,id',
            'credit_debit_note_id'  => 'nullable|exists:credit_debit_notes,id',
            'cufe'                  => 'required|string|max:255|unique:electronic_documents,cufe',
            'cude'                  => 'required|string|max:50',
            'xml_document'          => 'required|string',
            'dian_status'           => 'required|string|max:50',
            'validation_date'       => 'nullable|date',
            'digital_signature'     => 'nullable|string',
            'document_hash'         => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'environment'           => ['required', Rule::in(['Pruebas', 'Producción', 'Produccion'])],
            'document_type'         => 'required|string|max:50',
            'qr_code'               => 'nullable|string',
            'cdr'                   => 'nullable|string',
            'emission_mode'         => ['required', Rule::in(['normal', 'en contingencia'])],
        ]);

        $loggedUser = Auth::user();
        $invoice = ElectronicInvoice::with('user')->findOrFail($validated['electronic_invoice_id']);
        $numbering = DianNumbering::findOrFail($validated['dian_numbering_id']);
        if (!$loggedUser || $invoice->user->company_id !== $loggedUser->company_id || $numbering->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado para crear documento electrónico en otra empresa'], 403);
        }

        $electronicDocument = ElectronicDocument::create($validated);
        return response()->json($electronicDocument, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $electronicDocument = ElectronicDocument::findOrFail($id);
        $loggedUser = Auth::user();
        if (!$loggedUser || !$loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado o sin empresa asociada'], 401);
        }
        $electronicDocument->loadMissing('electronicInvoice.user');
        if (optional($electronicDocument->electronicInvoice->user)->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        return response()->json($electronicDocument);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectronicDocument $electronicDocument)
    {
        $validated = $request->validate([
            'electronic_invoice_id' => 'sometimes|required|exists:electronic_invoices,id',
            'dian_numbering_id'     => 'sometimes|required|exists:dian_numberings,id',
            'credit_debit_note_id'  => 'nullable|exists:credit_debit_notes,id',
            'cufe'                  => 'sometimes|required|string|max:255|unique:electronic_documents,cufe,' . $electronicDocument->id,
            'cude'                  => 'sometimes|required|string|max:50',
            'xml_document'          => 'sometimes|required|string',
            'dian_status'           => 'sometimes|required|string|max:50',
            'validation_date'       => 'nullable|date',
            'digital_signature'     => 'nullable|string',
            'document_hash'         => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'environment'           => ['sometimes', Rule::in(['Pruebas', 'Producción', 'Produccion'])],
            'document_type'         => 'sometimes|required|string|max:50',
            'qr_code'               => 'nullable|string',
            'cdr'                   => 'nullable|string',
            'emission_mode'         => ['sometimes', Rule::in(['normal', 'en contingencia'])],
        ]);

        $loggedUser = Auth::user();
        if (!$loggedUser || !$loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado o sin empresa asociada'], 401);
        }
        $electronicDocument->loadMissing('electronicInvoice.user');
        if (optional($electronicDocument->electronicInvoice->user)->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        if (isset($validated['electronic_invoice_id'])) {
            $invoice = ElectronicInvoice::with('user')->findOrFail($validated['electronic_invoice_id']);
            if ($invoice->user->company_id !== $loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'Factura pertenece a otra empresa'], 403);
            }
        }
        if (isset($validated['dian_numbering_id'])) {
            $numbering = DianNumbering::findOrFail($validated['dian_numbering_id']);
            if ($numbering->company_id !== $loggedUser->company_id) {
                return response()->json(['success' => false, 'message' => 'Numeración DIAN pertenece a otra empresa'], 403);
            }
        }

        $electronicDocument->update($validated);
        return response()->json($electronicDocument);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectronicDocument $electronicDocument)
    {
        $loggedUser = Auth::user();
        if (!$loggedUser || !$loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado o sin empresa asociada'], 401);
        }
        $electronicDocument->loadMissing('electronicInvoice.user');
        if (optional($electronicDocument->electronicInvoice->user)->company_id !== $loggedUser->company_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        $electronicDocument->delete();
        return response()->json($electronicDocument);
    }
}
