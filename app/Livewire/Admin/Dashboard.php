<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorEarning;
use App\Models\VendorProfile;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    // Stat cards
    public int   $totalUsers       = 0;
    public int   $totalVendors     = 0;
    public int   $totalProducts    = 0;
    public int   $totalOrders      = 0;
    public float $todayRevenue     = 0;
    public float $monthRevenue     = 0;
    public int   $pendingApprovals = 0;
    public array $recentOrders     = [];

    // Chart series data
    public array $chartDates   = [];
    public array $chartRevenue = [];
    public array $chartOrders  = [];
    public array $statusLabels = [];
    public array $statusCounts = [];
    public array $vendorNames  = [];
    public array $vendorTotals = [];

    public function mount(): void
    {
        // ── Stat cards ────────────────────────────────────────────────────────
        $this->totalUsers       = User::where('role', 'customer')->count();
        $this->totalVendors     = VendorProfile::where('is_approved', true)->count();
        $this->totalProducts    = Product::where('is_active', true)->count();
        $this->totalOrders      = Order::count();
        $this->todayRevenue     = (float) Order::whereDate('created_at', today())
                                      ->where('payment_status', 'paid')->sum('total');
        $this->monthRevenue     = (float) Order::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->where('payment_status', 'paid')->sum('total');
        $this->pendingApprovals = VendorProfile::where('is_approved', false)->count();
        $this->recentOrders     = Order::with(['user', 'vendorProfile'])
                                      ->latest()->take(8)->get()->toArray();

        // ── Last 30 days: revenue + orders ───────────────────────────────────
        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));

        $grouped = Order::selectRaw(
                'DATE(created_at) as date,
                 COUNT(*) as order_count,
                 SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as revenue'
            )
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $this->chartDates   = $days->map(fn ($d) => Carbon::parse($d)->format('d M'))->values()->toArray();
        $this->chartRevenue = $days->map(fn ($d) => (float) ($grouped[$d]->revenue     ?? 0))->values()->toArray();
        $this->chartOrders  = $days->map(fn ($d) => (int)   ($grouped[$d]->order_count ?? 0))->values()->toArray();

        // ── Orders by status ──────────────────────────────────────────────────
        $statuses = Order::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $this->statusLabels = $statuses->keys()
            ->map(fn ($s) => ucfirst(str_replace('_', ' ', $s)))
            ->values()->toArray();
        $this->statusCounts = $statuses->values()->toArray();

        // ── Top 5 vendors by gross revenue ───────────────────────────────────
        $top = VendorEarning::join('vendor_profiles', 'vendor_profiles.user_id', '=', 'vendor_earnings.vendor_id')
            ->selectRaw('vendor_profiles.business_name, SUM(vendor_earnings.gross_amount) as total')
            ->groupBy('vendor_profiles.user_id', 'vendor_profiles.business_name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $this->vendorNames  = $top->pluck('business_name')->toArray();
        $this->vendorTotals = $top->pluck('total')->map(fn ($v) => (float) $v)->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
