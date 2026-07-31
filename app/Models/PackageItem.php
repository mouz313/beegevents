<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['package_id', 'itemable_type', 'itemable_id'])]
class PackageItem extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Package>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return MorphTo
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
