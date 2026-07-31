@extends('layouts.app')

@section('title', $query ? 'Search: ' . $query : 'Search')

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti ti-search"></i> {{ $halls->count() + $listings->count() }} result{{ $halls->count() + $listings->count() != 1 ? 's' : '' }} found</div>
                <h1>Search <span>Results</span></h1>
                @if($query || $date)
                    <p>
                        @if($query)Showing results for "<strong>{{ $query }}</strong>"@endif
                        @if($date)@if($query) &middot; @else Showing results for @endif <strong>{{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</strong>@endif
                    </p>
                @endif
            </div>
            <div class="col-lg-5">
                <form action="{{ route('browse.search') }}" method="GET" class="search-box" style="flex-wrap:wrap;gap:6px;">
                    <div style="display:flex;align-items:center;gap:4px;flex:1;min-width:0;background:rgba(255,255,255,0.1);border-radius:8px;padding:0 10px;">
                        <i class="ti ti-calendar-event" style="color:rgba(255,255,255,0.3);font-size:14px;"></i>
                        <input type="date" name="date" value="{{ $date }}" style="color:var(--white);background:transparent;border:none;outline:none;font-size:13px;padding:8px 4px;min-width:0;width:100%;" onfocus="this.showPicker?.()">
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex:2;min-width:120px;background:rgba(255,255,255,0.1);border-radius:8px;padding:0 10px;">
                        <i class="ti ti-search" style="color:rgba(255,255,255,0.3);font-size:14px;"></i>
                        <input type="text" name="q" placeholder="Search again..." value="{{ $query }}" style="flex:1;min-width:60px;background:none;border:none;outline:none;color:var(--white);font-size:13px;padding:8px 4px;">
                    </div>
                    <select name="event_type" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.1);color:var(--white);border-radius:8px;padding:8px 10px;font-size:12px;outline:none;flex:0 0 auto;">
                        <option value="" {{ !$eventType ? 'selected' : '' }} style="background:var(--charcoal);">All Events</option>
                        <option value="wedding" {{ $eventType == 'wedding' ? 'selected' : '' }} style="background:var(--charcoal);">Wedding</option>
                        <option value="engagement" {{ $eventType == 'engagement' ? 'selected' : '' }} style="background:var(--charcoal);">Engagement</option>
                        <option value="corporate" {{ $eventType == 'corporate' ? 'selected' : '' }} style="background:var(--charcoal);">Corporate</option>
                        <option value="birthday" {{ $eventType == 'birthday' ? 'selected' : '' }} style="background:var(--charcoal);">Birthday</option>
                    </select>
                    <button type="submit" style="background:var(--gold);border:none;color:var(--charcoal);padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if($date || $query || $eventType || $city)
    <div class="filter-tags mb-4">
        @if($date)
            <span class="filter-tag"><i class="ti ti-calendar-event"></i> {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</span>
        @endif
        @if($eventType)
            <span class="filter-tag"><i class="ti ti-category"></i> {{ ucfirst($eventType) }}</span>
        @endif
        @if($city)
            <span class="filter-tag"><i class="ti ti-map-pin"></i> {{ $city }}</span>
        @endif
        @if($query)
            <span class="filter-tag"><i class="ti ti-search"></i> "{{ $query }}"</span>
        @endif
        <a href="{{ route('browse.search') }}" class="filter-clear">Clear All</a>
    </div>
    @endif

    @if($halls->count() > 0)
        <div class="section-title"><i class="ti ti-building-arch"></i> Halls <span class="text-muted" style="font-weight:400;font-size:13px;">({{ $halls->count() }})</span></div>
        <div class="row g-3 mb-5">
            @foreach($halls as $hall)
                <div class="col-md-4">
                    <a href="{{ route('browse.hall', $hall) }}{{ $date ? '?date='.$date : '' }}" class="browse-card browse-card-hover">
                        <div class="card-badge-group">
                            @if(isset($hall->available_units) && $date)
                                <span class="badge-available {{ $hall->available_units > 0 ? 'yes' : 'no' }}">
                                    @if($hall->available_units > 0)
                                        <i class="ti ti-circle-check-filled"></i> {{ $hall->available_units }}/{{ $hall->total_units }} available
                                    @else
                                        <i class="ti ti-circle-x-filled"></i> Booked on this date
                                    @endif
                                </span>
                            @endif
                            @if($hall->hallUnits->count() > 0)
                                <span class="badge-unit">{{ $hall->hallUnits->count() }} unit{{ $hall->hallUnits->count() > 1 ? 's' : '' }}</span>
                            @endif
                            @if($hall->vendorProfile)
                                <span class="badge-vendor">{{ $hall->vendorProfile->business_name }}</span>
                            @endif
                        </div>
                        <div class="card-title" style="font-size:14px;">{{ $hall->name }}</div>
                        <div class="card-meta" style="font-size:12px;"><i class="ti ti-map-pin"></i> {{ $hall->address }}</div>
                        @if($hall->hallUnits->count() > 0)
                            <div class="card-price-row">
                                <span class="card-price">PKR {{ number_format($hall->hallUnits->min('base_price')) }}</span>
                                <span class="card-price-sub">– PKR {{ number_format($hall->hallUnits->max('base_price')) }}</span>
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($listings->count() > 0)
        <div class="section-title"><i class="ti ti-list-check"></i> Services <span class="text-muted" style="font-weight:400;font-size:13px;">({{ $listings->count() }})</span></div>
        <div class="row g-3 mb-5">
            @foreach($listings as $listing)
                <div class="col-md-4">
                    <div class="browse-card browse-card-hover d-flex flex-column">
                        <a href="{{ route('browse.listing', $listing) }}{{ $date ? '?date='.$date : '' }}" class="text-decoration-none" style="color:inherit;">
                            <div class="card-badge-group">
                                <span class="badge-available yes" style="display:{{ $date ? 'inline-block' : 'none' }}"><i class="ti ti-circle-check-filled"></i> Available</span>
                                <span class="badge-category">{{ $listing->serviceCategory->name ?? 'Service' }}</span>
                            </div>
                            <div class="card-title" style="font-size:14px;">{{ $listing->title }}</div>
                            <div class="card-meta" style="font-size:12px;">
                                <i class="ti ti-building-store"></i> {{ $listing->vendorProfile->business_name ?? 'Unknown' }}
                                &middot; PKR {{ number_format($listing->price) }}
                            </div>
                        </a>
                        <div class="d-flex gap-2 mt-auto pt-2">
                            <a href="{{ route('browse.listing', $listing) }}{{ $date ? '?date='.$date : '' }}" class="btn-outline-gold btn-sm">Details</a>
                            @auth
                                @if(auth()->user()->role == 'customer')
                                    <button class="btn-gold btn-sm add-to-cart" data-type="service_listing" data-id="{{ $listing->id }}">Add to Cart</button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($halls->count() == 0 && $listings->count() == 0)
        <div class="empty-state">
            <i class="ti ti-search-off"></i>
            <h5>No results found</h5>
            @if($date)
                <p class="text-muted">Nothing available on <strong>{{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</strong> for your search. Try a different date or remove filters.</p>
            @else
                <p class="text-muted">Try different search terms or browse categories instead.</p>
            @endif
            <div class="d-flex gap-2 justify-content-center mt-3">
                <a href="{{ route('browse.index') }}" class="btn-gold">Browse All</a>
                <a href="{{ route('browse.category', 'hall') }}" class="btn-outline-gold">Browse Halls</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: this.dataset.type, id: this.dataset.id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Added to cart! (' + data.cart_count + ' items)', 'success');
            }
        });
    });
});
</script>
@endpush
