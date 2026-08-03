@extends('layouts.app')

@section('title', $hall->name)

@section('content')
<style>
    /* Premium UI Enhancements */
    :root {
        --gold-gradient: linear-gradient(135deg, #D4AF37 0%, #AA8C2C 100%);
        --premium-shadow: 0 12px 40px rgba(0,0,0,0.08);
        --hover-shadow: 0 16px 50px rgba(0,0,0,0.12);
        --soft-border: 1px solid rgba(0,0,0,0.06);
    }
    
    body { background-color: #F8F9FA; font-family: 'Inter', sans-serif; }
    
    /* Immersive Hero Section */
    .hall-hero {
        position: relative;
        padding: 100px 0 80px;
        background: linear-gradient(135deg, rgba(15,15,15,0.95) 0%, rgba(30,25,20,0.85) 100%), url('{{ $hall->hallImages->count() > 0 ? asset("storage/" . $hall->hallImages->first()->image_path) : "" }}') center/cover no-repeat;
        color: white;
        border-bottom: 4px solid var(--gold);
        margin-bottom: 50px;
        animation: fadeIn 0.8s ease-out;
    }
    .hall-hero-bg { display: none; }
    .hall-hero-title { font-size: 3.5rem; font-weight: 800; text-shadow: 0 4px 15px rgba(0,0,0,0.5); margin-bottom: 12px; letter-spacing: -1px; line-height: 1.1; }
    .hall-hero-address { font-size: 1.15rem; opacity: 0.9; display: flex; align-items: center; gap: 8px; }
    
    .hall-hero-stats {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 24px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .hero-stat-item { margin-bottom: 16px; transition: transform 0.3s ease; }
    .hero-stat-item:hover { transform: translateX(5px); }
    .hero-stat-item:last-child { margin-bottom: 0; }
    .hero-stat-label { display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.6); font-weight: 600; margin-bottom: 4px; }
    .hero-stat-value { display: block; font-size: 1.3rem; font-weight: 700; color: white; }
    
    /* Elevated Sections */
    .detail-section, .about-section, .pricing-section, .vendor-card, .reviews-section, .similar-section {
        background: #ffffff;
        border-radius: 24px;
        padding: 35px;
        box-shadow: var(--premium-shadow);
        border: var(--soft-border);
        margin-bottom: 35px;
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
    }
    .detail-section:hover, .about-section:hover, .pricing-section:hover, .vendor-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--hover-shadow);
    }
    .section-title { font-size: 1.6rem; font-weight: 800; color: #1a1a1a; margin-bottom: 28px; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; }
    .section-title i { color: var(--gold); font-size: 2rem; background: rgba(212,175,55,0.1); padding: 10px; border-radius: 12px; }
    
    /* Gallery Polish */
    .gallery-main img { border-radius: 24px; height: 550px; box-shadow: var(--premium-shadow); transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1); }
    .gallery-main { border-radius: 24px; overflow: hidden; }
    .gallery-main:hover img { transform: scale(1.03); }
    .gallery-thumbs { gap: 16px; margin-top: 20px; padding-bottom: 12px; }
    .gallery-thumb { border-radius: 14px; opacity: 0.5; border: 3px solid transparent; transition: all 0.3s; }
    .gallery-thumb:hover, .gallery-thumb.active { opacity: 1; border-color: var(--gold); transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    
    /* Dynamic Unit Cards */
    .unit-card {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        border: var(--soft-border);
        box-shadow: 0 6px 20px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }
    .unit-card::before {
        content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--gold-gradient); opacity: 0; transition: opacity 0.3s;
    }
    .unit-card:hover { transform: translateY(-5px); box-shadow: var(--hover-shadow); border-color: rgba(212,175,55,0.4); }
    .unit-card:hover::before { opacity: 1; }
    
    /* Button Glows */
    .btn-gold { background: var(--gold-gradient); border: none; box-shadow: 0 8px 20px rgba(212,175,55,0.3); font-weight: 700; letter-spacing: 0.5px; transition: all 0.3s; }
    .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(212,175,55,0.5); filter: brightness(1.05); }
    
    /* About Cards Grid */
    .about-card { background: #fafafa; border-radius: 20px; padding: 24px; border: var(--soft-border); transition: all 0.3s; }
    .about-card:hover { background: #fff; transform: translateY(-6px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: rgba(212,175,55,0.2); }
    .about-card i { background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, rgba(212,175,55,0.2) 100%); padding: 16px; border-radius: 50%; color: var(--gold-dark); margin-bottom: 16px; font-size: 24px; display: inline-flex; }
    
    /* Clean Menus & Specs */
    .detail-section .row.g-3 .col-md-6 > div { background: #fafafa !important; border-radius: 16px !important; border: var(--soft-border); transition: all 0.3s; }
    .detail-section .row.g-3 .col-md-6 > div:hover { background: #fff !important; box-shadow: 0 10px 25px rgba(0,0,0,0.06); transform: translateY(-3px); border-color: rgba(212,175,55,0.2); }
    
    .detail-section .col-md-6 > div[style*="border:1px solid"] { border-radius: 20px !important; border: var(--soft-border) !important; background: #fafafa !important; transition: all 0.3s; }
    .detail-section .col-md-6 > div[style*="border:1px solid"]:hover { background: #fff !important; box-shadow: 0 12px 30px rgba(0,0,0,0.06) !important; transform: translateY(-4px); border-color: rgba(212,175,55,0.3) !important; }
    
    /* Animations */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .detail-main { animation: fadeIn 1s ease-out 0.2s both; }
</style>
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
                    <button type="button" class="btn-gold mt-3" data-bs-toggle="modal" data-bs-target="#bookingModal" data-booking-mode="custom" data-booking-hall="{{ $hall->id }}" data-booking-date="{{ $selectedDate }}" data-booking-time-slot="{{ $selectedTimeSlot }}" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;">
                        <i class="ti ti-calendar-plus"></i> Book Now
                    </button>
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

    {{-- Venue Pricing (floor-wise) --}}
    @if($hall->hallUnits->count() > 0)
        <div class="pricing-section">
            <div class="section-title"><i class="ti ti-currency-dollar"></i> Venue Pricing</div>
            <div class="table-responsive">
                <table class="table pricing-table">
                    <thead>
                        <tr>
                            <th>Floor</th>
                            <th>Hall / Unit</th>
                            <th>Capacity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hall->has_floors && $hall->floors->count() > 0)
                            @foreach($hall->floors as $floor)
                                @php $floorUnits = $hall->hallUnits->where('floor_id', $floor->id); @endphp
                                @forelse($floorUnits as $unit)
                                    <tr>
                                        <td><strong>{{ $floor->floor_label }}</strong></td>
                                        <td>{{ $unit->unit_name }}</td>
                                        <td>{{ $unit->min_capacity }}–{{ $unit->max_capacity }} guests</td>
                                        <td><strong style="color:var(--gold-dark);">PKR {{ number_format($unit->base_price) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted" style="font-size:13px;">No units on this floor.</td></tr>
                                @endforelse
                            @endforeach
                        @else
                            @foreach($hall->hallUnits as $unit)
                                <tr>
                                    <td>—</td>
                                    <td>{{ $unit->unit_name }}</td>
                                    <td>{{ $unit->min_capacity }}–{{ $unit->max_capacity }} guests</td>
                                    <td><strong style="color:var(--gold-dark);">PKR {{ number_format($unit->base_price) }}</strong></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Main content + Sidebar --}}
    <div class="row g-4 mt-1">
        <div class="col-lg-8">

            @php $specRows = $hall->vendorProfile ? $hall->vendorProfile->specDisplayList() : []; @endphp
            @if(count($specRows) > 0)
                <div class="detail-section mb-4">
                    <div class="section-title mb-3"><i class="ti ti-settings"></i> Hall Specifications</div>
                    <div class="row g-3">
                        @foreach($specRows as $row)
                            <div class="col-md-6 col-lg-4">
                                <div style="background:var(--cream);padding:12px 16px;border-radius:8px;height:100%;">
                                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">{{ $row['label'] }}</div>
                                    <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $row['value'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php $menuCats = $hall->vendorProfile ? $hall->vendorProfile->menuCategories()->with('menuItems')->get() : collect(); @endphp
            @if($menuCats->count() > 0)
                <div class="detail-section mb-4">
                    <div class="section-title mb-3"><i class="ti ti-cookie"></i> Food Menu</div>
                    <div class="row g-4">
                        @foreach($menuCats as $cat)
                            @php $catItems = $cat->menuItems->where('is_available', true); @endphp
                            @if($catItems->count() > 0)
                                <div class="col-md-6">
                                    <div style="border:1px solid var(--border);border-radius:12px;padding:16px;background:#fff;height:100%;">
                                        <h5 style="font-size:16px;font-weight:700;color:var(--gold-dark);margin-bottom:12px;border-bottom:2px solid var(--cream);padding-bottom:8px;">{{ $cat->name }}</h5>
                                        <ul style="list-style:none;padding:0;margin:0;">
                                        @foreach($catItems as $item)
                                            <li class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed var(--border);">
                                                <span style="font-size:14px;font-weight:500;color:var(--charcoal);">{{ $item->name }}</span>
                                                @if($item->price)
                                                    <span style="font-weight:700;color:var(--gold);font-size:14px;">PKR {{ number_format($item->price) }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if($menuSets->count() > 0)
                <div class="detail-section mb-4">
                    <div class="section-title mb-3"><i class="ti ti-license"></i> Menu Sets (Packages)</div>
                    <div class="row g-4">
                        @foreach($menuSets as $set)
                            @php $setItems = $set->items->where('is_available', true); @endphp
                            <div class="col-md-6">
                                <div style="border:1px solid var(--border);border-radius:12px;padding:16px;background:var(--cream);height:100%;">
                                    <h5 style="font-size:16px;font-weight:700;color:var(--charcoal);margin-bottom:4px;">{{ $set->name }}</h5>
                                    @if($set->description)
                                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">{{ $set->description }}</div>
                                    @endif
                                    <ul style="list-style:none;padding:0;margin:0;margin-bottom:16px;">
                                    @foreach($setItems as $item)
                                        <li class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed rgba(0,0,0,0.05);">
                                            <span style="font-size:13px;color:var(--charcoal);">{{ $item->name }}</span>
                                        </li>
                                    @endforeach
                                    </ul>
                                    @if($setItems->count() > 0)
                                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                            <span style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);">Set Total</span>
                                            <span style="font-size:18px;font-weight:800;color:var(--gold-dark);">PKR {{ number_format($set->items->sum('price')) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @include('browse.partials.combo-card', ['profile' => $hall->vendorProfile])

            {{-- Units --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="section-title mb-0"><i class="ti ti-layers"></i> Available Units</div>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12px;color:var(--text-muted);">{{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() != 1 ? 's' : '' }}</span>
                </div>
            </div>

            <div id="unitBookedData" data-slots='{{ json_encode($unitBookedSlots) }}' style="display:none;"></div>

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
                            @include('browse.partials.feature-badge', ['profile' => $sh->vendorProfile])
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
    const unitBookedSlots = JSON.parse(document.getElementById('unitBookedData').dataset.slots);
    const dateInput = document.getElementById('eventDate');

    function updateUnitAvailability(date) {
        document.querySelectorAll('.unit-card').forEach(card => {
            const unitId = card.dataset.unitId;
            const slot = card.querySelector('input[name="time_slot"]:checked')?.value || 'noon';
            const bookedSlots = (unitBookedSlots[unitId] && unitBookedSlots[unitId][date]) || [];
            const isBooked = bookedSlots.includes(slot);
            const btn = card.querySelector('.add-to-cart');
            const badge = card.querySelector('.unit-status');
            if (isBooked) {
                if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; btn.textContent = 'Booked'; }
                if (badge) { badge.textContent = 'Booked (' + slot + ')'; badge.className = 'unit-status unavail'; }
            } else {
                if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.textContent = 'Add'; }
                if (badge) { badge.textContent = 'Available (' + slot + ')'; badge.className = 'unit-status avail'; }
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

    // Time slot change re-checks availability per unit
    document.querySelectorAll('.unit-card input[name="time_slot"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateUnitAvailability(dateInput.value);
        });
    });

    // Pre-select time slot from URL (?time_slot=noon|evening)
    const urlSlot = new URL(window.location).searchParams.get('time_slot');
    if (urlSlot === 'noon' || urlSlot === 'evening') {
        document.querySelectorAll(`.unit-card input[name="time_slot"][value="${urlSlot}"]`).forEach(r => {
            r.checked = true;
        });
    }

    // Init for default/selected date
    if (dateInput.value) {
        applyDate(dateInput.value);
    }

    // Add to cart with date, time slot, menu set, extras, guests and catering
    function updateUnitSummary(card) {
        const summary = card.querySelector('.unit-mini-summary');
        if (!summary) return;
        const base = parseFloat(summary.dataset.basePrice) || 0;
        const menuSel = card.querySelector('select[name="menu_set_id"]');
        const menuPrice = (menuSel && menuSel.selectedOptions[0]) ? (parseFloat(menuSel.selectedOptions[0].dataset.price) || 0) : 0;
        let extrasTotal = 0;
        card.querySelectorAll('input[name="extras[]"]:checked').forEach(cb => {
            extrasTotal += parseFloat(cb.dataset.price) || 0;
        });
        const total = base + menuPrice + extrasTotal;
        summary.querySelector('div:nth-child(2) strong').textContent = '+PKR ' + menuPrice.toLocaleString();
        summary.querySelector('div:nth-child(3) strong').textContent = '+PKR ' + extrasTotal.toLocaleString();
        summary.querySelector('div:nth-child(4) strong').textContent = 'PKR ' + total.toLocaleString();
    }

    function validateUnitGuests(card) {
        const input = card.querySelector('.guests-input');
        const hint = card.querySelector('.guests-hint');
        if (!input || !hint) return true;
        const v = parseInt(input.value, 10);
        const min = parseInt(input.dataset.min, 10);
        const max = parseInt(input.dataset.max, 10);
        if (!input.value || isNaN(v) || v < min || v > max) {
            hint.style.color = 'var(--red)';
            hint.textContent = 'Enter ' + min + '–' + max + ' guests for this unit.';
            return false;
        }
        hint.style.color = 'var(--text-muted)';
        hint.textContent = 'Capacity: ' + min + '–' + max + ' guests';
        return true;
    }

    document.querySelectorAll('.unit-card .guests-input').forEach(inp => {
        inp.addEventListener('input', function() {
            validateUnitGuests(this.closest('.unit-card'));
        });
    });

    document.querySelectorAll('.unit-card select[name="menu_set_id"]').forEach(sel => {
        sel.addEventListener('change', function() {
            updateUnitSummary(this.closest('.unit-card'));
        });
    });

    document.querySelectorAll('.unit-card input[name="extras[]"]').forEach(cb => {
        cb.addEventListener('change', function() {
            updateUnitSummary(this.closest('.unit-card'));
        });
    });

    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const date = dateInput.value;
            if (!date) {
                showToast('Please select an event date first', 'error');
                return;
            }
            const card = this.closest('.unit-card');
            if (!validateUnitGuests(card)) {
                showToast('Please enter a valid guest count for this unit.', 'error');
                return;
            }
            const timeSlot = card.querySelector('input[name="time_slot"]:checked')?.value || 'noon';
            const menuSetId = card.querySelector('select[name="menu_set_id"]')?.value || null;
            const guests = card.querySelector('.guests-input')?.value || null;
            const cateringMode = card.querySelector('.catering-select')?.value || null;
            const extras = [];
            card.querySelectorAll('input[name="extras[]"]:checked').forEach(cb => {
                extras.push({
                    id: cb.value,
                    name: cb.dataset.name,
                    price: cb.dataset.price,
                    price_unit: cb.dataset.priceUnit
                });
            });
            fetch('{{ route("customer.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: this.dataset.type,
                    id: this.dataset.id,
                    date: date,
                    time_slot: timeSlot,
                    menu_set_id: menuSetId,
                    guests: guests,
                    catering_mode: cateringMode,
                    extras: extras
                })
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
