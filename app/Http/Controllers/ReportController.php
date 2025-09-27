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

    // ReportController.php
public function reporteUsuarios(Request $request)
{
    $query = User::with('role:id,nombre'); // solo traemos lo necesario del rol

    // 🔎 Filtro por nombre/correo/documento
    if ($request->filled('usuario')) {
        $search = $request->usuario;
        $query->where(function ($q) use ($search) {
            $q->where('nombre', 'like', '%' . $search . '%')
                ->orWhere('correo_electronico', 'like', '%' . $search . '%')
                ->orWhere('numero_documento', 'like', '%' . $search . '%');
        });
    }

    // 🔎 Filtro por estado (Activo/Inactivo)
    if ($request->filled('estado') && $request->estado !== 'Todos') {
        $query->where('estado', $request->estado);
    }

    // 🔎 Filtro por rol (administrador, facturador, contador, cliente)
    if ($request->filled('rol') && $request->rol !== 'Todos') {
        $query->whereHas('role', function ($q) use ($request) {
            $q->where('nombre', $request->rol);
        });
    }

    // 🔎 Filtro por fechas (opcional: creación entre rango)
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }
    if ($request->filled('fecha_fin')) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }

    $usuarios = $query->orderBy('nombre')->get([
        'id',
        'nombre',
        'correo_electronico',
        'numero_documento',
        'estado',
        'created_at',
        'ultimo_acceso',
        'role_id'
    ]);

    // Estructura más limpia para Angular
    $usuarios = $usuarios->map(function ($u) {
        return [
            'id' => $u->id,
            'nombre' => $u->nombre,
            'correo' => $u->correo_electronico,
            'documento' => $u->numero_documento,
            'estado' => $u->estado,
            'fecha_creacion' => $u->created_at?->format('Y-m-d H:i:s'),

            'ultimo_acceso' => $u->ultimo_acceso ? $u->ultimo_acceso->format('Y-m-d H:i') : null,
            'rol' => $u->role?->nombre ?? 'Sin rol',
        ];
    });

    return response()->json($usuarios);
}

}
