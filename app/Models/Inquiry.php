<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_profile_id',
        'user_id',
        'inquiriable_type',
        'inquiriable_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inquiriable(): MorphTo
    {
        return $this->morphTo();
    }
}
