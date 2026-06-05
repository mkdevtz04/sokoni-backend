<div>
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search business name or phone..."
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="filter"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="all">All Vendors</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Business</th>
                        <th class="px-5 py-3 text-left">Owner</th>
                        <th class="px-5 py-3 text-left">Region</th>
                        <th class="px-5 py-3 text-left">Rating</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($vendors as $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center font-bold text-primary text-sm flex-shrink-0">
                                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $vendor->business_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $vendor->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $vendor->user->full_name ?? '-' }}<br>
                            <span class="text-xs text-gray-400">{{ $vendor->user->phone ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $vendor->region ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="flex items-center gap-1 text-amber-500 font-medium">
                                ★ {{ number_format($vendor->rating, 1) }}
                                <span class="text-gray-400 text-xs">({{ $vendor->total_reviews }})</span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($vendor->is_approved)
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>
                            @else
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                @if (! $vendor->is_approved)
                                    <button wire:click="approve({{ $vendor->id }})"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors">
                                        Approve
                                    </button>
                                @else
                                    <button wire:click="reject({{ $vendor->id }})"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 text-xs font-medium rounded-lg transition-colors">
                                        Revoke
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No vendors found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $vendors->links() }}
        </div>
    </div>
</div>
