<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['hall_id', 'floor_id', 'unit_name', 'min_capacity', 'max_capacity', 'menu_summary', 'decor_type', 'base_price'])]
class HallUnit extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Hall>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * @return BelongsTo<Floor>
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * @return MorphMany<ExtraService>
     */
    public function extraServices(): MorphMany
    {
        return $this->morphMany(ExtraService::class, 'serviceable');
    }

    /**
     * @return MorphMany<AvailabilitySlot>
     */
    public function availabilitySlots(): MorphMany
    {
        return $this->morphMany(AvailabilitySlot::class, 'resource');
    }
}
