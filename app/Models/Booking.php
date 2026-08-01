<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['customer_id', 'booking_type', 'event_date', 'event_type', 'status', 'budget_input', 'total_price', 'negotiated_price', 'price_offer', 'price_offer_status', 'price_offer_note', 'price_offer_sent_at', 'price_negotiation_note', 'commission_amount', 'notes'])]
class Booking extends Model
{
    use HasFactory;

    protected $casts = [
        'event_date' => 'date',
        'price_offer_sent_at' => 'datetime',
    ];

    public function price(): float
    {
        return $this->negotiated_price !== null ? (float) $this->negotiated_price : (float) $this->total_price;
    }

    public function hasPendingOffer(): bool
    {
        return $this->price_offer !== null && $this->price_offer_status === 'pending';
    }

    /**
     * @return BelongsTo<User>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * @return HasMany<BookingItem>
     */
    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    /**
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Review>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Dispute>
     */
    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    /**
     * @return HasMany<Message>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
