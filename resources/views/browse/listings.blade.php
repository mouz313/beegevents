@extends('layouts.app')

@section('title', $category->name)

@section('content')
@php
    $icons = ['hall' => 'ti-building-arch', 'decor' => 'ti-flower', 'catering' => 'ti-kitchen', 'photography' => 'ti-camera', 'dj' => 'ti-music', 'car' => 'ti-car'];
    $icon = $icons[$category->slug] ?? 'ti-list';
@endphp
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti {{ $icon }}"></i> {{ $listings->count() }} services available</div>
                <h1>{{ $category->name }} <span>Services</span></h1>
                <p>Find the best {{ strtolower($category->name) }} vendors for your event — compare packages and pricing.</p>
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
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row g-3">
        @forelse($listings as $listing)
            <div class="col-md-4">
                <div class="browse-card browse-card-hover d-flex flex-column">
                    <a href="{{ route('browse.listing', $listing) }}" class="text-decoration-none" style="color:inherit;">
                        <div class="card-badge-group">
                            <span class="badge-category">{{ $listing->serviceCategory->name ?? 'Service' }}</span>
                        </div>
                        <div class="card-title">{{ $listing->title }}</div>
                        <div class="card-meta">
                            <i class="ti ti-building-store"></i> {{ $listing->vendorProfile->business_name ?? 'Unknown' }}
                        </div>
                        <p style="font-size:13px;color:var(--text-muted);flex:1;margin:8px 0;">{{ Str::limit($listing->description, 100) }}</p>
                        <div class="card-price-row">
                            <span class="card-price">PKR {{ number_format($listing->price) }}</span>
                            <span class="card-price-unit">/ {{ str_replace('_', ' ', $listing->price_unit) }}</span>
                        </div>
                    </a>
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('browse.listing', $listing) }}" class="btn-outline-gold btn-sm">Details</a>
                        @auth
                            @if(auth()->user()->role == 'customer')
                                <button class="btn-gold btn-sm add-to-cart" data-type="service_listing" data-id="{{ $listing->id }}">Add to Cart</button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="ti {{ $icon }}"></i>
                    <h5>No services listed yet</h5>
                    <p class="text-muted">Check back soon for new listings in this category.</p>
                    <a href="{{ route('browse.index') }}" class="btn-gold mt-2">Browse All Categories</a>
                </div>
            </div>
        @endforelse
    </div>
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
                showToast(data.cart_count + ' items in cart', 'success');
            }
        });
    });
});
</script>
@endpush
