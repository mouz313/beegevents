@extends('admin.layouts.master')

@section('title', 'Booking #' . $booking->id)

@section('content')
@php
    $stepOrder = ['requested', 'verified', 'confirmed', 'completed'];
    $currentIndex = array_search($booking->status, $stepOrder);
    if ($currentIndex === false) {
        $currentIndex = 1;
    }
    $paidAmount = $booking->payments->whereIn('status', ['received'])->sum('amount');
    $agreedPrice = $booking->price();
    $remainingAmount = max(0, $agreedPrice - $paidAmount);
    $packageTitle = str_starts_with($booking->notes ?? '', 'Package: ') ? substr($booking->notes, 9) : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4" style="flex-wrap:wrap;gap:8px;">
    <div>
        <h2 style="font-size:20px;font-weight:600;margin:0;">
            <i class="ti ti-receipt"></i> Booking #{{ $booking->id }}
        </h2>
        <span style="font-size:13px;color:var(--text-muted);">{{ $booking->customer->name ?? 'N/A' }} · {{ $booking->customer->email ?? '' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.download', $booking) }}" class="btn btn-ghost" target="_blank">
            <i class="ti ti-download"></i> Download PDF
        </a>
        <a href="{{ route('admin.bookings.invoice', $booking) }}" class="btn btn-ghost" target="_blank">
            <i class="ti ti-file-invoice"></i> Invoice
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="admin-card mb-3">
    <div class="card-body">
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:16px;">
            @foreach($stepOrder as $i => $step)
                <div style="display:flex;align-items:center;gap:6px;">
                    <div style="width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;background:{{ $i < $currentIndex ? 'var(--green)' : ($i == $currentIndex ? 'var(--gold)' : '#eee') }};color:{{ $i < $currentIndex || $i == $currentIndex ? '#fff' : '#999' }};">
                        @if($i < $currentIndex)<i class="ti ti-check"></i>@else{{ $i + 1 }}@endif
                    </div>
                    <span style="font-size:13px;font-weight:{{ $i == $currentIndex ? '700' : '500' }};color:{{ $i == $currentIndex ? 'var(--charcoal)' : 'var(--text-muted)' }};text-transform:capitalize;">{{ $step }}</span>
                </div>
                @if(! $loop->last)
                    <div style="flex:1;min-width:18px;height:2px;background:{{ $i < $currentIndex ? 'var(--green)' : '#e3ddd2' }};"></div>
                @endif
            @endforeach
        </div>

        @if($booking->status == 'cancelled')
            <span class="status-badge status-cancelled" style="margin-right:8px;">Cancelled</span>
        @elseif($booking->status == 'discussing')
            <span class="status-badge status-requested" style="margin-right:8px;">Discussing</span>
        @else
            <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
        @endif

        <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
            @if($booking->status == 'requested')
                <button class="btn btn-gold btn-sm booking-status" data-status="verified">Verify</button>
                <button class="btn btn-ghost btn-sm booking-status" data-status="discussing">Mark Discussing</button>
            @elseif($booking->status == 'discussing' || $booking->status == 'verified')
                <button class="btn btn-gold btn-sm booking-status" data-status="confirmed">Confirm</button>
                @if($booking->status == 'verified')
                    <button class="btn btn-ghost btn-sm booking-status" data-status="discussing">Mark Discussing</button>
                @endif
            @elseif($booking->status == 'confirmed')
                <button class="btn btn-gold btn-sm booking-status" data-status="completed">Complete</button>
            @endif
            @if(! in_array($booking->status, ['completed', 'cancelled']))
                <button class="btn btn-ghost btn-sm booking-status" data-status="cancelled" style="color:var(--red);border-color:var(--red);">Cancel</button>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-info-circle"></i> Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2"><strong>Customer:</strong> {{ $booking->customer->name ?? 'N/A' }}</div>
                    <div class="col-md-4 mb-2"><strong>Email:</strong> {{ $booking->customer->email ?? 'N/A' }}</div>
                    <div class="col-md-4 mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</div>
                    <div class="col-md-4 mb-2"><strong>Type:</strong> {{ ucfirst($booking->event_type) }}</div>
                    <div class="col-md-4 mb-2"><strong>Requested:</strong> {{ $booking->created_at->format('M d, Y') }}</div>
                    <div class="col-md-4 mb-2">
                        <strong>Total:</strong> PKR {{ number_format($agreedPrice) }}
                        @if($booking->negotiated_price !== null)
                            <span class="status-badge status-verified" style="font-size:10px;">Agreed</span>
                        @endif
                    </div>
                </div>

                @if($booking->booking_type == 'package')
                    <div class="row mt-1" style="border-top:1px dashed var(--border);padding-top:10px;">
                        <div class="col-md-8">
                            <span class="status-badge status-pending">Package</span>
                            <strong style="margin-left:6px;">{{ $packageTitle ?? $booking->notes ?? 'Package booking' }}</strong>
                        </div>
                        <div class="col-md-4" style="text-align:right;">
                            <form id="changePackageForm" class="d-inline-flex gap-2" style="align-items:center;">
                                @csrf
                                <select class="form-control-admin" name="package_id" style="padding:5px 8px;font-size:12px;max-width:170px;">
                                    @foreach($packages as $pkg)
                                        <option value="{{ $pkg->id }}" {{ $packageTitle && $packageTitle == $pkg->title ? 'selected' : '' }}>{{ $pkg->title }} — PKR {{ number_format($pkg->total_price) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-ghost btn-sm">Change Package</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($booking->notes)
                    <div style="border-top:1px dashed var(--border);margin-top:10px;padding-top:10px;">
                        <span style="font-size:12px;color:var(--text-muted);">Notes:</span>
                        <p style="margin:2px 0 0;font-size:13px;">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-package"></i> Items ({{ $booking->bookingItems->count() }})</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-ghost btn-sm" id="toggleAddItem"><i class="ti ti-plus"></i> Add Item</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="addItemForm" style="display:none;padding:14px 18px;border-bottom:1px solid var(--border);background:var(--cream);">
                    <form id="addItemFormInner" class="d-flex gap-2" style="align-items:center;">
                        @csrf
                        <select class="form-control-admin" name="type" id="addItemType" style="max-width:130px;">
                            <option value="hall_unit">Hall Unit</option>
                            <option value="service_listing">Service</option>
                        </select>
                        <select class="form-control-admin" name="itemable_id" id="addItemId" style="flex:1;min-width:180px;">
                            <optgroup label="Hall Units">
                                @foreach($hallUnits as $unit)
                                    <option value="{{ $unit->id }}" data-type="hall_unit">{{ $unit->unit_name }} — {{ $unit->hall?->name ?? '' }} (PKR {{ number_format($unit->base_price) }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Services">
                                @foreach($listings as $listing)
                                    <option value="{{ $listing->id }}" data-type="service_listing">{{ $listing->title }} — {{ $listing->vendorProfile->business_name ?? '' }} (PKR {{ number_format($listing->price) }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <button type="submit" class="btn btn-gold btn-sm">Add</button>
                    </form>
                </div>

                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->bookingItems as $item)
                            <tr>
                                <td>{{ str_replace('_', ' ', class_basename($item->itemable_type)) }} #{{ $item->itemable_id }}</td>
                                <td>{{ $item->vendorProfile->business_name ?? 'N/A' }}</td>
                                <td>PKR {{ number_format($item->price) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $item->vendor_status == 'accepted' ? 'confirmed' : ($item->vendor_status == 'declined' ? 'cancelled' : 'pending') }}">
                                        {{ ucfirst($item->vendor_status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-ghost btn-sm remove-item" data-id="{{ $item->id }}" title="Remove item" style="color:var(--red);padding:2px 8px;font-size:12px;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="padding:16px;color:var(--text-muted);">
                                    No items attached to this booking.
                                    @if($booking->booking_type == 'package')
                                        This is a package booking — its services come from the package definition.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card mt-3">
            <div class="card-header">
                <h5><i class="ti ti-message-2"></i> Chat</h5>
            </div>
            <div class="card-body">
                @include('partials.message-thread', ['booking' => $booking, 'route' => route('admin.messages.store', $booking), 'endpoint' => route('admin.messages.store', $booking)])
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-coin"></i> Pricing</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Original total</span>
                    <strong>PKR {{ number_format($booking->total_price) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Agreed price</span>
                    <strong style="color:{{ $booking->negotiated_price !== null ? 'var(--gold-dark)' : 'inherit' }};">PKR {{ number_format($agreedPrice) }}</strong>
                </div>
                @if($booking->price_offer_status == 'pending')
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span style="color:var(--gold-dark);">Pending offer</span>
                        <strong style="color:var(--gold-dark);">PKR {{ number_format($booking->price_offer) }}</strong>
                    </div>
                @elseif($booking->price_offer_status == 'declined')
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span style="color:var(--red);">Offer declined</span>
                        <strong style="color:var(--red);">PKR {{ number_format($booking->price_offer) }}</strong>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Commission</span>
                    <strong>PKR {{ number_format($booking->commission_amount) }}</strong>
                </div>
                <hr style="border-color:var(--border);margin:12px 0;">
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Paid</span>
                    <strong style="color:var(--green);">PKR {{ number_format($paidAmount) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Remaining</span>
                    <strong style="color:var(--gold-dark);">PKR {{ number_format($remainingAmount) }}</strong>
                </div>

                @if($booking->price_negotiation_note)
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">
                        <i class="ti ti-note"></i> {{ $booking->price_negotiation_note }}
                    </div>
                @endif

                <button class="btn btn-ghost w-100" id="togglePriceForm" style="margin-bottom:10px;">
                    <i class="ti ti-edit"></i> {{ $booking->negotiated_price !== null ? 'Edit Agreed Price' : 'Set Agreed Price' }}
                </button>
                <form id="priceForm" style="display:none;margin-bottom:14px;" class="d-grid gap-2">
                    @csrf
                    <input type="number" step="0.01" min="0" class="form-control-admin w-100" name="negotiated_price" placeholder="Agreed price (leave empty to reset)" value="{{ $booking->negotiated_price }}">
                    <input type="text" class="form-control-admin w-100" name="price_negotiation_note" placeholder="Negotiation note" value="{{ $booking->price_negotiation_note }}">
                    <button type="submit" class="btn btn-gold w-100">Save Agreed Price</button>
                </form>

                <button class="btn btn-ghost w-100" id="toggleOfferForm" style="margin-bottom:10px;">
                    <i class="ti ti-send"></i> Send Price Offer to Customer
                </button>
                <form id="offerForm" style="display:none;" class="d-grid gap-2">
                    @csrf
                    <input type="number" step="0.01" min="0" class="form-control-admin w-100" name="price_offer" placeholder="Offer amount" required>
                    <input type="text" class="form-control-admin w-100" name="price_offer_note" placeholder="Offer note (optional)">
                    <button type="submit" class="btn btn-gold w-100">Send Offer</button>
                </form>
            </div>
        </div>

        <div class="admin-card mt-3">
            <div class="card-header">
                <h5><i class="ti ti-wallet"></i> Payments</h5>
            </div>
            <div class="card-body">
                @if($booking->payments->count() > 0)
                    <table class="table-admin w-100">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                                <tr>
                                    <td>{{ ucfirst($payment->type) }}</td>
                                    <td>PKR {{ number_format($payment->amount) }}</td>
                                    <td>
                                        @if($payment->proof_path)
                                            <a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank" style="color:var(--gold-dark);font-size:12px;">
                                                <i class="ti ti-file"></i> Proof
                                            </a>
                                        @else
                                            <span style="color:var(--text-muted);font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'pending')
                                            <button class="btn btn-gold btn-sm verify-payment" data-id="{{ $payment->id }}" style="font-size:11px;">Verify</button>
                                        @else
                                            <span class="status-badge status-confirmed">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                @else
                    <p class="text-muted" style="font-size:13px;">No payments yet.</p>
                    <hr>
                @endif
                <form id="paymentForm">
                    @csrf
                    <h6 style="font-size:13px;font-weight:600;margin-bottom:12px;">Record Payment</h6>
                    <div class="mb-2">
                        <select class="form-control-admin w-100" name="type" required>
                            <option value="advance">Advance</option>
                            <option value="balance">Balance</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <input type="number" step="0.01" class="form-control-admin w-100" name="amount" placeholder="Amount" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control-admin w-100" name="method" placeholder="Method (Cash, Bank)" required>
                    </div>
                    <button type="submit" class="btn btn-gold w-100">Record Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function postJson(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data || {})
    }).then(res => res.json());
}

function deleteRequest(url) {
    return fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(res => res.json());
}

function handleResult(data, successTitle, successMsg) {
    if (data.success) {
        showToast('success', successTitle, successMsg);
        setTimeout(() => location.reload(), 1200);
    } else {
        showToast('error', 'Error', data.message || 'Action failed.');
    }
}

document.querySelectorAll('.booking-status').forEach(btn => {
    btn.addEventListener('click', function () {
        const status = this.dataset.status;
        if (status === 'cancelled' && !confirm('Cancel this booking? This will process refunds if applicable.')) return;
        postJson('/admin/bookings/{{ $booking->id }}/status', { status: status })
            .then(data => handleResult(data, 'Status Updated', 'Booking #{{ $booking->id }} status updated.'));
    });
});

document.getElementById('toggleAddItem')?.addEventListener('click', function () {
    const f = document.getElementById('addItemForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('addItemFormInner')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    postJson('/admin/bookings/{{ $booking->id }}/items', Object.fromEntries(fd))
        .then(data => handleResult(data, 'Item Added', 'Item added to booking.'));
});

document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!confirm('Remove this item from the booking?')) return;
        deleteRequest('/admin/bookings/{{ $booking->id }}/items/' + this.dataset.id)
            .then(data => handleResult(data, 'Item Removed', 'Item removed from booking.'));
    });
});

document.getElementById('changePackageForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    if (!confirm('Change package? This will replace the items and price.')) return;
    postJson('/admin/bookings/{{ $booking->id }}/package', Object.fromEntries(fd))
        .then(data => handleResult(data, 'Package Changed', 'Booking package updated.'));
});

document.getElementById('togglePriceForm')?.addEventListener('click', function () {
    const f = document.getElementById('priceForm');
    f.style.display = f.style.display === 'none' ? 'grid' : 'none';
});

document.getElementById('priceForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    postJson('/admin/bookings/{{ $booking->id }}/price', Object.fromEntries(fd))
        .then(data => handleResult(data, 'Price Updated', 'Agreed price saved.'));
});

document.getElementById('toggleOfferForm')?.addEventListener('click', function () {
    const f = document.getElementById('offerForm');
    f.style.display = f.style.display === 'none' ? 'grid' : 'none';
});

document.getElementById('offerForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    postJson('/admin/bookings/{{ $booking->id }}/price-offer', Object.fromEntries(fd))
        .then(data => handleResult(data, 'Offer Sent', 'Price offer sent to customer.'));
});

document.getElementById('paymentForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('{{ route("admin.bookings.payment", $booking) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: fd
    })
    .then(res => res.json())
    .then(data => handleResult(data, 'Payment Recorded', 'Payment has been recorded successfully.'));
});

document.querySelectorAll('.verify-payment').forEach(btn => {
    btn.addEventListener('click', function () {
        fetch('/admin/bookings/payments/' + this.dataset.id + '/verify', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => handleResult(data, 'Payment Verified', 'Payment has been marked as received.'));
    });
});
</script>
@endpush
