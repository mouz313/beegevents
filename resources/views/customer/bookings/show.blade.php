@extends('layouts.app')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4" style="flex-wrap:wrap;gap:8px;">
        <div>
            <h2 style="color:var(--charcoal);font-weight:700;margin:0;">Booking #{{ $booking->id }}</h2>
            <span style="font-size:13px;color:var(--text-muted);">{{ ucfirst($booking->event_type) }} · {{ \Carbon\Carbon::parse($booking->event_date)->format('F d, Y') }}</span>
        </div>
        <div style="display:flex;gap:8px;">
            @if($booking->status != 'cancelled' && $booking->status != 'completed')
                <a href="{{ route('customer.bookings.payment', $booking) }}" style="background:var(--gold);color:var(--charcoal);padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                    <i class="ti ti-credit-card"></i> Pay
                </a>
            @endif
            <a href="{{ route('customer.messages.index', $booking) }}" style="background:var(--cream);color:var(--charcoal);padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);">
                <i class="ti ti-message-2"></i> Messages
            </a>
            <a href="{{ route('customer.bookings.index') }}" class="btn-outline-gold" style="padding:8px 16px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="border:1px solid var(--border);border-radius:14px;margin-bottom:20px;">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Event Date</span>
                            <p style="font-size:15px;font-weight:600;color:var(--charcoal);margin:2px 0 0;">{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Event Type</span>
                            <p style="font-size:15px;font-weight:600;color:var(--charcoal);margin:2px 0 0;">{{ ucfirst($booking->event_type) }}</p>
                        </div>
                        <div class="col-md-4">
                            <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Status</span>
                            <p style="margin:2px 0 0;">
                                <span style="display:inline-block;padding:2px 12px;border-radius:6px;font-size:12px;font-weight:600;background:{{ $booking->status == 'confirmed' ? '#E6F7ED' : ($booking->status == 'cancelled' ? '#FDE8E8' : ($booking->status == 'completed' ? '#E8EEF1' : 'var(--light-honey)')) }};color:{{ $booking->status == 'confirmed' ? 'var(--green)' : ($booking->status == 'cancelled' ? 'var(--red)' : ($booking->status == 'completed' ? 'var(--blue-grey)' : 'var(--gold-dark)')) }};">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($booking->notes)
                        <hr style="border-color:var(--border);margin:16px 0;">
                        <strong style="font-size:13px;color:var(--charcoal);">Notes</strong>
                        <p style="color:var(--text-muted);font-size:13px;margin:4px 0 0;">{{ $booking->notes }}</p>
                    @endif
                </div>
            </div>

            <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-header" style="background:none;border-bottom:1px solid var(--border);padding:16px 20px;">
                    <h5 style="margin:0;color:var(--charcoal);font-weight:700;">Items ({{ $booking->bookingItems->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($booking->bookingItems as $item)
                        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);">
                            <div style="width:40px;height:40px;background:var(--light-honey);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="ti ti-building-arch" style="color:var(--gold-dark);font-size:20px;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <strong style="font-size:14px;color:var(--charcoal);display:block;">{{ str_replace('_', ' ', class_basename($item->itemable_type)) }} #{{ $item->itemable_id }}</strong>
                                <span style="font-size:12px;color:var(--text-muted);">{{ $item->vendorProfile->business_name ?? 'N/A' }}</span>
                            </div>
                            <div style="text-align:right;">
                                <strong style="font-size:15px;color:var(--gold-dark);display:block;">PKR {{ number_format($item->price) }}</strong>
                                <span style="display:inline-block;padding:1px 8px;border-radius:4px;font-size:10px;font-weight:600;background:{{ $item->vendor_status == 'accepted' ? '#E6F7ED' : ($item->vendor_status == 'declined' ? '#FDE8E8' : 'var(--light-honey)') }};color:{{ $item->vendor_status == 'accepted' ? 'var(--green)' : ($item->vendor_status == 'declined' ? 'var(--red)' : 'var(--gold-dark)') }};">
                                    {{ ucfirst($item->vendor_status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:16px;">Payment</h5>
                    @php
                        $advancePaid = $booking->payments()->where('type','advance')->where('status','received')->sum('amount');
                        $totalPaid = $booking->payments()->whereIn('status',['received'])->sum('amount');
                        $remaining = max(0, $booking->total_price - $totalPaid);
                    @endphp
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span style="color:var(--text-muted);">Total</span>
                        <strong>PKR {{ number_format($booking->total_price) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span style="color:var(--text-muted);">Paid</span>
                        <strong style="color:var(--green);">PKR {{ number_format($totalPaid) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3" style="font-size:13px;">
                        <span style="color:var(--text-muted);">Remaining</span>
                        <strong style="color:var(--gold-dark);">PKR {{ number_format($remaining) }}</strong>
                    </div>
                    @if($remaining > 0 && $booking->status != 'cancelled' && $booking->status != 'completed')
                        <a href="{{ route('customer.bookings.payment', $booking) }}" style="display:block;background:var(--gold);color:var(--charcoal);text-align:center;padding:10px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;">
                            <i class="ti ti-credit-card"></i> Pay Now
                        </a>
                    @elseif($booking->status == 'cancelled')
                        <div style="text-align:center;padding:8px;background:#FDE8E8;border-radius:8px;font-size:13px;color:var(--red);font-weight:600;">
                            <i class="ti ti-circle-off"></i> Cancelled
                        </div>
                    @else
                        <div style="text-align:center;padding:8px;background:var(--cream);border-radius:8px;font-size:13px;color:var(--green);font-weight:600;">
                            <i class="ti ti-circle-check-filled"></i> Fully Paid
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4" style="text-align:center;">
                    <a href="{{ route('customer.messages.index', $booking) }}" style="display:inline-flex;align-items:center;gap:8px;color:var(--gold-dark);text-decoration:none;font-weight:600;font-size:14px;padding:8px 16px;border-radius:8px;transition:all 0.15s;" onmouseover="this.style.background='var(--light-honey)'" onmouseout="this.style.background='none'">
                        <i class="ti ti-message-2" style="font-size:20px;"></i>
                        View Messages ({{ $booking->messages_count ?? $booking->messages()->count() }})
                    </a>
                </div>
            </div>

            @if($refundInfo && in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed']))
                <div class="card mb-3" style="border:1px solid var(--border);border-radius:14px;">
                    <div class="card-body p-4">
                        <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:12px;">Cancellation</h5>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">
                            <i class="ti ti-info-circle"></i>
                            @if($refundInfo['cancellation_fee'] > 0)
                                Cancellation fee: PKR {{ number_format($refundInfo['cancellation_fee']) }}. Refund: PKR {{ number_format($refundInfo['refund_amount']) }}.
                            @elseif($refundInfo['refund_amount'] > 0)
                                Full refund available.
                            @else
                                No refund applicable.
                            @endif
                        </p>
                        <button class="btn-gold" style="background:#FDE8E8;color:var(--red);width:100%;border:none;padding:10px;" onclick="cancelBooking({{ $booking->id }})">
                            <i class="ti ti-circle-off"></i> Cancel Booking
                        </button>
                    </div>
                </div>
            @endif

            <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:16px;">Timeline</h5>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <li style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px;color:var(--text-muted);">
                            <i class="ti ti-circle-check-filled" style="color:var(--green);font-size:16px;"></i>
                            Requested — {{ $booking->created_at->format('M d, Y') }}
                        </li>
                        @if($booking->status != 'requested')
                            <li style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px;color:var(--text-muted);">
                                <i class="ti ti-circle-check-filled" style="color:var(--green);font-size:16px;"></i>
                                {{ ucfirst($booking->status) }} — {{ $booking->updated_at->format('M d, Y') }}
                            </li>
                        @endif
                    </ul>
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
