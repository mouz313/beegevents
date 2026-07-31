<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['resource_type', 'resource_id', 'date', 'time_slot', 'status', 'booking_id', 'notes', 'held_until'])]
class AvailabilitySlot extends Model
{
    use HasFactory;

    /**
     * @return MorphTo
     */
    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Booking>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
