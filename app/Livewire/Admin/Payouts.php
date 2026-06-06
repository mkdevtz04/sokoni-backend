<?php

namespace App\Livewire\Admin;

use App\Models\VendorEarning;
use App\Models\WithdrawalRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Payouts')]
class Payouts extends Component
{
    use WithPagination;

    public string $filter = 'pending'; // pending | approved | sent | rejected

    public ?int   $rejectingId   = null;
    public string $rejectNote    = '';

    public function updatedFilter(): void { $this->resetPage(); }

    public function approve(int $id): void
    {
        WithdrawalRequest::findOrFail($id)->update([
            'status'       => 'approved',
            'processed_at' => now(),
        ]);
        session()->flash('success', 'Request approved. Send the M-Pesa payment now.');
    }

    public function startReject(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectNote  = '';
    }

    public function confirmReject(): void
    {
        if (! $this->rejectingId) return;

        WithdrawalRequest::findOrFail($this->rejectingId)->update([
            'status'       => 'rejected',
            'note'         => $this->rejectNote ?: 'Rejected by admin.',
            'processed_at' => now(),
        ]);

        $this->rejectingId = null;
        $this->rejectNote  = '';
        session()->flash('success', 'Request rejected.');
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectNote  = '';
    }

    /** Mark as sent and update vendor earnings to 'withdrawn'. */
    public function markSent(int $id): void
    {
        $wr = WithdrawalRequest::findOrFail($id);

        $wr->update([
            'status'       => 'sent',
            'processed_at' => now(),
        ]);

        // Mark vendor's oldest pending earnings as withdrawn up to the requested amount
        $remaining = $wr->amount;
        VendorEarning::where('vendor_id', $wr->vendor_id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get()
            ->each(function ($e) use (&$remaining) {
                if ($remaining <= 0) return false;
                $e->update(['status' => 'withdrawn']);
                $remaining -= $e->net_amount;
            });

        session()->flash('success', 'Payout marked as sent.');
    }

    public function render()
    {
        $requests = WithdrawalRequest::with(['vendor.vendorProfile'])
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'  => WithdrawalRequest::where('status', 'pending')->count(),
            'approved' => WithdrawalRequest::where('status', 'approved')->count(),
            'sent'     => WithdrawalRequest::where('status', 'sent')->sum('amount'),
        ];

        return view('livewire.admin.payouts', compact('requests', 'stats'));
    }
}
