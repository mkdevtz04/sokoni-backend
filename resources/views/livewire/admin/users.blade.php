<div>
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search name, phone or email..."
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="filter"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="all">All Roles</option>
            <option value="customer">Customers</option>
            <option value="vendor">Vendors</option>
            <option value="super_admin">Admins</option>
        </select>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">User</th>
                        <th class="px-5 py-3 text-left">Phone</th>
                        <th class="px-5 py-3 text-left">Role</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Joined</th>
                        <th class="px-5 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 {{ $user->trashed() ? 'opacity-50' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email ?? 'No email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $user->phone }}</td>
                        <td class="px-5 py-3">
                            @php $roleColors = ['super_admin'=>'bg-purple-100 text-purple-700','vendor'=>'bg-blue-100 text-blue-700','customer'=>'bg-gray-100 text-gray-600']; @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @unless ($user->role === 'super_admin')
                            <button wire:click="toggleStatus({{ $user->id }})"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                       {{ $user->status === 'active'
                                           ? 'bg-red-50 hover:bg-red-100 text-red-600'
                                           : 'bg-green-50 hover:bg-green-100 text-green-600' }}">
                                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                            @endunless
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No users found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
