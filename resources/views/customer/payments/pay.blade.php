@extends('customer.layouts.master')

@section('title', 'Pay for Booking ' . $booking->reference)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:var(--charcoal);font-weight:700;">Payment for Booking {{ $booking->reference }}</h2>
        <a href="{{ route('customer.bookings.show', $booking) }}" style="font-size:13px;color:var(--gold-dark);">← Back to Booking</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:20px;">Payment Summary</h5>

                    <div style="background:var(--cream);border-radius:12px;padding:20px;margin-bottom:20px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--text-muted);font-size:13px;">Total Booking Amount</span>
                            <strong style="color:var(--charcoal);">PKR {{ number_format($summary['total']) }}</strong>
                        </div>
                        <hr style="border-color:var(--border);margin:12px 0;">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--text-muted);font-size:13px;">Advance Required (30%)</span>
                            <strong style="color:var(--gold-dark);">PKR {{ number_format($summary['advance_required']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--text-muted);font-size:13px;">Advance Paid</span>
                            <strong style="color:{{ $summary['is_advance_paid'] ? 'var(--green)' : 'var(--text-muted)' }};">
                                PKR {{ number_format($summary['advance_paid']) }}
                                @if($summary['is_advance_paid'])
                                    <i class="ti ti-circle-check-filled" style="color:var(--green);"></i>
                                @endif
                            </strong>
                        </div>
                        <hr style="border-color:var(--border);margin:12px 0;">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--text-muted);font-size:13px;">Remaining Balance</span>
                            <strong style="color:var(--charcoal);">PKR {{ number_format($summary['remaining']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span style="color:var(--text-muted);font-size:13px;">Status</span>
                            <span style="font-size:13px;font-weight:600;">
                                @if($summary['is_fully_paid'])
                                    <span style="color:var(--green);">Fully Paid</span>
                                @elseif($summary['is_advance_paid'])
                                    <span style="color:var(--gold-dark);">Advance Paid</span>
                                @else
                                    <span style="color:var(--red);">Pending</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    @if(!$summary['is_fully_paid'])
                        <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:16px;">Pay Now</h5>

                        @if(!$summary['is_advance_paid'])
                            <div style="background:#fff;border:2px solid var(--gold);border-radius:14px;padding:20px;margin-bottom:16px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong style="color:var(--charcoal);font-size:15px;">Pay Advance (30%)</strong>
                                        <p style="color:var(--text-muted);font-size:12px;margin:2px 0 0;">Secure your booking with an advance payment</p>
                                    </div>
                                    <strong style="font-size:20px;color:var(--gold-dark);">PKR {{ number_format($summary['advance_required']) }}</strong>
                                </div>

                                @if($stripeKey)
                                    <button onclick="processStripePayment('advance')" class="btn-gold" style="width:100%;padding:11px;font-size:14px;border:none;">
                                        <i class="ti ti-credit-card"></i> Pay with Card
                                    </button>
                                @else
                                    <p style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">Bank transfer to:</p>
                                    <div style="background:var(--cream);border-radius:8px;padding:12px;font-size:13px;margin-bottom:10px;">
                                        <strong>Bank:</strong> HBL Pakistan<br>
                                        <strong>Account:</strong> 1234-5678-9012-3456<br>
                                        <strong>Title:</strong> BeeG Events Pvt Ltd<br>
                                        <strong>IBAN:</strong> PK36HABB0012345678901
                                    </div>
                                    <form method="POST" action="{{ route('customer.payments.manual', $booking) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="advance">
                                        <input type="hidden" name="amount" value="{{ $summary['advance_required'] }}">
                                        <div class="mb-2">
                                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm" style="font-size:13px;" required>
                                            <small style="color:var(--text-muted);">Upload transfer receipt (JPG, PNG, PDF — max 5MB)</small>
                                        </div>
                                        <button type="submit" class="btn-outline-gold" style="width:100%;padding:11px;font-size:14px;">
                                            <i class="ti ti-building-bank"></i> Submit Advance Proof
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        @if($summary['is_advance_paid'] && !$summary['is_fully_paid'])
                            <div style="background:#fff;border:2px solid var(--border);border-radius:14px;padding:20px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong style="color:var(--charcoal);font-size:15px;">Pay Balance</strong>
                                        <p style="color:var(--text-muted);font-size:12px;margin:2px 0 0;">Complete payment before your event date</p>
                                    </div>
                                    <strong style="font-size:20px;color:var(--gold-dark);">PKR {{ number_format($summary['remaining']) }}</strong>
                                </div>

                                @if($stripeKey)
                                    <button onclick="processStripePayment('balance')" class="btn-gold" style="width:100%;padding:11px;font-size:14px;border:none;">
                                        <i class="ti ti-credit-card"></i> Pay with Card
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('customer.payments.manual', $booking) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="balance">
                                        <input type="hidden" name="amount" value="{{ $summary['remaining'] }}">
                                        <div class="mb-2">
                                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm" style="font-size:13px;" required>
                                            <small style="color:var(--text-muted);">Upload transfer receipt (JPG, PNG, PDF — max 5MB)</small>
                                        </div>
                                        <button type="submit" class="btn-outline-gold" style="width:100%;padding:11px;font-size:14px;">
                                            <i class="ti ti-building-bank"></i> Submit Balance Proof
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @else
                        <div style="text-align:center;padding:20px;background:var(--cream);border-radius:12px;">
                            <i class="ti ti-circle-check-filled" style="font-size:40px;color:var(--green);"></i>
                            <h5 style="color:var(--charcoal);margin-top:8px;">All Payments Completed</h5>
                            <p style="color:var(--text-muted);font-size:13px;">This booking is fully paid.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:16px;">Payment History</h5>
                    @php $payments = $booking->payments()->latest()->get(); @endphp
                    @forelse($payments as $payment)
                        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);">
                            <div style="width:36px;height:36px;border-radius:10px;background:{{ $payment->status === 'received' ? 'var(--light-honey)' : '#FDE8E8' }};display:flex;align-items:center;justify-content:center;">
                                <i class="ti ti-{{ $payment->status === 'received' ? 'circle-check-filled' : 'clock' }}" style="color:{{ $payment->status === 'received' ? 'var(--green)' : 'var(--red)' }};font-size:18px;"></i>
                            </div>
                            <div style="flex:1;">
                                <strong style="font-size:13px;color:var(--charcoal);text-transform:capitalize;">{{ $payment->type }} Payment</strong>
                                <p style="font-size:11px;color:var(--text-muted);margin:0;">{{ $payment->created_at->format('M d, Y h:i A') }} · {{ ucfirst($payment->method) }}</p>
                            </div>
                            <strong style="font-size:14px;color:var(--charcoal);">PKR {{ number_format($payment->amount) }}</strong>
                        </div>
                    @empty
                        <p style="color:var(--text-muted);font-size:13px;text-align:center;padding:20px 0;">No payments yet</p>
                    @endforelse
                </div>
            </div>

            <div class="card mt-3" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <h5 style="color:var(--charcoal);font-weight:700;margin-bottom:8px;">Cancellation Policy</h5>
                    <p style="font-size:12px;color:var(--text-muted);line-height:1.6;margin:0;">
                        Advance payments are non-refundable if cancelled within 7 days of the event. 
                        Full refund of advance available if cancelled 30+ days before the event.
                        Balance payment is due 3 days before the event.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stripeKey)
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        let processing = false;

        async function processStripePayment(type) {
            if (processing) return;
            processing = true;

            const btn = event.target;
            const origText = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-loader"></i> Processing...';
            btn.disabled = true;

            try {
                const intentRes = await fetch('{{ route("customer.payments.intent", $booking) }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({type})
                });
                const intentData = await intentRes.json();
                if (!intentData.success) { showToast(intentData.message, 'error'); return; }

                const {error, paymentIntent} = await stripe.confirmCardPayment(intentData.client_secret);
                if (error) { showToast(error.message, 'error'); return; }

                const confirmRes = await fetch('{{ route("customer.payments.confirm", $booking) }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({payment_intent_id: paymentIntent.id, type})
                });
                const confirmData = await confirmRes.json();
                if (confirmData.success) {
                    showToast('Payment successful!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(confirmData.message || 'Payment confirmation failed', 'error');
                }
            } catch (e) {
                showToast('Payment error: ' + e.message, 'error');
            } finally {
                btn.innerHTML = origText;
                btn.disabled = false;
                processing = false;
            }
        }
    </script>
@endif

@endsection
