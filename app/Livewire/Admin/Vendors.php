<?php

namespace App\Livewire\Admin;

use App\Models\VendorProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Vendors')]
class Vendors extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $filter   = 'all'; // all | pending | approved

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFilter(): void  { $this->resetPage(); }

    public function approve(int $id): void
    {
        $vendor = VendorProfile::with('user')->findOrFail($id);
        $vendor->update(['is_approved' => true]);
        $vendor->user?->update(['role' => 'vendor']);
        session()->flash('success', 'Vendor approved.');
    }

    public function reject(int $id): void
    {
        $vendor = VendorProfile::with('user')->findOrFail($id);
        $vendor->update(['is_approved' => false]);
        $vendor->user?->update(['role' => 'customer']);
        session()->flash('success', 'Vendor rejected.');
    }

    public function render()
    {
        $vendors = VendorProfile::with('user')
            ->when($this->search, fn ($q) =>
                $q->where('business_name', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn ($u) =>
                      $u->where('phone', 'like', "%{$this->search}%")))
            ->when($this->filter === 'pending',  fn ($q) => $q->where('is_approved', false))
            ->when($this->filter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.vendors', compact('vendors'));
    }
}
