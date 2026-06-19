<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\VendorProfile;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** GET /chat/sessions — list all sessions for the authenticated user */
    public function sessions(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $sessions = ChatSession::with(['customer', 'vendor'])
            ->where('customer_id', $userId)
            ->orWhere('vendor_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json([
            'data' => $sessions->map(fn ($s) => $this->formatSession($s, $userId)),
        ]);
    }

    /** POST /chat/sessions — get or create a session {vendor_id} (sent by customer) */
    public function getOrCreate(Request $request): JsonResponse
    {
        $request->validate(['vendor_id' => 'required|integer|exists:users,id']);

        $customerId = $request->user()->id;
        $vendorId   = (int) $request->vendor_id;

        if ($customerId === $vendorId) {
            return response()->json(['message' => 'Cannot chat with yourself'], 422);
        }

        $session = ChatSession::firstOrCreate(
            ['customer_id' => $customerId, 'vendor_id' => $vendorId],
        );

        $session->load(['customer', 'vendor']);

        return response()->json(['data' => $this->formatSession($session, $customerId)]);
    }

    /** GET /chat/sessions/{session}/messages?since_id=X — polling endpoint */
    public function messages(Request $request, ChatSession $session): JsonResponse
    {
        $this->authorize($request, $session);

        $query = $session->messages()->orderBy('id');

        if ($sinceId = $request->query('since_id')) {
            $query->where('id', '>', (int) $sinceId);
        }

        $messages = $query->get();

        return response()->json([
            'data' => $messages->map(fn ($m) => $this->formatMessage($m)),
        ]);
    }

    /** POST /chat/sessions/{session}/messages — send a message */
    public function send(Request $request, ChatSession $session): JsonResponse
    {
        $this->authorize($request, $session);
        $request->validate(['body' => 'required|string|max:2000']);

        $userId    = $request->user()->id;
        $isVendor  = $session->vendor_id === $userId;

        $message = $session->messages()->create([
            'sender_id' => $userId,
            'body'      => $request->body,
        ]);

        // Update session preview and increment unread for the other party
        $session->update([
            'last_message'    => mb_strimwidth($request->body, 0, 80, '…'),
            'last_message_at' => now(),
            $isVendor ? 'customer_unread' : 'vendor_unread' => \DB::raw(
                ($isVendor ? 'customer_unread' : 'vendor_unread') . ' + 1'
            ),
        ]);

        // Push notification to recipient
        $recipientId = $isVendor ? $session->customer_id : $session->vendor_id;
        $senderName  = $request->user()->full_name ?? $request->user()->first_name;
        app(FcmService::class)->notifyUser(
            $recipientId,
            "💬 New message from $senderName",
            $request->body,
            [
                'type'       => 'chat_message',
                'session_id' => (string) $session->id,
                'sender_id'  => (string) $userId,
            ]
        );

        return response()->json(['data' => $this->formatMessage($message)], 201);
    }

    /** POST /chat/sessions/{session}/read — mark all messages as read */
    public function markRead(Request $request, ChatSession $session): JsonResponse
    {
        $this->authorize($request, $session);

        $userId   = $request->user()->id;
        $isVendor = $session->vendor_id === $userId;

        // Mark all messages NOT sent by me as read
        $session->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $session->update([
            $isVendor ? 'vendor_unread' : 'customer_unread' => 0,
        ]);

        return response()->json(['ok' => true]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function authorize(Request $request, ChatSession $session): void
    {
        $userId = $request->user()->id;
        if ($session->customer_id !== $userId && $session->vendor_id !== $userId) {
            abort(403);
        }
    }

    private function formatSession(ChatSession $s, int $meId): array
    {
        $isVendor       = $s->vendor_id === $meId;
        $other          = $isVendor ? $s->customer : $s->vendor;
        $vendorProfile  = VendorProfile::where('user_id', $s->vendor_id)->first();

        return [
            'id'              => $s->id,
            'other_user'      => $other ? [
                'id'         => $other->id,
                'name'       => $other->full_name ?? $other->first_name,
                'avatar'     => $other->profile_picture,
                // For the vendor side, expose business name
                'shop_name'  => $vendorProfile?->business_name,
            ] : null,
            'last_message'    => $s->last_message,
            'last_message_at' => $s->last_message_at?->toIso8601String(),
            'unread_count'    => $isVendor ? $s->vendor_unread : $s->customer_unread,
            'vendor_id'       => $s->vendor_id,
            'customer_id'     => $s->customer_id,
        ];
    }

    private function formatMessage(ChatMessage $m): array
    {
        return [
            'id'         => $m->id,
            'session_id' => $m->session_id,
            'sender_id'  => $m->sender_id,
            'body'       => $m->body,
            'is_read'    => $m->is_read,
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
