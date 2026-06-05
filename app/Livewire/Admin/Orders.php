<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Orders')]
class Orders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';

    public ?int   $editingOrderId = null;
    public string $newStatus      = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function startEdit(int $id, string $current): void
    {
        $this->editingOrderId = $id;
        $this->newStatus      = $current;
    }

    public function saveStatus(): void
    {
        if (! $this->editingOrderId) return;

        $order = Order::findOrFail($this->editingOrderId);
        $order->update(['status' => $this->newStatus]);

        if ($this->newStatus === 'delivered') {
            $order->update(['payment_status' => 'paid', 'delivered_at' => now()]);
        }
        if ($this->newStatus === 'confirmed') {
            $order->update(['confirmed_at' => now()]);
        }

        $this->editingOrderId = null;
        session()->flash('success', 'Order status updated.');
    }

    public function cancelEdit(): void
    {
        $this->editingOrderId = null;
    }

    public function render()
    {
        $orders = Order::with(['user', 'vendor'])
            ->when($this->search, fn ($q) =>
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn ($u) =>
                      $u->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")))
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(20);

        $statuses = ['pending','confirmed','preparing','ready','out_for_delivery','delivered','cancelled','refunded'];

        return view('livewire.admin.orders', compact('orders', 'statuses'));
    }
}
