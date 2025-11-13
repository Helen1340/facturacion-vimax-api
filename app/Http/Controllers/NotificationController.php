<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Ably\AblyRest;

class NotificationController extends Controller
{
    protected $notificacionService;

    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    // =================================================================
    // 1. PULL - Obtener notificaciones (GET /api/notificaciones)
    // =================================================================
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Usuario no autenticado. La ruta debe estar protegida.'
            ], 401);
        }

        $userId = $user->id;
        $notificaciones = $this->notificacionService->getAllNotificaciones($userId);

        return response()->json([
            'success' => true,
            'data' => $notificaciones,
            'count' => $notificaciones->count()
        ]);
    }

    // =================================================================
    // 2. PUSH - Disparar evento en tiempo real (POST /api/notificaciones)
    // =================================================================
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'resource_id' => 'required|integer',
            'table_source' => 'required|string|in:users,electronic_invoices,taxes,credit_debit_notes',
            'type' => 'required|string|in:sistema,dian_operativa,cumplimiento,operativa',
        ]);

        try {
            $data = $this->sendRealtime($request);

            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada en tiempo real.',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            Log::error("❌ Error al enviar notificación PUSH: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la notificación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía la notificación en tiempo real vía Ably.
     */
    protected function sendRealtime(Request $request)
    {
        // ✅ Usa la configuración de Laravel en lugar de env()
        $ablyKey = config('broadcasting.connections.ably.key');

        if (!$ablyKey || !str_contains($ablyKey, ':')) {
            throw new \Exception('ABLY_KEY no configurada correctamente en .env o broadcasting.php');
        }

        $ably = new AblyRest($ablyKey);

        $userId = $request->input('user_id');
        $channelName = "notifications:{$userId}";

        $channel = $ably->channel($channelName);

        $data = [
            'type' => $request->input('type'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'resource_id' => $request->input('resource_id'),
            'table_source' => $request->input('table_source'),
            'created_at' => now()->toDateTimeString(),
            'is_read' => 0,
            'read' => false,
        ];

        // 📡 Publicar evento en Ably
        $channel->publish('new_alert', $data);

        Log::info("✅ Notificación PUSH enviada a canal {$channelName}", $data);

        return $data;
    }

    // =================================================================
    // 3. UPDATE - Marcar como leído (PATCH /api/notificaciones/{id})
    // =================================================================
    public function update(Request $request, $id)
    {
        $sourceTable = $request->query('table_source');

        if (!$sourceTable) {
            return response()->json([
                'success' => false,
                'error' => 'Se requiere el parámetro "table_source" en la URL.'
            ], 400);
        }

        $resultado = $this->notificacionService->markAsRead((int) $id, $sourceTable);

        return response()->json($resultado, $resultado['success'] ? 200 : 400);
    }

    // Métodos no implementados
    public function show($id)
    {
        return response()->json(['error' => 'Método no implementado.'], 405);
    }

    public function destroy($id)
    {
        return response()->json(['error' => 'Método no implementado.'], 405);
    }
}
