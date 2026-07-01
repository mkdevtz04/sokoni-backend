<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

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

    private function authUser(): User
    {
        return User::findOrFail(Auth::id());
    }

    public function mount(): void
    {
        $user = $this->authUser();
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

        $this->authUser()->update([
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

        $user = $this->authUser();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', __('admin.profile.wrong_password'));
            return;
        }

        $user->update(['password' => $this->newPassword]);

        $this->reset('currentPassword', 'newPassword', 'newPasswordConfirm');
        session()->flash('success', __('admin.profile.password_updated'));
    }

    public function render()
    {
        /** @disregard P1013 Livewire adds layout() to View via macro at runtime */
        return view('livewire.admin.admin-profile')
            ->layout('admin.layouts.app', ['title' => __('admin.profile.title')]);
    }
}
