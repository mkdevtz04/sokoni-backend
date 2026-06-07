<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * POST /device-token
     * Register or refresh the user's FCM device token.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'    => ['required', 'string'],
            'platform' => ['nullable', 'in:android,ios'],
        ]);

        // Upsert: update if token exists, create otherwise
        DeviceToken::updateOrCreate(
            ['token'   => $data['token']],
            ['user_id' => $request->user()->id,
             'platform'=> $data['platform'] ?? 'android'],
        );

        return response()->json(['message' => 'Token registered.']);
    }

    /**
     * DELETE /device-token
     * Remove the token on logout.
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        DeviceToken::where('token', $data['token'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Token removed.']);
    }
}
