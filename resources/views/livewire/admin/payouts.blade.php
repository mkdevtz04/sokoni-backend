<div>
    <!-- Stats row -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Pending Requests</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Approved (to send)</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total Paid Out</p>
            <p class="text-2xl font-bold text-green-600">TZS {{ number_format($stats['sent']) }}</p>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="flex gap-2 mb-5">
        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'sent' => 'Sent', 'rejected' => 'Rejected'] as $val => $label)
            <button wire:click="$set('filter', '{{ $val }}')"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                       {{ $filter === $val ? 'bg-primary text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Reject modal -->
    @if ($rejectingId)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center" wire:click.self="cancelReject">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="font-bold text-gray-800 mb-3">Reject Request</h3>
            <textarea wire:model="rejectNote" rows="3"
                placeholder="Reason for rejection (optional)..."
                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary mb-4">
            </textarea>
            <div class="flex gap-3">
                <button wire:click="cancelReject"
                    class="flex-1 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button wire:click="confirmReject"
                    class="flex-1 py-2 bg-red-500 text-white rounded-xl text-sm font-medium hover:bg-red-600 transition-colors">
                    Confirm Reject
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Vendor</th>
                        <th class="px-5 py-3 text-left">M-Pesa Phone</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Requested</th>
                        <th class="px-5 py-3 text-left">Note</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($requests as $wr)
                    @php
                        $sc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-blue-100 text-blue-700','sent'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">
                                {{ $wr->vendor?->vendorProfile?->business_name ?? $wr->vendor?->full_name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $wr->vendor?->phone ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3 font-mono text-gray-700">{{ $wr->phone }}</td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">
                            TZS {{ number_format($wr->amount) }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc[$wr->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($wr->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">
                            {{ $wr->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs max-w-xs truncate">
                            {{ $wr->note ?? '-' }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2 flex-wrap">
                                @if ($wr->status === 'pending')
                                    <button wire:click="approve({{ $wr->id }})"
                                        class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-colors">
                                        Approve
                                    </button>
                                    <button wire:click="startReject({{ $wr->id }})"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 text-xs font-medium rounded-lg transition-colors">
                                        Reject
                                    </button>
                                @elseif ($wr->status === 'approved')
                                    <button wire:click="markSent({{ $wr->id }})"
                                        wire:confirm="Confirm that TZS {{ number_format($wr->amount) }} has been sent to {{ $wr->phone }}?"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors">
                                        ✓ Mark Sent
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">
                                        {{ $wr->processed_at?->format('d M Y') ?? '—' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            No {{ $filter }} payout requests
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
    </div>
</div>
