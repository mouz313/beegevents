<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallImage extends Model
{
    use HasFactory;

    protected $fillable = ['hall_id', 'image_path', 'sort_order', 'caption'];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }
}
