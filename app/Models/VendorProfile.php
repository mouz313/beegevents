<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'business_name', 'vendor_type', 'city', 'phone', 'address', 'logo_path', 'status', 'cancellation_policy', 'cancel_free_days', 'cancel_refund_percent', 'bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban', 'cnic_front_path', 'cnic_back_path', 'onboarding_completed'])]
class VendorProfile extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }

    public function serviceListings(): HasMany
    {
        return $this->hasMany(ServiceListing::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
