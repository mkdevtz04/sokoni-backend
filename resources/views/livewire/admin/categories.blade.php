<div>

    {{-- ── Error flash ────────────────────────────────────────────────────── --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Toolbar ────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search categories..."
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <button wire:click="openCreate"
            class="flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Category
        </button>
    </div>

    {{-- ── Table ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left w-8">Order</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">Products</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        {{-- Sort order controls --}}
                        <td class="px-5 py-3">
                            <div class="flex flex-col gap-0.5">
                                <button wire:click="moveUp({{ $cat->id }})"
                                    class="p-0.5 text-gray-400 hover:text-primary transition-colors"
                                    title="Move up">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                                <span class="text-xs text-gray-400 text-center leading-none">{{ $cat->sort_order }}</span>
                                <button wire:click="moveDown({{ $cat->id }})"
                                    class="p-0.5 text-gray-400 hover:text-primary transition-colors"
                                    title="Move down">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </td>

                        {{-- Name + icon --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($cat->icon)
                                    <img src="{{ asset('storage/' . $cat->icon) }}"
                                         class="w-9 h-9 rounded-xl object-cover flex-shrink-0"
                                         alt="{{ $cat->name }}">
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center font-bold text-primary text-sm flex-shrink-0">
                                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $cat->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $cat->slug }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Description --}}
                        <td class="px-5 py-3 text-gray-500 max-w-xs">
                            {{ $cat->description ? Str::limit($cat->description, 60) : '—' }}
                        </td>

                        {{-- Products count --}}
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                {{ $cat->products_count }}
                            </span>
                        </td>

                        {{-- Status toggle --}}
                        <td class="px-5 py-3">
                            <button wire:click="toggleActive({{ $cat->id }})"
                                class="{{ $cat->is_active
                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}
                                    px-2.5 py-1 rounded-full text-xs font-medium transition-colors cursor-pointer">
                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $cat->id }})"
                                    class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-lg transition-colors">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $cat->id }})"
                                    wire:confirm="Delete '{{ $cat->name }}'? This cannot be undone."
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            @if ($search)
                                No categories match "{{ $search }}"
                            @else
                                No categories yet — create your first one.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Create / Edit Modal ─────────────────────────────────────────────── --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.closeModal()">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" wire:click="closeModal"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">
                    {{ $editingId ? 'Edit Category' : 'New Category' }}
                </h2>
                <button wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Name <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="name" type="text" placeholder="e.g. Vegetables"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Description
                    </label>
                    <textarea wire:model="description" rows="2" placeholder="Short description (optional)"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sort order --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Sort Order
                    </label>
                    <input wire:model="sortOrder" type="number" min="0"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('sortOrder')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon upload --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Icon Image
                        <span class="normal-case font-normal text-gray-400 ml-1">(PNG/JPG, max 1 MB)</span>
                    </label>

                    {{-- Existing icon preview --}}
                    @if ($existingIcon && ! $icon)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('storage/' . $existingIcon) }}"
                                 class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                            <span class="text-xs text-gray-400">Current icon</span>
                        </div>
                    @endif

                    {{-- New upload preview --}}
                    @if ($icon)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ $icon->temporaryUrl() }}"
                                 class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                            <span class="text-xs text-gray-400">New icon (not saved yet)</span>
                        </div>
                    @endif

                    <label class="flex items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-200
                                  hover:border-primary rounded-xl cursor-pointer transition-colors group">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-primary transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm text-gray-400 group-hover:text-primary transition-colors">
                            {{ $icon ? 'Change image' : 'Choose image' }}
                        </span>
                        <input wire:model="icon" type="file" accept="image/*" class="hidden">
                    </label>
                    @error('icon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="closeModal"
                    class="px-5 py-2.5 text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="save" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-medium rounded-xl transition-colors disabled:opacity-60 flex items-center gap-2">
                    <span wire:loading wire:target="save">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                    </span>
                    {{ $editingId ? 'Save Changes' : 'Create Category' }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
