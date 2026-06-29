<div>
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="{{ __('admin.orders.search_placeholder') }}"
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="status"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="all">{{ __('admin.orders.all_statuses') }}</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[780px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.order_no') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.customer') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.vendor') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.status') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.payment') }}</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">{{ __('admin.orders.total') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.date') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.orders.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $order->user->full_name ?? '-' }}<br>
                            <span class="text-xs text-gray-400">{{ $order->user->phone ?? '' }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $order->vendorProfile->business_name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if ($editingOrderId === $order->id)
                                <select wire:model="newStatus"
                                    class="px-2 py-1 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                                    @foreach ($statuses as $s)
                                        <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                            @else
                                @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','preparing'=>'bg-purple-100 text-purple-700','ready'=>'bg-teal-100 text-teal-700','out_for_delivery'=>'bg-green-100 text-green-700','delivered'=>'bg-green-100 text-green-800','cancelled'=>'bg-red-100 text-red-700','refunded'=>'bg-gray-100 text-gray-600']; @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            <br><span class="text-xs text-gray-400">{{ str_replace('_',' ', $order->payment_method) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">TZS {{ number_format($order->total) }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @if ($editingOrderId === $order->id)
                                <div class="flex gap-1">
                                    <button wire:click="saveStatus"
                                        class="px-2.5 py-1.5 bg-primary hover:bg-orange-700 text-white text-xs font-medium rounded-lg">
                                        {{ __('admin.orders.save') }}
                                    </button>
                                    <button wire:click="cancelEdit"
                                        class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg">
                                        ✕
                                    </button>
                                </div>
                            @else
                                <button wire:click="startEdit({{ $order->id }}, '{{ $order->status }}')"
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg transition-colors">
                                    {{ __('admin.orders.update') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">{{ __('admin.orders.no_orders') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
