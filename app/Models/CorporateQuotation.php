<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['corporate_lead_id', 'token', 'event_date', 'venue', 'seating_capacity', 'budget', 'amount', 'inclusions', 'notes', 'valid_until', 'status', 'created_by'])]
class CorporateQuotation extends Model
{
    use HasFactory;

    protected $casts = [
        'event_date' => 'date',
        'valid_until' => 'date',
        'seating_capacity' => 'integer',
        'budget' => 'float',
        'amount' => 'float',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CorporateLead::class, 'corporate_lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getQuoteNoAttribute(): string
    {
        return 'QT-'.($this->created_at?->year ?? date('Y')).'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
