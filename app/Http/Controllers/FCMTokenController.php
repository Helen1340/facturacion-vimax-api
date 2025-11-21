<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FCMTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $user->id],
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

    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        DB::table('device_tokens')
            ->where('user_id', $user->id)
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Token desactivado'
        ]);
    }
}