<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('admin.layouts.app')]
class AdminProfile extends Component
{
    // Personal info fields
    public string $firstName = '';
    public string $lastName  = '';
    public string $email     = '';

    // Password fields
    public string $currentPassword     = '';
    public string $newPassword         = '';
    public string $newPasswordConfirm  = '';

    public string $activeTab = 'info'; // info | password

    public function mount(): void
    {
        $user = Auth::user();
        $this->firstName = $user->first_name;
        $this->lastName  = $user->last_name;
        $this->email     = $user->email ?? '';
    }

    public function updateInfo(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:100',
            'lastName'  => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update([
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
        ]);

        session()->flash('success', __('admin.profile.updated'));
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword'    => 'required',
            'newPassword'        => 'required|min:8',
            'newPasswordConfirm' => 'required|same:newPassword',
        ]);

        if (! Hash::check($this->currentPassword, Auth::user()->password)) {
            $this->addError('currentPassword', __('admin.profile.wrong_password'));
            return;
        }

        Auth::user()->update(['password' => $this->newPassword]);

        $this->reset('currentPassword', 'newPassword', 'newPasswordConfirm');
        session()->flash('success', __('admin.profile.password_updated'));
    }

    public function render()
    {
        return view('livewire.admin.admin-profile')
            ->layout('admin.layouts.app', ['title' => __('admin.profile.title')]);
    }
}
