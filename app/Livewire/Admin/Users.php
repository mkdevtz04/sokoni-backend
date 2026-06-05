<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Users')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all | customer | vendor | super_admin

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilter(): void { $this->resetPage(); }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
    }

    public function render()
    {
        $users = User::withTrashed()
            ->when($this->search, fn ($q) =>
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name',  'like', "%{$this->search}%")
                  ->orWhere('phone',      'like', "%{$this->search}%")
                  ->orWhere('email',      'like', "%{$this->search}%"))
            ->when($this->filter !== 'all', fn ($q) => $q->where('role', $this->filter))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.users', compact('users'));
    }
}
