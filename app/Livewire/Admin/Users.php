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

    // ── Edit modal ────────────────────────────────────────────────────────────
    public bool   $showEditModal = false;
    public int    $editUserId    = 0;
    public string $editFirstName = '';
    public string $editLastName  = '';
    public string $editEmail     = '';
    public string $editPhone     = '';
    public string $editRole      = 'customer';

    // ── Delete modal ──────────────────────────────────────────────────────────
    public bool   $showDeleteModal = false;
    public int    $deleteUserId   = 0;
    public string $deleteUserName = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilter(): void { $this->resetPage(); }

    // ── Status toggle ─────────────────────────────────────────────────────────
    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editUserId    = $user->id;
        $this->editFirstName = $user->first_name ?? '';
        $this->editLastName  = $user->last_name  ?? '';
        $this->editEmail     = $user->email       ?? '';
        $this->editPhone     = $user->phone       ?? '';
        $this->editRole      = $user->role        ?? 'customer';

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function saveEdit(): void
    {
        $target = User::findOrFail($this->editUserId);

        $isAdminAccount = in_array($target->role, ['admin', 'super_admin']);

        $rules = [
            'editFirstName' => 'required|string|max:80',
            'editLastName'  => 'required|string|max:80',
            'editEmail'     => "nullable|email|max:180|unique:users,email,{$this->editUserId}",
            'editPhone'     => 'required|string|max:30',
        ];

        if (! $isAdminAccount) {
            $rules['editRole'] = 'required|in:customer,vendor';
        }

        $this->validate($rules);

        $data = [
            'first_name' => $this->editFirstName,
            'last_name'  => $this->editLastName,
            'email'      => $this->editEmail ?: null,
            'phone'      => $this->editPhone,
        ];

        // Role is locked for admin accounts — cannot be changed from this page
        if (! $isAdminAccount) {
            $data['role'] = $this->editRole;
        }

        $target->update($data);

        $this->showEditModal = false;
        session()->flash('success', 'User updated successfully.');
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function openDelete(int $id): void
    {
        $user = User::findOrFail($id);
        abort_if($user->role === 'super_admin', 403, 'Cannot delete a super admin.');

        $this->deleteUserId   = $user->id;
        $this->deleteUserName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        $user = User::findOrFail($this->deleteUserId);
        abort_if($user->role === 'super_admin', 403);

        $user->delete(); // soft delete

        $this->showDeleteModal = false;
        session()->flash('success', 'User deleted successfully.');
    }

    // ── Render ────────────────────────────────────────────────────────────────
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
