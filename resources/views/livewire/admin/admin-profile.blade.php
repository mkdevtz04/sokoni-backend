<div class="max-w-2xl mx-auto space-y-6">

    {{-- Flash --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Personal Info ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">

        {{-- Avatar --}}
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
            <div class="w-14 h-14 rounded-full bg-primary flex items-center justify-center text-white text-xl font-bold shrink-0">
                {{ strtoupper(substr($firstName, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-gray-800">{{ $firstName }} {{ $lastName }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ auth()->user()->role === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ auth()->user()->role === 'super_admin' ? __('admin.admins.super_admin') : __('admin.admins.admin') }}
                </span>
            </div>
        </div>

        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">{{ __('admin.profile.personal_info') }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.first_name') }}</label>
                <input wire:model="firstName" type="text"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                              @error('firstName') border-red-300 @enderror">
                @error('firstName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.last_name') }}</label>
                <input wire:model="lastName" type="text"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                              @error('lastName') border-red-300 @enderror">
                @error('lastName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.email') }}</label>
            <input wire:model="email" type="email"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                          @error('email') border-red-300 @enderror">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <button wire:click="updateInfo" wire:loading.attr="disabled"
                    class="bg-primary hover:bg-orange-700 disabled:opacity-60 text-white text-sm font-semibold
                           px-6 py-2.5 rounded-xl transition-colors min-h-10 flex items-center gap-2">
                <span wire:loading wire:target="updateInfo">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </span>
                {{ __('admin.profile.save') }}
            </button>
        </div>
    </div>

    {{-- ── Change Password ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">{{ __('admin.profile.change_password') }}</h2>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.current_password') }}</label>
            <input wire:model="currentPassword" type="password"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                          @error('currentPassword') border-red-300 @enderror">
            @error('currentPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.new_password') }}</label>
            <input wire:model="newPassword" type="password"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                          @error('newPassword') border-red-300 @enderror">
            @error('newPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.profile.confirm_password') }}</label>
            <input wire:model="newPasswordConfirm" type="password"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                          @error('newPasswordConfirm') border-red-300 @enderror">
            @error('newPasswordConfirm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <button wire:click="updatePassword" wire:loading.attr="disabled"
                    class="bg-primary hover:bg-orange-700 disabled:opacity-60 text-white text-sm font-semibold
                           px-6 py-2.5 rounded-xl transition-colors min-h-10 flex items-center gap-2">
                <span wire:loading wire:target="updatePassword">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </span>
                {{ __('admin.profile.update_password') }}
            </button>
        </div>
    </div>

</div>
