<?php

namespace App\Http\Controllers;

use App\Models\CreditDebitNote;
use App\Models\ElectronicDocument;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditDebitNoteController extends Controller
{
    public function index()
    {
        $credit_debit_notes = CreditDebitNote::included()->filter()->sort()->getOrPaginate();
        return response()->json($credit_debit_notes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'electronic_invoice_id' => 'required|exists:electronic_invoices,id',
            'reason'                => 'required|string|max:250',
            'note_type'             => ['required', Rule::in(['debit','credit'])],
            'note_number'           => 'required|string|max:50|unique:credit_debit_notes,note_number',
            'status'                => ['required', Rule::in(['accepted','rejected','pending'])],
            'issue_date'            => 'required|date',
            'total_amount'          => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $credit_debit_note = CreditDebitNote::create($request->all());
        return response()->json($credit_debit_note, 201);
    }

    public function show($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        return response()->json($credit_debit_note);
    }

    public function update(Request $request, CreditDebitNote $creditDebitNote)
    {
        $request->validate([
            'electronic_invoice_id' => 'sometimes|required|exists:electronic_invoices,id',
            'reason'                => 'sometimes|required|string|max:250',
            'note_type'             => ['sometimes','required', Rule::in(['debit','credit'])],
            'note_number'           => 'sometimes|required|string|max:50|unique:credit_debit_notes,note_number,' . $creditDebitNote->id,
            'status'                => ['sometimes','required', Rule::in(['accepted','rejected','pending'])],
            'issue_date'            => 'sometimes|required|date',
            'total_amount'          => 'sometimes|required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $creditDebitNote->update($request->only(array_keys($request->all())));
        return response()->json($creditDebitNote);
    }

    public function destroy($id)
    {
        $credit_debit_note = CreditDebitNote::findOrFail($id);
        $credit_debit_note->delete();
        return response()->json(null, 204);
    }

    public function downloadPDF($id)
    {
        try {
            $note = CreditDebitNote::with(['electronicInvoice.user.company'])->findOrFail($id);
            $pdf = Pdf::loadView('pdf.note', [
                'note' => $note,
            ])->setPaper('letter');
            $filename = 'note-' . ($note->note_number ?? $note->id) . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadXML($id)
    {
        $doc = ElectronicDocument::where('credit_debit_note_id', $id)->orderByDesc('id')->first();
        if (!$doc || !$doc->xml_document) {
            return response()->json(['success' => false, 'message' => 'Documento electrónico no encontrado para la nota'], 404);
        }
        return response($doc->xml_document, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="note-' . $id . '.xml"');
    }
}

