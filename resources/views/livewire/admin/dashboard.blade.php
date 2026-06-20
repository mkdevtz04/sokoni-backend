<div>
    {{-- Stats grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
        @php
            $cards = [
                ['label' => 'Customers',    'value' => $totalUsers,    'color' => 'blue',   'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
                ['label' => 'Vendors',      'value' => $totalVendors,  'color' => 'purple', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1'],
                ['label' => 'Products',     'value' => $totalProducts, 'color' => 'green',  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Orders',       'value' => $totalOrders,   'color' => 'orange', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                ['label' => "Today's Rev",  'value' => 'TZS ' . number_format($todayRevenue), 'color' => 'teal', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 13v-1m-5.657-9.343A8 8 0 1112 4v0'],
                ['label' => 'Pending Appr', 'value' => $pendingApprovals, 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ];
            $colorMap = ['blue'=>'bg-blue-50 text-blue-600', 'purple'=>'bg-purple-50 text-purple-600', 'green'=>'bg-green-50 text-green-600', 'orange'=>'bg-orange-50 text-orange-600', 'teal'=>'bg-teal-50 text-teal-600', 'red'=>'bg-red-50 text-red-600'];
        @endphp

        @foreach ($cards as $card)
        <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-sm">
            <div class="flex items-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center shrink-0 {{ $colorMap[$card['color']] }}">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-bold text-gray-800 leading-tight break-words">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Recent orders --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm text-primary hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[560px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 sm:px-5 py-3 text-left whitespace-nowrap">Order #</th>
                        <th class="px-4 sm:px-5 py-3 text-left whitespace-nowrap">Customer</th>
                        <th class="px-4 sm:px-5 py-3 text-left whitespace-nowrap">Vendor</th>
                        <th class="px-4 sm:px-5 py-3 text-left whitespace-nowrap">Status</th>
                        <th class="px-4 sm:px-5 py-3 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 sm:px-5 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $order['order_number'] }}</td>
                        <td class="px-4 sm:px-5 py-3 text-gray-600 whitespace-nowrap">{{ $order['user']['first_name'] ?? '-' }} {{ $order['user']['last_name'] ?? '' }}</td>
                        <td class="px-4 sm:px-5 py-3 text-gray-600">{{ $order['vendor_profile']['business_name'] ?? '-' }}</td>
                        <td class="px-4 sm:px-5 py-3">
                            @php
                                $sc = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','preparing'=>'bg-purple-100 text-purple-700','ready'=>'bg-teal-100 text-teal-700','out_for_delivery'=>'bg-green-100 text-green-700','delivered'=>'bg-green-100 text-green-800','cancelled'=>'bg-red-100 text-red-700','refunded'=>'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $sc[$order['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">TZS {{ number_format($order['total']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
