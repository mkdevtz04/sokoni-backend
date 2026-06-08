<?php

namespace App\Livewire\Admin;

use App\Models\Driver;
use App\Models\DriverApplication;
use App\Models\User;
use App\Services\KilakonaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Drivers')]
class Drivers extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'pending';

    // Reject modal state
    public bool   $showRejectModal = false;
    public string $rejectReason    = '';
    public ?int   $rejectingId     = null;

    // Inline feedback (replaces session flash for Livewire actions)
    public string $successMessage = '';
    public string $errorMessage   = '';

    // Approve confirm dialog state
    public bool   $showApproveConfirm = false;
    public ?int   $approvingId        = null;
    public string $approvingName      = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilter(): void { $this->resetPage(); }

    // ── Approve flow ──────────────────────────────────────────────────────────

    public function openApproveConfirm(int $id, string $name): void
    {
        $this->approvingId      = $id;
        $this->approvingName    = $name;
        $this->showApproveConfirm = true;
        $this->successMessage   = '';
        $this->errorMessage     = '';
    }

    public function cancelApprove(): void
    {
        $this->showApproveConfirm = false;
        $this->approvingId        = null;
        $this->approvingName      = '';
    }

    public function confirmApprove(): void
    {
        if (! $this->approvingId) return;

        $application = DriverApplication::findOrFail($this->approvingId);

        $this->showApproveConfirm = false;
        $this->approvingId        = null;
        $this->approvingName      = '';

        if (! $application->isPending()) {
            $this->errorMessage = 'This application has already been reviewed.';
            return;
        }

        // Generate a readable temp password: SKN + 6 digits
        $tempPassword = 'SKN' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            DB::transaction(function () use ($application, $tempPassword) {
                $user = User::firstOrCreate(
                    ['phone' => $application->phone],
                    [
                        'first_name'        => explode(' ', $application->full_name)[0],
                        'last_name'         => implode(' ', array_slice(explode(' ', $application->full_name), 1)) ?: '-',
                        'email'             => $application->email,
                        'password'          => Hash::make($tempPassword),
                        'role'              => 'driver',
                        'status'            => 'active',
                        'profile_completed' => true,
                    ]
                );

                // If user already existed (e.g. they registered before), update role and reset password
                if ($user->wasRecentlyCreated === false) {
                    $user->update([
                        'role'     => 'driver',
                        'password' => Hash::make($tempPassword),
                    ]);
                }

                Driver::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'driver_application_id' => $application->id,
                        'status'                => 'active',
                    ]
                );

                $application->update([
                    'status'      => 'approved',
                    'reviewed_by' => auth()->user()?->id,
                    'reviewed_at' => now(),
                ]);
            });

            // SMS with login credentials — sent outside transaction
            try {
                (new KilakonaService())->send(
                    $application->phone,
                    "Hongera {$application->full_name}! Umekubaliwa kuwa dereva wa Sokoni. " .
                    "Pakua programu ya Sokoni, ingia kwa: " .
                    "Namba: {$application->phone}, Nywila: {$tempPassword}. " .
                    "Badilisha nywila baada ya kuingia kwa mara ya kwanza."
                );
            } catch (\Throwable $e) {
                Log::error('Driver approval SMS failed', ['error' => $e->getMessage()]);
            }

            $this->successMessage = "Driver {$application->full_name} approved. Login credentials sent via SMS.";

        } catch (\Throwable $e) {
            Log::error('Driver approval failed', ['error' => $e->getMessage()]);
            $this->errorMessage = 'Approval failed: ' . $e->getMessage();
        }
    }

    // ── Reject flow ───────────────────────────────────────────────────────────

    public function openRejectModal(int $id): void
    {
        $this->rejectingId     = $id;
        $this->rejectReason    = '';
        $this->showRejectModal = true;
        $this->successMessage  = '';
        $this->errorMessage    = '';
        $this->resetErrorBag();
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectingId     = null;
        $this->rejectReason    = '';
        $this->resetErrorBag();
    }

    public function reject(): void
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:255',
        ]);

        if (! $this->rejectingId) return;

        $application = DriverApplication::findOrFail($this->rejectingId);

        if (! $application->isPending()) {
            $this->errorMessage    = 'This application has already been reviewed.';
            $this->showRejectModal = false;
            return;
        }

        try {
            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => $this->rejectReason,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            try {
                (new KilakonaService())->send(
                    $application->phone,
                    "Habari {$application->full_name}, kwa bahati mbaya ombi lako la Sokoni driver " .
                    "halijakubaliwa kwa sababu: {$this->rejectReason}. " .
                    "Unaweza kuwasiliana nasi kwa msaada zaidi."
                );
            } catch (\Throwable $e) {
                Log::error('Driver rejection SMS failed', ['error' => $e->getMessage()]);
            }

            $name = $application->full_name;
            $this->showRejectModal = false;
            $this->rejectingId     = null;
            $this->rejectReason    = '';
            $this->successMessage  = "Application from {$name} rejected and applicant notified.";

        } catch (\Throwable $e) {
            Log::error('Driver rejection failed', ['error' => $e->getMessage()]);
            $this->errorMessage = 'Rejection failed: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $applications = DriverApplication::with('reviewer')
            ->when($this->search, fn ($q) =>
                $q->where('full_name', 'like', "%{$this->search}%")
                  ->orWhere('phone',    'like', "%{$this->search}%"))
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.drivers', compact('applications'));
    }
}
