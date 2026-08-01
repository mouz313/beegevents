@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-badge"><i class="ti ti-category"></i> {{ $listing->serviceCategory->name ?? 'Service' }}</div>
                <h1>{{ $listing->title }}</h1>
                <p><i class="ti ti-building-store"></i> by <strong>{{ $listing->vendorProfile->business_name ?? 'N/A' }}</strong></p>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats" style="justify-content:flex-end;">
                    <span style="font-size:20px;font-weight:700;color:var(--gold);">PKR {{ number_format($listing->price) }}</span>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">/ {{ str_replace('_', ' ', $listing->price_unit) }}</span>
                </div>
                <button type="button" class="btn-gold mt-3" data-bs-toggle="modal" data-bs-target="#bookingModal" data-booking-mode="custom" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;">
                    <i class="ti ti-calendar-plus"></i> Book Now
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container detail-main">
    <nav aria-label="breadcrumb" class="breadcrumb-gold">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('browse.index') }}">Browse</a></li>
            <li class="breadcrumb-item"><a href="{{ route('browse.category', $listing->serviceCategory->slug) }}">{{ $listing->serviceCategory->name }}</a></li>
            <li class="breadcrumb-item active">{{ $listing->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="section-title"><i class="ti ti-info-circle"></i> Description</div>
            <div class="browse-card mb-4">
                <p style="font-size:14px;color:var(--text-primary);line-height:1.7;margin:0;">{{ $listing->description }}</p>
            </div>

            @if(count($dateList) > 0)
                <div class="date-section">
                    <div class="section-title"><i class="ti ti-calendar"></i> Select Your Event Date</div>
                    <div class="date-picker-row mb-3">
                        <input type="date" id="eventDate" class="form-control date-picker-input"
                               min="{{ date('Y-m-d') }}" value="{{ $selectedDate }}">
                        <span class="date-help-text">Pick a date or click one below</span>
                        <button id="checkDateBtn" class="btn-gold btn-sm"><i class="ti ti-refresh"></i> Check</button>
                    </div>

                    @php
                        $dateLookup = [];
                        foreach ($dateList as $d) { $dateLookup[$d['date']] = $d; }
                        $today = \Carbon\Carbon::today();
                        $months = [];
                        for ($i = 0; $i < 12; $i++) {
                            $months[] = $today->copy()->startOfMonth()->addMonthsNoOverflow($i);
                        }
                    @endphp

                    <div class="calendar-grid">
                        @foreach($months as $month)
                            @php
                                $start = $month->copy();
                                $daysInMonth = $month->daysInMonth;
                                $dayOfWeek = (int)$start->dayOfWeek;
                                $leadingEmpty = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;
                                $totalCells = ceil(($leadingEmpty + $daysInMonth) / 7) * 7;
                            @endphp
                            <div class="cal-month">
                                <div class="cal-month-label">{{ $month->format('F Y') }}</div>
                                <table class="cal-table">
                                    <thead>
                                        <tr>
                                            <th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $dayNum = 1; @endphp
                                        @for($cell = 0; $cell < $totalCells; $cell++)
                                            @if($cell % 7 == 0)<tr>@endif
                                            @if($cell < $leadingEmpty || $dayNum > $daysInMonth)
                                                <td class="cal-empty"></td>
                                            @else
                                                @php
                                                    $dateStr = $month->format('Y-m') . '-' . str_pad($dayNum, 2, '0', STR_PAD_LEFT);
                                                    $isPast = \Carbon\Carbon::parse($dateStr)->lt($today);
                                                    $dd = $dateLookup[$dateStr] ?? null;
                                                    $cls = $isPast ? 'cal-past' : ($dd ? $dd['class'] : '');
                                                    $cls = str_replace('dt-', 'cal-', $cls);
                                                @endphp
                                                <td class="{{ $cls }}" data-date="{{ $dateStr }}" data-status="{{ $dd['status'] ?? '' }}">
                                                    <span class="cal-day-num">{{ $dayNum }}</span>
                                                    @if($dd && !$isPast)
                                                        <span class="cal-day-label">{{ $dd['label'] }}</span>
                                                    @endif
                                                </td>
                                                @php $dayNum++; @endphp
                                            @endif
                                            @if($cell % 7 == 6)</tr>@endif
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    <div class="cal-legend">
                        <span class="cal-legend-item"><span class="cal-dot cal-dot-avail"></span> Available</span>
                        <span class="cal-legend-item"><span class="cal-dot cal-dot-booked"></span> Booked</span>
                        <span class="cal-legend-item"><span class="cal-dot cal-dot-inquiry"></span> Inquiry</span>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->role == 'customer')
                        <button class="btn-gold btn-lg add-to-cart mb-3" data-type="service_listing" data-id="{{ $listing->id }}">
                            <i class="ti ti-shopping-cart"></i> Add to Cart — PKR {{ number_format($listing->price) }}
                        </button>
                    @endif
                @endauth
            @else
                <div class="empty-state">
                    <i class="ti ti-calendar-off"></i>
                    <h5>No availability data</h5>
                    <p class="text-muted">This service currently has no availability information.</p>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="vendor-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="vendor-avatar">
                        {{ substr($listing->vendorProfile->business_name ?? 'V', 0, 1) }}
                    </div>
                    <div>
                        <h6 style="font-weight:600;margin:0;">{{ $listing->vendorProfile->business_name ?? 'N/A' }}</h6>
                        <span class="text-muted" style="font-size:13px;"><i class="ti ti-map-pin"></i> {{ $listing->vendorProfile->city ?? '' }}</span>
                    </div>
                </div>
                <hr style="border-color:var(--border);margin:12px 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size:12px;">Price</span>
                    <strong style="font-size:24px;color:var(--gold-dark);">PKR {{ number_format($listing->price) }}</strong>
                </div>
                <div class="text-muted text-end" style="font-size:12px;">/ {{ str_replace('_', ' ', $listing->price_unit) }}</div>
                <div class="d-flex gap-2 mt-2">
                    <button class="btn-outline-gold btn-sm w-100 text-center" data-bs-toggle="modal" data-bs-target="#inquiryModal"
                        data-vendor-id="{{ $listing->vendor_profile_id }}"
                        data-inq-type="App\Models\ServiceListing"
                        data-inq-id="{{ $listing->id }}">
                        <i class="ti ti-mail"></i> Send Inquiry
                    </button>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('browse.category', $listing->serviceCategory->slug) }}" class="btn-outline-gold btn-sm w-100 text-center">
                        Browse All {{ $listing->serviceCategory->name }}
                    </a>
                </div>
            </div>

            @php $specRows = $listing->vendorProfile ? $listing->vendorProfile->specDisplayList() : []; @endphp
            @if(count($specRows) > 0)
                <div class="vendor-card mt-3">
                    <h6 style="font-weight:700;font-size:13px;margin-bottom:12px;"><i class="ti ti-settings" style="color:var(--gold);"></i> Specifications</h6>
                    <div class="vendor-stats">
                        @foreach($specRows as $row)
                            <div class="vendor-stat-row">
                                <span class="vendor-stat-label">{{ $row['label'] }}</span>
                                <span class="vendor-stat-value">{{ $row['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php $menuCats = $listing->vendorProfile ? $listing->vendorProfile->menuCategories()->with('menuItems')->get() : collect(); @endphp
            @if($menuCats->count() > 0)
                <div class="vendor-card mt-3">
                    <h6 style="font-weight:700;font-size:13px;margin-bottom:12px;"><i class="ti ti-cookie" style="color:var(--gold);"></i> Food Menu</h6>
                    @foreach($menuCats as $cat)
                        @php $catItems = $cat->menuItems->where('is_available', true); @endphp
                        @if($catItems->count() > 0)
                            <div style="margin-bottom:14px;">
                                <div style="font-size:13px;font-weight:700;color:var(--charcoal);">{{ $cat->name }}</div>
                                <div style="border-top:1px dashed var(--border);margin:6px 0;padding-top:6px;">
                                    @foreach($catItems as $item)
                                        <div class="d-flex justify-content-between align-items-center py-1" style="font-size:12px;gap:8px;">
                                            <span style="color:var(--text-primary);">{{ $item->name }}</span>
                                            @if($item->price)
                                                <span style="font-weight:600;color:var(--gold-dark);white-space:nowrap;">PKR {{ number_format($item->price) }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Inquiry Modal -->
<div class="modal fade" id="inquiryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 12px 40px rgba(43,38,32,0.2);">
            <div class="modal-header" style="border:none;padding:24px 24px 0;">
                <h5 style="font-weight:700;"><i class="ti ti-mail" style="color:var(--gold);"></i> Send Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <div id="inqAlert" style="display:none;" class="alert mb-3" style="font-size:13px;border-radius:8px;"></div>
                <form id="inquiryForm">
                    <input type="hidden" name="vendor_profile_id" id="inqVendorId">
                    <input type="hidden" name="inquiriable_type" id="inqType">
                    <input type="hidden" name="inquiriable_id" id="inqId">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Your Name</label>
                        <input type="text" name="name" id="inqName" class="form-control" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" value="{{ auth()->check() ? auth()->user()->name : '' }}">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Email</label>
                            <input type="email" name="email" id="inqEmail" class="form-control" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" value="{{ auth()->check() ? auth()->user()->email : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Phone</label>
                            <input type="text" name="phone" id="inqPhone" class="form-control" style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" value="{{ auth()->check() ? auth()->user()->phone : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Message</label>
                        <textarea name="message" id="inqMessage" class="form-control" rows="4" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="I'm interested in booking this service. Please share availability and pricing details..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border:none;padding:0 24px 24px;">
                <button type="button" class="btn-outline-gold btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-gold btn-sm" id="inqSubmit" style="padding:10px 24px;">Send Inquiry</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Inquiry modal
(function() {
    const modal = document.getElementById('inquiryModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('inqVendorId').value = btn.dataset.vendorId;
            document.getElementById('inqType').value = btn.dataset.inqType;
            document.getElementById('inqId').value = btn.dataset.inqId;
        });

        document.getElementById('inqSubmit')?.addEventListener('click', function() {
            const form = document.getElementById('inquiryForm');
            const data = new FormData(form);
            const alert = document.getElementById('inqAlert');

            fetch('{{ route("inquiry.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(res => res.json())
            .then(d => {
                if (d.success) {
                    alert.className = 'alert alert-success mb-3';
                    alert.textContent = d.message;
                    alert.style.display = 'block';
                    form.reset();
                    setTimeout(() => { bootstrap.Modal.getInstance(modal).hide(); alert.style.display = 'none'; }, 2000);
                } else {
                    alert.className = 'alert alert-danger mb-3';
                    alert.textContent = d.message || 'Something went wrong.';
                    alert.style.display = 'block';
                }
            });
        });
    }
})();

(function() {
    const dateInput = document.getElementById('eventDate');

    function highlightCalendarDate(date) {
        document.querySelectorAll('.cal-table td[data-date]').forEach(cell => {
            cell.classList.remove('cal-selected');
        });
        if (date) {
            const selected = document.querySelector('.cal-table td[data-date="' + date + '"]');
            if (selected) selected.classList.add('cal-selected');
        }
    }

    function syncDateToUrl(date) {
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('date', date);
            window.history.replaceState({}, '', url);
        }
    }

    function applyDate(date) {
        highlightCalendarDate(date);
        syncDateToUrl(date);
    }

    // Calendar day click
    document.querySelectorAll('.cal-table td[data-date]:not(.cal-past)').forEach(cell => {
        cell.addEventListener('click', function() {
            const date = this.dataset.date;
            dateInput.value = date;
            applyDate(date);
        });
    });

    // Date picker change
    dateInput.addEventListener('change', function() {
        applyDate(this.value);
    });

    // Check button
    document.getElementById('checkDateBtn')?.addEventListener('click', function() {
        const date = dateInput.value;
        if (date) applyDate(date);
    });

    // Init for default/selected date
    if (dateInput.value) {
        applyDate(dateInput.value);
    }

    // Add to cart with date
    document.querySelector('.add-to-cart')?.addEventListener('click', function() {
        const date = dateInput.value;
        if (!date) {
            showToast('Please select an event date first', 'error');
            return;
        }
        fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: this.dataset.type, id: this.dataset.id, date: date })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Added to cart! (' + data.cart_count + ' items)', 'success');
            } else if (data.message) {
                showToast(data.message, 'error');
            }
        });
    });
})();
</script>
@endpush
