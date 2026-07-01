<div>

    {{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="{{ __('admin.users.search_placeholder') }}"
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="filter"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="all">{{ __('admin.users.all_roles') }}</option>
            <option value="customer">{{ __('admin.users.customers') }}</option>
            <option value="vendor">{{ __('admin.users.vendors') }}</option>
            <option value="super_admin">{{ __('admin.users.admins') }}</option>
        </select>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.user') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.phone') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.role') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.status') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.joined') }}</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">{{ __('admin.users.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">

                        {{-- User --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                                    {{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email ?? __('admin.users.no_email') }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Phone --}}
                        <td class="px-5 py-3 text-gray-600">{{ $user->phone }}</td>

                        {{-- Role --}}
                        <td class="px-5 py-3">
                            @php $roleColors = ['super_admin'=>'bg-purple-100 text-purple-700','vendor'=>'bg-blue-100 text-blue-700','customer'=>'bg-gray-100 text-gray-600','driver'=>'bg-green-100 text-green-700']; @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            @if ($user->trashed())
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ __('admin.users.deleted') }}</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->status === 'active' ? __('admin.users.active') : __('admin.users.inactive') }}
                                </span>
                            @endif
                        </td>

                        {{-- Joined --}}
                        <td class="px-5 py-3 text-gray-400 text-xs whitespace-nowrap">{{ $user->created_at->format('d M Y') }}</td>

                        {{-- Actions --}}
                        <td class="px-5 py-3">
                            @unless ($user->trashed())
                                <div class="flex items-center gap-1.5">
                                    {{-- Suspend / Activate --}}
                                    @unless ($user->role === 'super_admin')
                                    <button wire:click="toggleStatus({{ $user->id }})"
                                        title="{{ $user->status === 'active' ? __('admin.users.suspend_user') : __('admin.users.activate_user') }}"
                                        class="p-1.5 rounded-lg transition-colors {{ $user->status === 'active' ? 'text-amber-500 hover:bg-amber-50' : 'text-green-600 hover:bg-green-50' }}">
                                        @if ($user->status === 'active')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                    @endunless

                                    {{-- Edit --}}
                                    <button wire:click="openEdit({{ $user->id }})"
                                        title="{{ __('admin.users.edit_user') }}"
                                        class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete (not for super_admin) --}}
                                    @unless ($user->role === 'super_admin')
                                    <button wire:click="openDelete({{ $user->id }})"
                                        title="{{ __('admin.users.delete_user') }}"
                                        class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endunless
                                </div>
                            @endunless
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">{{ __('admin.users.no_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>

    {{-- ── Edit Modal ───────────────────────────────────────────────────────── --}}
    @if ($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">{{ __('admin.users.edit_modal_title') }}</h3>
                <button wire:click="$set('showEditModal', false)"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Name row --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('admin.users.first_name') }}</label>
                        <input wire:model="editFirstName" type="text" placeholder="{{ __('admin.users.first_name') }}"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('editFirstName') border-red-300 @enderror">
                        @error('editFirstName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('admin.users.last_name') }}</label>
                        <input wire:model="editLastName" type="text" placeholder="{{ __('admin.users.last_name') }}"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('editLastName') border-red-300 @enderror">
                        @error('editLastName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('admin.users.email_optional') }}</label>
                    <input wire:model="editEmail" type="email" placeholder="user@example.com"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('editEmail') border-red-300 @enderror">
                    @error('editEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('admin.users.phone') }}</label>
                    <input wire:model="editPhone" type="tel" placeholder="+255 7XX XXX XXX"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('editPhone') border-red-300 @enderror">
                    @error('editPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ __('admin.users.role_label') }}</label>
                    @if (in_array($editRole, ['admin', 'super_admin']))
                        <div class="w-full px-3 py-2.5 border border-gray-100 bg-gray-50 rounded-xl text-sm text-gray-500 flex items-center justify-between">
                            <span>{{ $editRole === 'super_admin' ? __('admin.users.super_admin_role') : __('admin.users.admin_role') }}</span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    @else
                        <select wire:model="editRole"
                            class="w-full px-3 py-2.5 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary {{ $errors->has('editRole') ? 'border border-red-300' : 'border border-gray-200' }}">
                            <option value="customer">{{ __('admin.users.customer_role') }}</option>
                            <option value="vendor">{{ __('admin.users.vendor_role') }}</option>
                        </select>
                        @error('editRole') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 pb-5">
                <button wire:click="$set('showEditModal', false)"
                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    {{ __('admin.users.cancel') }}
                </button>
                <button wire:click="saveEdit" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveEdit">{{ __('admin.users.save_changes') }}</span>
                    <span wire:loading wire:target="saveEdit">{{ __('admin.users.saving') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Delete Confirmation Modal ─────────────────────────────────────────── --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">

            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="font-bold text-lg text-gray-800 mb-2">{{ __('admin.users.delete_modal_title') }}</h3>
            <p class="text-gray-500 text-sm mb-6">
                {{ __('admin.users.delete_confirm', ['name' => $deleteUserName]) }}
            </p>

            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    {{ __('admin.users.cancel') }}
                </button>
                <button wire:click="confirmDelete" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="confirmDelete">{{ __('admin.users.delete_modal_title') }}</span>
                    <span wire:loading wire:target="confirmDelete">{{ __('admin.users.deleting') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
