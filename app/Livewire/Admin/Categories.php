<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('admin.layouts.app')]
#[Title('Categories')]
class Categories extends Component
{
    use WithFileUploads;

    public string $search = '';

    // Modal state
    public bool  $showModal = false;
    public ?int  $editingId = null;

    // Form fields
    public string  $name         = '';
    public string  $description  = '';
    public int     $sortOrder    = 1;
    public         $icon         = null;   // new upload (Livewire TemporaryUploadedFile)
    public ?string $existingIcon = null;   // stored path when editing

    // ── Open / close ──────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->sortOrder = (Category::max('sort_order') ?? 0) + 1;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->resetForm();
        $this->editingId    = $id;
        $this->name         = $cat->name;
        $this->description  = $cat->description ?? '';
        $this->sortOrder    = $cat->sort_order;
        $this->existingIcon = $cat->icon;
        $this->showModal    = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string|max:255',
            'sortOrder'   => 'required|integer|min:0',
            'icon'        => 'nullable|image|max:1024',
        ]);

        $iconPath = $this->existingIcon;
        if ($this->icon) {
            $stored = $this->icon->store('categories', 'public');
            if ($stored === false) {
                $this->addError('icon', 'Image could not be saved. Check server storage permissions.');
                return;
            }
            $iconPath = $stored;
        }

        $data = [
            'name'        => $this->name,
            'slug'        => Str::slug($this->name),
            'description' => $this->description ?: null,
            'sort_order'  => $this->sortOrder,
            'icon'        => $iconPath ?: null,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Category updated successfully.');
        } else {
            Category::create(array_merge($data, ['is_active' => true]));
            session()->flash('success', 'Category created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function toggleActive(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_active' => ! $cat->is_active]);
    }

    public function moveUp(int $id): void
    {
        $cat  = Category::findOrFail($id);
        $prev = Category::where('sort_order', '<', $cat->sort_order)
            ->orderByDesc('sort_order')->first();

        if ($prev) {
            [$cat->sort_order, $prev->sort_order] = [$prev->sort_order, $cat->sort_order];
            $cat->save();
            $prev->save();
        }
    }

    public function moveDown(int $id): void
    {
        $cat  = Category::findOrFail($id);
        $next = Category::where('sort_order', '>', $cat->sort_order)
            ->orderBy('sort_order')->first();

        if ($next) {
            [$cat->sort_order, $next->sort_order] = [$next->sort_order, $cat->sort_order];
            $cat->save();
            $next->save();
        }
    }

    public function delete(int $id): void
    {
        $cat = Category::withCount('products')->findOrFail($id);

        if ($cat->products_count > 0) {
            session()->flash('error', "Cannot delete — \"{$cat->name}\" still has {$cat->products_count} product(s). Move or delete those first.");
            return;
        }

        $cat->delete();
        session()->flash('success', 'Category deleted.');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.admin.categories', compact('categories'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingId    = null;
        $this->name         = '';
        $this->description  = '';
        $this->sortOrder    = 1;
        $this->icon         = null;
        $this->existingIcon = null;
        $this->resetValidation();
    }
}
