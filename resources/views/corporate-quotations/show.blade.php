@extends('layouts.app')

@section('title', 'Quotation '.$quotation->quote_no)

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti ti-file-invoice"></i> Corporate Quotation</div>
                <h1>Your <span>Event Quotation</span></h1>
                <p>Review the quotation prepared for {{ $quotation->lead?->company_name ?? 'your event' }}.</p>
            </div>
        </div>
    </div>
</div>

<div class="container detail-main">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('success'))
                <div class="alert alert-success" style="background:#E6F7ED;border:none;color:var(--green);font-size:13px;border-radius:8px;">{{ session('success') }}</div>
            @endif

            <div class="vendor-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                    <div>
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Quotation</div>
                        <h4 style="font-weight:700;margin:4px 0 0;">{{ $quotation->quote_no }}</h4>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">{{ $quotation->lead?->company_name }} · {{ $quotation->lead?->contact_person }}</div>
                    </div>
                    <div style="text-align:right;">
                        <span class="status-badge status-{{ $quotation->status == 'accepted' ? 'confirmed' : ($quotation->status == 'declined' ? 'cancelled' : ($quotation->status == 'sent' ? 'pending' : 'requested')) }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Event Date</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $quotation->event_date?->format('M d, Y') ?? 'To be confirmed' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Venue</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $quotation->venue ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Seating Capacity</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $quotation->seating_capacity ? number_format($quotation->seating_capacity).' guests' : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Client Budget</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $quotation->budget ? 'PKR '.number_format($quotation->budget) : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Total Quoted Amount</div>
                            <div style="font-size:16px;font-weight:700;color:var(--gold-dark);margin-top:4px;">PKR {{ number_format($quotation->amount ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Valid Until</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $quotation->valid_until?->format('M d, Y') ?? '—' }}</div>
                        </div>
                    </div>
                    @if($quotation->inclusions)
                        <div class="col-12">
                            <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">What's Included</div>
                                <div style="font-size:13px;margin-top:6px;white-space:pre-line;line-height:1.7;">{{ $quotation->inclusions }}</div>
                            </div>
                        </div>
                    @endif
                    @if($quotation->notes)
                        <div class="col-12">
                            <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Notes</div>
                                <div style="font-size:13px;margin-top:6px;white-space:pre-line;line-height:1.7;">{{ $quotation->notes }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($quotation->status === 'sent')
                    <div style="margin-top:24px;text-align:center;">
                        <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Does this quotation work for you?</p>
                        <div style="display:flex;gap:10px;justify-content:center;">
                            <button class="btn-gold respond-btn" data-response="accepted" style="border:none;padding:12px 28px;font-size:14px;">Accept Quotation</button>
                            <button class="btn-outline-gold respond-btn" data-response="declined" style="padding:12px 28px;font-size:14px;">Decline</button>
                        </div>
                    </div>
                @elseif($quotation->status === 'accepted')
                    <div style="margin-top:24px;text-align:center;background:#E6F7ED;border-radius:8px;padding:16px;">
                        <i class="ti ti-circle-check" style="color:var(--green);font-size:20px;"></i>
                        <strong style="color:var(--green);margin-left:6px;">Quotation accepted — our team will be in touch to finalise the details.</strong>
                    </div>
                @elseif($quotation->status === 'declined')
                    <div style="margin-top:24px;text-align:center;background:#FDE8E8;border-radius:8px;padding:16px;">
                        <i class="ti ti-circle-off" style="color:var(--red);font-size:20px;"></i>
                        <strong style="color:var(--red);margin-left:6px;">This quotation was declined.</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.respond-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Submit your response?')) return;
        const response = this.dataset.response;
        fetch('{{ route("corporate.quotations.respond", $quotation->token) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ response })
        })
        .then(res => res.json())
        .then(data => { if (data.success) window.location.reload(); });
    });
});
</script>
@endpush
