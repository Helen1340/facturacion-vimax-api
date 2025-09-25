<?php

namespace App\Http\Controllers;

use App\Models\ElectronicInvoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // REPORTE DE FACTURAS (JSON para Angular)
    public function reporteFacturas(Request $request)
    {
        $query = ElectronicInvoice::with(['user', 'payment'])
            ->join('users', 'electronic_invoices.user_id', '=', 'users.id');

        // filtros desde query params
        if ($request->numero_factura) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->cliente) {
            $query->where(function ($q) use ($request) {
                $q->where('users.nombre', 'like', '%' . $request->cliente . '%')
                    ->orWhere('users.numero_documento', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->desde) {
            $query->whereDate('fecha_emision', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('fecha_emision', '<=', $request->hasta);
        }

        if ($request->estado && $request->estado != 'Todos') {
            $query->where('estado_interno', $request->estado);
        }

        $facturas = $query->select(
            'electronic_invoices.*',
            'users.nombre as cliente_nombre',
            'users.numero_documento as cliente_nit'
        )
            ->orderBy('fecha_emision', 'desc')
            ->get();

        return response()->json($facturas);
    }

    // REPORTE DE PAGOS
    public function reportePagos(Request $request)
    {
        $query = Payment::with(['electronicInvoice.user', 'paymentMethod'])
            ->join('electronic_invoices', 'payments.electronic_invoice_id', '=', 'electronic_invoices.id')
            ->join('users', 'electronic_invoices.user_id', '=', 'users.id');

        // Filtros (similar a facturas)
        if ($request->desde) {
            $query->whereDate('payments.fecha_pago', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('payments.fecha_pago', '<=', $request->hasta);
        }

        if ($request->cliente) {
            $query->where(function($q) use ($request) {
                $q->where('users.nombre', 'like', '%' . $request->cliente . '%')
                    ->orWhere('users.numero_documento', 'like', '%' . $request->cliente . '%');
            });
        }

        $pagos = $query->select('payments.*', 'users.nombre as cliente_nombre', 
                                'users.numero_documento as cliente_nit',
                                'electronic_invoices.numero_factura')
                        ->orderBy('payments.fecha_pago', 'desc')
                        ->get();

        return response()->json($pagos);
    }

    // REPORTE DE CLIENTES
    public function reporteClientes(Request $request)
    {
        $query = User::whereHas('role', function($q) {
            $q->where('nombre', 'cliente');
        });

        if ($request->cliente) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%')
                    ->orWhere('numero_documento', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->estado && $request->estado != 'Todos') {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->orderBy('nombre')->get();

        return response()->json($clientes);
    }

    // REPORTE DE USUARIOS
    public function reporteUsuarios(Request $request)
    {
        $query = User::with('role');

        if ($request->usuario) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->usuario . '%')
                    ->orWhere('correo_electronico', 'like', '%' . $request->usuario . '%');
            });
        }

        if ($request->estado && $request->estado != 'Todos') {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->orderBy('nombre')->get();

        return response()->json($usuarios);
    }
}
