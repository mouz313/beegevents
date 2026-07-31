<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:auto-complete-bookings')]
#[Description('Mark confirmed bookings as completed once their event date has passed')]
class AutoCompleteBookings extends Command
{
    public function handle()
    {
        $updated = Booking::where('status', 'confirmed')
            ->whereDate('event_date', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        $this->info("Auto-completed {$updated} booking(s).");
    }
}
