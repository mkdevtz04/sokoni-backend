<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public int   $totalUsers    = 0;
    public int   $totalVendors  = 0;
    public int   $totalProducts = 0;
    public int   $totalOrders   = 0;
    public float $todayRevenue  = 0;
    public int   $pendingApprovals = 0;
    public array $recentOrders  = [];

    public function mount(): void
    {
        $this->totalUsers       = User::where('role', 'customer')->count();
        $this->totalVendors     = VendorProfile::where('is_approved', true)->count();
        $this->totalProducts    = Product::where('is_active', true)->count();
        $this->totalOrders      = Order::count();
        $this->todayRevenue     = (float) Order::whereDate('created_at', today())
                                      ->where('payment_status', 'paid')
                                      ->sum('total');
        $this->pendingApprovals = VendorProfile::where('is_approved', false)->count();
        $this->recentOrders     = Order::with(['user', 'vendor.user'])
                                      ->latest()
                                      ->take(8)
                                      ->get()
                                      ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
