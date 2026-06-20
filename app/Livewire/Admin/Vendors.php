<?php

namespace App\Livewire\Admin;

use App\Models\VendorProfile;
use App\Services\FcmService;
use App\Services\KilakonaService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Vendors')]
class Vendors extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $filter   = 'all'; // all | pending | approved

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFilter(): void  { $this->resetPage(); }

    public function approve(int $id): void
    {
        $vendor = VendorProfile::with('user')->findOrFail($id);
        $vendor->update(['is_approved' => true]);
        $vendor->user?->update(['role' => 'vendor']);

        if ($vendor->user) {
            // SMS to registered phone
            try {
                (new KilakonaService())->send(
                    $vendor->user->phone,
                    "Hongera {$vendor->user->first_name}! Ombi lako la muuzaji kwa \"{$vendor->business_name}\" limekubaliwa. " .
                    "Sasa unaweza kuanza kuuza kwenye Sokoni. Ingia kwenye programu kuanza."
                );
            } catch (\Throwable $e) {
                Log::error('Vendor approval SMS failed', ['error' => $e->getMessage()]);
            }

            // FCM push notification
            try {
                app(FcmService::class)->notifyUser(
                    userId: $vendor->user->id,
                    title:  '✅ Application Approved!',
                    body:   "Congratulations! Your vendor application for \"{$vendor->business_name}\" has been approved. You can now start selling on Sokoni.",
                    data:   ['type' => 'vendor_approved'],
                );
            } catch (\Throwable $e) {
                Log::error('Vendor approval FCM failed', ['error' => $e->getMessage()]);
            }
        }

        session()->flash('success', 'Vendor approved.');
    }

    public function reject(int $id): void
    {
        $vendor = VendorProfile::with('user')->findOrFail($id);
        $vendor->update(['is_approved' => false]);
        $vendor->user?->update(['role' => 'customer']);
        session()->flash('success', 'Vendor rejected.');
    }

    public function render()
    {
        $vendors = VendorProfile::with('user')
            ->when($this->search, fn ($q) =>
                $q->where('business_name', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn ($u) =>
                      $u->where('phone', 'like', "%{$this->search}%")))
            ->when($this->filter === 'pending',  fn ($q) => $q->where('is_approved', false))
            ->when($this->filter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.vendors', compact('vendors'));
    }
}
