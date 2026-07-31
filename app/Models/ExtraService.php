<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['serviceable_type', 'serviceable_id', 'name', 'price', 'price_unit'])]
class ExtraService extends Model
{
    use HasFactory;

    /**
     * @return MorphTo
     */
    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }
}
