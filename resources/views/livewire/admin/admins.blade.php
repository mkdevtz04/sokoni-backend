<div>

    {{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="{{ __('admin.admins.search_placeholder') }}"
               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 bg-primary hover:bg-orange-700 text-white text-sm font-semibold
                       px-5 py-2.5 rounded-xl transition-colors shrink-0 min-h-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.admins.new') }}
        </button>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[680px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.name') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.email') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.role') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.status') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.created') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('admin.admins.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($admins as $admin)
                    <tr class="hover:bg-gray-50 transition-colors {{ $admin->id === auth()->id() ? 'bg-orange-50/40' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                                    {{ strtoupper(substr($admin->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $admin->full_name }}
                                        @if ($admin->id === auth()->id())
                                            <span class="text-xs text-gray-400 font-normal">({{ __('admin.admins.you') }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $admin->email ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $admin->role === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $admin->role === 'super_admin' ? __('admin.admins.super_admin') : __('admin.admins.admin') }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $admin->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $admin->status === 'active' ? __('admin.admins.active') : __('admin.admins.inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $admin->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button wire:click="openEdit({{ $admin->id }})"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600
                                               hover:border-primary hover:text-primary transition-colors">
                                    {{ __('admin.admins.edit') }}
                                </button>
                                @if ($admin->id !== auth()->id())
                                    <button wire:click="toggleStatus({{ $admin->id }})"
                                            class="text-xs px-3 py-1.5 rounded-lg transition-colors
                                               {{ $admin->status === 'active'
                                                   ? 'border border-yellow-200 text-yellow-700 hover:bg-yellow-50'
                                                   : 'border border-green-200 text-green-700 hover:bg-green-50' }}">
                                        {{ $admin->status === 'active' ? __('admin.admins.deactivate') : __('admin.admins.activate') }}
                                    </button>
                                    <button wire:click="confirmDelete({{ $admin->id }})"
                                            class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-600
                                                   hover:bg-red-50 transition-colors">
                                        {{ __('admin.admins.delete') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                            {{ __('admin.admins.no_admins') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($admins->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">{{ $admins->links() }}</div>
        @endif
    </div>

    {{-- ── Create / Edit Modal ─────────────────────────────────────────────── --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5">
                {{ $editingId ? __('admin.admins.edit') : __('admin.admins.new') }}
            </h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.admins.first_name') }}</label>
                    <input wire:model="firstName" type="text"
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary
                                  @error('firstName') border-red-300 @enderror">
                    @error('firstName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.admins.last_name') }}</label>
                    <input wire:model="lastName" type="text"
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary
                                  @error('lastName') border-red-300 @enderror">
                    @error('lastName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.admins.email') }}</label>
                <input wire:model="email" type="email"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary
                              @error('email') border-red-300 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    {{ __('admin.admins.password') }}
                    @if ($editingId)
                        <span class="font-normal text-gray-400">— {{ __('admin.admins.password_hint') }}</span>
                    @endif
                </label>
                <input wire:model="password" type="password"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary
                              @error('password') border-red-300 @enderror">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.admins.role') }}</label>
                    <select wire:model="role"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="admin">{{ __('admin.admins.admin') }}</option>
                        <option value="super_admin">{{ __('admin.admins.super_admin') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.admins.status') }}</label>
                    <select wire:model="status"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="active">{{ __('admin.admins.active') }}</option>
                        <option value="inactive">{{ __('admin.admins.inactive') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="closeModal"
                        class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    {{ __('admin.admins.cancel') }}
                </button>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-primary hover:bg-orange-700 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                    <span wire:loading wire:target="save">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    {{ __('admin.admins.save') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Delete Confirm Modal ─────────────────────────────────────────────── --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <p class="text-sm text-gray-700 mb-5">{{ __('admin.admins.confirm_delete') }}</p>
            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete"
                        class="px-5 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors">
                    {{ __('admin.admins.cancel') }}
                </button>
                <button wire:click="delete" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">
                    {{ __('admin.admins.delete') }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
