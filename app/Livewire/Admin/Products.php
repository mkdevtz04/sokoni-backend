<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Products')]
class Products extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function toggleActive(int $id): void
    {
        $p = Product::findOrFail($id);
        $p->update(['is_active' => ! $p->is_active]);
    }

    public function toggleFeatured(int $id): void
    {
        $p = Product::findOrFail($id);
        $p->update(['is_featured' => ! $p->is_featured]);
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function render()
    {
        $products = Product::with(['vendorProfile', 'category'])
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhereHas('vendorProfile', fn ($v) =>
                      $v->where('business_name', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.products', compact('products'));
    }
}
