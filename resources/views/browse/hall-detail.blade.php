@extends('layouts.app')

@section('title', $hall->name)

@section('content')
<div class="hall-hero">
    <div class="hall-hero-bg"></div>
    <div class="container position-relative">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb" style="background:none;padding:0;margin:0;">
                        <li class="breadcrumb-item"><a href="{{ route('browse.index') }}" style="color:rgba(255,255,255,0.5);text-decoration:none;font-size:12px;">Browse</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('browse.category', 'hall') }}" style="color:rgba(255,255,255,0.5);text-decoration:none;font-size:12px;">Halls</a></li>
                        <li class="breadcrumb-item active" style="color:var(--gold);font-size:12px;">{{ $hall->name }}</li>
                    </ol>
                </nav>
                <div class="hero-badge mb-3"><i class="ti ti-building-arch"></i> {{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() != 1 ? 's' : '' }}</div>
                <h1 class="hall-hero-title">{{ $hall->name }}</h1>
                <p class="hall-hero-address"><i class="ti ti-map-pin"></i> {{ $hall->address }}</p>
            </div>
            <div class="col-lg-4">
                @if($hall->hallUnits->count() > 0)
                    <div class="hall-hero-stats">
                        <div class="hero-stat-item">
                            <span class="hero-stat-label">Price Range</span>
                            <span class="hero-stat-value">PKR {{ number_format($hall->hallUnits->min('base_price')) }} – {{ number_format($hall->hallUnits->max('base_price')) }}</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-label">Capacity</span>
                            <span class="hero-stat-value"><i class="ti ti-users"></i> {{ $hall->hallUnits->min('min_capacity') }}–{{ $hall->hallUnits->max('max_capacity') }}</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-label">Rating</span>
                            <span class="hero-stat-value" style="color:var(--gold);">
                                @if($totalReviews > 0)
                                    <i class="ti ti-star-filled"></i> {{ number_format($avgRating, 1) }} ({{ $totalReviews }})
                                @else
                                    No reviews yet
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container detail-main">

    {{-- Image Gallery --}}
    @if($hall->hallImages->count() > 0)
        <div class="gallery-section">
            <div class="gallery-main">
                <img src="{{ asset('storage/' . $hall->hallImages->first()->image_path) }}" id="galleryMainImg" alt="{{ $hall->name }}">
                @if($hall->hallImages->count() > 1)
                    <button class="gallery-nav gallery-prev" id="galleryPrev"><i class="ti ti-chevron-left"></i></button>
                    <button class="gallery-nav gallery-next" id="galleryNext"><i class="ti ti-chevron-right"></i></button>
                @endif
                <div class="gallery-view-all" data-bs-toggle="modal" data-bs-target="#hallGalleryModal">
                    <i class="ti ti-layout-grid"></i> View all {{ $hall->hallImages->count() }} photos
                </div>
            </div>
            @if($hall->hallImages->count() > 1)
                <div class="gallery-thumbs">
                    @foreach($hall->hallImages as $i => $img)
                        <div class="gallery-thumb {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" data-path="{{ $img->image_path }}">
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Gallery Carousel Modal --}}
        <div class="modal fade" id="hallGalleryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content" style="background:#2B2620;border:none;border-radius:12px;">
                    <div class="modal-body p-0">
                        <div id="hallCarousel" class="carousel slide">
                            <div class="carousel-indicators">
                                @foreach($hall->hallImages as $i => $img)
                                    <button type="button" data-bs-target="#hallCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($hall->hallImages as $i => $img)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="d-block w-100" style="max-height:80vh;object-fit:contain;">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#hallCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#hallCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="gallery-section">
            <div class="gallery-main no-image">
                <div class="gallery-placeholder">
                    <i class="ti ti-building-arch"></i>
                    <span>No images available</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Calendar --}}
    @if(count($dateList) > 0)
        <div class="date-section">
            <div class="date-section-header">
                <div class="section-title mb-0"><i class="ti ti-calendar"></i> Select Event Date</div>
                <div class="date-section-controls">
                    <button id="calPrev" class="cal-arrow-btn" title="Scroll left"><i class="ti ti-chevron-left"></i></button>
                    <input type="date" id="eventDate" class="form-control date-picker-input"
                           min="{{ date('Y-m-d') }}" value="{{ $selectedDate }}">
                    <button id="checkDateBtn" class="btn-gold btn-sm" title="Check availability"><i class="ti ti-refresh"></i></button>
                    <button id="calNext" class="cal-arrow-btn" title="Scroll right"><i class="ti ti-chevron-right"></i></button>
                </div>
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

            <div class="calendar-wrapper">
                <div class="calendar-grid" id="calendarGrid">
                    @foreach($months as $month)
                        @php
                            $start = $month->copy();
                            $daysInMonth = $month->daysInMonth;
                            $dayOfWeek = (int)$start->dayOfWeek;
                            $leadingEmpty = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;
                            $totalCells = ceil(($leadingEmpty + $daysInMonth) / 7) * 7;
                        @endphp
                        <div class="cal-month" data-month="{{ $month->format('Y-m') }}">
                            <div class="cal-month-label">{{ $month->format('F Y') }}</div>
                            <table class="cal-table">
                                <thead>
                                    <tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>
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
            </div>

            <div class="cal-legend">
                <span class="cal-legend-item"><span class="cal-dot cal-dot-avail"></span> Available</span>
                <span class="cal-legend-item"><span class="cal-dot cal-dot-partial"></span> Limited</span>
                <span class="cal-legend-item"><span class="cal-dot cal-dot-booked"></span> Booked</span>
                <span class="cal-legend-item"><span class="cal-dot cal-dot-inquiry"></span> Inquiry</span>
            </div>
        </div>
    @endif

    @php
        $decorTypes = $hall->hallUnits->pluck('decor_type')->unique()->filter()->values();
        $minCap = $hall->hallUnits->min('min_capacity');
        $maxCap = $hall->hallUnits->max('max_capacity');
        $minPrice = $hall->hallUnits->min('base_price');
        $maxPrice = $hall->hallUnits->max('base_price');
    @endphp

    {{-- About Section --}}
    <div class="about-section">
        <div class="section-title"><i class="ti ti-info-circle"></i> About {{ $hall->name }}</div>
        @if($hall->description)
            <div class="about-description">{{ $hall->description }}</div>
        @endif
        <div class="about-grid">
            <div class="about-card">
                <i class="ti ti-users"></i>
                <strong>{{ $minCap }}–{{ $maxCap }}</strong>
                <span>Guest Capacity</span>
            </div>
            <div class="about-card">
                <i class="ti ti-building"></i>
                <strong>{{ $hall->hallUnits->count() }}</strong>
                <span>Event Spaces</span>
            </div>
            <div class="about-card">
                <i class="ti ti-currency-dollar"></i>
                <strong>PKR {{ number_format($minPrice) }}</strong>
                <span>Starting Price</span>
            </div>
            @if($decorTypes->count())
                <div class="about-card">
                    <i class="ti ti-palette"></i>
                    <strong>{{ $decorTypes->count() }}</strong>
                    <span>Decor Styles</span>
                </div>
            @endif
            <div class="about-card">
                <i class="ti ti-map-pin"></i>
                <strong>{{ $hall->vendorProfile->city ?? 'N/A' }}</strong>
                <span>Location</span>
            </div>
            <div class="about-card">
                <i class="ti ti-calendar-check"></i>
                <strong>{{ $hall->created_at->format('M Y') }}</strong>
                <span>Listed Since</span>
            </div>
        </div>
    </div>

    {{-- Main content + Sidebar --}}
    <div class="row g-4 mt-1">
        <div class="col-lg-8">

            {{-- Units --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="section-title mb-0"><i class="ti ti-layers"></i> Available Units</div>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12px;color:var(--text-muted);">{{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() != 1 ? 's' : '' }}</span>
                </div>
            </div>

            <div id="unitBookedData" data-dates='{{ json_encode($unitBookedDates) }}' style="display:none;"></div>

            @if($hall->has_floors && $hall->floors->count() > 0)
                @foreach($hall->floors as $floor)
                    @php $floorUnits = $hall->hallUnits->where('floor_id', $floor->id); @endphp
                    @if($floorUnits->count() > 0)
                        <div class="floor-section mb-3">
                            <div class="floor-label"><i class="ti ti-layers"></i> {{ $floor->floor_label }}</div>
                            @foreach($floorUnits as $unit)
                                @include('browse.partials.unit-card', ['unit' => $unit])
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @else
                @foreach($hall->hallUnits as $unit)
                    @include('browse.partials.unit-card', ['unit' => $unit])
                @endforeach
            @endif

            @if($hall->hallUnits->count() == 0)
                <div class="empty-state">
                    <i class="ti ti-building-arch"></i>
                    <h5>No units available</h5>
                    <p class="text-muted">This hall hasn't added any bookable units yet.</p>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="reviews-section mt-4">
                <div class="section-title"><i class="ti ti-star"></i> Reviews</div>
                @if($totalReviews > 0)
                    <div class="reviews-summary">
                        <div class="reviews-avg">
                            <span class="reviews-avg-num">{{ number_format($avgRating, 1) }}</span>
                            <div class="reviews-stars">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="ti ti-star{{ $s <= round($avgRating) ? '-filled' : '' }}" style="color:{{ $s <= round($avgRating) ? 'var(--gold)' : 'var(--border)' }};font-size:16px;"></i>
                                @endfor
                            </div>
                            <span class="reviews-count">{{ $totalReviews }} review{{ $totalReviews != 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    <div class="reviews-list">
                        @foreach($reviews as $review)
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="review-avatar">{{ substr($review->customer->name ?? 'A', 0, 1) }}</div>
                                    <div>
                                        <strong>{{ $review->customer->name ?? 'Anonymous' }}</strong>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $review->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div class="review-rating" style="margin-left:auto;">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="ti ti-star{{ $s <= $review->rating ? '-filled' : '' }}" style="font-size:12px;color:{{ $s <= $review->rating ? 'var(--gold)' : 'var(--border)' }};"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="review-comment">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding:32px 20px;">
                        <i class="ti ti-star" style="font-size:32px;"></i>
                        <h5>No reviews yet</h5>
                        <p class="text-muted" style="font-size:13px;">Be the first to book and leave a review!</p>
                    </div>
                @endif
            </div>

        </div>

        <div class="col-lg-4">
            <div class="sidebar-sticky">

            {{-- Vendor Card (merged: stats + floor plan + map + cancellation) --}}
            <div class="vendor-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="vendor-avatar-lg">
                        {{ substr($hall->vendorProfile->business_name ?? 'V', 0, 1) }}
                    </div>
                    <div>
                        <h6 style="font-weight:700;margin:0;font-size:15px;">{{ $hall->vendorProfile->business_name ?? 'N/A' }}</h6>
                        <span style="font-size:12px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> {{ $hall->vendorProfile->city ?? '' }}</span>
                    </div>
                </div>

                <div class="vendor-stats">
                    @if($hall->hallUnits->count() > 0)
                        <div class="vendor-stat-row">
                            <span class="vendor-stat-label">Price Range</span>
                            <span class="vendor-stat-value">PKR {{ number_format($minPrice) }} – {{ number_format($maxPrice) }}</span>
                        </div>
                        <div class="vendor-stat-row">
                            <span class="vendor-stat-label">Capacity</span>
                            <span class="vendor-stat-value">{{ $minCap }}–{{ $maxCap }} guests</span>
                        </div>
                        <div class="vendor-stat-row">
                            <span class="vendor-stat-label">Total Units</span>
                            <span class="vendor-stat-value">{{ $hall->hallUnits->count() }}</span>
                        </div>
                    @endif
                    <div class="vendor-stat-row">
                        <span class="vendor-stat-label">Rating</span>
                        <span class="vendor-stat-value" style="color:var(--gold);">{{ number_format($avgRating, 1) }} <i class="ti ti-star-filled" style="font-size:11px;"></i></span>
                    </div>
                    <div class="vendor-stat-row">
                        <span class="vendor-stat-label">Listed Since</span>
                        <span class="vendor-stat-value">{{ $hall->created_at->format('M Y') }}</span>
                    </div>
                </div>

                {{-- Floor Plan --}}
                @if($hall->has_floors && $hall->floors->count() > 0)
                    <hr style="margin:14px 0;border-color:var(--border);">
                    <h6 style="font-weight:700;font-size:13px;margin-bottom:10px;"><i class="ti ti-layers" style="color:var(--gold);"></i> Floor Plan</h6>
                    <div style="font-size:12px;color:var(--text-muted);">
                        @foreach($hall->floors as $floor)
                            @php $count = $hall->hallUnits->where('floor_id', $floor->id)->count(); @endphp
                            <div class="d-flex justify-content-between py-1">
                                <span>{{ $floor->floor_label }}</span>
                                <span style="font-weight:600;color:var(--charcoal);font-size:12px;">{{ $count }} unit{{ $count != 1 ? 's' : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Map --}}
                <hr style="margin:14px 0;border-color:var(--border);">
                <h6 style="font-weight:700;font-size:13px;margin-bottom:10px;"><i class="ti ti-map-2" style="color:var(--gold);"></i> Location</h6>
                <div style="border-radius:10px;overflow:hidden;">
                    <iframe
                        width="100%"
                        height="140"
                        frameborder="0"
                        style="border:0;display:block;"
                        src="https://www.openstreetmap.org/export/embed.html?bbox=74.0,31.0,74.5,31.6&layer=mapnik&marker=31.5204,74.3587&q={{ urlencode($hall->address) }}"
                        allowfullscreen>
                    </iframe>
                </div>
                <small style="display:block;margin-top:6px;font-size:11px;color:var(--text-muted);">{{ $hall->address }}</small>

                {{-- Cancellation Policy --}}
                @if($hall->vendorProfile->cancellation_policy)
                    <hr style="margin:14px 0;border-color:var(--border);">
                    <h6 style="font-weight:700;font-size:13px;margin-bottom:6px;"><i class="ti ti-file-text" style="color:var(--gold);"></i> Cancellation Policy</h6>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">{{ $hall->vendorProfile->cancellation_policy }}</p>
                @endif

                <button class="btn-gold w-100 mt-3" data-bs-toggle="modal" data-bs-target="#inquiryModal"
                    data-vendor-id="{{ $hall->vendor_profile_id }}"
                    data-inq-type="App\Models\Hall"
                    data-inq-id="{{ $hall->id }}">
                    <i class="ti ti-mail"></i> Send Inquiry
                </button>
                <a href="{{ route('browse.category', 'hall') }}" class="btn-outline-gold w-100 mt-2 text-center d-block">
                    Browse All Halls
                </a>
            </div>

            </div>
        </div>
    </div>

    {{-- Similar Halls --}}
    @if($similarHalls->count() > 0)
        <div class="similar-section mt-5">
            <div class="section-title"><i class="ti ti-building"></i> Similar Halls in {{ $hall->vendorProfile->city ?? 'Your City' }}</div>
            <div class="similar-grid">
                @foreach($similarHalls as $sh)
                    <a href="{{ route('browse.hall', $sh) }}@if(request('date'))?date={{ request('date') }}@endif" class="similar-card">
                        @if($sh->hallImages->count() > 0)
                            <div class="similar-card-img">
                                <img src="{{ asset('storage/' . $sh->hallImages->first()->image_path) }}" alt="{{ $sh->name }}">
                            </div>
                        @else
                            <div class="similar-card-img no-img">
                                <i class="ti ti-building"></i>
                            </div>
                        @endif
                        <div class="similar-card-body">
                            <h6>{{ $sh->name }}</h6>
                            <span style="font-size:11px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> {{ $sh->address }}</span>
                            @if($sh->hallUnits->count() > 0)
                                <div style="font-size:13px;font-weight:700;color:var(--gold-dark);margin-top:4px;">PKR {{ number_format($sh->hallUnits->min('base_price')) }}+</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

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
                        <textarea name="message" id="inqMessage" class="form-control" rows="4" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="I'm interested in booking this hall. Please share availability and pricing details..."></textarea>
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
    // Gallery
    const mainImg = document.getElementById('galleryMainImg');
    const thumbs = document.querySelectorAll('.gallery-thumb');
    let currentIndex = 0;

    function setGalleryImage(index) {
        if (!mainImg || !thumbs.length) return;
        currentIndex = index;
        const thumb = thumbs[index];
        mainImg.src = thumb.querySelector('img').src;
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }

    document.getElementById('galleryPrev')?.addEventListener('click', function() {
        const idx = (currentIndex - 1 + thumbs.length) % thumbs.length;
        setGalleryImage(idx);
    });

    document.getElementById('galleryNext')?.addEventListener('click', function() {
        const idx = (currentIndex + 1) % thumbs.length;
        setGalleryImage(idx);
    });

    thumbs.forEach((thumb, i) => {
        thumb.addEventListener('click', () => setGalleryImage(i));
    });

    // Calendar
    const unitBookedData = JSON.parse(document.getElementById('unitBookedData').dataset.dates);
    const dateInput = document.getElementById('eventDate');

    function updateUnitAvailability(date) {
        document.querySelectorAll('.unit-card').forEach(card => {
            const unitId = card.dataset.unitId;
            const isBooked = unitBookedData[unitId] && unitBookedData[unitId].includes(date);
            const btn = card.querySelector('.add-to-cart');
            const badge = card.querySelector('.unit-status');
            if (isBooked) {
                if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; btn.textContent = 'Booked'; }
                if (badge) { badge.textContent = 'Booked on this date'; badge.className = 'unit-status unavail'; }
            } else {
                if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.textContent = 'Add'; }
                if (badge) { badge.textContent = 'Available'; badge.className = 'unit-status avail'; }
            }
        });
    }

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
        updateUnitAvailability(date);
        syncDateToUrl(date);
    }

    // Calendar arrow scroll
    const calGrid = document.getElementById('calendarGrid');
    document.getElementById('calPrev')?.addEventListener('click', function() {
        calGrid.scrollBy({ left: -260, behavior: 'smooth' });
    });
    document.getElementById('calNext')?.addEventListener('click', function() {
        calGrid.scrollBy({ left: 260, behavior: 'smooth' });
    });

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
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
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
    });
})();
</script>
@endpush
