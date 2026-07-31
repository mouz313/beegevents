<?php

namespace App\Console\Commands;

use App\Models\AvailabilitySlot;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:release-expired-holds')]
#[Description('Release expired "held" availability slots that were not confirmed within the time limit')]
class ReleaseExpiredHolds extends Command
{
    public function handle()
    {
        $released = AvailabilitySlot::where('status', 'held')
            ->where(function ($q) {
                $q->where('held_until', '<', now())
                    ->orWhere(function ($legacy) {
                        $legacy->whereNull('held_until')
                            ->where('created_at', '<', now()->subHours(24));
                    });
            })
            ->update(['status' => 'available', 'booking_id' => null, 'held_until' => null]);

        $this->info("Released {$released} expired hold(s).");
    }
}
