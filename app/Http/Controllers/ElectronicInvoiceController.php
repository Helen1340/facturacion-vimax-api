<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Services\DianSimulatorService;
use Illuminate\Http\Request;
use App\Models\ElectronicInvoice;
use App\Models\ElectronicDocument;
use App\Models\CreditDebitNote;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;



class ElectronicInvoiceController extends Controller
{
    private $invoiceService;
    private $dianSimulator;

    // ⬇️ AGREGA ESTE CONSTRUCTOR
    public function __construct(InvoiceService $invoiceService, DianSimulatorService $dianSimulator)
    {
        $this->invoiceService = $invoiceService;
        $this->dianSimulator = $dianSimulator;
    }

    /**
     * Listar facturas con filtros opcionales
     * GET /api/invoices?dian_status=accepted&date_from=2024-01-01
     * Nota: Las facturas se filtran automáticamente por la empresa del usuario logueado
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'user_id',
            'dian_status',
            'internal_status',
            'date_from',
            'date_to',
            'per_page'
        ]);

        $invoices = $this->invoiceService->listInvoices($filters);

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Crear factura completa con detalles
     * POST /api/invoices
     * {
     *   "user_id": 3,  // Opcional: si no se envía, se usa el usuario logueado
     *   "buyer_id": 5,  // Requerido: ID del cliente (usuario con role 'client')
     *   "observation": "Factura de venta",
     *   "items": [
     *     {"type": "product", "id": 1, "quantity": 2, "discount": 5000},
     *     {"type": "service", "id": 1, "quantity": 1}
     *   ]
     * }
     * Nota: La factura se crea automáticamente para la empresa del usuario logueado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'buyer_id' => 'required|exists:users,id',  // Cliente (comprador) - debe ser usuario con role 'client'
            'observation' => 'nullable|string|max:255',
            'payment_means_code' => 'nullable|string|max:6',
            'payment_means_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0'
        ]);

        // Si no se envía user_id, usar el usuario logueado
        if (!isset($validated['user_id'])) {
            $validated['user_id'] = Auth::id();
        }

        $result = $this->invoiceService->createInvoice($validated);

        return response()->json($result, $result['success'] ? 201 : 400);
    }




    /**
     * Obtener datos necesarios para crear una factura (productos, servicios, clientes)
     * GET /api/invoices/create/data
     */
    public function createData(Request $request)
    {
        try {
            $loggedUser = Auth::user();

            if (!$loggedUser || !$loggedUser->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado o sin empresa asociada'
                ], 401);
            }

            // Obtener productos activos de la empresa
            $products = \App\Models\Product::where('status', 'Active')
                ->with(['measurementUnit', 'taxes'])
                ->select('id', 'product_code', 'name', 'description', 'unit_price', 'measurement_unit_id', 'status')
                ->orderBy('name')
                ->get();

            // Obtener servicios activos de la empresa
            $services = \App\Models\Service::where('status', 'Active')
                ->with(['measurementUnit', 'taxes'])
                ->select('id', 'service_code', 'name', 'description', 'unit_price', 'measurement_unit_id', 'status')
                ->orderBy('name')
                ->get();

            // Obtener clientes activos de la empresa
            $clients = \App\Models\User::where('company_id', $loggedUser->company_id)
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'cliente');
                })
                ->where('status', 'Active')
                ->select('id', 'first_name', 'document_type', 'document_number', 'email', 'phone', 'address')
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'services' => $services,
                    'clients' => $clients
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de clientes (usuarios con role 'client') de la empresa logueada
     * GET /api/invoices/clients
     */
    public function getClients(Request $request)
    {
        try {
            $loggedUser = Auth::user();

            if (!$loggedUser || !$loggedUser->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado o sin empresa asociada'
                ], 401);
            }

            // Obtener usuarios con role 'client' de la misma empresa
            $clients = \App\Models\User::where('company_id', $loggedUser->company_id)
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'cliente');
                })
                ->where('status', 'Active')
                ->select('id', 'first_name', 'document_type', 'document_number', 'email', 'phone', 'address')
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $clients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver factura completa con todos sus detalles
     * GET /api/invoices/{id}
     */
    public function show($id)
    {
        try {
            // comprueba si existe la factura
            $invoice = ElectronicInvoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }

            // llama al servicio (mantiene la lógica existente)
            $invoiceComplete = $this->invoiceService->getInvoiceComplete($id);

            return response()->json([
                'success' => true,
                'data' => $invoiceComplete
            ]);
        } catch (\Exception $e) {
            // log para depuración
            Log::error('ElectronicInvoiceController@show error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // muestra mensaje más útil en entorno de desarrollo
            $message = config('app.debug') ? $e->getMessage() : 'Error al obtener la factura';

            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }
    }

    /**
     * Enviar factura a la DIAN (SIMULADO)
     * POST /api/invoices/{id}/send-dian
     */
    public function sendToDian($id)
    {
        $result = $this->invoiceService->sendToDian($id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Consultar estado de factura en la DIAN
     * GET /api/invoices/{id}/status
     */
    public function checkStatus($id)
    {
        try {
            $invoice = ElectronicInvoice::findOrFail($id);

            if (!$invoice->uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura aún no ha sido enviada a la DIAN'
                ], 400);
            }

            $result = $this->dianSimulator->checkInvoiceStatus($invoice->uuid);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar factura (solo si no está aceptada por la DIAN)
     * POST /api/invoices/{id}/cancel
     */
    public function cancel($id)
    {
        $result = $this->invoiceService->cancelInvoice($id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Actualizar factura (solo en estado borrador)
     * PUT /api/invoices/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $invoice = ElectronicInvoice::findOrFail($id);

            if ($invoice->internal_status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden editar facturas en estado borrador'
                ], 400);
            }

            $validated = $request->validate([
                'buyer_id' => 'nullable|exists:users,id',  // Cliente (comprador) - debe ser usuario con role 'client'
                'observation' => 'nullable|string|max:255',
                'payment_means_code' => 'nullable|string|max:6',
                'payment_means_name' => 'nullable|string|max:255'
            ]);

            // Si se envía buyer_id, validar que sea un cliente válido
            if (isset($validated['buyer_id'])) {
                $loggedUser = Auth::user();
                $buyer = \App\Models\User::with('role')->findOrFail($validated['buyer_id']);

                // Validar que pertenezca a la misma empresa
                if ($buyer->company_id !== $loggedUser->company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El cliente seleccionado no pertenece a su empresa'
                    ], 400);
                }

                // Validar que tenga el role 'client'
                if (!$buyer->role || $buyer->role->role_name !== 'cliente') {
                    return response()->json([
                        'success' => false,
                        'message' => 'El usuario seleccionado no es un cliente. Debe tener el role "cliente"'
                    ], 400);
                }
            }

            $invoice->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Factura actualizada exitosamente',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar factura (solo en estado borrador)
     * DELETE /api/invoices/{id}
     */
    public function destroy($id)
    {
        try {
            $invoice = ElectronicInvoice::findOrFail($id);

            if ($invoice->internal_status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar facturas en estado borrador'
                ], 400);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Factura eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de facturación
     * GET /api/invoices/stats?company_id=1&date_from=2024-01-01&date_to=2024-12-31
     */
    public function stats(Request $request)
    {
        $companyId = $request->input('company_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $stats = $this->invoiceService->getInvoiceStats($companyId, $dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Generar código QR de la factura
     * GET /api/invoices/{id}/qr
     */
    public function generateQR($id)
    {
        try {
            $invoice = ElectronicInvoice::findOrFail($id);

            if (!$invoice->uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura no tiene CUFE generado'
                ], 400);
            }

            $qrUrl = $this->dianSimulator->generateQRCodeImage($invoice);

            return response()->json([
                'success' => true,
                'data' => [
                    'qr_url' => $qrUrl,
                    'cufe' => $invoice->uuid
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadXML($id)
    {
        $doc = ElectronicDocument::where('electronic_invoice_id', $id)
            ->whereNull('credit_debit_note_id')
            ->orderByDesc('id')
            ->first();
        if (!$doc || !$doc->xml_document) {
            return response()->json(['success' => false, 'message' => 'Documento electrónico no encontrado para la factura'], 404);
        }
        return response($doc->xml_document, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="invoice-' . $id . '.xml"');
    }

    public function preview($id)
    {
        $invoice = ElectronicInvoice::with([
            'user.company',
            'buyer',
            'invoiceDetails.item.taxes',
            'invoiceDetails.item.measurementUnit',
            'electronicDocuments'
        ])->findOrFail($id);

        // Generar URL del QR
        $qrUrl = null;
        if ($invoice->electronicDocuments->isNotEmpty()) {
            $qrUrl = $invoice->electronicDocuments->first()->qr_code;
        } else if ($invoice->uuid) {
            $qrUrl = $this->dianSimulator->generateQRUrl($invoice, $invoice->uuid);
        }

        $enableImages = extension_loaded('gd') || extension_loaded('imagick');

        $html = view('pdf.invoice', [
            'invoice' => $invoice,
            'qrUrl' => $qrUrl,
            'enableImages' => $enableImages
        ])->render();

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    /**
     * Descargar PDF de factura con logo y QR
     * GET /api/invoices/{id}/download-pdf
     */
    public function downloadPDF($id)
    {
        try {
            $invoice = ElectronicInvoice::with([
                'user.company',
                'buyer',
                'invoiceDetails.item.taxes',
                'invoiceDetails.item.measurementUnit',
                'electronicDocuments'
            ])->findOrFail($id);

            // Generar URL del QR si no existe en los documentos electrónicos
            $qrUrl = null;
            if ($invoice->electronicDocuments->isNotEmpty()) {
                $qrUrl = $invoice->electronicDocuments->first()->qr_code;
            } else if ($invoice->uuid) {
                $qrUrl = $this->dianSimulator->generateQRUrl($invoice, $invoice->uuid);
            }

            $enableImages = extension_loaded('gd') || extension_loaded('imagick');
            $options = [
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true
            ];
            if ($enableImages) {
                $options['isRemoteEnabled'] = true;
            }

            $pdf = Pdf::loadView('pdf.invoice', [
                'invoice' => $invoice,
                'qrUrl' => $qrUrl,
                'enableImages' => $enableImages
            ])->setPaper('letter')
                ->setOptions($options);

            $filename = 'factura-' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error generando PDF completo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createNote(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:250',
            'note_type' => ['required', Rule::in(['debit', 'credit'])],
            'total_amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        $invoice = ElectronicInvoice::with('user.company')->findOrFail($id);
        $amount = (float)$validated['total_amount'];
        if ($validated['note_type'] === 'credit' && $amount > (float)($invoice->payable_amount ?? 0)) {
            return response()->json(['success' => false, 'message' => 'El valor de la nota crédito no puede exceder el total de la factura'], 400);
        }
        $companyId = $invoice->user->company_id;
        $prefix = $validated['note_type'] === 'credit' ? 'CN' : 'DN';
        $noteNumber = $prefix . '-' . $companyId . '-' . now()->format('YmdHis');
        $note = CreditDebitNote::create([
            'electronic_invoice_id' => $invoice->id,
            'reason' => $validated['reason'],
            'note_type' => $validated['note_type'],
            'note_number' => $noteNumber,
            'status' => 'pending',
            'issue_date' => now(),
            'total_amount' => $amount,
        ]);
        return response()->json(['success' => true, 'data' => $note], 201);
    }

    public function listNotes($id)
    {
        $notes = CreditDebitNote::where('electronic_invoice_id', $id)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'data' => $notes]);
    }

    public function annulWithCreditNote(Request $request, $id)
    {
        $invoice = ElectronicInvoice::findOrFail($id);
        $amount = (float)($invoice->payable_amount ?? 0);
        if ($amount <= 0) {
            return response()->json(['success' => false, 'message' => 'La factura no tiene total pagadero válido para anulación'], 400);
        }
        $validated = $request->validate(['reason' => 'required|string|max:250']);
        $note = CreditDebitNote::create([
            'electronic_invoice_id' => $invoice->id,
            'reason' => $validated['reason'],
            'note_type' => 'credit',
            'note_number' => 'CN-' . ($invoice->user->company_id) . '-' . now()->format('YmdHis'),
            'status' => 'pending',
            'issue_date' => now(),
            'total_amount' => $amount,
        ]);
        return response()->json(['success' => true, 'data' => $note], 201);
    }
}
