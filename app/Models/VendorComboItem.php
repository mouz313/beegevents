<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['combo_id', 'slot_index', 'itemable_type', 'itemable_id'])]
class VendorComboItem extends Model
{
    use HasFactory;

    public function combo(): BelongsTo
    {
        return $this->belongsTo(VendorCombo::class, 'combo_id');
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
