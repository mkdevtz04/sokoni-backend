<div>

    {{-- Inline feedback --}}
    @if ($successMessage)
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $successMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-5 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ $errorMessage }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search name or phone..."
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">

        <select wire:model.live="filter"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="all">All</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Applicant</th>
                        <th class="px-5 py-3 text-left">ID Number</th>
                        <th class="px-5 py-3 text-left">Documents</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Applied</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($applications as $app)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl overflow-hidden bg-orange-100 shrink-0">
                                    @if ($app->application_photo_path)
                                        <img src="{{ Storage::url($app->application_photo_path) }}"
                                             alt="{{ $app->full_name }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-bold text-primary text-sm">
                                            {{ strtoupper(substr($app->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $app->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $app->phone }}</p>
                                    @if ($app->email)
                                        <p class="text-xs text-gray-400">{{ $app->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 font-mono text-xs">
                            {{ $app->national_id_number }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-col gap-1">
                                @if ($app->driver_license_path)
                                    <a href="{{ Storage::url($app->driver_license_path) }}" target="_blank"
                                       class="text-xs text-primary hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Driver License
                                    </a>
                                @endif
                                @if ($app->national_id_image_path)
                                    <a href="{{ Storage::url($app->national_id_image_path) }}" target="_blank"
                                       class="text-xs text-primary hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        National ID
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @if ($app->status === 'pending')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
                            @elseif ($app->status === 'approved')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>
                            @else
                                <span class="px-2.5 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium"
                                      title="{{ $app->rejection_reason }}">Rejected</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">
                            {{ $app->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3">
                            @if ($app->status === 'pending')
                                <div class="flex gap-2">
                                    <button wire:click="openApproveConfirm({{ $app->id }}, '{{ addslashes($app->full_name) }}')"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors">
                                        Approve
                                    </button>
                                    <button wire:click="openRejectModal({{ $app->id }})"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 text-xs font-medium rounded-lg transition-colors">
                                        Reject
                                    </button>
                                </div>
                            @elseif ($app->status === 'approved')
                                <span class="text-xs text-gray-400">
                                    by {{ $app->reviewer?->first_name ?? 'Admin' }}<br>
                                    {{ $app->reviewed_at?->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 max-w-[140px] block truncate"
                                      title="{{ $app->rejection_reason }}">
                                    {{ $app->rejection_reason }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            No {{ $filter === 'all' ? '' : $filter }} applications found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
    </div>

    {{-- ── Approve confirmation modal (Livewire-only, no Alpine event dispatch) ── --}}
    @if ($showApproveConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Approve Driver</h3>
                        <p class="text-xs text-gray-500">This will create an account and send an SMS</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">
                    Approve <strong>{{ $approvingName }}</strong> as a Sokoni driver?
                    They will receive an SMS notification and can log into the app immediately.
                </p>
                <div class="flex gap-3">
                    <button wire:click="cancelApprove"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="confirmApprove" wire:loading.attr="disabled" wire:target="confirmApprove"
                        class="flex-1 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="confirmApprove">Yes, Approve</span>
                        <span wire:loading wire:target="confirmApprove">Approving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Reject modal (Livewire-only) ── --}}
    @if ($showRejectModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="font-bold text-gray-800 text-lg mb-2">Reject Application</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Provide a reason. It will be sent to the applicant via SMS.
                </p>
                <textarea wire:model="rejectReason" rows="3"
                    placeholder="e.g. Driver license appears expired. Please reapply with a valid license."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                @error('rejectReason')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="closeRejectModal"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="reject" wire:loading.attr="disabled" wire:target="reject"
                        class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="reject">Reject & Notify</span>
                        <span wire:loading wire:target="reject">Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
