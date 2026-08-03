@extends('customer.layouts.master')

@section('title', 'Checkout')

@section('content')
@php
    $cartDate = collect($cart)->firstWhere('type', 'hall_unit')['date']
        ?? collect($cart)->pluck('date')->first()
        ?? '';
@endphp
<style>
    .co-hero {
        background: linear-gradient(135deg, var(--charcoal) 0%, #3D342D 100%);
        color: var(--white);
        padding: 40px 0 34px;
        margin-bottom: 36px;
        border-bottom: 3px solid var(--gold);
    }
    .co-hero h1 { font-size: 26px; font-weight: 800; display: flex; align-items: center; gap: 12px; margin: 0 0 6px; }
    .co-hero h1 i { width: 40px; height: 40px; background: var(--gold); color: var(--charcoal); border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; }
    .co-hero p { font-size: 13px; color: rgba(255,255,255,0.7); margin: 0; }
    .co-breadcrumb { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 10px; }
    .co-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
    .co-breadcrumb a:hover { color: var(--gold); }

    .co-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 34px rgba(43,38,32,0.07);
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 22px;
    }
    .co-card-head {
        padding: 18px 26px;
        border-bottom: 1px solid var(--border);
        background: var(--cream);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .co-card-head i { color: var(--gold-dark); font-size: 20px; }
    .co-card-head h5 { margin: 0; font-weight: 700; font-size: 15px; color: var(--charcoal); }
    .co-card-body { padding: 26px; }

    .co-label { font-size: 12px; font-weight: 600; color: var(--charcoal); margin-bottom: 6px; display: block; }
    .co-input {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        padding: 10px 14px;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .co-input:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(212,160,23,0.15);
    }
    textarea.co-input { resize: vertical; }

    .co-agree {
        border: 1px dashed var(--gold);
        background: var(--cream);
        border-radius: 10px;
        padding: 14px 16px;
    }
    .co-agree .form-check-label { font-size: 13px; color: var(--charcoal); }

    .co-submit {
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        color: var(--white);
        border: none;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.3px;
        padding: 13px 18px;
        border-radius: 10px;
        width: 100%;
        transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        box-shadow: 0 8px 20px rgba(212,160,23,0.28);
    }
    .co-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(212,160,23,0.4); filter: brightness(1.05); }
    .co-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    .co-item { display: flex; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
    .co-item:first-of-type { padding-top: 0; }
    .co-item:last-of-type { border-bottom: none; padding-bottom: 4px; }
    .co-item-icon {
        width: 42px; height: 42px; flex-shrink: 0;
        background: var(--light-honey, var(--cream));
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        color: var(--gold-dark); font-size: 20px;
        border: 1px solid var(--border);
    }
    .co-item-name { font-size: 13.5px; font-weight: 700; color: var(--charcoal); }
    .co-item-meta { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; }
    .co-item-price { font-size: 13.5px; font-weight: 800; color: var(--gold-dark); white-space: nowrap; }
    .co-meta-badge {
        display: inline-block;
        font-size: 10.5px; font-weight: 700;
        padding: 2px 9px; border-radius: 20px;
        background: var(--cream); color: var(--gold-dark);
        border: 1px solid var(--border);
        margin: 2px 4px 0 0;
    }
    .co-total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px; }
    .co-total-row.grand { border-top: 1.5px solid var(--charcoal); margin-top: 8px; padding-top: 12px; }
    .co-total-row.grand span:first-child { font-weight: 700; color: var(--charcoal); font-size: 14px; }
    .co-total-row.grand span:last-child { font-weight: 800; color: var(--gold-dark); font-size: 17px; }
    .co-note {
        display: flex; gap: 8px; align-items: flex-start;
        font-size: 11.5px; color: var(--text-muted);
        background: var(--cream); border: 1px solid var(--border); border-radius: 10px;
        padding: 10px 12px; margin-top: 14px;
    }
    .co-note i { color: var(--gold-dark); font-size: 15px; margin-top: 1px; }
    .co-alert { display: none; font-size: 13px; border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; }
    .co-alert.show { display: block; }
    .co-alert.error { background: #FDE8E8; color: #B3261E; border: 1px solid #F5C6C6; }
    .co-alert.success { background: #E6F7ED; color: #1B7F44; border: 1px solid #BCE3CD; }
</style>

<div class="co-hero">
    <div class="container">
        <nav class="co-breadcrumb">
            <a href="{{ route('customer.cart') }}"><i class="ti ti-arrow-left"></i> Back to Cart</a>
        </nav>
        <h1><i class="ti ti-receipt-2"></i> Checkout</h1>
        <p>Review your order and submit a booking request. Vendors confirm availability and pricing.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="co-card">
                <div class="co-card-head">
                    <i class="ti ti-calendar-event"></i>
                    <h5>Booking Details</h5>
                </div>
                <div class="co-card-body">
                    <div id="coAlert" class="co-alert error"></div>
                    <form id="checkoutForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="co-label" for="coDate">Event Date</label>
                                <input type="date" class="co-input" name="event_date" id="coDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $cartDate }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="co-label" for="coType">Event Type</label>
                                <select class="co-input" name="event_type" id="coType" required>
                                    <option value="wedding">Wedding</option>
                                    <option value="engagement">Engagement</option>
                                    <option value="corporate">Corporate</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="home">Home Event</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="co-label" for="coNotes">Special Notes (Optional)</label>
                                <textarea class="co-input" name="notes" id="coNotes" rows="3" placeholder="Any special requirements..."></textarea>
                            </div>
                        </div>

                        <div class="co-agree mt-4">
                            <div class="form-check" style="margin:0;">
                                <input class="form-check-input" type="checkbox" name="agreement_accepted" value="1" id="agreementAccepted" required>
                                <label class="form-check-label" for="agreementAccepted">
                                    I agree to the BeeG Events <strong style="color:var(--gold-dark);">booking terms, pricing, and cancellation policy</strong>.
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="co-submit mt-4" id="coSubmitBtn">
                            <i class="ti ti-send"></i> Place Booking Request
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="co-card">
                <div class="co-card-head">
                    <i class="ti ti-shopping-cart"></i>
                    <h5>Order Summary</h5>
                </div>
                <div class="co-card-body">
                    @php $grandTotal = 0; @endphp
                    @foreach($cart as $item)
                        @php
                            $subtotal = $item['price'] + ($item['extras_total'] ?? 0) + ($item['menu_set_price'] ?? 0);
                            $grandTotal += $subtotal;
                        @endphp
                        <div class="co-item">
                            <div class="co-item-icon">
                                <i class="ti {{ $item['type'] === 'hall_unit' ? 'ti-building-arch' : 'ti-briefcase' }}"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="co-item-name">{{ $item['name'] }}</div>
                                    <div class="co-item-price">PKR {{ number_format($subtotal) }}</div>
                                </div>
                                <div class="co-item-meta">
                                    @if(isset($item['time_slot']) && isset($item['date']))
                                        <span class="co-meta-badge">{{ ucfirst($item['time_slot']) }}</span>
                                        <span class="co-meta-badge">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</span>
                                    @endif
                                    @if(isset($item['guests']) && $item['guests'])
                                        <span class="co-meta-badge"><i class="ti ti-users"></i> {{ number_format($item['guests']) }} guests</span>
                                    @endif
                                    @if(isset($item['catering_mode']) && $item['catering_mode'])
                                        <span class="co-meta-badge">
                                            <i class="ti ti-cooking-pot"></i> {{ ['internal' => 'In-house catering', 'external' => 'Outside catering', 'none' => 'Self-arrange'][$item['catering_mode']] ?? ucfirst($item['catering_mode']) }}
                                        </span>
                                    @endif
                                    @if(isset($item['capacity']) && $item['capacity'])
                                        <span class="co-meta-badge"><i class="ti ti-users-group"></i> Cap {{ $item['capacity'] }}</span>
                                    @endif
                                </div>
                                @if(isset($item['menu_set_name']) && $item['menu_set_name'])
                                    <div class="co-item-meta" style="color:var(--gold-dark);margin-top:5px;">
                                        <i class="ti ti-license"></i> Menu set: {{ $item['menu_set_name'] }} (+PKR {{ number_format($item['menu_set_price'] ?? 0) }})
                                    </div>
                                @endif
                                @if(!empty($item['extras']))
                                    <div class="co-item-meta" style="margin-top:5px;">
                                        <i class="ti ti-plus"></i>
                                        {{ collect($item['extras'])->map(fn($e) => $e['name'].' (+PKR '.number_format($e['price']).')')->implode(', ') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <div class="co-total-row">
                            <span style="color:var(--text-muted);">Subtotal ({{ count($cart) }} item{{ count($cart) != 1 ? 's' : '' }})</span>
                            <strong>PKR {{ number_format($grandTotal) }}</strong>
                        </div>
                        <div class="co-total-row grand">
                            <span>Estimated Total</span>
                            <span>PKR {{ number_format($grandTotal) }}</span>
                        </div>
                    </div>

                    <div class="co-note">
                        <i class="ti ti-info-circle"></i>
                        <div>
                            Prices shown are estimates. Your final price is confirmed by each vendor after reviewing your request.
                            A holding fee may apply — slots are auto-released if unconfirmed within 24 hours.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('coAlert');
    const btn = document.getElementById('coSubmitBtn');
    alertBox.classList.remove('show', 'success', 'error');

    const formData = new FormData(this);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="vertical-align:-2px;margin-right:6px;"></span> Submitting...';

    fetch('{{ route("customer.bookings.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json().then(d => ({ ok: res.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            alertBox.className = 'co-alert success show';
            alertBox.innerHTML = '<i class="ti ti-circle-check"></i> Booking requested! Redirecting...';
            btn.innerHTML = '<i class="ti ti-check"></i> Booking Placed';
            setTimeout(() => { window.location.href = '/customer/bookings/' + data.booking_id; }, 900);
        } else {
            alertBox.className = 'co-alert error show';
            alertBox.innerHTML = '<i class="ti ti-alert-circle"></i> ' + (data.message || 'Could not place your booking. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-send"></i> Place Booking Request';
        }
    })
    .catch(() => {
        alertBox.className = 'co-alert error show';
        alertBox.innerHTML = '<i class="ti ti-alert-circle"></i> Something went wrong. Please try again.';
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-send"></i> Place Booking Request';
    });
});
</script>
@endpush
