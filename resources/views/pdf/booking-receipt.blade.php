@php
    $paidAmount = $booking->payments->whereIn('status', ['received'])->sum('amount');
    $agreedPrice = $booking->price();
    $originalPrice = $booking->total_price;
    $remaining = max(0, $agreedPrice - $paidAmount);
    $totalGuests = $booking->bookingItems->pluck('guests')->filter()->max();
    $cateringModes = $booking->bookingItems->pluck('catering_mode')->filter()->unique()->values();
    $cateringLabels = [
        'internal' => 'In-house catering',
        'external' => 'Outside catering',
        'none' => 'Self-arrange',
    ];
    $statusLabel = ucfirst($booking->status);
    $statusClass = in_array($booking->status, ['cancelled', 'completed', 'confirmed']) ? $booking->status : '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking {{ $booking->reference }} Receipt</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .top { border-bottom: 2.5px solid #c9a227; padding-bottom: 7px; margin-bottom: 10px; }
        .brand { font-size: 16px; font-weight: bold; color: #1f2937; }
        .brand-sub { font-size: 8px; color: #999; letter-spacing: 1px; text-transform: uppercase; }
        .doc-type { text-align: right; font-size: 13px; font-weight: bold; color: #c9a227; letter-spacing: 2px; text-transform: uppercase; }
        .title-row { margin-bottom: 8px; }
        .title-row h1 { font-size: 13px; margin: 0; color: #1f2937; display: inline-block; }
        .status-box {
            float: right; font-size: 8.5px; font-weight: bold; text-transform: uppercase;
            padding: 2px 9px; border-radius: 3px; background: #c9a227; color: #fff;
        }
        .status-box.cancelled { background: #dc3545; }
        .status-box.completed, .status-box.confirmed { background: #198754; }
        table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 2px 0; font-size: 9.5px; }
        .meta-table td.label { color: #888; width: 16%; }
        .meta-table .col td { padding-top: 3px; }
        .meta-table .col td.label { width: 30%; }
        .section-title {
            font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px;
            color: #1f2937; background: #f5f5f5; border-left: 3px solid #c9a227;
            padding: 4px 8px; margin: 10px 0 6px;
        }
        .items-table th {
            background: #1f2937; color: #fff; font-size: 8.5px; text-transform: uppercase;
            letter-spacing: 0.4px; text-align: left; padding: 4px 7px; border-bottom: 1.5px solid #c9a227;
        }
        .items-table td { padding: 4px 7px; border-bottom: 1px solid #eee; font-size: 9px; }
        .items-table tr:nth-child(even) td { background: #fafaf7; }
        .items-table .num { text-align: right; }
        .sub { font-size: 8.5px; color: #666; }
        .sub b { color: #1f2937; }
        .pricing-table { width: 45%; margin-left: 55%; }
        .pricing-table td { padding: 2px 0; font-size: 9.5px; }
        .pricing-table td.label { color: #555; }
        .pricing-table td.amount { text-align: right; font-weight: bold; }
        .pricing-table .grand td { font-size: 11px; font-weight: bold; border-top: 1.5px solid #333; color: #1f2937; }
        .pricing-table .grand td.amount { color: #c9a227; }
        .payments-table th {
            background: #1f2937; color: #fff; font-size: 8.5px; text-transform: uppercase;
            text-align: left; padding: 4px 7px; border-bottom: 1.5px solid #c9a227;
        }
        .payments-table td { padding: 4px 7px; border-bottom: 1px solid #eee; font-size: 9px; }
        .payments-table .num { text-align: right; }
        .notes-box { background: #fafafa; border-left: 3px solid #c9a227; padding: 5px 9px; font-size: 9px; color: #555; }
        .agreement { margin-top: 8px; padding: 5px 9px; border: 1px solid #b7e0c5; background: #f0faf3; border-radius: 3px; font-size: 9px; color: #1b7f44; }
        .footer { margin-top: 12px; padding-top: 5px; border-top: 1px solid #ddd; font-size: 8px; color: #aaa; text-align: center; }
        .empty { color: #aaa; font-style: italic; }
    </style>
</head>
<body>
    <div class="top">
        <table>
            <tr>
                <td>
                    <div class="brand">{{ setting('site_name', config('app.name', 'BeeG Events')) }}</div>
                    <div class="brand-sub">{{ setting('site_tagline', 'Weddings & Events Platform') }}</div>
                </td>
                <td class="doc-type" style="width:30%;">Receipt</td>
            </tr>
        </table>
    </div>

    <div class="title-row">
        <h1>Booking Receipt {{ $booking->reference }}</h1>
        <span class="status-box {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>

    <div class="section-title">Booking &amp; Event Details</div>
    <table class="meta-table">
        <tr class="col">
            <td class="label">Customer</td>
            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
            <td class="label">Event Date</td>
            <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
        </tr>
        <tr class="col">
            <td class="label">Email</td>
            <td>{{ $booking->customer->email ?? 'N/A' }}</td>
            <td class="label">Time Slot</td>
            <td>{{ $booking->time_slot ? ucfirst($booking->time_slot).' slot' : 'Flexible' }}</td>
        </tr>
        <tr class="col">
            <td class="label">Phone</td>
            <td>{{ $booking->customer->phone ?? 'N/A' }}</td>
            <td class="label">Event Type</td>
            <td>{{ ucfirst($booking->event_type) }}</td>
        </tr>
        <tr class="col">
            <td class="label">Booking Type</td>
            <td>{{ ucfirst($booking->booking_type) }}</td>
            <td class="label">Guests</td>
            <td>{{ $totalGuests ? number_format($totalGuests) : 'N/A' }}</td>
        </tr>
        <tr class="col">
            <td class="label">Requested On</td>
            <td>{{ $booking->created_at->format('M d, Y') }}</td>
            <td class="label">Catering</td>
            <td>{{ $cateringModes->isNotEmpty() ? $cateringModes->map(fn($m) => $cateringLabels[$m] ?? ucfirst($m))->implode(', ') : 'N/A' }}</td>
        </tr>
        @if($booking->price_negotiation_note)
            <tr class="col">
                <td class="label">Price Note</td>
                <td colspan="3">{{ $booking->price_negotiation_note }}</td>
            </tr>
        @endif
    </table>

    <div class="section-title">Services &amp; Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th>Item</th>
                <th>Vendor</th>
                <th>Status</th>
                <th class="num" style="width:13%;">Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($booking->bookingItems as $item)
                @php
                    $itemName = $item->itemable?->unit_name ?? $item->itemable?->title ?? ('#' . $item->itemable_id);
                    $details = collect([]);
                    if ($item->time_slot) $details->push('Slot: '.ucfirst($item->time_slot));
                    if ($item->guests) $details->push('Guests: '.number_format($item->guests));
                    if ($item->catering_mode) $details->push('Catering: '.($cateringLabels[$item->catering_mode] ?? ucfirst($item->catering_mode)));
                    if ($item->menuSet) $details->push('Menu: '.$item->menuSet->name.' (+PKR '.number_format($item->menuSet->getTotalPriceAttribute()).')');
                    foreach (($item->extras ?? []) as $x) {
                        $details->push($x['name'].' (+PKR '.number_format($x['price'] ?? 0).')');
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $itemName }}
                        @if($details->isNotEmpty())
                            <div class="sub">{{ $details->implode(' · ') }}</div>
                        @endif
                    </td>
                    <td>{{ $item->vendorProfile->business_name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item->vendor_status) }}</td>
                    <td class="num">PKR {{ number_format($item->price) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No items attached to this booking.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Pricing</div>
    <table class="pricing-table">
        @if($originalPrice != $agreedPrice)
            <tr>
                <td class="label">Original total</td>
                <td class="amount">PKR {{ number_format($originalPrice) }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Agreed price</td>
            <td class="amount">PKR {{ number_format($agreedPrice) }}</td>
        </tr>
        <tr>
            <td class="label">Amount paid</td>
            <td class="amount">PKR {{ number_format($paidAmount) }}</td>
        </tr>
        <tr class="grand">
            <td>Balance due</td>
            <td class="amount">PKR {{ number_format($remaining) }}</td>
        </tr>
    </table>

    @if($booking->payments->isNotEmpty())
        <div class="section-title">Payments</div>
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td>{{ ucfirst($payment->type) }}</td>
                        <td>{{ ucfirst($payment->method) }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                        <td class="num">PKR {{ number_format($payment->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($booking->agreement_accepted_at)
        <div class="agreement">
            <strong>Terms &amp; agreement accepted</strong> on {{ \Carbon\Carbon::parse($booking->agreement_accepted_at)->format('M d, Y') }}.
        </div>
    @endif

    @if($booking->notes)
        <div class="section-title">Notes</div>
        <div class="notes-box">{{ $booking->notes }}</div>
    @endif

    <div class="footer">
        {{ setting('site_name', config('app.name', 'BeeG Events')) }} · {{ setting('contact_email', '') }} · {{ setting('contact_phone', '') }}
        <br>Generated on {{ now()->format('M d, Y H:i') }} · This is a system-generated receipt.
    </div>
</body>
</html>
