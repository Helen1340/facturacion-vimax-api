<?php

namespace App\Http\Controllers;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class InvoiceDetailController extends Controller
{
    /**
     * Listado (GET /api/invoice-details)
     */
    public function index()
    {
        $details = InvoiceDetail::with(['item','electronicInvoice'])->get();
        return response()->json($details);
    }

    /**
     * Store (POST /api/invoice-details)
     * Espera:
     *  - electronic_invoice_id
     *  - item_type: 'product' o 'service'
     *  - item_id (opcional, si se pasa, se usa ese)
     *  - cantidad, precio_unitario, descripcion, etc.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'electronic_invoice_id' => 'required|exists:electronic_invoices,id',
            'item_type' => 'required|string|in:product,service',
            'item_id' => 'nullable|integer',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        // Mapear short name a clase
        $map = [
            'product' => Product::class,
            'service' => Service::class,
        ];

        $itemClass = $map[$data['item_type']];

        // Si no proveen item_id, crear uno nuevo
        if (empty($data['item_id'])) {
            // Podemos crear un item básico
            $item = $itemClass::factory()->create([
                'name' => $data['descripcion'] ?? 'Item creado por API',
            ]);
        } else {
            $item = $itemClass::find($data['item_id']);
            if (!$item) {
                return response()->json(['message' => 'Item no encontrado'], 404);
            }
        }

        // Precio unitario: si no viene lo tomamos del item (asumiendo columna precio_unitario o price)
        $precio_unitario = $data['precio_unitario'] ?? ($item->precio_unitario ?? ($item->price ?? 0));
        $cantidad = $data['cantidad'];
        $valor_total = round($precio_unitario * $cantidad, 2);

        $detail = InvoiceDetail::create([
            'electronic_invoice_id' => $data['electronic_invoice_id'],
            'descripcion' => $data['descripcion'] ?? $item->name ?? null,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'valor_total' => $valor_total,
            'subtotal' => $valor_total,
            'descuento' => $data['descuento'] ?? null,
            'impuestos_aplicados' => null,
            'valor_impuesto' => null,
            'item_id' => $item->id,
            'item_type' => $itemClass,
        ]);

        return response()->json($detail->load('item'), 201);
    }

    /**
     * Mostrar detalle (GET /api/invoice-details/{id})
     */
    public function show($id)
    {
        $detail = InvoiceDetail::with(['item','electronicInvoice'])->findOrFail($id);
        return response()->json($detail);
    }

    /**
     * Actualizar (PUT/PATCH /api/invoice-details/{id})
     */
    public function update(Request $request, $id)
    {
        $detail = InvoiceDetail::findOrFail($id);

        $data = $request->validate([
            'cantidad' => 'nullable|integer|min:1',
            'precio_unitario' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        if (isset($data['precio_unitario'])) {
            $detail->precio_unitario = $data['precio_unitario'];
        }
        if (isset($data['cantidad'])) {
            $detail->cantidad = $data['cantidad'];
        }
        if (isset($data['descripcion'])) {
            $detail->descripcion = $data['descripcion'];
        }
        if (isset($data['descuento'])) {
            $detail->descuento = $data['descuento'];
        }

        // recalcular valor_total/subtotal
        $detail->valor_total = round($detail->precio_unitario * $detail->cantidad, 2);
        $detail->subtotal = $detail->valor_total;

        $detail->save();

        return response()->json($detail->load('item'));
    }

    /**
     * Eliminar (DELETE /api/invoice-details/{id})
     */
    public function destroy($id)
    {
        $detail = InvoiceDetail::findOrFail($id);
        $detail->delete();
        return response()->json(['message' => 'Detalle eliminado']);
    }

}
