<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'total_price', 'event_type'])]
class Package extends Model
{
    use HasFactory;

    /**
     * @return HasMany<PackageItem>
     */
    public function packageItems(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }
}
