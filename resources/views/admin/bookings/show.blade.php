@extends('admin.layouts.master')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size:20px;font-weight:600;">
        <i class="ti ti-receipt"></i> Booking #{{ $booking->id }}
    </h2>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-info-circle"></i> Details</h5>
                <div class="d-flex gap-2">
                    @if($booking->status == 'requested')
                        <button class="btn btn-gold btn-sm verify-booking" data-id="{{ $booking->id }}">Verify</button>
                    @elseif($booking->status == 'verified')
                        <button class="btn btn-gold btn-sm confirm-booking" data-id="{{ $booking->id }}">Confirm</button>
                        <button class="btn btn-ghost btn-sm cancel-booking" data-id="{{ $booking->id }}">Cancel</button>
                    @elseif($booking->status == 'confirmed')
                        <button class="btn btn-gold btn-sm complete-booking" data-id="{{ $booking->id }}">Complete</button>
                        <button class="btn btn-ghost btn-sm cancel-booking" data-id="{{ $booking->id }}">Cancel</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2"><strong>Customer:</strong> {{ $booking->customer->name ?? 'N/A' }}</div>
                    <div class="col-md-4 mb-2"><strong>Email:</strong> {{ $booking->customer->email ?? 'N/A' }}</div>
                    <div class="col-md-4 mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</div>
                    <div class="col-md-4 mb-2"><strong>Type:</strong> {{ ucfirst($booking->event_type) }}</div>
                    <div class="col-md-4 mb-2">
                        <strong>Status:</strong>
                        <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <div class="col-md-4 mb-2"><strong>Total:</strong> PKR {{ number_format($booking->total_price) }}</div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-package"></i> Items</h5>
            </div>
            <div class="card-body p-0">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->bookingItems as $item)
                            <tr>
                                <td>{{ str_replace('_', ' ', class_basename($item->itemable_type)) }}</td>
                                <td>{{ $item->vendorProfile->business_name ?? 'N/A' }}</td>
                                <td>PKR {{ number_format($item->price) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $item->vendor_status == 'accepted' ? 'confirmed' : ($item->vendor_status == 'declined' ? 'cancelled' : 'pending') }}">
                                        {{ ucfirst($item->vendor_status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-coin"></i> Payments</h5>
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
                                    <td><span class="status-badge status-confirmed">{{ ucfirst($payment->status) }}</span></td>
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

        <div class="admin-card mt-3">
            <div class="card-header">
                <h5><i class="ti ti-message-2"></i> Messages</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.messages.index', $booking) }}" class="btn btn-ghost w-100">
                    <i class="ti ti-message-2"></i> View Messages
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateBookingStatus(id, action) {
    const labels = { verify: 'Verified', confirm: 'Confirmed', complete: 'Completed', cancel: 'Cancelled' };
    const icons = { verify: 'success', confirm: 'success', complete: 'info', cancel: 'warning' };
    const urls = {
        verify: '/admin/bookings/' + id + '/verify',
        confirm: '/admin/bookings/' + id + '/confirm',
        complete: '/admin/bookings/' + id + '/complete',
        cancel: '/admin/bookings/' + id + '/cancel',
    };
    fetch(urls[action], {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(icons[action], 'Booking ' + labels[action], 'Booking #' + id + ' has been ' + labels[action].toLowerCase() + '.');
            setTimeout(() => location.reload(), 1200);
        }
    });
}

document.querySelector('.verify-booking')?.addEventListener('click', function() {
    updateBookingStatus(this.dataset.id, 'verify');
});
document.querySelector('.confirm-booking')?.addEventListener('click', function() {
    updateBookingStatus(this.dataset.id, 'confirm');
});
document.querySelector('.complete-booking')?.addEventListener('click', function() {
    updateBookingStatus(this.dataset.id, 'complete');
});
document.querySelector('.cancel-booking')?.addEventListener('click', function() {
    if (confirm('Cancel this booking? This will process refunds if applicable.')) updateBookingStatus(this.dataset.id, 'cancel');
});

document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('{{ route("admin.bookings.payment", $booking) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Payment Recorded', 'Payment has been recorded successfully.');
            setTimeout(() => location.reload(), 1200);
        }
    });
});
</script>
@endpush
