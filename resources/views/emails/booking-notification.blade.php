<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:-apple-system,sans-serif;background:#FBF6EC;padding:24px;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;border:1px solid #E8E2D5;">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:48px;height:48px;background:#D4A017;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#2B2620;">B</div>
        <h2 style="color:#2B2620;margin:12px 0 4px;">BeeG Events</h2>
        <p style="color:#6B6660;font-size:13px;margin:0;">{{ $subject }}</p>
    </div>

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <p style="color:#2B2620;font-size:14px;line-height:1.6;">{{ $greeting }}</p>

    <div style="background:#FBF6EC;border-radius:10px;padding:16px;margin:16px 0;">
        <table style="width:100%;font-size:13px;color:#2B2620;">
            <tr><td style="padding:4px 0;color:#6B6660;width:120px;">Booking #</td><td style="padding:4px 0;"><strong>{{ $booking->reference }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Status</td><td style="padding:4px 0;"><strong>{{ ucfirst($booking->status) }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Event Date</td><td style="padding:4px 0;"><strong>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Event Time</td><td style="padding:4px 0;"><strong>{{ $booking->time_slot ? ucfirst($booking->time_slot) : 'N/A' }}</strong></td></tr>
            <tr><td style="padding:4px 0;color:#6B6660;">Total</td><td style="padding:4px 0;"><strong>PKR {{ number_format($booking->total_price) }}</strong></td></tr>
        </table>
        @if($booking->bookingItems->count() > 0)
            <div style="margin-top:12px;font-size:12px;color:#6B6660;">
                <div style="font-weight:600;margin-bottom:4px;">Items:</div>
                @foreach($booking->bookingItems as $item)
                    <div style="padding:2px 0;">
                        {{ $item->itemable?->unit_name ?? $item->itemable?->title ?? ('#'.$item->itemable_id) }}
                        @if($item->time_slot) — {{ ucfirst($item->time_slot) }} slot @endif
                        @if($item->menuSet) · Menu: {{ $item->menuSet->name }} @endif
                        @if(!empty($item->extras)) · {{ count($item->extras) }} extra(s) @endif
                        — <strong>PKR {{ number_format($item->price) }}</strong>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <p style="color:#6B6660;font-size:13px;">{{ $body }}</p>

    @if($actionUrl)
    <div style="text-align:center;margin:24px 0;">
        <a href="{{ $actionUrl }}" style="display:inline-block;background:#D4A017;color:#2B2620;padding:10px 24px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
            {{ $actionText }}
        </a>
    </div>
    @endif

    <hr style="border:none;border-top:1px solid #E8E2D5;margin:16px 0;">

    <p style="font-size:12px;color:#6B6660;text-align:center;margin:0;">
        Sent via BeeG Events — Pakistan's trusted event planning platform
    </p>
</div>
</body>
</html>
