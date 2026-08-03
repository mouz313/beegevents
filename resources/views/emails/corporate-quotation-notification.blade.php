<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:-apple-system,sans-serif;background:#FBF6EC;padding:24px;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;border:1px solid #E8E2D5;">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:48px;height:48px;background:#D4A017;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#2B2620;">B</div>
        <h2 style="color:#2B2620;margin:12px 0 4px;">BeeG Events</h2>
        <p style="color:#6B6660;font-size:13px;margin:0;">Your corporate event quotation is ready</p>
    </div>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <p style="color:#2B2620;font-size:14px;line-height:1.6;">Hi {{ $quotation->lead?->contact_person ?? 'there' }},</p>

    <p style="color:#6B6660;font-size:13px;line-height:1.6;">
        Thank you for your inquiry from {{ $quotation->lead?->company_name }}. We have prepared a quotation for your event —
        please review the details below and let us know if you would like to accept it.
    </p>

    <div style="background:#FBF6EC;border-radius:10px;padding:16px;margin:16px 0;">
        <table style="width:100%;font-size:13px;color:#2B2620;">
            <tr><td style="padding:4px 0;color:#6B6660;width:140px;">Quotation #</td><td style="padding:4px 0;"><strong>{{ $quotation->quote_no }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Event Date</td><td style="padding:4px 0;"><strong>{{ $quotation->event_date?->format('M d, Y') ?? 'To be confirmed' }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Venue</td><td style="padding:4px 0;"><strong>{{ $quotation->venue ?? '—' }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Seating Capacity</td><td style="padding:4px 0;"><strong>{{ $quotation->seating_capacity ? number_format($quotation->seating_capacity) .' guests' : '—' }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Budget</td><td style="padding:4px 0;"><strong>{{ $quotation->budget ? 'PKR '.number_format($quotation->budget) : '—' }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Total Quote</td><td style="padding:4px 0;"><strong>PKR {{ number_format($quotation->amount) }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Valid Until</td><td style="padding:4px 0;"><strong>{{ $quotation->valid_until?->format('M d, Y') ?? '—' }}</strong></td></tr>
        </table>
        @if($quotation->inclusions)
            <div style="margin-top:12px;font-size:12px;color:#6B6660;">
                <div style="font-weight:600;margin-bottom:4px;">What's included:</div>
                <div style="white-space:pre-line;line-height:1.6;">{{ $quotation->inclusions }}</div>
            </div>
        @endif
    </div>

    <div style="text-align:center;margin:24px 0;">
        <a href="{{ route('corporate.quotations.show', $quotation->token) }}" style="display:inline-block;background:#D4A017;color:#2B2620;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;">
            View &amp; Respond to Quotation
        </a>
    </div>

    <p style="font-size:12px;color:#6B6660;text-align:center;margin:0;">
        If the button does not work, copy this link into your browser: <br>
        <a href="{{ route('corporate.quotations.show', $quotation->token) }}" style="color:#D4A017;">{{ route('corporate.quotations.show', $quotation->token) }}</a>
    </p>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <p style="font-size:12px;color:#6B6660;text-align:center;margin:0;">
        Sent via BeeG Events — Pakistan's trusted event planning platform
    </p>
</div>
</body>
</html>
