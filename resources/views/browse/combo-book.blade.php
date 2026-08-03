@extends('layouts.app')

@section('title', ($combo->package->title ?? 'Combo').' — '.($vendor->business_name ?? 'Vendor'))

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-badge"><i class="ti ti-package"></i> Combo Package</div>
                <h1>{{ $combo->package->title ?? 'Combo Package' }}</h1>
                <p><i class="ti ti-building-store"></i> by <strong>{{ $vendor->business_name ?? 'N/A' }}</strong></p>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats" style="justify-content:flex-end;">
                    <span style="font-size:20px;font-weight:700;color:var(--gold);">PKR {{ number_format($combo->total_price) }}</span>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">combo total</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container detail-main">
    <nav aria-label="breadcrumb" class="breadcrumb-gold">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('browse.index') }}">Browse</a></li>
            <li class="breadcrumb-item active">{{ $combo->package->title ?? 'Combo' }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="section-title"><i class="ti ti-list-details"></i> What's Included</div>
            <div class="browse-card mb-4">
                @if($combo->package->description)
                    <p style="font-size:14px;color:var(--text-primary);line-height:1.7;">{{ $combo->package->description }}</p>
                    <hr style="border-color:var(--border);margin:16px 0;">
                @endif
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">
                    This bundle combines {{ $combo->items->count() }} items chosen by the vendor for your event.
                </div>
                <div>
                    @foreach($combo->items as $comboItem)
                        @php
                            $item = $comboItem->itemable;
                            $isHall = $comboItem->itemable_type === 'App\Models\HallUnit';
                            $name = $isHall
                                ? ($item->hall->name ?? 'Hall').' — '.($item->unit_name ?? 'Unit')
                                : ($item->title ?? 'Service');
                            $price = (float) ($item->price ?? $item->base_price ?? 0);
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed var(--border);">
                            <div>
                                <div style="font-weight:600;font-size:14px;color:var(--charcoal);">
                                    @if($isHall)
                                        <i class="ti ti-building" style="color:var(--gold-dark);"></i>
                                    @else
                                        <i class="ti ti-tool" style="color:var(--gold-dark);"></i>
                                    @endif
                                    {{ $name }}
                                </div>
                                <div style="font-size:12px;color:var(--text-muted);">
                                    {{ $isHall
                                        ? ($item->min_capacity ? 'Capacity '.$item->min_capacity.'–'.$item->max_capacity.' guests' : 'Hall unit')
                                        : str_replace('_', ' ', $item->price_unit ?? '') }}
                                </div>
                            </div>
                            <strong style="color:var(--gold-dark);white-space:nowrap;">PKR {{ number_format($price) }}</strong>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between align-items-center mt-3" style="padding-top:12px;border-top:2px solid var(--charcoal);">
                        <strong style="font-size:15px;">Combo Total</strong>
                        <strong style="font-size:20px;color:var(--gold-dark);">PKR {{ number_format($combo->total_price) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="vendor-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="vendor-avatar">
                        {{ substr($vendor->business_name ?? 'V', 0, 1) }}
                    </div>
                    <div>
                        <h6 style="font-weight:600;margin:0;">{{ $vendor->business_name ?? 'N/A' }}</h6>
                        <span class="text-muted" style="font-size:13px;"><i class="ti ti-map-pin"></i> {{ $vendor->city ?? '' }}</span>
                    </div>
                </div>
                <hr style="border-color:var(--border);margin:12px 0;">
                @if($vendor->starting_price)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size:12px;">Starting Price</span>
                        <strong style="color:var(--gold-dark);">PKR {{ number_format($vendor->starting_price) }}</strong>
                    </div>
                @endif
                <a href="{{ route('browse.category', 'hall') }}" class="btn-outline-gold btn-sm w-100 text-center d-block mt-2">
                    Browse All Halls
                </a>
            </div>
        </div>
    </div>

    {{-- Booking form --}}
    <div class="browse-card mt-4">
        <div class="section-title"><i class="ti ti-calendar-plus"></i> Book This Combo</div>

        @auth
            @if(auth()->user()->role === 'customer')
                @if($errors->any())
                    <div class="alert alert-danger" style="font-size:13px;border-radius:10px;">
                        <ul style="margin:0;padding-left:16px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('customer.combos.book', $combo) }}" id="comboBookForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Event Date</label>
                            <input type="date" name="event_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('event_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Event Type</label>
                            <select name="event_type" class="form-select" required>
                                <option value="">Select type</option>
                                <option value="wedding" @selected(old('event_type') === 'wedding')>Wedding</option>
                                <option value="engagement" @selected(old('event_type') === 'engagement')>Engagement</option>
                                <option value="corporate" @selected(old('event_type') === 'corporate')>Corporate</option>
                                <option value="birthday" @selected(old('event_type') === 'birthday')>Birthday</option>
                                <option value="home" @selected(old('event_type') === 'home')>Home</option>
                                <option value="other" @selected(old('event_type') === 'other')>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Time Slot</label>
                            <select name="time_slot" class="form-select">
                                <option value="noon" @selected(old('time_slot', 'noon') === 'noon')>Noon</option>
                                <option value="evening" @selected(old('time_slot') === 'evening')>Evening</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Tell the vendor about your event...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="mt-3" style="border:1px solid var(--border);border-radius:10px;padding:12px;background:var(--cream);">
                        <label class="form-check" style="margin:0;font-size:13px;">
                            <input type="checkbox" name="agreement_accepted" value="1" class="form-check-input" style="margin-right:6px;" required>
                            I agree to the BeeG Events Booking Agreement
                        </label>
                    </div>
                    <button type="submit" class="btn-gold mt-3" style="padding:12px 28px;border-radius:10px;font-weight:600;">
                        <i class="ti ti-send"></i> Submit Booking Request
                    </button>
                </form>
            @else
                <div class="alert alert-info mb-0" style="font-size:13px;border-radius:10px;">
                    Only customer accounts can book combos. Please <a href="{{ route('login') }}">login</a> as a customer.
                </div>
            @endif
        @else
            <div class="alert alert-warning mb-0" style="font-size:13px;border-radius:10px;">
                <a href="{{ route('login') }}">Login</a> or <a href="{{ route('register') }}">register</a> to book this combo.
            </div>
        @endauth
    </div>
</div>
@endsection
