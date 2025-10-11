<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class InvoiceDetailController extends Controller
{
    /**
     * Listar todos los detalles de factura.
     */
    public function index()
    {
        $details = InvoiceDetail::included()->filter()->sort()->getOrPaginate();
        return response()->json($details);
    }

    /**
     * Registrar un nuevo detalle de factura.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'electronic_invoice_id' => 'required|exists:electronic_invoices,id',
            'item_type' => 'required|string|in:product,service',
            'item_id' => 'required|integer', // ahora obligatorio
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        // Mapear tipo de ítem al modelo correspondiente
        $map = [
            'product' => Product::class,
            'service' => Service::class,
        ];

        $itemClass = $map[$data['item_type']];
        $item = $itemClass::find($data['item_id']);

        if (!$item) {
            return response()->json(['message' => 'El ítem especificado no existe.'], 404);
        }

        // Determinar precio unitario
        $unit_price = $data['unit_price'] ?? ($item->unit_price ?? ($item->price ?? 0));
        $quantity = $data['quantity'];
        $discount = $data['discount_amount'] ?? 0;
        $tax = $data['tax_amount'] ?? 0;

        // Calcular totales conforme al estándar UBL 2.1
        $line_extension_amount = round($unit_price * $quantity, 2) - $discount;
        $total_line_amount = $line_extension_amount + $tax;

        // Crear el detalle
        $detail = InvoiceDetail::create([
            'electronic_invoice_id' => $data['electronic_invoice_id'],
            'item_id' => $item->id,
            'item_type' => $itemClass,
            'description' => $data['description'] ?? $item->name,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'line_extension_amount' => $line_extension_amount,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_line_amount' => $total_line_amount,
        ]);

        return response()->json($detail->load(['item', 'electronicInvoice']), 201);
    }

    /**
     * Mostrar un detalle específico.
     */
    public function show($id)
    {
        $detail = InvoiceDetail::with(['item', 'electronicInvoice'])->findOrFail($id);
        return response()->json($detail);
    }

    /**
     * Actualizar un detalle existente.
     */
    public function update(Request $request, $id)
    {
        $detail = InvoiceDetail::findOrFail($id);

        $data = $request->validate([
            'description' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        // Actualización de campos
        if (isset($data['description'])) $detail->description = $data['description'];
        if (isset($data['quantity'])) $detail->quantity = $data['quantity'];
        if (isset($data['unit_price'])) $detail->unit_price = $data['unit_price'];
        if (isset($data['discount_amount'])) $detail->discount_amount = $data['discount_amount'];
        if (isset($data['tax_amount'])) $detail->tax_amount = $data['tax_amount'];

        // Recalcular totales UBL
        $detail->line_extension_amount = round($detail->unit_price * $detail->quantity, 2) - ($detail->discount_amount ?? 0);
        $detail->total_line_amount = $detail->line_extension_amount + ($detail->tax_amount ?? 0);

        $detail->save();

        return response()->json($detail->load(['item', 'electronicInvoice']));
    }

    /**
     * Eliminar un detalle.
     */
    public function destroy($id)
    {
        $detail = InvoiceDetail::findOrFail($id);
        $detail->delete();

        return response()->json(['message' => 'Detalle de factura eliminado correctamente.']);
    }
}
