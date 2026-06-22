<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VendorEarning;
use App\Models\WithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorEarningController extends Controller
{
    /** GET /vendor/earnings — summary + paginated list */
    public function index(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;

        $earnings = VendorEarning::where('vendor_id', $vendorId)
            ->with(['order:id,order_number,created_at,status'])
            ->latest()
            ->paginate(20);

        $totals = VendorEarning::where('vendor_id', $vendorId)
            ->selectRaw('
                SUM(net_amount)                                   as total_earned,
                SUM(CASE WHEN status = "pending"  THEN net_amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN status = "settled"  THEN net_amount ELSE 0 END) as settled_amount,
                SUM(CASE WHEN status = "withdrawn" THEN net_amount ELSE 0 END) as withdrawn_amount,
                COUNT(*)                                          as total_orders
            ')
            ->first();

        $pendingWithdrawal = WithdrawalRequest::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->exists();

        return response()->json([
            'summary' => [
                'total_earned'      => (float) ($totals->total_earned ?? 0),
                'pending_amount'    => (float) ($totals->pending_amount ?? 0),
                'settled_amount'    => (float) ($totals->settled_amount ?? 0),
                'withdrawn_amount'  => (float) ($totals->withdrawn_amount ?? 0),
                'total_orders'      => (int)   ($totals->total_orders ?? 0),
                'has_pending_payout'=> $pendingWithdrawal,
            ],
            'data' => $earnings->map(fn ($e) => [
                'id'            => $e->id,
                'order_number'  => $e->order?->order_number,
                'order_date'    => $e->order?->created_at?->toISOString(),
                'gross_amount'  => $e->gross_amount,
                'commission'    => $e->commission,
                'net_amount'    => $e->net_amount,
                'status'        => $e->status,
                'created_at'    => $e->created_at,
            ]),
            'meta' => [
                'current_page' => $earnings->currentPage(),
                'last_page'    => $earnings->lastPage(),
            ],
        ]);
    }

    /** POST /vendor/earnings/withdraw — request a payout */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $request->validate([
            'phone'  => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:50000'],
        ]);

        $vendorId = $request->user()->id;

        // Check there's no pending withdrawal already
        if (WithdrawalRequest::where('vendor_id', $vendorId)->where('status', 'pending')->exists()) {
            return response()->json([
                'message' => 'You already have a pending withdrawal request.',
            ], 422);
        }

        $available = VendorEarning::where('vendor_id', $vendorId)
            ->where('status', 'settled')
            ->sum('net_amount');

        if ($request->amount > $available) {
            return response()->json([
                'message' => "Insufficient balance. Available: TZS " . number_format($available),
            ], 422);
        }

        $wr = WithdrawalRequest::create([
            'vendor_id' => $vendorId,
            'amount'    => $request->amount,
            'phone'     => $request->phone,
            'status'    => 'pending',
        ]);

        return response()->json([
            'message' => 'Withdrawal request submitted. Admin will process it shortly.',
            'data'    => [
                'id'     => $wr->id,
                'amount' => $wr->amount,
                'phone'  => $wr->phone,
                'status' => $wr->status,
            ],
        ], 201);
    }
}
