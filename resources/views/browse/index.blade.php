@extends('layouts.app')

@section('title', 'Browse Services')

@section('content')
<div class="browse-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <div class="row align-items-end mb-3">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti ti-shield-check-filled"></i> Verified vendors, real availability</div>
                <h1>Find Everything for Your <span>Perfect Event</span></h1>
                <p>Browse <strong>{{ $halls->count() + $listings->count() }}</strong> verified venues and services across Pakistan — halls, decor, catering, and more.</p>
            </div>
            <div class="col-lg-5">
                <div class="hero-stats">
                    <div class="hero-stat-card">
                        <i class="ti ti-building-arch"></i>
                        <strong class="stat-number">{{ $halls->count() }}</strong>
                        <span class="stat-label">Halls</span>
                    </div>
                    <div class="hero-stat-card">
                        <i class="ti ti-list-check"></i>
                        <strong class="stat-number">{{ $listings->count() }}</strong>
                        <span class="stat-label">Services</span>
                    </div>
                    <div class="hero-stat-card">
                        <i class="ti ti-building-store"></i>
                        <strong class="stat-number">{{ $halls->pluck('vendor_profile_id')->unique()->count() + $listings->pluck('vendor_profile_id')->unique()->count() }}</strong>
                        <span class="stat-label">Vendors</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full-width Search --}}
        <div class="browse-search-wrap">
            <form action="{{ route('browse.search') }}" method="GET" class="search-box-wide">
                <div class="search-field search-field-date">
                    <i class="ti ti-calendar-event"></i>
                    <input type="date" name="date" onfocus="this.showPicker?.()" value="{{ request('date') }}">
                </div>
                <div class="search-divider"></div>
                <div class="search-field search-field-query">
                    <i class="ti ti-search"></i>
                    <input type="text" name="q" placeholder="Search halls, services, vendors..." value="{{ request('q') }}">
                </div>
                <div class="search-divider"></div>
                <div class="search-field search-field-type">
                    <i class="ti ti-category"></i>
                    <select name="event_type">
                        <option value="">All Events</option>
                        <option value="wedding" {{ request('event_type') === 'wedding' ? 'selected' : '' }}>Wedding</option>
                        <option value="engagement" {{ request('event_type') === 'engagement' ? 'selected' : '' }}>Engagement</option>
                        <option value="corporate" {{ request('event_type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                        <option value="birthday" {{ request('event_type') === 'birthday' ? 'selected' : '' }}>Birthday</option>
                    </select>
                </div>
                <button type="submit"><i class="ti ti-search"></i> Search</button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="section-title">
        <i class="ti ti-category"></i> Categories
        <a href="{{ route('browse.category', 'hall') }}" class="section-link">View All <i class="ti ti-chevron-right"></i></a>
    </div>
    <div class="browse-category-grid">
        @php
            $cats = [
                ['slug' => 'hall', 'icon' => 'ti-building-arch', 'label' => 'Halls &amp; Farmhouses', 'count' => $halls->count()],
                ['slug' => 'decor', 'icon' => 'ti-flower', 'label' => 'Decor', 'count' => $listings->where('service_category_id', $categories->where('slug','decor')->first()?->id)->count()],
                ['slug' => 'catering', 'icon' => 'ti-kitchen', 'label' => 'Catering', 'count' => $listings->where('service_category_id', $categories->where('slug','catering')->first()?->id)->count()],
                ['slug' => 'photography', 'icon' => 'ti-camera', 'label' => 'Photography', 'count' => $listings->where('service_category_id', $categories->where('slug','photography')->first()?->id)->count()],
                ['slug' => 'dj', 'icon' => 'ti-music', 'label' => 'DJ / Sound', 'count' => $listings->where('service_category_id', $categories->where('slug','dj')->first()?->id)->count()],
                ['slug' => 'car', 'icon' => 'ti-car', 'label' => 'Car Rental', 'count' => $listings->where('service_category_id', $categories->where('slug','car')->first()?->id)->count()],
            ];
        @endphp
            @foreach($cats as $cat)
            <a href="{{ route('browse.category', $cat['slug']) }}" class="browse-cat-card">
                <div class="cat-icon"><i class="ti {{ $cat['icon'] }}"></i></div>
                <h6>{!! $cat['label'] !!}</h6>
                <span class="cat-count">{{ $cat['count'] }} listed</span>
                <div class="cat-hover-glow"></div>
            </a>
        @endforeach
    </div>

    <div class="section-divider"></div>

    @if($halls->count() > 0)
        <div class="section-title">
            <i class="ti ti-building-arch"></i> Featured Halls
            <a href="{{ route('browse.category', 'hall') }}" class="section-link">View All Halls <i class="ti ti-chevron-right"></i></a>
        </div>
        <div class="row g-3 mb-5">
            @foreach($halls->take(4) as $hall)
                <div class="col-md-3">
                    <a href="{{ route('browse.hall', $hall) }}" class="browse-card browse-card-hover">
                        <div class="browse-card-img">
                            @if($hall->hallImages->count() > 0)
                                <img src="{{ asset('storage/' . $hall->hallImages->first()->image_path) }}" alt="{{ $hall->name }}">
                            @else
                                <div class="browse-card-img-placeholder">
                                    <i class="ti ti-building-arch"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-badge-group">
                            @if($hall->hallUnits->count() > 0)
                                <span class="badge-unit">{{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() > 1 ? 's' : '' }}</span>
                            @endif
                            @if($hall->floors->count() > 0)
                                <span class="badge-floor">{{ $hall->floors->count() }} floor{{ $hall->floors->count() > 1 ? 's' : '' }}</span>
                            @endif
                            @if($hall->vendorProfile)
                                <span class="badge-vendor">{{ $hall->vendorProfile->business_name }}</span>
                            @endif
                        </div>
                        <div class="card-title">{{ $hall->name }}</div>
                        <div class="card-meta"><i class="ti ti-map-pin"></i> {{ Str::limit($hall->address, 40) }}</div>
                        @if($hall->hallUnits->count() > 0)
                            <div class="card-price-row">
                                <span class="card-price">PKR {{ number_format($hall->hallUnits->min('base_price')) }}</span>
                                @if($hall->hallUnits->min('base_price') != $hall->hallUnits->max('base_price'))
                                    <span class="card-price-sub">– PKR {{ number_format($hall->hallUnits->max('base_price')) }}</span>
                                @endif
                            </div>
                            <div class="card-footer-info">
                                <span><i class="ti ti-users"></i> {{ $hall->hallUnits->min('min_capacity') }}-{{ $hall->hallUnits->max('max_capacity') }} guests</span>
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($listings->count() > 0)
        <div class="section-title">
            <i class="ti ti-list-check"></i> Featured Services
            <a href="{{ route('browse.category', 'decor') }}" class="section-link">View All Services <i class="ti ti-chevron-right"></i></a>
        </div>
        <div class="row g-3 mb-5">
            @foreach($listings->take(4) as $listing)
                <div class="col-md-3">
                    <a href="{{ route('browse.listing', $listing) }}" class="browse-card browse-card-hover d-flex flex-column">
                        <div class="card-badge-group">
                            <span class="badge-category">{{ $listing->serviceCategory->name ?? 'Service' }}</span>
                        </div>
                        <div class="card-title">{{ $listing->title }}</div>
                        <div class="card-meta" style="margin-bottom:auto;">
                            by <strong>{{ $listing->vendorProfile->business_name ?? 'Vendor' }}</strong>
                        </div>
                        <div class="card-price-row">
                            <span class="card-price">PKR {{ number_format($listing->price) }}</span>
                            <span class="card-price-unit">/ {{ str_replace('_', ' ', $listing->price_unit) }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <div class="cta-section">
        <div class="cta-card">
            <div class="cta-content">
                <h3>Are you a vendor?</h3>
                <p>List your venue or service on BeeG Events and start receiving bookings from verified customers.</p>
            </div>
            <a href="{{ route('register') }}" class="btn-gold btn-lg">Become a Vendor</a>
        </div>
    </div>
</div>
@endsection
