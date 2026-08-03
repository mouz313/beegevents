@extends('customer.layouts.master')

@section('title', 'Booking ' . $booking->reference)

@section('content')
@php
    $advancePaid = $booking->payments()->where('type', 'advance')->where('status', 'received')->sum('amount');
    $totalPaid = $booking->payments()->whereIn('status', ['received'])->sum('amount');
    $total = $booking->price();
    $remaining = max(0, $total - $totalPaid);
    $paidPct = $total > 0 ? round(min(100, ($totalPaid / $total) * 100)) : 0;

    $statusMeta = [
        'requested'  => ['Requested', 'var(--light-honey)', 'var(--gold-dark)', 'ti-clock'],
        'discussing' => ['In Discussion', 'var(--light-honey)', 'var(--gold-dark)', 'ti-message-circle'],
        'verified'   => ['Verified', 'var(--light-honey)', 'var(--gold-dark)', 'ti-shield-check'],
        'confirmed'  => ['Confirmed', '#E6F7ED', 'var(--green)', 'ti-circle-check-filled'],
        'cancelled'  => ['Cancelled', '#FDE8E8', 'var(--red)', 'ti-circle-off'],
        'completed'  => ['Completed', '#E8EEF1', 'var(--blue-grey)', 'ti-circle-check'],
    ];
    $meta = $statusMeta[$booking->status] ?? ['Booked', 'var(--light-honey)', 'var(--gold-dark)', 'ti-calendar'];
@endphp
<style>
    .bk-hero {
        background: linear-gradient(135deg, var(--charcoal) 0%, #3D342D 55%, #4A3F36 100%);
        color: var(--white);
        padding: 38px 0 34px;
        margin-bottom: 34px;
        border-bottom: 3px solid var(--gold);
        position: relative;
        overflow: hidden;
    }
    .bk-hero::after {
        content: '';
        position: absolute;
        right: -60px; top: -60px;
        width: 240px; height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(212,160,23,0.16) 0%, transparent 70%);
    }
    .bk-breadcrumb { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 12px; }
    .bk-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
    .bk-breadcrumb a:hover { color: var(--gold); }
    .bk-breadcrumb .sep { margin: 0 8px; opacity: 0.5; }
    .bk-hero-title { font-size: 26px; font-weight: 800; display: flex; align-items: center; gap: 12px; margin: 0 0 8px; }
    .bk-hero-title .hash { color: var(--gold); font-weight: 700; }
    .bk-hero-meta { font-size: 13px; color: rgba(255,255,255,0.72); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .bk-hero-meta i { color: var(--gold); }

    .bk-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 16px; border-radius: 30px;
        font-size: 12.5px; font-weight: 700;
    }
    .bk-hero-badge { background: var(--gold); color: var(--charcoal); }
    .bk-badge-outline { background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.18); }

    .bk-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .bk-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all 0.2s;
    }
    .bk-btn-solid { background: var(--gold); color: var(--charcoal); box-shadow: 0 6px 16px rgba(212,160,23,0.25); }
    .bk-btn-solid:hover { background: var(--gold-dark); color: var(--white); transform: translateY(-2px); }
    .bk-btn-ghost { background: rgba(255,255,255,0.08); color: var(--white); border: 1px solid rgba(255,255,255,0.16); }
    .bk-btn-ghost:hover { background: rgba(255,255,255,0.16); color: var(--white); }
    .bk-btn-light { background: var(--cream); color: var(--charcoal); border: 1px solid var(--border); }
    .bk-btn-light:hover { border-color: var(--gold); color: var(--gold-dark); }

    .bk-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 34px rgba(43,38,32,0.06);
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 22px;
    }
    .bk-card-head {
        padding: 17px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--cream);
        display: flex; align-items: center; gap: 10px;
    }
    .bk-card-head i { color: var(--gold-dark); font-size: 19px; }
    .bk-card-head h5 { margin: 0; font-weight: 700; font-size: 14.5px; color: var(--charcoal); }
    .bk-card-head .count { margin-left: auto; font-size: 11px; color: var(--text-muted); background: #fff; border: 1px solid var(--border); padding: 2px 10px; border-radius: 20px; font-weight: 600; }
    .bk-card-body { padding: 22px 24px; }

    .bk-info-tile {
        background: var(--cream);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        height: 100%;
        transition: all 0.2s;
    }
    .bk-info-tile:hover { border-color: var(--gold); transform: translateY(-2px); }
    .bk-info-tile .lbl { font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); font-weight: 700; margin-bottom: 5px; display: flex; align-items: center; gap: 5px; }
    .bk-info-tile .lbl i { color: var(--gold-dark); }
    .bk-info-tile .val { font-size: 14.5px; font-weight: 700; color: var(--charcoal); }

    .bk-item { display: flex; gap: 14px; padding: 18px 24px; border-bottom: 1px solid var(--border); }
    .bk-item:last-of-type { border-bottom: none; }
    .bk-item-icon {
        width: 46px; height: 46px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--light-honey) 0%, var(--cream) 100%);
        border: 1px solid var(--border);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: var(--gold-dark); font-size: 21px;
    }
    .bk-item-name { font-size: 14px; font-weight: 700; color: var(--charcoal); }
    .bk-item-vendor { font-size: 12px; color: var(--text-muted); margin-top: 1px; }
    .bk-tag {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10.5px; font-weight: 700;
        padding: 2px 10px; border-radius: 20px;
        background: var(--cream); color: var(--gold-dark);
        border: 1px solid var(--border);
        margin: 4px 4px 0 0;
    }
    .bk-item-price { font-size: 15px; font-weight: 800; color: var(--gold-dark); white-space: nowrap; }
    .bk-vstat {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px; border-radius: 20px;
        font-size: 10.5px; font-weight: 700;
        margin-top: 4px;
    }

    .bk-pay-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 13px; }
    .bk-pay-row.grand { border-top: 1.5px solid var(--charcoal); margin-top: 8px; padding-top: 11px; }
    .bk-pay-row.grand span:first-child { font-weight: 700; color: var(--charcoal); font-size: 14px; }
    .bk-pay-row.grand span:last-child { font-weight: 800; color: var(--gold-dark); font-size: 17px; }
    .bk-progress { height: 8px; background: var(--border); border-radius: 10px; overflow: hidden; margin: 12px 0 4px; }
    .bk-progress > div { height: 100%; background: linear-gradient(90deg, var(--gold) 0%, var(--gold-dark) 100%); border-radius: 10px; transition: width 0.6s ease; }
    .bk-progress-label { font-size: 11px; color: var(--text-muted); text-align: right; margin-bottom: 14px; }

    .bk-timeline { position: relative; padding-left: 4px; }
    .bk-tl-item { display: flex; gap: 12px; position: relative; padding-bottom: 18px; }
    .bk-tl-item:last-child { padding-bottom: 0; }
    .bk-tl-item::before {
        content: '';
        position: absolute; left: 15px; top: 30px; bottom: 2px;
        width: 2px; background: var(--border);
    }
    .bk-tl-item:last-child::before { display: none; }
    .bk-tl-dot {
        width: 32px; height: 32px; flex-shrink: 0;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        background: var(--cream); color: var(--gold-dark);
        border: 1px solid var(--border);
    }
    .bk-tl-item.done .bk-tl-dot { background: #E6F7ED; color: var(--green); border-color: #BCE3CD; }
    .bk-tl-item.active .bk-tl-dot { background: var(--gold); color: var(--white); border-color: var(--gold); box-shadow: 0 0 0 4px rgba(212,160,23,0.15); }
    .bk-tl-title { font-size: 13px; font-weight: 700; color: var(--charcoal); margin: 1px 0 1px; }
    .bk-tl-date { font-size: 11px; color: var(--text-muted); }
</style>

<div class="bk-hero">
    <div class="container position-relative" style="z-index:1;">
        <nav class="bk-breadcrumb">
            <a href="{{ route('customer.bookings.index') }}"><i class="ti ti-arrow-left"></i> My Bookings</a>
            <span class="sep">/</span>
            <span>Booking {{ $booking->reference }}</span>
        </nav>
        <div class="d-flex justify-content-between align-items-end" style="flex-wrap:wrap;gap:18px;">
            <div>
                <h1 class="bk-hero-title">
                    {{ $booking->reference }}
                    <span class="bk-badge bk-hero-badge"><i class="ti {{ $meta[3] }}"></i> {{ $meta[0] }}</span>
                </h1>
                <div class="bk-hero-meta">
                    <i class="ti ti-calendar"></i> {{ \Carbon\Carbon::parse($booking->event_date)->format('F d, Y') }}
                    @if($booking->time_slot)
                        <i class="ti ti-clock" style="margin-left:10px;"></i> {{ ucfirst($booking->time_slot) }} slot
                    @endif
                    <i class="ti ti-tag" style="margin-left:10px;"></i> {{ ucfirst($booking->event_type) }} event
                    @if($booking->created_at)
                        <i class="ti ti-history" style="margin-left:10px;"></i> requested {{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}
                    @endif
                </div>
            </div>
            <div class="bk-actions">
                <a href="{{ route('customer.bookings.download', $booking) }}" target="_blank" class="bk-btn bk-btn-ghost">
                    <i class="ti ti-download"></i> PDF
                </a>
                @if($totalPaid > 0)
                    <a href="{{ route('customer.bookings.invoice', $booking) }}" target="_blank" class="bk-btn bk-btn-ghost">
                        <i class="ti ti-file-invoice"></i> Invoice
                    </a>
                @endif
                <a href="{{ route('customer.messages.index', $booking) }}" class="bk-btn bk-btn-ghost">
                    <i class="ti ti-message-2"></i> Messages
                </a>
                @if($booking->status != 'cancelled' && $booking->status != 'completed')
                    <a href="{{ route('customer.bookings.payment', $booking) }}" class="bk-btn bk-btn-solid">
                        <i class="ti ti-credit-card"></i> Pay
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    @if($booking->hasPendingOffer())
        <div class="bk-card mb-4" style="border:1px solid var(--gold);">
            <div class="bk-card-body" style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                <div>
                    <strong style="color:var(--charcoal);font-size:15px;"><i class="ti ti-tag" style="color:var(--gold-dark);"></i> New Price Offer</strong>
                    <p style="margin:5px 0 0;font-size:13px;color:var(--text-muted);">
                        Our team has proposed a new price of <strong style="color:var(--gold-dark);">PKR {{ number_format($booking->price_offer) }}</strong> for this booking.
                        @if($booking->price_offer_note)
                            <br>"{{ $booking->price_offer_note }}"
                        @endif
                    </p>
                </div>
                <div style="display:flex;gap:8px;">
                    <form method="POST" action="{{ route('customer.bookings.price-offer.accept', $booking) }}">
                        @csrf
                        <button type="submit" class="bk-btn bk-btn-solid" style="border:none;cursor:pointer;">Accept Offer</button>
                    </form>
                    <form method="POST" action="{{ route('customer.bookings.price-offer.decline', $booking) }}">
                        @csrf
                        <button type="submit" class="bk-btn bk-btn-light" style="cursor:pointer;">Decline</button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($booking->price_offer_status == 'declined' && $booking->price_offer !== null)
        <div class="bk-card mb-4">
            <div class="bk-card-body" style="padding:14px 22px;font-size:13px;color:var(--text-muted);">
                <i class="ti ti-circle-off"></i> Price offer of <strong style="color:var(--charcoal);">PKR {{ number_format($booking->price_offer) }}</strong> was declined.
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Event details --}}
            <div class="bk-card">
                <div class="bk-card-head">
                    <i class="ti ti-calendar-event"></i>
                    <h5>Event Details</h5>
                </div>
                <div class="bk-card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="bk-info-tile">
                                <span class="lbl"><i class="ti ti-calendar"></i> Event Date</span>
                                <span class="val">{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="bk-info-tile">
                                <span class="lbl"><i class="ti ti-clock"></i> Time Slot</span>
                                <span class="val">{{ $booking->time_slot ? ucfirst($booking->time_slot) : 'Flexible' }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="bk-info-tile">
                                <span class="lbl"><i class="ti ti-tag"></i> Event Type</span>
                                <span class="val" style="text-transform:capitalize;">{{ $booking->event_type }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="bk-info-tile">
                                <span class="lbl"><i class="ti ti-status-change"></i> Status</span>
                                <span class="val"><span class="bk-badge" style="background:{{ $meta[1] }};color:{{ $meta[2] }};padding:3px 12px;font-size:11px;">{{ $meta[0] }}</span></span>
                            </div>
                        </div>
                    </div>
                    @if($booking->notes)
                        <div style="border-top:1px dashed var(--border);margin-top:18px;padding-top:16px;">
                            <span style="font-size:11px;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:700;"><i class="ti ti-note"></i> Notes</span>
                            <p style="font-size:13px;color:var(--charcoal);margin:6px 0 0;">{{ $booking->notes }}</p>
                        </div>
                    @endif
                    @if($booking->agreement_accepted_at)
                        <div style="border-top:1px dashed var(--border);margin-top:16px;padding-top:12px;font-size:12px;color:var(--green);">
                            <i class="ti ti-shield-check"></i> <strong>Terms &amp; agreement accepted</strong> on {{ \Carbon\Carbon::parse($booking->agreement_accepted_at)->format('M d, Y') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items --}}
            <div class="bk-card">
                <div class="bk-card-head">
                    <i class="ti ti-package"></i>
                    <h5>Your Services</h5>
                    <span class="count">{{ $booking->bookingItems->count() }} item{{ $booking->bookingItems->count() != 1 ? 's' : '' }}</span>
                </div>
                <div style="padding:0;">
                    @foreach($booking->bookingItems as $item)
                        @php
                            $isHall = $item->itemable_type === 'App\Models\HallUnit';
                            $vStat = $item->vendor_status;
                            $vBg = $vStat == 'accepted' ? '#E6F7ED' : ($vStat == 'declined' ? '#FDE8E8' : 'var(--light-honey)');
                            $vFg = $vStat == 'accepted' ? 'var(--green)' : ($vStat == 'declined' ? 'var(--red)' : 'var(--gold-dark)');
                        @endphp
                        <div class="bk-item">
                            <div class="bk-item-icon">
                                <i class="ti {{ $isHall ? 'ti-building-arch' : 'ti-briefcase' }}"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="bk-item-name">
                                            @if($item->itemable)
                                                {{ $item->itemable->unit_name ?? $item->itemable->title ?? ('#' . $item->itemable_id) }}
                                            @else
                                                {{ str_replace('_', ' ', class_basename($item->itemable_type)) }} #{{ $item->itemable_id }}
                                            @endif
                                        </div>
                                        <div class="bk-item-vendor">
                                            <i class="ti ti-building-store"></i> {{ $item->vendorProfile->business_name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="bk-item-price">PKR {{ number_format($item->price) }}</div>
                                </div>
                                <div style="margin-top:4px;">
                                    @if($item->time_slot)
                                        <span class="bk-tag"><i class="ti ti-clock"></i> {{ ucfirst($item->time_slot) }}</span>
                                    @endif
                                    @if($item->guests)
                                        <span class="bk-tag"><i class="ti ti-users"></i> {{ number_format($item->guests) }} guests</span>
                                    @endif
                                    @if($item->catering_mode)
                                        <span class="bk-tag"><i class="ti ti-cooking-pot"></i> {{ ['internal' => 'In-house catering', 'external' => 'Outside catering', 'none' => 'Self-arrange'][$item->catering_mode] ?? ucfirst($item->catering_mode) }}</span>
                                    @endif
                                    @if($item->menuSet)
                                        <span class="bk-tag" style="color:var(--gold-dark);border-color:var(--gold);"><i class="ti ti-license"></i> {{ $item->menuSet->name }} (+PKR {{ number_format($item->menuSet->getTotalPriceAttribute()) }})</span>
                                    @endif
                                </div>
                                @if(!empty($item->extras))
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-top:6px;">
                                        <i class="ti ti-plus"></i>
                                        {{ collect($item->extras)->map(fn($e) => $e['name'].' (+PKR '.number_format($e['price'] ?? 0).')')->implode(', ') }}
                                    </div>
                                @endif
                            </div>
                            <div style="text-align:right;">
                                <span class="bk-vstat" style="background:{{ $vBg }};color:{{ $vFg }};">
                                    <i class="ti {{ $vStat == 'accepted' ? 'ti-circle-check-filled' : ($vStat == 'declined' ? 'ti-circle-off' : 'ti-clock') }}"></i>
                                    {{ ucfirst($vStat) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Payment --}}
            <div class="bk-card">
                <div class="bk-card-head">
                    <i class="ti ti-wallet"></i>
                    <h5>Payment</h5>
                </div>
                <div class="bk-card-body">
                    <div class="bk-pay-row">
                        <span style="color:var(--text-muted);">Total</span>
                        <strong>PKR {{ number_format($total) }}</strong>
                    </div>
                    @if($booking->negotiated_price !== null && $booking->negotiated_price != $booking->total_price)
                        <div class="bk-pay-row" style="font-size:12px;">
                            <span style="color:var(--text-muted);">Original price</span>
                            <span style="color:var(--text-muted);text-decoration:line-through;">PKR {{ number_format($booking->total_price) }}</span>
                        </div>
                    @endif
                    @if($booking->price_negotiation_note)
                        <div style="font-size:12px;color:var(--text-muted);padding:6px 0;">
                            <i class="ti ti-note"></i> {{ $booking->price_negotiation_note }}
                        </div>
                    @endif
                    <div class="bk-pay-row">
                        <span style="color:var(--text-muted);">Paid</span>
                        <strong style="color:var(--green);">PKR {{ number_format($totalPaid) }}</strong>
                    </div>
                    <div class="bk-pay-row">
                        <span style="color:var(--text-muted);">Remaining</span>
                        <strong style="color:var(--gold-dark);">PKR {{ number_format($remaining) }}</strong>
                    </div>
                    <div class="bk-progress"><div style="width:{{ $paidPct }}%;"></div></div>
                    <div class="bk-progress-label">{{ $paidPct }}% paid</div>

                    @if($remaining > 0 && $booking->status != 'cancelled' && $booking->status != 'completed')
                        <a href="{{ route('customer.bookings.payment', $booking) }}" class="bk-btn bk-btn-solid" style="justify-content:center;width:100%;border:none;">
                            <i class="ti ti-credit-card"></i> Pay Now
                        </a>
                    @elseif($booking->status == 'cancelled')
                        <div style="text-align:center;padding:10px;background:#FDE8E8;border-radius:10px;font-size:13px;color:var(--red);font-weight:600;">
                            <i class="ti ti-circle-off"></i> Booking Cancelled
                        </div>
                    @else
                        <div style="text-align:center;padding:10px;background:#E6F7ED;border-radius:10px;font-size:13px;color:var(--green);font-weight:600;">
                            <i class="ti ti-circle-check-filled"></i> Fully Paid
                        </div>
                    @endif
                </div>
            </div>

            {{-- Messages --}}
            <div class="bk-card">
                <div class="bk-card-body">
                    <a href="{{ route('customer.messages.index', $booking) }}" style="display:flex;align-items:center;justify-content:space-between;color:var(--charcoal);text-decoration:none;padding:4px 0;">
                        <span style="font-weight:700;font-size:14px;"><i class="ti ti-message-2" style="color:var(--gold-dark);"></i> Messages</span>
                        <span style="background:var(--gold);color:var(--charcoal);border-radius:30px;font-size:11px;font-weight:700;padding:2px 10px;">
                            {{ $booking->messages_count ?? $booking->messages()->count() }}
                        </span>
                    </a>
                </div>
            </div>

            {{-- Cancellation --}}
            @if($refundInfo && in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed']))
                <div class="bk-card">
                    <div class="bk-card-head">
                        <i class="ti ti-circle-off"></i>
                        <h5>Cancellation</h5>
                    </div>
                    <div class="bk-card-body">
                        <p style="font-size:12.5px;color:var(--text-muted);margin-bottom:14px;">
                            <i class="ti ti-info-circle" style="color:var(--gold-dark);"></i>
                            @if($refundInfo['cancellation_fee'] > 0)
                                Cancellation fee: <strong style="color:var(--red);">PKR {{ number_format($refundInfo['cancellation_fee']) }}</strong>. Refund: <strong style="color:var(--green);">PKR {{ number_format($refundInfo['refund_amount']) }}</strong>.
                            @elseif($refundInfo['refund_amount'] > 0)
                                <strong style="color:var(--green);">Full refund available.</strong>
                            @else
                                No refund applicable.
                            @endif
                        </p>
                        <button class="bk-btn" style="justify-content:center;width:100%;background:#FDE8E8;color:var(--red);border:1px solid #F5C6C6;cursor:pointer;" onclick="cancelBooking({{ $booking->id }})">
                            <i class="ti ti-circle-off"></i> Cancel Booking
                        </button>
                    </div>
                </div>
            @endif

            {{-- Timeline --}}
            <div class="bk-card">
                <div class="bk-card-head">
                    <i class="ti ti-history"></i>
                    <h5>Timeline</h5>
                </div>
                <div class="bk-card-body">
                    <div class="bk-timeline">
                        <div class="bk-tl-item done">
                            <div class="bk-tl-dot"><i class="ti ti-send"></i></div>
                            <div>
                                <div class="bk-tl-title">Booking Requested</div>
                                <div class="bk-tl-date">{{ $booking->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @if(in_array($booking->status, ['confirmed', 'cancelled', 'completed']))
                            <div class="bk-tl-item done">
                                <div class="bk-tl-dot"><i class="ti ti-shield-check"></i></div>
                                <div>
                                    <div class="bk-tl-title">Confirmed</div>
                                    <div class="bk-tl-date">{{ $booking->updated_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="bk-tl-item {{ $booking->status == 'requested' ? 'active' : 'done' }}">
                                <div class="bk-tl-dot"><i class="ti ti-shield-check"></i></div>
                                <div>
                                    <div class="bk-tl-title">Awaiting Confirmation</div>
                                    <div class="bk-tl-date">Vendors are reviewing your request</div>
                                </div>
                            </div>
                        @endif
                        @if($booking->status == 'completed')
                            <div class="bk-tl-item done">
                                <div class="bk-tl-dot"><i class="ti ti-circle-check"></i></div>
                                <div>
                                    <div class="bk-tl-title">Event Completed</div>
                                    <div class="bk-tl-date">{{ $booking->updated_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @elseif($booking->status == 'cancelled')
                            <div class="bk-tl-item done">
                                <div class="bk-tl-dot" style="background:#FDE8E8;color:var(--red);border-color:#F5C6C6;"><i class="ti ti-circle-off"></i></div>
                                <div>
                                    <div class="bk-tl-title">Booking Cancelled</div>
                                    <div class="bk-tl-date">{{ $booking->updated_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelBooking(id) {
    if (!confirm('Are you sure you want to cancel this booking?')) return;
    fetch('/customer/bookings/' + id + '/cancel', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Booking cancelled', 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(data.message || 'Failed to cancel', 'error');
        }
    });
}
</script>
@endpush
