<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'business_name', 'vendor_type', 'city', 'phone', 'address', 'logo_path', 'status', 'cancellation_policy', 'cancel_free_days', 'cancel_refund_percent', 'bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban', 'cnic_front_path', 'cnic_back_path', 'onboarding_completed', 'contact_person_name', 'contact_person_phone', 'legal_doc_path', 'min_capacity', 'max_capacity', 'starting_price', 'years_experience', 'type_specs'])]
class VendorProfile extends Model
{
    use HasFactory;

    protected $casts = [
        'type_specs' => 'array',
        'cancel_refund_percent' => 'float',
    ];

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

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('sort_order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * The per-type spec row for this vendor (dynamic table), if it exists.
     */
    public function spec(): ?VendorSpec
    {
        return VendorSpec::forProfile($this);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getTypeLabelAttribute(): string
    {
        return config("vendor-specs.types.{$this->vendor_type}.label", ucwords(str_replace('_', ' ', (string) $this->vendor_type)));
    }

    /**
     * Merged key => value map of every field relevant to this vendor's type
     * (spec table row + type_specs JSON mirror + flat columns) for form
     * re-filling and display.
     */
    public function specFormValues(): array
    {
        $values = $this->type_specs ?? [];

        $spec = $this->spec();
        if ($spec) {
            $values = array_merge($values, collect($spec->toArray())
                ->except(['id', 'vendor_profile_id', 'created_at', 'updated_at'])
                ->toArray());
        }

        foreach ([
            'contact_person_name', 'contact_person_phone', 'legal_doc_path',
            'min_capacity', 'max_capacity', 'starting_price', 'years_experience',
        ] as $key) {
            $values[$key] = $this->{$key} ?? null;
        }

        return $values;
    }

    /**
     * Readable key => value list for public/admin display, formatted.
     */
    public function specDisplayList(): array
    {
        $type = $this->vendor_type;
        $def = config("vendor-specs.types.{$type}");
        if (!$def) {
            return [];
        }

        $values = $this->specFormValues();
        $rows = [];

        foreach ($def['fields'] as $field) {
            $key = $field['key'];
            $value = $values[$key] ?? null;

            if ($value === null || $value === '' || $value === [] || $value === false) {
                continue;
            }

            if ($field['type'] === 'checkbox') {
                $display = $value ? 'Yes' : 'No';
            } elseif ($field['type'] === 'multi_select') {
                $display = implode(', ', (array) $value);
            } elseif (is_array($value)) {
                $display = implode(', ', $value);
            } else {
                $display = $value;
                if (($field['suffix'] ?? null) === 'PKR') {
                    $display = 'PKR ' . number_format((float) $value);
                } elseif (($field['suffix'] ?? null)) {
                    $display .= ' ' . $field['suffix'];
                }
            }

            $rows[] = [
                'label' => $field['label'],
                'value' => $display,
            ];
        }

        return $rows;
    }
}
