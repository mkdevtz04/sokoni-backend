<div>
    <!-- Toolbar -->
    <div class="mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search product name or vendor..."
            class="w-full sm:w-96 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Product</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Vendor</th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">Category</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">Price</th>
                        <th class="px-5 py-3 text-center whitespace-nowrap">Stock</th>
                        <th class="px-5 py-3 text-center whitespace-nowrap">Active</th>
                        <th class="px-5 py-3 text-center whitespace-nowrap">Featured</th>
                        <th class="px-5 py-3 text-center whitespace-nowrap">Delete</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $product->sku ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $product->vendorProfile->business_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $product->category->name ?? 'Uncategorised' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            TZS {{ number_format($product->sale_price ?? $product->price) }}
                            @if ($product->sale_price)
                                <br><span class="text-xs text-gray-400 line-through">{{ number_format($product->price) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $product->id }})"
                                class="w-10 h-6 rounded-full transition-colors {{ $product->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                <span class="block w-4 h-4 bg-white rounded-full shadow transition-transform mx-1
                                             {{ $product->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleFeatured({{ $product->id }})"
                                class="w-10 h-6 rounded-full transition-colors {{ $product->is_featured ? 'bg-primary' : 'bg-gray-300' }}">
                                <span class="block w-4 h-4 bg-white rounded-full shadow transition-transform mx-1
                                             {{ $product->is_featured ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="delete({{ $product->id }})"
                                wire:confirm="Delete '{{ $product->name }}'? This cannot be undone."
                                class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">No products found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>
</div>
