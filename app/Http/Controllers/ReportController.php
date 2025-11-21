<?php

namespace App\Http\Controllers;

use App\Models\ElectronicInvoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // REPORTE DE FACTURAS (JSON para Angular)
    public function reporteFacturas(Request $request)
    {
        $query = ElectronicInvoice::with(['buyer', 'payment', 'payment.paymentMethod'])
            ->join('users', 'electronic_invoices.buyer_id', '=', 'users.id');

        // filtros desde query params
        if ($request->numero_factura) {
            $query->where('invoice_number', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->cliente) {
            $query->where(function ($q) use ($request) {
                $q->where('users.first_name', 'like', '%' . $request->cliente . '%')
                    ->orWhere('users.document_number', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->desde) {
            $query->whereDate('issue_date', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('issue_date', '<=', $request->hasta);
        }

        if ($request->estado && $request->estado != 'Todos') {
            $query->where('internal_status', $request->estado);
        }

        $facturas = $query->select(
            'electronic_invoices.*',
            'users.first_name as cliente_nombre',
            'users.document_number as cliente_documento',
            'users.email as buyer_email'
        )
            ->orderBy('issue_date', 'desc')
            ->get();

        $out = $facturas->map(function ($f) {
            return [
                'invoice_number' => $f->invoice_number,
                'issue_date' => optional($f->issue_date)->format('Y-m-d'),
                'internal_status' => $f->internal_status,
                'payable_amount' => $f->payable_amount,
                'sub_total' => $f->sub_total,
                'total_tax' => $f->total_tax,
                'total_invoice' => $f->total_invoice,
                'cliente_nombre' => $f->cliente_nombre,
                'cliente_documento' => $f->cliente_documento,
                'buyer_email' => $f->buyer_email,
                'document_currency_code' => $f->document_currency_code,
                'uuid' => $f->uuid,
                'dian_status' => $f->dian_status,
                'payment_date' => optional(optional($f->payment)->payment_date)->format('Y-m-d'),
                'amount_paid' => optional($f->payment)->amount_paid,
                'metodo_pago' => optional(optional($f->payment)->paymentMethod)->name,
            ];
        });

        if ($request->query('format') === 'csv') {
            return $this->toCsv(
                $out,
                ['invoice_number', 'issue_date', 'internal_status', 'payable_amount', 'sub_total', 'total_tax', 'total_invoice', 'document_currency_code', 'uuid', 'dian_status', 'payment_date', 'amount_paid', 'metodo_pago', 'cliente_nombre', 'cliente_documento', 'buyer_email'],
                'reporte-facturas.csv'
            );
        }

        return response()->json($out);
    }

    // REPORTE DE PAGOS
    public function reportePagos(Request $request)
    {
        $query = Payment::with(['electronicInvoice.buyer', 'paymentMethod'])
            ->join('electronic_invoices', 'payments.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->join('users', 'electronic_invoices.buyer_id', '=', 'users.id')
            ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id');

        // Filtros (similar a facturas)
        if ($request->desde) {
            $query->whereDate('payments.payment_date', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('payments.payment_date', '<=', $request->hasta);
        }

        if ($request->cliente) {
            $query->where(function ($q) use ($request) {
                $q->where('users.first_name', 'like', '%' . $request->cliente . '%')
                    ->orWhere('users.document_number', 'like', '%' . $request->cliente . '%');
            });
        }

        $pagos = $query->select(
            'payments.*',
            'users.first_name as cliente_nombre',
            'users.document_number as cliente_documento',
            'electronic_invoices.invoice_number',
            'electronic_invoices.issue_date',
            'electronic_invoices.internal_status',
            'electronic_invoices.payable_amount',
            'payment_methods.name as metodo_pago'
        )
            ->orderBy('payments.payment_date', 'desc')
            ->get();

        $out = $pagos->map(function ($p) {
            return [
                'payment_date' => optional($p->payment_date)->format('Y-m-d'),
                'amount_paid' => $p->amount_paid,
                'currency' => $p->currency,
                'invoice_number' => $p->invoice_number,
                'issue_date' => optional($p->issue_date)->format('Y-m-d'),
                'internal_status' => $p->internal_status,
                'metodo_pago' => $p->metodo_pago,
                'payable_amount' => $p->payable_amount,
                'paid_ratio' => $p->payable_amount ? round(((float) $p->amount_paid) / ((float) $p->payable_amount), 4) : null,
                'cliente_nombre' => $p->cliente_nombre,
                'cliente_documento' => $p->cliente_documento,
            ];
        });

        if ($request->query('format') === 'csv') {
            return $this->toCsv(
                $out,
                ['payment_date', 'amount_paid', 'currency', 'invoice_number', 'issue_date', 'internal_status', 'metodo_pago', 'payable_amount', 'paid_ratio', 'cliente_nombre', 'cliente_documento'],
                'reporte-pagos.csv'
            );
        }

        return response()->json($out);
    }

    public function reporteUsuarios(Request $request)
    {
        $query = User::with('role:id,role_name');
        if ($request->filled('usuario')) {
            $search = $request->usuario;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('document_number', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('estado') && $request->estado !== 'Todos') {
            $query->where('status', $request->estado);
        }
        if ($request->filled('rol') && $request->rol !== 'Todos') {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('role_name', $request->rol);
            });
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        $usuarios = $query->orderBy('first_name')->get([
            'id',
            'first_name',
            'email',
            'document_number',
            'status',
            'created_at',
            'last_access',
            'role_id'
        ]);
        $mapped = $usuarios->map(function ($u) {
            return [
                'id' => $u->id,
                'nombre' => $u->first_name,
                'correo' => $u->email,
                'documento' => $u->document_number,
                'estado' => $u->status,
                'fecha_creacion' => optional($u->created_at)->format('Y-m-d H:i:s'),
                'ultimo_acceso' => $u->last_access ? $u->last_access->format('Y-m-d H:i') : null,
                'rol' => optional($u->role)->role_name ?? 'Sin rol',
            ];
        });
        if ($request->query('format') === 'csv') {
            return $this->toCsv(
                $mapped,
                ['id', 'nombre', 'correo', 'documento', 'estado', 'fecha_creacion', 'ultimo_acceso', 'rol'],
                'reporte-usuarios.csv'
            );
        }
        return response()->json($mapped);
    }

    public function resumenFacturas(Request $request)
    {
        $porEstado = ElectronicInvoice::selectRaw('internal_status, COUNT(*) as cantidad, COALESCE(SUM(payable_amount),0) as total')
            ->groupBy('internal_status')
            ->orderByDesc('cantidad')
            ->get();
        $porMes = ElectronicInvoice::selectRaw("DATE_FORMAT(issue_date, '%Y-%m') as mes, COALESCE(SUM(payable_amount),0) as total, COUNT(*) as cantidad")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $topClientes = ElectronicInvoice::join('users', 'electronic_invoices.buyer_id', '=', 'users.id')
            ->selectRaw('users.first_name as cliente, users.document_number as documento, COALESCE(SUM(electronic_invoices.payable_amount),0) as total, COUNT(*) as cantidad')
            ->groupBy('users.id', 'users.first_name', 'users.document_number')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        return response()->json([
            'por_estado' => $porEstado,
            'por_mes' => $porMes,
            'top_clientes' => $topClientes,
        ]);
    }

    public function resumenPagos(Request $request)
    {
        $porMes = Payment::selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as mes, COALESCE(SUM(amount_paid),0) as total, COUNT(*) as cantidad")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $porMetodo = Payment::join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->selectRaw('payment_methods.name as metodo, COUNT(*) as cantidad, COALESCE(SUM(payments.amount_paid),0) as total')
            ->groupBy('payment_methods.id', 'payment_methods.name')
            ->orderByDesc('total')
            ->get();
        return response()->json([
            'por_mes' => $porMes,
            'por_metodo' => $porMetodo,
        ]);
    }

    public function reporteProductos(Request $request)
    {
        $q = InvoiceDetail::query()
            ->where('item_type', Product::class)
            ->join('products', 'invoice_details.item_id', '=', 'products.id')
            ->join('electronic_invoices', 'invoice_details.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->leftJoin('measurement_units', 'products.measurement_unit_id', '=', 'measurement_units.id');
        if ($request->desde) {
            $q->whereDate('electronic_invoices.issue_date', '>=', $request->desde);
        }
        if ($request->hasta) {
            $q->whereDate('electronic_invoices.issue_date', '<=', $request->hasta);
        }
        if ($request->estado && $request->estado !== 'Todos') {
            $q->where('electronic_invoices.internal_status', $request->estado);
        }
        if ($request->q) {
            $search = $request->q;
            $q->where(function ($w) use ($search) {
                $w->where('products.name', 'like', "%$search%")
                    ->orWhere('products.product_code', 'like', "%$search%");
            });
        }
        $q = $q->selectRaw(
            'products.id, products.product_code, products.name,
                 measurement_units.name as unidad, measurement_units.dian_code as unidad_dian,
                 COALESCE(SUM(invoice_details.quantity),0) as unidades,
                 COALESCE(SUM(invoice_details.line_extension_amount),0) as subtotal,
                 COALESCE(SUM(invoice_details.tax_amount),0) as impuestos,
                 COALESCE(SUM(invoice_details.total_line_amount),0) as total,
                 COALESCE(AVG(invoice_details.unit_price),0) as precio_promedio'
        )
            ->groupBy('products.id', 'products.product_code', 'products.name', 'measurement_units.name', 'measurement_units.dian_code')
            ->orderByDesc('total');
        if ($request->query('format') === 'csv') {
            $rows = $q->get();
            return $this->toCsv($rows, ['product_code', 'name', 'unidad', 'unidad_dian', 'unidades', 'subtotal', 'impuestos', 'total', 'precio_promedio'], 'reporte-productos.csv');
        }
        $perPage = intval($request->query('perPage', 50));
        $rows = $q->paginate($perPage);
        return response()->json($rows);
    }

    public function reporteServicios(Request $request)
    {
        $q = InvoiceDetail::query()
            ->where('item_type', Service::class)
            ->join('services', 'invoice_details.item_id', '=', 'services.id')
            ->join('electronic_invoices', 'invoice_details.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->leftJoin('measurement_units', 'services.measurement_unit_id', '=', 'measurement_units.id');
        if ($request->desde) {
            $q->whereDate('electronic_invoices.issue_date', '>=', $request->desde);
        }
        if ($request->hasta) {
            $q->whereDate('electronic_invoices.issue_date', '<=', $request->hasta);
        }
        if ($request->estado && $request->estado !== 'Todos') {
            $q->where('electronic_invoices.internal_status', $request->estado);
        }
        if ($request->q) {
            $search = $request->q;
            $q->where(function ($w) use ($search) {
                $w->where('services.name', 'like', "%$search%")
                    ->orWhere('services.service_code', 'like', "%$search%");
            });
        }
        $q = $q->selectRaw(
            'services.id, services.service_code, services.name,
                 measurement_units.name as unidad, measurement_units.dian_code as unidad_dian,
                 COALESCE(SUM(invoice_details.quantity),0) as unidades,
                 COALESCE(SUM(invoice_details.line_extension_amount),0) as subtotal,
                 COALESCE(SUM(invoice_details.tax_amount),0) as impuestos,
                 COALESCE(SUM(invoice_details.total_line_amount),0) as total,
                 COALESCE(AVG(invoice_details.unit_price),0) as precio_promedio'
        )
            ->groupBy('services.id', 'services.service_code', 'services.name', 'measurement_units.name', 'measurement_units.dian_code')
            ->orderByDesc('total');
        if ($request->query('format') === 'csv') {
            $rows = $q->get();
            return $this->toCsv($rows, ['service_code', 'name', 'unidad', 'unidad_dian', 'unidades', 'subtotal', 'impuestos', 'total', 'precio_promedio'], 'reporte-servicios.csv');
        }
        $perPage = intval($request->query('perPage', 50));
        $rows = $q->paginate($perPage);
        return response()->json($rows);
    }

    public function resumenProductos(Request $request)
    {
        $porMes = InvoiceDetail::where('item_type', Product::class)
            ->join('electronic_invoices', 'invoice_details.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->selectRaw("DATE_FORMAT(electronic_invoices.issue_date, '%Y-%m') as mes, COALESCE(SUM(invoice_details.total_line_amount),0) as total, COALESCE(SUM(invoice_details.quantity),0) as unidades")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $top = InvoiceDetail::where('item_type', Product::class)
            ->join('products', 'invoice_details.item_id', '=', 'products.id')
            ->selectRaw('products.name, products.product_code, COALESCE(SUM(invoice_details.total_line_amount),0) as total, COALESCE(SUM(invoice_details.quantity),0) as unidades')
            ->groupBy('products.id', 'products.name', 'products.product_code')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        return response()->json(['por_mes' => $porMes, 'top' => $top]);
    }

    public function resumenServicios(Request $request)
    {
        $porMes = InvoiceDetail::where('item_type', Service::class)
            ->join('electronic_invoices', 'invoice_details.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->selectRaw("DATE_FORMAT(electronic_invoices.issue_date, '%Y-%m') as mes, COALESCE(SUM(invoice_details.total_line_amount),0) as total, COALESCE(SUM(invoice_details.quantity),0) as unidades")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        $top = InvoiceDetail::where('item_type', Service::class)
            ->join('services', 'invoice_details.item_id', '=', 'services.id')
            ->selectRaw('services.name, services.service_code, COALESCE(SUM(invoice_details.total_line_amount),0) as total, COALESCE(SUM(invoice_details.quantity),0) as unidades')
            ->groupBy('services.id', 'services.name', 'services.service_code')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        return response()->json(['por_mes' => $porMes, 'top' => $top]);
    }

    private function toCsv($rows, array $columns, string $filename)
    {
        $callback = function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $row) {
                $arr = is_array($row) ? $row : $row->toArray();
                $data = [];
                foreach ($columns as $col) {
                    $data[] = data_get($arr, $col);
                }
                fputcsv($out, $data);
            }
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
