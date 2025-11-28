<?php

namespace App\Services;

use App\Models\ElectronicInvoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaAprobada;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InvoiceService
{
    private $dianSimulator;

    public function __construct(DianSimulatorService $dianSimulator)
    {
        $this->dianSimulator = $dianSimulator;
    }

    /**
     * Crea una factura completa con sus detalles
     * 
     * @param array $data
     * @return array
     */
    public function createInvoice(array $data)
    {
        DB::beginTransaction();

        try {
            // Obtener usuario logueado y su empresa
            $loggedUser = Auth::user();
            if (!$loggedUser || !$loggedUser->company_id) {
                throw new \Exception('Usuario no autenticado o sin empresa asociada');
            }

            // Validar que el usuario (facturador) existe y tiene empresa
            $user = User::with('company')->findOrFail($data['user_id']);

            if (!$user->company) {
                throw new \Exception('El usuario no tiene una empresa asociada');
            }

            // Validar que el usuario pertenezca a la misma empresa del usuario logueado
            if ($user->company_id !== $loggedUser->company_id) {
                throw new \Exception('No puede crear facturas para usuarios de otra empresa');
            }

            // Validar que el buyer_id (cliente) existe y es un cliente
            $buyer = User::with(['company', 'role'])->findOrFail($data['buyer_id']);

            if (!$buyer->company) {
                throw new \Exception('El cliente no tiene una empresa asociada');
            }

            // Validar que el buyer pertenezca a la misma empresa del usuario logueado
            if ($buyer->company_id !== $loggedUser->company_id) {
                throw new \Exception('No puede crear facturas para clientes de otra empresa');
            }

            // Validar que el buyer tenga el role 'client'
            if (!$buyer->role || $buyer->role->role_name !== 'cliente') {
                throw new \Exception('El usuario seleccionado no es un cliente. Debe tener el role "cliente"');
            }

            // 1. Generar número de factura
            $invoiceNumber = $this->generateInvoiceNumber($user);

            // 2. Crear factura (sin totales aún)
            $invoice = ElectronicInvoice::create([
                'user_id' => $data['user_id'],
                'buyer_id' => $data['buyer_id'],  // Cliente (comprador)
                'invoice_number' => $invoiceNumber,
                'issue_date' => now(),
                'internal_status' => 'draft',
                'observation' => $data['observation'] ?? null,
                'ubl_version' => '2.1',
                'customization_id' => 'DIAN 2.1: Factura Electrónica de Venta',
                'profile_id' => 'DIAN 2.1',
                'document_currency_code' => 'COP',
                'invoice_type_code' => '01',
                'payment_means_code' => $data['payment_means_code'] ?? '10',
                'payment_means_name' => $data['payment_means_name'] ?? 'Contado',
                'dian_status' => 'pending'
            ]);

            // 3. Crear detalles de factura y calcular totales
            $totals = $this->createInvoiceDetails($invoice, $data['items']);

            // 4. Actualizar totales de la factura
            $invoice->update($totals);

            DB::commit();

            // Cargar relaciones para la respuesta
            $invoice->load([
                'invoiceDetails.item.taxes',
                'invoiceDetails.item.measurementUnit',
                'user.company',
                'buyer.company',
                'buyer.role'
            ]);

            return [
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => $invoice
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creando factura', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear la factura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea los detalles de la factura y calcula totales UBL
     */
    private function createInvoiceDetails($invoice, $items)
    {
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        // Obtener empresa del usuario logueado para validar productos/servicios
        $loggedUser = Auth::user();
        if (!$loggedUser || !$loggedUser->company_id) {
            throw new \Exception('Usuario no autenticado o sin empresa asociada');
        }

        foreach ($items as $itemData) {
            // Obtener el item (producto o servicio) - CompanyScope filtra automáticamente por empresa
            $item = $itemData['type'] === 'product'
                ? Product::with(['taxes', 'measurementUnit'])->find($itemData['id'])
                : Service::with(['taxes', 'measurementUnit'])->find($itemData['id']);

            if (!$item) {
                throw new \Exception("Item no encontrado: {$itemData['type']} ID {$itemData['id']}. Verifique que pertenezca a su empresa.");
            }

            // Validar que el item pertenezca a la empresa del usuario logueado
            if ($item->company_id !== $loggedUser->company_id) {
                throw new \Exception("El item '{$item->name}' no pertenece a su empresa");
            }

            // Validar que el item esté activo
            if ($item->status !== 'Active') {
                throw new \Exception("El item '{$item->name}' no está activo");
            }

            $quantity = $itemData['quantity'];
            $unitPrice = $item->unit_price;
            $discount = $itemData['discount'] ?? 0;

            // Calcular subtotal de la línea (sin impuestos)
            $lineSubtotal = ($unitPrice * $quantity) - $discount;

            // Calcular impuestos de la línea
            $lineTax = $this->calculateItemTax($item, $lineSubtotal);

            // Total de la línea (subtotal + impuestos)
            $lineTotal = $lineSubtotal + $lineTax;

            // Crear detalle
            InvoiceDetail::create([
                'electronic_invoice_id' => $invoice->id,
                'item_id' => $item->id,
                'item_type' => get_class($item),
                'description' => $item->description ?? $item->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_extension_amount' => $lineSubtotal,
                'discount_amount' => $discount,
                'tax_amount' => $lineTax,
                'total_line_amount' => $lineTotal
            ]);

            // Acumular totales
            $subtotal += $lineSubtotal;
            $totalTax += $lineTax;
            $totalDiscount += $discount;
        }

        // Retornar totales en formato UBL 2.1
        return [
            'line_extension_amount' => round($subtotal, 2),
            'tax_exclusive_amount' => round($subtotal, 2),
            'tax_inclusive_amount' => round($subtotal + $totalTax, 2),
            'payable_amount' => round($subtotal + $totalTax, 2),
            'total_discount' => round($totalDiscount, 2)
        ];
    }

    /**
     * Calcula el impuesto para un item basado en sus impuestos configurados
     */
    private function calculateItemTax($item, $amount)
    {
        $totalTax = 0;

        // Obtener impuestos del item
        $taxes = $item->taxes;

        if ($taxes->isEmpty()) {
            return 0; // Sin impuestos
        }

        foreach ($taxes as $tax) {
            if ($tax->status !== 'Activo') {
                continue; // Saltar impuestos inactivos
            }

            switch ($tax->application_type) {
                case 'Porcentaje':
                    $taxValue = ($amount * $tax->percentage) / 100;
                    break;

                case 'ValorFijo':
                    $taxValue = $tax->fixed_value;
                    break;

                case 'Retencion':
                    // Las retenciones son negativas (restan del total)
                    $taxValue = - ($amount * $tax->percentage) / 100;
                    break;

                default:
                    $taxValue = 0;
            }

            $totalTax += $taxValue;
        }

        return round($totalTax, 2);
    }

    /**
     * Genera un número de factura único con prefijo de la numeración DIAN
     */
    private function generateInvoiceNumber(User $user)
    {
        $company = $user->company;

        // Obtener numeración activa de tipo Factura
        $numbering = $company->dianNumberings()
            ->where('document_type', 'Factura')
            ->where('current_status', 'Activo')
            ->first();

        if (!$numbering) {
            throw new \Exception('No hay numeración DIAN activa para facturas en esta empresa. Por favor configure una numeración en el sistema.');
        }

        // Verificar vigencia de la resolución
        $today = now()->toDateString();
        if ($today < $numbering->validity_start_date || $today > $numbering->validity_end_date) {
            throw new \Exception('La resolución DIAN no está vigente. Vigencia: ' . $numbering->validity_start_date . ' a ' . $numbering->validity_end_date);
        }

        // Buscar el último número usado con este prefijo
        $lastInvoice = ElectronicInvoice::whereHas('user', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })
            ->where('invoice_number', 'like', $numbering->prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extraer el número consecutivo de la factura anterior
            $parts = explode('-', $lastInvoice->invoice_number);
            $lastNumber = (int)end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            // Primera factura con esta numeración
            $nextNumber = $numbering->start_number;
        }

        // Verificar que no exceda el rango autorizado
        if ($nextNumber > $numbering->end_number) {
            throw new \Exception("Se ha agotado el rango de numeración DIAN. Rango autorizado: {$numbering->start_number} a {$numbering->end_number}");
        }

        // Formato: PREFIJO-COMPANY_ID-NUMERO
        // Ejemplo: FD-1-1, FE-1-523
        return "{$numbering->prefix}-{$company->id}-{$nextNumber}";
    }

    /**
     * Envía email al cliente cuando la factura es aceptada por la DIAN
     */
    private function sendApprovalEmail($invoice)
    {
        try {
            $cliente = $invoice->buyer;

            if (!$cliente || !$cliente->email) {
                Log::warning('No se puede enviar email: cliente sin email', [
                    'factura_id' => $invoice->id,
                    'cliente_id' => $invoice->buyer_id
                ]);
                return false;
            }

            // Enviar email con PDF adjunto
            Mail::to($cliente->email)
                ->send(new FacturaAprobada($invoice, $cliente));

            Log::info('✅ Email de factura aprobada enviado CON PDF', [
                'factura' => $invoice->invoice_number,
                'cliente' => $cliente->email,
                'pdf_generado' => true
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error enviando email de factura aprobada', [
                'factura_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Envía la factura a la DIAN (simulado)
     */
    public function sendToDian($invoiceId)
    {
        $invoice = ElectronicInvoice::with([
            'user.company',
            'user.company.dianNumberings',
            'user.company.digitalCertificates',
            'buyer',
            'buyer.company',
            'buyer.role',
            'invoiceDetails.item.taxes',
            'invoiceDetails.item.measurementUnit'
        ])->findOrFail($invoiceId);

        // Validar que la factura esté en estado borrador
        if ($invoice->internal_status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Solo se pueden enviar facturas en estado borrador. Estado actual: ' . $invoice->internal_status
            ];
        }

        // Validar que tenga buyer (cliente)
        if (!$invoice->buyer) {
            return [
                'success' => false,
                'message' => 'La factura no tiene un cliente asignado. Debe seleccionar un cliente (buyer_id).'
            ];
        }

        // Validar que tenga detalles
        if ($invoice->invoiceDetails->count() === 0) {
            return [
                'success' => false,
                'message' => 'La factura no tiene detalles. Debe agregar al menos un producto o servicio.'
            ];
        }

        // Validar que los totales sean correctos
        $calculatedSubtotal = $invoice->invoiceDetails->sum('line_extension_amount');
        $calculatedTax = $invoice->invoiceDetails->sum('tax_amount');
        $calculatedTotal = $calculatedSubtotal + $calculatedTax;

        // Tolerancia de 0.01 para diferencias de redondeo
        if (abs($invoice->payable_amount - $calculatedTotal) > 0.01) {
            Log::warning('Inconsistencia en totales de factura', [
                'invoice_id' => $invoice->id,
                'stored_total' => $invoice->payable_amount,
                'calculated_total' => $calculatedTotal
            ]);
        }

        // Cambiar estado interno a "emitida"
        $invoice->update(['internal_status' => 'issued']);

        // Simular envío a la DIAN
        $result = $this->dianSimulator->sendInvoiceToDian($invoice);

        //  ENVIAR EMAIL SI FUE EXITOSO
        if ($result['success'] && $result['data']['status'] === 'accepted') {
            $this->sendApprovalEmail($invoice);
        }

        return $result;
    }

    /**
     * Obtiene una factura con todos sus datos relacionados
     */
    public function getInvoiceComplete($invoiceId)
    {
        $invoice = ElectronicInvoice::with([
            'user.company',
            'buyer.company',
            'buyer.role',
            'invoiceDetails.item.taxes',
            'invoiceDetails.item.measurementUnit',
            'electronicDocuments.dianStatusResponses',
            'payment.paymentMethod'
        ])->findOrFail($invoiceId);

        // Asegurar que invoiceDetails sea una colección/array
        // Convertir a array para asegurar que se serialice correctamente
        $invoiceArray = $invoice->toArray();

        // Si invoiceDetails no está como array, forzarlo
        if (isset($invoiceArray['invoice_details']) && !is_array($invoiceArray['invoice_details'])) {
            $invoiceArray['invoice_details'] = [];
        }

        // También asegurar el formato correcto de invoiceDetails
        if ($invoice->relationLoaded('invoiceDetails')) {
            $invoiceArray['invoiceDetails'] = $invoice->invoiceDetails->map(function ($detail) {
                $detailArray = $detail->toArray();
                // Asegurar que el item esté correctamente formateado
                if ($detail->relationLoaded('item') && $detail->item) {
                    $detailArray['item'] = $detail->item->toArray();
                    // Asegurar que taxes y measurementUnit estén como arrays
                    if ($detail->item->relationLoaded('taxes')) {
                        $detailArray['item']['taxes'] = $detail->item->taxes->toArray();
                    }
                    if ($detail->item->relationLoaded('measurementUnit')) {
                        $detailArray['item']['measurementUnit'] = $detail->item->measurementUnit->toArray();
                    }
                }
                return $detailArray;
            })->toArray();
        } else {
            $invoiceArray['invoiceDetails'] = [];
        }

        return $invoiceArray;
    }

    /**
     * Cancela una factura (solo si no ha sido aceptada por la DIAN)
     */
    public function cancelInvoice($invoiceId)
    {
        $invoice = ElectronicInvoice::findOrFail($invoiceId);

        // Validar que no esté aceptada por la DIAN
        if ($invoice->dian_status === 'accepted') {
            return [
                'success' => false,
                'message' => 'No se puede cancelar una factura aceptada por la DIAN. Debe crear una nota crédito para anularla.'
            ];
        }

        $invoice->update([
            'internal_status' => 'cancelled',
            'dian_status' => 'cancelled'
        ]);

        return [
            'success' => true,
            'message' => 'Factura cancelada exitosamente',
            'data' => $invoice
        ];
    }

    /**
     * Lista facturas con filtros
     * Nota: Las facturas se filtran automáticamente por la empresa del usuario logueado
     */
    public function listInvoices($filters = [])
    {
        $query = ElectronicInvoice::with(['user', 'user.company']);

        // Filtrar por usuario (solo usuarios de la misma empresa)
        if (isset($filters['user_id'])) {
            $loggedUser = Auth::user();
            if ($loggedUser && $loggedUser->company_id) {
                // Validar que el usuario filtrado pertenezca a la misma empresa
                $query->where('user_id', $filters['user_id'])
                    ->whereHas('user', function ($q) use ($loggedUser) {
                        $q->where('company_id', $loggedUser->company_id);
                    });
            } else {
                $query->where('user_id', $filters['user_id']);
            }
        }

        // El filtro por empresa se maneja automáticamente por CompanyScope
        // No permitimos filtrar manualmente por company_id por seguridad

        // Filtrar por estado DIAN
        if (isset($filters['dian_status'])) {
            $query->where('dian_status', $filters['dian_status']);
        }

        // Filtrar por estado interno
        if (isset($filters['internal_status'])) {
            $query->where('internal_status', $filters['internal_status']);
        }

        // Filtrar por rango de fechas
        if (isset($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        // Ordenar por más reciente
        $query->orderBy('id', 'desc');

        // Paginar o retornar todo
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }

        return $query->get();
    }

    /**
     * Obtiene estadísticas de facturación
     */
    public function getInvoiceStats($companyId = null, $dateFrom = null, $dateTo = null)
    {
        $query = ElectronicInvoice::query();

        if ($companyId) {
            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        if ($dateFrom) {
            $query->where('issue_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('issue_date', '<=', $dateTo);
        }

        return [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('payable_amount'),
            'accepted' => (clone $query)->where('dian_status', 'accepted')->count(),
            'rejected' => (clone $query)->where('dian_status', 'rejected')->count(),
            'pending' => (clone $query)->where('dian_status', 'pending')->count(),
            'cancelled' => (clone $query)->where('dian_status', 'cancelled')->count(),
            'average_amount' => $query->avg('payable_amount'),
            'by_status' => [
                'draft' => (clone $query)->where('internal_status', 'draft')->count(),
                'issued' => (clone $query)->where('internal_status', 'issued')->count(),
                'cancelled' => (clone $query)->where('internal_status', 'cancelled')->count(),
            ]
        ];
    }

    /**
     * Actualiza una factura en estado borrador
     */
    public function updateInvoice($invoiceId, array $data)
    {
        $invoice = ElectronicInvoice::findOrFail($invoiceId);

        if ($invoice->internal_status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Solo se pueden editar facturas en estado borrador'
            ];
        }

        $invoice->update($data);

        return [
            'success' => true,
            'message' => 'Factura actualizada exitosamente',
            'data' => $invoice
        ];
    }

    /**
     * Elimina una factura en estado borrador
     */
    public function deleteInvoice($invoiceId)
    {
        $invoice = ElectronicInvoice::findOrFail($invoiceId);

        if ($invoice->internal_status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Solo se pueden eliminar facturas en estado borrador'
            ];
        }

        $invoice->delete();

        return [
            'success' => true,
            'message' => 'Factura eliminada exitosamente'
        ];
    }
}
