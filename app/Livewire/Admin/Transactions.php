<?php

namespace App\Livewire\Admin;

use App\Models\VendorEarning;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Transactions')]
class Transactions extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $filter    = 'all';   // all | pending | settled | withdrawn
    public string $dateFrom  = '';
    public string $dateTo    = '';

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFilter(): void  { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search   = '';
        $this->filter   = 'all';
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = VendorEarning::with([
                'order.user',
                'order.deliveryAddress',
                'vendor.vendorProfile',
            ])
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->when($this->search, fn ($q) =>
                $q->whereHas('order', fn ($o) =>
                    $o->where('order_number', 'like', "%{$this->search}%"))
                  ->orWhereHas('vendor.vendorProfile', fn ($v) =>
                    $v->where('business_name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) =>
                $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) =>
                $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest();

        $transactions = $query->paginate(20);

        // Summary — always scoped to the same filters (excluding pagination)
        $summaryBase = VendorEarning::query()
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        $summary = [
            'gross_total'      => (clone $summaryBase)->sum('gross_amount'),
            'commission_total' => (clone $summaryBase)->sum('commission'),
            'net_total'        => (clone $summaryBase)->sum('net_amount'),
            'count'            => (clone $summaryBase)->count(),
        ];

        return view('livewire.admin.transactions', compact('transactions', 'summary'));
    }
}
