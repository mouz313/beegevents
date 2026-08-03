@extends('layouts.app')

@section('title', $category->name . 's')

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti ti-building-arch"></i> {{ $halls->count() }} venues available</div>
                <h1>{{ $category->name }}s <span>for Your Event</span></h1>
                <p>Browse verified {{ strtolower($category->name) }} venues — compare prices, capacities, and amenities.</p>
            </div>
            <div class="col-lg-5">
                <form action="{{ route('browse.search') }}" method="GET" class="search-box" style="flex-wrap:wrap;gap:6px;">
                    <div style="display:flex;align-items:center;gap:4px;flex:1;min-width:100px;background:rgba(255,255,255,0.1);border-radius:8px;padding:0 10px;">
                        <i class="ti ti-calendar-event" style="color:rgba(255,255,255,0.3);font-size:14px;"></i>
                        <input type="date" name="date" style="color:var(--white);background:transparent;border:none;outline:none;font-size:13px;padding:8px 4px;min-width:0;width:100%;" onfocus="this.showPicker?.()">
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex:2;min-width:120px;background:rgba(255,255,255,0.1);border-radius:8px;padding:0 10px;">
                        <i class="ti ti-search" style="color:rgba(255,255,255,0.3);font-size:14px;"></i>
                        <input type="text" name="q" placeholder="Search {{ strtolower($category->name) }}..." value="{{ request('q') }}" style="flex:1;min-width:60px;background:none;border:none;outline:none;color:var(--white);font-size:13px;padding:8px 4px;">
                    </div>
                    <select name="event_type" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.1);color:var(--white);border-radius:8px;padding:8px 10px;font-size:12px;outline:none;flex:0 0 auto;">
                        <option value="" style="background:var(--charcoal);">All Events</option>
                        <option value="wedding" style="background:var(--charcoal);">Wedding</option>
                        <option value="engagement" style="background:var(--charcoal);">Engagement</option>
                        <option value="corporate" style="background:var(--charcoal);">Corporate</option>
                        <option value="birthday" style="background:var(--charcoal);">Birthday</option>
                    </select>
                    <select name="venue_type" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.1);color:var(--white);border-radius:8px;padding:8px 10px;font-size:12px;outline:none;flex:0 0 auto;">
                        <option value="" style="background:var(--charcoal);">All Venues</option>
                        <option value="marriage_hall" {{ request('venue_type') === 'marriage_hall' ? 'selected' : '' }} style="background:var(--charcoal);">Marriage Hall</option>
                        <option value="banquet_hall" {{ request('venue_type') === 'banquet_hall' ? 'selected' : '' }} style="background:var(--charcoal);">Banquet Hall</option>
                        <option value="farm_house" {{ request('venue_type') === 'farm_house' ? 'selected' : '' }} style="background:var(--charcoal);">Farm House</option>
                        <option value="community_center" {{ request('venue_type') === 'community_center' ? 'selected' : '' }} style="background:var(--charcoal);">Community Center</option>
                        <option value="hotel_ballroom" {{ request('venue_type') === 'hotel_ballroom' ? 'selected' : '' }} style="background:var(--charcoal);">Hotel Ballroom</option>
                        <option value="rooftop" {{ request('venue_type') === 'rooftop' ? 'selected' : '' }} style="background:var(--charcoal);">Rooftop</option>
                        <option value="lawn" {{ request('venue_type') === 'lawn' ? 'selected' : '' }} style="background:var(--charcoal);">Lawn</option>
                        <option value="marquee" {{ request('venue_type') === 'marquee' ? 'selected' : '' }} style="background:var(--charcoal);">Marquee</option>
                    </select>
                    <select name="time_slot" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.1);color:var(--white);border-radius:8px;padding:8px 10px;font-size:12px;outline:none;flex:0 0 auto;">
                        <option value="" style="background:var(--charcoal);">Any Time</option>
                        <option value="noon" {{ request('time_slot') === 'noon' ? 'selected' : '' }} style="background:var(--charcoal);">Noon</option>
                        <option value="evening" {{ request('time_slot') === 'evening' ? 'selected' : '' }} style="background:var(--charcoal);">Evening</option>
                    </select>
                    <button type="submit" style="background:var(--gold);border:none;color:var(--charcoal);padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <nav aria-label="breadcrumb" class="breadcrumb-gold">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('browse.index') }}">Browse</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}s</li>
        </ol>
    </nav>

    <div class="row g-3">
        @forelse($halls as $hall)
            <div class="col-md-4">
                <a href="{{ route('browse.hall', $hall) }}" class="browse-card browse-card-hover">
                    <div class="card-badge-group">
                        @include('browse.partials.feature-badge', ['profile' => $hall->vendorProfile])
                        @if($hall->venue_type)
                            <span class="badge-unit">{{ ucwords(str_replace('_', ' ', $hall->venue_type)) }}</span>
                        @endif
                        @if($hall->hallUnits->count() > 0)
                            <span class="badge-unit">{{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() > 1 ? 's' : '' }}</span>
                        @endif
                        @if($hall->vendorProfile)
                            <span class="badge-vendor">{{ $hall->vendorProfile->business_name }}</span>
                        @endif
                    </div>
                    <div class="card-title">{{ $hall->name }}</div>
                    <div class="card-meta"><i class="ti ti-map-pin"></i> {{ $hall->address }}</div>
                    @php
                        $unitMin = $hall->hallUnits->count() > 0 ? $hall->hallUnits->min('base_price') : null;
                        $unitMax = $hall->hallUnits->count() > 0 ? $hall->hallUnits->max('base_price') : null;
                        $startPrice = $hall->vendorProfile->starting_price ?? $unitMin;
                        $minCap = $hall->vendorProfile->min_capacity ?? ($hall->hallUnits->count() > 0 ? $hall->hallUnits->min('min_capacity') : null);
                        $maxCap = $hall->vendorProfile->max_capacity ?? ($hall->hallUnits->count() > 0 ? $hall->hallUnits->max('max_capacity') : null);
                    @endphp
                    @if($startPrice)
                        <div class="card-price-row">
                            <span class="card-price">PKR {{ number_format($startPrice) }}</span>
                            @if($unitMax && $unitMax > $startPrice)
                                <span class="card-price-sub">– PKR {{ number_format($unitMax) }}</span>
                            @endif
                        </div>
                    @endif
                    @if($minCap && $maxCap)
                        <div class="card-footer-info">
                            <span><i class="ti ti-users"></i> {{ $minCap }}-{{ $maxCap }} guests</span>
                        </div>
                    @endif
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="ti ti-building-arch"></i>
                    <h5>No halls listed yet</h5>
                    <p class="text-muted">Check back soon for new venues in this category.</p>
                    <a href="{{ route('browse.index') }}" class="btn-gold mt-2">Browse All Categories</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
