<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'business_name', 'vendor_type', 'city', 'phone', 'address', 'logo_path', 'status', 'trial_ends_at', 'feature_tier', 'featured_until', 'cancellation_policy', 'cancel_free_days', 'cancel_refund_percent', 'bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban', 'cnic_front_path', 'cnic_back_path', 'onboarding_completed', 'contact_person_name', 'contact_person_phone', 'legal_doc_path', 'min_capacity', 'max_capacity', 'starting_price', 'years_experience', 'type_specs'])]
class VendorProfile extends Model
{
    use HasFactory;

    protected $casts = [
        'type_specs' => 'array',
        'cancel_refund_percent' => 'float',
        'trial_ends_at' => 'datetime',
        'featured_until' => 'datetime',
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

    public function menuSets(): HasMany
    {
        return $this->hasMany(MenuSet::class)->orderBy('sort_order');
    }

    public function packagePurchases(): HasMany
    {
        return $this->hasMany(VendorPackagePurchase::class)->latest();
    }

    public function combos(): HasMany
    {
        return $this->hasMany(VendorCombo::class);
    }

    /**
     * The vendor's currently active combo, if it is fully filled and backed
     * by an active purchase (or unlocked without one).
     */
    public function activeCombo(): ?VendorCombo
    {
        return $this->combos()
            ->active()
            ->get()
            ->first(function (VendorCombo $combo) {
                if (! $combo->isFullyFilled()) {
                    return false;
                }
                $purchase = $combo->packagePurchase;

                return ! $purchase || $purchase->status === 'active';
            });
    }

    public function isFeatured(): bool
    {
        return $this->feature_tier !== null && $this->featured_until !== null && $this->featured_until->gt(now());
    }

    public function getFeatureLabelAttribute(): ?string
    {
        return $this->isFeatured() ? ucfirst($this->feature_tier) : null;
    }

    /**
     * The vendor is inside their free trial window (used when no paid package exists yet).
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->gt(now());
    }

    /**
     * The most recent active package purchase, if any.
     */
    public function activePackage(): ?VendorPackagePurchase
    {
        return $this->packagePurchases()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
    }

    /**
     * Whether the vendor is currently entitled to appear on the public site
     * (verified AND either on trial OR holding an active package).
     */
    public function visibleOnSite(): bool
    {
        return $this->status === 'verified' && ($this->onTrial() || $this->activePackage() !== null);
    }

    /**
     * Whether the vendor still has listing slots left under their current plan.
     */
    public function hasListingSlot(): bool
    {
        $package = $this->activePackage();
        $max = $package?->max_listings;

        if ($max === null) {
            return $this->onTrial();
        }

        return $this->serviceListings()->count() < $max;
    }

    /**
     * Whether the vendor still has hall slots left under their current plan.
     */
    public function hasHallSlot(): bool
    {
        $package = $this->activePackage();
        $max = $package?->max_halls;

        if ($max === null) {
            return $this->onTrial();
        }

        return $this->halls()->count() < $max;
    }

    /**
     * Only vendors that should be publicly listed (verified, not blocked,
     * and inside trial or holding an active package).
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'verified')
            ->where(function (Builder $q) {
                $q->where('trial_ends_at', '>', now())
                    ->orWhereHas('packagePurchases', function (Builder $p) {
                        $p->where('status', 'active')->where('ends_at', '>', now());
                    });
            });
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
     * KYC is considered complete only when every required identity, legal,
     * contact and payout field has been provided.
     */
    public function kycComplete(): bool
    {
        return empty($this->kycMissing());
    }

    /**
     * Readable labels of the KYC fields that are still missing.
     */
    public function kycMissing(): array
    {
        $checks = [
            'CNIC Front' => $this->cnic_front_path,
            'CNIC Back' => $this->cnic_back_path,
            'Legal Document' => $this->legal_doc_path,
            'Contact Person Name' => $this->contact_person_name,
            'Contact Person Number' => $this->contact_person_phone,
            'Bank Name' => $this->bank_name,
            'Bank Account Title' => $this->bank_account_title,
            'Bank Account Number' => $this->bank_account_number,
            'IBAN' => $this->bank_iban,
        ];

        return array_keys(array_filter($checks, fn ($v) => empty($v)));
    }

    /**
     * Badge used on vendor & admin screens to reflect KYC / verification state.
     */
    public function kycBadge(): array
    {
        if ($this->status === 'suspended') {
            return ['label' => 'Suspended', 'class' => 'kyc-badge-suspended'];
        }
        if ($this->status === 'blocked') {
            return ['label' => 'Blocked (No Package)', 'class' => 'kyc-badge-suspended'];
        }
        if (!$this->kycComplete()) {
            return ['label' => 'KYC Incomplete', 'class' => 'kyc-badge-incomplete'];
        }
        if ($this->status === 'verified') {
            return ['label' => 'Verified', 'class' => 'kyc-badge-verified'];
        }

        return ['label' => 'Pending Verification', 'class' => 'kyc-badge-pending'];
    }

    /**
     * Vendors missing at least one required KYC field.
     */
    public function scopeIncompleteKyc(Builder $query): Builder
    {
        $fields = ['cnic_front_path', 'cnic_back_path', 'legal_doc_path', 'contact_person_name', 'contact_person_phone', 'bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban'];

        return $query->where(function (Builder $q) use ($fields) {
            foreach ($fields as $field) {
                $q->orWhereNull($field)->orWhere($field, '');
            }
        });
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
