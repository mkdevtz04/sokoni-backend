<div>

    {{-- ── Summary cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Gross Volume</p>
            <p class="text-xl font-bold text-gray-800">TZS {{ number_format($summary['gross_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['count']) }} transactions</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Platform Revenue</p>
            <p class="text-xl font-bold text-primary">TZS {{ number_format($summary['commission_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">After commission deduction</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Vendor Payouts</p>
            <p class="text-xl font-bold text-gray-800">TZS {{ number_format($summary['net_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Net to vendors</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Avg Commission</p>
            <p class="text-xl font-bold text-gray-800">
                @if ($summary['gross_total'] > 0)
                    {{ number_format(($summary['commission_total'] / $summary['gross_total']) * 100, 1) }}%
                @else
                    —
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">Of gross volume</p>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search order number or vendor..."
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="filter"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="settled">Settled</option>
            <option value="withdrawn">Withdrawn</option>
        </select>

        <input wire:model.live="dateFrom" type="date"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        <input wire:model.live="dateTo" type="date"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        @if ($search || $filter !== 'all' || $dateFrom || $dateTo)
            <button wire:click="clearFilters"
                class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors whitespace-nowrap">
                Clear filters
            </button>
        @endif
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Order</th>
                        <th class="px-5 py-3 text-left">Vendor</th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-right">Gross</th>
                        <th class="px-5 py-3 text-right">Commission</th>
                        <th class="px-5 py-3 text-right">Net to Vendor</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        {{-- Order --}}
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 font-mono text-xs">
                                {{ $tx->order?->order_number ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $tx->order?->payment_method === 'mobile_money' ? 'Mobile Money' : ucfirst($tx->order?->payment_method ?? '—') }}
                            </p>
                        </td>

                        {{-- Vendor --}}
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-700">
                                {{ $tx->vendor?->vendorProfile?->business_name ?? $tx->vendor?->full_name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $tx->vendor?->phone ?? '' }}</p>
                        </td>

                        {{-- Customer --}}
                        <td class="px-5 py-3">
                            <p class="text-gray-600">{{ $tx->order?->user?->full_name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $tx->order?->user?->phone ?? '' }}</p>
                        </td>

                        {{-- Amounts --}}
                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            TZS {{ number_format($tx->gross_amount) }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-primary">
                            TZS {{ number_format($tx->commission) }}
                            <span class="block text-xs text-gray-400 font-normal">
                                {{ $tx->gross_amount > 0 ? number_format(($tx->commission / $tx->gross_amount) * 100, 1) . '%' : '' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-700">
                            TZS {{ number_format($tx->net_amount) }}
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            @if ($tx->status === 'pending')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
                            @elseif ($tx->status === 'settled')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Settled</span>
                            @elseif ($tx->status === 'withdrawn')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Withdrawn</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">{{ ucfirst($tx->status) }}</span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="px-5 py-3 text-gray-400 text-xs whitespace-nowrap">
                            {{ $tx->created_at->format('d M Y') }}<br>
                            {{ $tx->created_at->format('H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                            No transactions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: pagination + totals --}}
        <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="text-xs text-gray-400">
                Showing page results —
                <span class="font-semibold text-primary">Platform earned: TZS {{ number_format($summary['commission_total']) }}</span>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>

</div>
