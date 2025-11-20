<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FCMTokenController extends Controller
{
    /**
     * Guardar token FCM
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $request->user_id],
            [
                'fcm_token' => $request->fcm_token,
                'platform' => 'web',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token guardado'
        ]);
    }

    /**
     * Desactivar token (logout)
     */
    public function destroy(Request $request)
    {
        $userId = $request->query('user_id') ?? $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'user_id requerido'
            ], 400);
        }

        DB::table('device_tokens')
            ->where('user_id', $userId)
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Token desactivado'
        ]);
    }
}