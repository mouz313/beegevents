<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['hall_id', 'floor_label'])]
class Floor extends Model
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
     * @return HasMany<HallUnit>
     */
    public function hallUnits(): HasMany
    {
        return $this->hasMany(HallUnit::class);
    }
}
