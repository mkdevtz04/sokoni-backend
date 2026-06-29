<div>

    {{-- ── Summary cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('admin.transactions.gross_volume') }}</p>
            <p class="text-xl font-bold text-gray-800">TZS {{ number_format($summary['gross_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('admin.transactions.transactions_count', ['count' => number_format($summary['count'])]) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('admin.transactions.platform_revenue') }}</p>
            <p class="text-xl font-bold text-primary">TZS {{ number_format($summary['commission_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('admin.transactions.commission_of') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('admin.transactions.vendor_payouts') }}</p>
            <p class="text-xl font-bold text-gray-800">TZS {{ number_format($summary['net_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('admin.transactions.net_to_vendors') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ __('admin.transactions.commission_rate') }}</p>
            <p class="text-xl font-bold text-gray-800">
                {{ config('services.palmpesa.commission_percent', 10) }}%
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ __('admin.transactions.fixed_rate') }}</p>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="flex flex-col gap-3 mb-5">
        <div class="flex flex-col sm:flex-row gap-3">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('admin.transactions.search_placeholder') }}"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

            <select wire:model.live="filter"
                class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="all">{{ __('admin.transactions.all_statuses') }}</option>
                <option value="pending">{{ __('admin.transactions.pending') }}</option>
                <option value="settled">{{ __('admin.transactions.settled') }}</option>
                <option value="withdrawn">{{ __('admin.transactions.withdrawn') }}</option>
            </select>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <input wire:model.live="dateFrom" type="date"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <input wire:model.live="dateTo" type="date"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

            @if ($search || $filter !== 'all' || $dateFrom || $dateTo)
                <button wire:click="clearFilters"
                    class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors whitespace-nowrap">
                    {{ __('admin.transactions.clear_filters') }}
                </button>
            @endif
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.transactions.order') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.transactions.vendor') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.transactions.customer') }}</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">{{ __('admin.transactions.gross') }}</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">{{ __('admin.transactions.commission') }}</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">{{ __('admin.transactions.net_to_vendor') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.transactions.status') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.transactions.date') }}</th>
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
                                {{ $tx->order?->payment_method === 'mobile_money'
                                    ? __('admin.transactions.mobile_money')
                                    : ucfirst($tx->order?->payment_method ?? '—') }}
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
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">{{ __('admin.transactions.status_pending') }}</span>
                            @elseif ($tx->status === 'settled')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ __('admin.transactions.status_settled') }}</span>
                            @elseif ($tx->status === 'withdrawn')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">{{ __('admin.transactions.status_withdrawn') }}</span>
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
                            {{ __('admin.transactions.no_transactions') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: pagination + totals --}}
        <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="text-xs text-gray-400">
                {{ __('admin.transactions.showing_results') }}
                <span class="font-semibold text-primary">{{ __('admin.transactions.platform_earned') }} TZS {{ number_format($summary['commission_total']) }}</span>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>

</div>
