<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
class Admins extends Component
{
    use WithPagination;

    public string $search = '';

    // Create / edit modal
    public bool    $showModal  = false;
    public ?int    $editingId  = null;
    public string  $firstName  = '';
    public string  $lastName   = '';
    public string  $email      = '';
    public string  $password   = '';
    public string  $role       = 'admin';
    public string  $status     = 'active';

    // Delete confirm modal
    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    public function mount(): void
    {
        // Only super_admin can manage other admins
        abort_unless(Auth::user()->role === 'super_admin', 403);
    }

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Modal open/close ─────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->reset('editingId', 'firstName', 'lastName', 'email', 'password');
        $this->role   = 'admin';
        $this->status = 'active';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $admin = User::findOrFail($id);
        $this->editingId  = $id;
        $this->firstName  = $admin->first_name;
        $this->lastName   = $admin->last_name;
        $this->email      = $admin->email ?? '';
        $this->role       = $admin->role;
        $this->status     = $admin->status;
        $this->password   = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    // ── Save (create / update) ────────────────────────────────────────────────

    public function save(): void
    {
        $rules = [
            'firstName' => 'required|string|max:100',
            'lastName'  => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            'role'      => 'required|in:admin,super_admin',
            'status'    => 'required|in:active,inactive',
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|min:8';
        } elseif ($this->password !== '') {
            $rules['password'] = 'min:8';
        }

        $this->validate($rules);

        $data = [
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
            'role'       => $this->role,
            'status'     => $this->status,
        ];

        if ($this->password !== '') {
            $data['password'] = $this->password;
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', __('admin.admins.updated'));
        } else {
            $data['phone']    = 'admin_' . time(); // placeholder — admins log in via email
            $data['password'] = $this->password;
            User::create($data);
            session()->flash('success', __('admin.admins.created'));
        }

        $this->showModal = false;
    }

    // ── Toggle active / inactive ──────────────────────────────────────────────

    public function toggleStatus(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', __('admin.admins.cannot_self'));
            return;
        }

        $admin  = User::findOrFail($id);
        $admin->update(['status' => $admin->status === 'active' ? 'inactive' : 'active']);
        session()->flash('success', __('admin.admins.status_updated'));
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function delete(): void
    {
        if ($this->deletingId === Auth::id()) {
            session()->flash('error', __('admin.admins.cannot_self'));
            $this->showDeleteModal = false;
            return;
        }

        $admin = User::findOrFail($this->deletingId);

        // Guard: at least one super_admin must remain
        if ($admin->role === 'super_admin') {
            $superCount = User::where('role', 'super_admin')->count();
            if ($superCount <= 1) {
                session()->flash('error', __('admin.admins.last_super'));
                $this->showDeleteModal = false;
                return;
            }
        }

        $admin->delete();
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        session()->flash('success', __('admin.admins.deleted'));
    }

    public function render()
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->when($this->search, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name',  'like', "%{$this->search}%")
                      ->orWhere('email',      'like', "%{$this->search}%")
                ))
            ->orderByRaw("FIELD(role, 'super_admin', 'admin')")
            ->orderBy('created_at')
            ->paginate(15);

        return view('livewire.admin.admins', compact('admins'))
            ->layout('admin.layouts.app', ['title' => __('admin.admins.title')]);
    }
}
