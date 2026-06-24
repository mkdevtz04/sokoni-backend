<div>

    {{-- ── Stat cards ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 mb-6">

        @php
            $stats = [
                [
                    'label'   => 'Customers',
                    'value'   => number_format($totalUsers),
                    'sub'     => 'Registered accounts',
                    'color'   => 'blue',
                    'bg'      => 'bg-blue-50',
                    'text'    => 'text-blue-600',
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                ],
                [
                    'label'   => 'Vendors',
                    'value'   => number_format($totalVendors),
                    'sub'     => 'Active stores',
                    'color'   => 'purple',
                    'bg'      => 'bg-purple-50',
                    'text'    => 'text-purple-600',
                    'icon'    => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                ],
                [
                    'label'   => 'Products',
                    'value'   => number_format($totalProducts),
                    'sub'     => 'Active listings',
                    'color'   => 'green',
                    'bg'      => 'bg-green-50',
                    'text'    => 'text-green-600',
                    'icon'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                ],
                [
                    'label'   => 'Orders',
                    'value'   => number_format($totalOrders),
                    'sub'     => 'All time',
                    'color'   => 'orange',
                    'bg'      => 'bg-orange-50',
                    'text'    => 'text-orange-600',
                    'icon'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
                [
                    'label'   => "Today's Revenue",
                    'value'   => 'TZS ' . number_format($todayRevenue),
                    'sub'     => now()->format('d M Y'),
                    'color'   => 'teal',
                    'bg'      => 'bg-teal-50',
                    'text'    => 'text-teal-600',
                    'icon'    => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 13v-1m-5.657-9.343A8 8 0 1112 4',
                ],
                [
                    'label'   => 'Month Revenue',
                    'value'   => 'TZS ' . number_format($monthRevenue),
                    'sub'     => now()->format('F Y'),
                    'color'   => 'indigo',
                    'bg'      => 'bg-indigo-50',
                    'text'    => 'text-indigo-600',
                    'icon'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                ],
                [
                    'label'   => 'Pending Approvals',
                    'value'   => number_format($pendingApprovals),
                    'sub'     => 'Vendors awaiting review',
                    'color'   => 'red',
                    'bg'      => 'bg-red-50',
                    'text'    => 'text-red-600',
                    'icon'    => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                ],
            ];
        @endphp

        @foreach ($stats as $s)
        <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 {{ $s['bg'] }} rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $s['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-800 leading-tight break-words">{{ $s['value'] }}</p>
            <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $s['label'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Row 1: Revenue area chart (full width) ───────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-4">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h2 class="font-semibold text-gray-800">Revenue — Last 30 Days</h2>
                <p class="text-xs text-gray-400 mt-0.5">Paid orders only</p>
            </div>
            <span class="text-xs text-gray-400">TZS</span>
        </div>
        <div wire:ignore>
            <div id="chart-revenue"></div>
        </div>
    </div>

    {{-- ── Row 2: Daily orders bar | Status donut ───────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="mb-1">
                <h2 class="font-semibold text-gray-800">Daily Orders — Last 30 Days</h2>
                <p class="text-xs text-gray-400 mt-0.5">All statuses</p>
            </div>
            <div wire:ignore>
                <div id="chart-orders"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="mb-1">
                <h2 class="font-semibold text-gray-800">Orders by Status</h2>
                <p class="text-xs text-gray-400 mt-0.5">All time distribution</p>
            </div>
            <div wire:ignore>
                <div id="chart-status"></div>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Top vendors horizontal bar ───────────────────────────────── --}}
    @if (count($vendorNames) > 0)
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
        <div class="mb-1">
            <h2 class="font-semibold text-gray-800">Top Vendors by Revenue</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gross sales, all time</p>
        </div>
        <div wire:ignore>
            <div id="chart-vendors"></div>
        </div>
    </div>
    @endif

    {{-- ── Recent orders table ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm text-primary hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[560px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Order #</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Customer</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Vendor</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Status</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $order['order_number'] }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ $order['user']['first_name'] ?? '-' }} {{ $order['user']['last_name'] ?? '' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $order['vendor_profile']['business_name'] ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @php
                                $sc = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','preparing'=>'bg-purple-100 text-purple-700','ready'=>'bg-teal-100 text-teal-700','out_for_delivery'=>'bg-green-100 text-green-700','delivered'=>'bg-emerald-100 text-emerald-800','cancelled'=>'bg-red-100 text-red-700','refunded'=>'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $sc[$order['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">TZS {{ number_format($order['total']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@script
<script>
(function () {
    const dates        = @json($chartDates);
    const revenue      = @json($chartRevenue);
    const orders       = @json($chartOrders);
    const statusLabels = @json($statusLabels);
    const statusCounts = @json($statusCounts);
    const vendorNames  = @json($vendorNames);
    const vendorTotals = @json($vendorTotals);

    const fmt = v => 'TZS ' + Intl.NumberFormat('en').format(Math.round(v));

    // ── Revenue area chart ──────────────────────────────────────────────────
    new ApexCharts(document.querySelector('#chart-revenue'), {
        series:      [{ name: 'Revenue', data: revenue }],
        chart:       { type: 'area', height: 240, toolbar: { show: false }, zoom: { enabled: false } },
        dataLabels:  { enabled: false },
        stroke:      { curve: 'smooth', width: 2 },
        colors:      ['#FF6600'],
        fill:        { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 95, 100] } },
        xaxis:       { categories: dates, tickAmount: 8, labels: { style: { fontSize: '11px', colors: '#9CA3AF' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis:       { labels: { formatter: fmt, style: { fontSize: '11px', colors: '#9CA3AF' } } },
        grid:        { strokeDashArray: 4, borderColor: '#F3F4F6' },
        tooltip:     { y: { formatter: fmt } },
        markers:     { size: 0, hover: { size: 4 } },
    }).render();

    // ── Daily orders bar chart ──────────────────────────────────────────────
    new ApexCharts(document.querySelector('#chart-orders'), {
        series:      [{ name: 'Orders', data: orders }],
        chart:       { type: 'bar', height: 220, toolbar: { show: false }, zoom: { enabled: false } },
        dataLabels:  { enabled: false },
        colors:      ['#6366F1'],
        plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
        xaxis:       { categories: dates, tickAmount: 6, labels: { style: { fontSize: '11px', colors: '#9CA3AF' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis:       { labels: { style: { fontSize: '11px', colors: '#9CA3AF' } }, tickAmount: 4 },
        grid:        { strokeDashArray: 4, borderColor: '#F3F4F6' },
        tooltip:     { y: { formatter: v => v + ' orders' } },
    }).render();

    // ── Orders by status donut ──────────────────────────────────────────────
    if (statusCounts.length > 0) {
        new ApexCharts(document.querySelector('#chart-status'), {
            series:      statusCounts,
            labels:      statusLabels,
            chart:       { type: 'donut', height: 220 },
            colors:      ['#F59E0B','#3B82F6','#8B5CF6','#14B8A6','#22C55E','#10B981','#EF4444','#9CA3AF'],
            dataLabels:  { enabled: false },
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '12px', color: '#6B7280', formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) } } } } },
            legend:      { position: 'bottom', fontSize: '11px', itemMargin: { horizontal: 6 } },
            tooltip:     { y: { formatter: v => v + ' orders' } },
        }).render();
    }

    // ── Top vendors horizontal bar ──────────────────────────────────────────
    if (vendorNames.length > 0) {
        new ApexCharts(document.querySelector('#chart-vendors'), {
            series:      [{ name: 'Revenue', data: vendorTotals }],
            chart:       { type: 'bar', height: 200, toolbar: { show: false } },
            dataLabels:  { enabled: false },
            colors:      ['#0EA5E9'],
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%' } },
            xaxis:       { categories: vendorNames, labels: { formatter: fmt, style: { fontSize: '11px', colors: '#9CA3AF' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis:       { labels: { style: { fontSize: '12px', colors: '#374151', fontWeight: 500 } } },
            grid:        { strokeDashArray: 4, borderColor: '#F3F4F6' },
            tooltip:     { y: { formatter: fmt } },
        }).render();
    }
})();
</script>
@endscript
