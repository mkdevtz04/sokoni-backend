<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wallet = $request->user()->getOrCreateWallet();
        $wallet->load('transactions');

        return response()->json(['data' => new WalletResource($wallet)]);
    }

    // Simulated top-up (real integration with M-Pesa/Airtel in production)
    public function topup(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:500', 'max:1000000'],
            'phone'  => ['required', 'string'],
        ]);

        $wallet = $request->user()->getOrCreateWallet();
        $ref    = 'TOPUP-' . strtoupper(uniqid());

        $wallet->credit(
            $request->amount,
            "Wallet top-up via mobile money",
            $ref
        );

        $wallet->load('transactions');

        return response()->json([
            'message' => 'Wallet topped up successfully.',
            'data'    => new WalletResource($wallet),
        ]);
    }
}
