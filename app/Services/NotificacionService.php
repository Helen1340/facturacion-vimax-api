<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificacionService
{
    protected function generateSelect($resourceId, $type, $subject, $body, $isRead, $createdAt, $tableName)
    {
        return [
            DB::raw("{$resourceId} as resource_id"),
            DB::raw("'$type' as type"),
            DB::raw("{$subject} as subject"),
            DB::raw("{$body} as body"),
            DB::raw("CAST({$isRead} AS UNSIGNED) as is_read"),
            DB::raw("{$createdAt} as created_at"),
            DB::raw("'$tableName' as table_source"),
        ];
    }

    public function getAllNotificaciones(int $userId, ?int $companyId = null, array $filters = [])
    {
        Log::info("Consultando notificaciones para usuario: {$userId}");
        $type = $filters['type'] ?? null; // sistema|dian_operativa|cumplimiento|operativa
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $limit = (int)($filters['limit'] ?? 50);
        $last90Days = Carbon::now()->subDays(90)->toDateTimeString();

        $userInactiveAlert = DB::table('users')
            ->select($this->generateSelect(
                'id',
                'sistema',
                "CONCAT('ALERTA CRÍTICA: Cuenta Inactiva')",
                "CONCAT('Tu cuenta ha sido desactivada. Contacta al administrador.')",
                '0',
                'updated_at',
                'users'
            ))
            ->where('id', $userId)
            ->where('status', 'Inactive')
            ->when($from, fn($q) => $q->where('updated_at', '>=', $from))
            ->when($to, fn($q) => $q->where('updated_at', '<=', $to))
            ->when(!$from && !$to, fn($q) => $q->where('updated_at', '>', $last90Days));

        $rejectedInvoices = DB::table('electronic_invoices')
            ->select($this->generateSelect(
                'id',
                'dian_operativa',
                "CONCAT('Factura Rechazada DIAN: ', invoice_number)",
                "CONCAT('Tu factura #', invoice_number, ' fue rechazada por la DIAN. Revisa y reenvía.')",
                "CASE WHEN internal_status = 'cancelled' THEN 1 ELSE 0 END",
                'COALESCE(received_at, updated_at)',
                'electronic_invoices'
            ))
            ->where('user_id', $userId)
            ->where('dian_status', 'rejected')
            ->where('internal_status', '!=', 'cancelled')
            ->when($from, fn($q) => $q->where(DB::raw('COALESCE(received_at, updated_at)'), '>=', $from))
            ->when($to, fn($q) => $q->where(DB::raw('COALESCE(received_at, updated_at)'), '<=', $to))
            ->when(!$from && !$to, fn($q) => $q->where(DB::raw('COALESCE(received_at, updated_at)'), '>', $last90Days));

        $newTaxes = DB::table('taxes')
            ->select($this->generateSelect(
                'id',
                'cumplimiento',
                "CONCAT('Nuevo Tributo Creado: ', name)",
                "CONCAT('Código: ', tax_code, ' | Tipo: ', type, ' | Aplicación: ', application_type)",
                '0',
                'created_at',
                'taxes'
            ))
            ->where('status', 'Activo')
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to))
            ->when(!$from && !$to, fn($q) => $q->where('created_at', '>', $last90Days))
            ->orderBy('created_at', 'desc')
            ->limit(10);

        $newNotes = DB::table('credit_debit_notes as cdn')
            ->join('electronic_invoices as ei', 'cdn.electronic_invoice_id', '=', 'ei.id')
            ->select($this->generateSelect(
                'cdn.id',
                'operativa',
                "CONCAT('Nueva Nota ', UPPER(cdn.note_type), ': ', cdn.note_number)",
                "CONCAT('Nota ', cdn.note_type, ' creada para factura #', ei.invoice_number, ' | Monto: $', FORMAT(cdn.total_amount, 2))",
                '0',
                'cdn.created_at',
                'credit_debit_notes'
            ))
            ->where('ei.user_id', $userId)
            ->when($from, fn($q) => $q->where('cdn.created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('cdn.created_at', '<=', $to))
            ->when(!$from && !$to, fn($q) => $q->where('cdn.created_at', '>', $last90Days))
            ->orderBy('cdn.created_at', 'desc')
            ->limit(10);

        $queries = [];
        if (!$type || $type === 'sistema') { $queries[] = $userInactiveAlert; }
        if (!$type || $type === 'dian_operativa') { $queries[] = $rejectedInvoices; }
        if (!$type || $type === 'cumplimiento') { $queries[] = $newTaxes; }
        if (!$type || $type === 'operativa') { $queries[] = $newNotes; }

        if (empty($queries)) {
            return collect();
        }

        $builder = array_shift($queries);
        foreach ($queries as $q) {
            $builder = $builder->unionAll($q);
        }

        $notificaciones = $builder
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $notificaciones->map(function ($item) {
            return [
                'resource_id' => $item->resource_id,
                'type' => $item->type,
                'subject' => $item->subject,
                'body' => $item->body,
                'is_read' => (bool)$item->is_read,
                'read' => (bool)$item->is_read,
                'created_at' => $item->created_at,
                'table_source' => $item->table_source,
            ];
        });
    }

    public function markAsRead(int $id, string $sourceTable)
    {
        try {
            switch ($sourceTable) {
                case 'electronic_invoices':
                    $updated = DB::table('electronic_invoices')
                        ->where('id', $id)
                        ->where('dian_status', 'rejected')
                        ->update(['internal_status' => 'cancelled']);

                    if ($updated) {
                        Log::info("Factura {$id} marcada como procesada.");
                        return ['success' => true, 'message' => 'Notificación marcada como leída.'];
                    }
                    break;

                case 'users':
                    return ['success' => false, 'message' => 'Esta alerta requiere contactar al administrador.'];

                case 'taxes':
                case 'credit_debit_notes':
                    return ['success' => true, 'message' => 'Notificación informativa registrada.'];

                default:
                    return ['success' => false, 'message' => 'Tabla fuente no reconocida.'];
            }

            return ['success' => false, 'message' => 'No se encontró el recurso.'];
        } catch (\Exception $e) {
            Log::error("Error al marcar como leído {$sourceTable}:{$id} - " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la solicitud.'];
        }
    }
}
