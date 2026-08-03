@php
    $invoiceNo = $booking->invoice_no ?? 'INV-'.($booking->created_at?->year ?? date('Y')).'-'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
    $paidAmount = $booking->payments->whereIn('status', ['received'])->sum('amount');
    $agreedPrice = $booking->price();
    $itemsTotal = $booking->bookingItems->sum('price');
    $adjustment = $agreedPrice - $itemsTotal;
    $balance = max(0, $agreedPrice - $paidAmount);
    $totalGuests = $booking->bookingItems->pluck('guests')->filter()->max();
    $cateringModes = $booking->bookingItems->pluck('catering_mode')->filter()->unique()->values();
    $cateringLabels = [
        'internal' => 'In-house catering',
        'external' => 'Outside catering',
        'none' => 'Self-arrange',
    ];
    if ($booking->status === 'cancelled') {
        $flag = 'cancelled';
        $flagText = 'Cancelled';
    } elseif ($paidAmount >= $agreedPrice && $agreedPrice > 0) {
        $flag = 'paid';
        $flagText = 'Paid';
    } elseif ($paidAmount > 0) {
        $flag = 'partial';
        $flagText = 'Partially Paid';
    } else {
        $flag = 'unpaid';
        $flagText = 'Unpaid';
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNo }} — Booking {{ $booking->reference }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .top { border-bottom: 2.5px solid #c9a227; padding-bottom: 7px; margin-bottom: 10px; }
        .brand { font-size: 16px; font-weight: bold; color: #1f2937; }
        .brand-sub { font-size: 8px; color: #999; letter-spacing: 1px; text-transform: uppercase; }
        .doc-type { text-align: right; font-size: 13px; font-weight: bold; color: #c9a227; letter-spacing: 2px; text-transform: uppercase; }
        .meta-box { border: 1px solid #ddd; border-radius: 3px; padding: 7px 10px; margin-bottom: 8px; }
        .meta-box table td { padding: 1.5px 0; font-size: 9.5px; }
        .meta-box td.label { color: #888; width: 30%; }
        .bill-box { border: 1px solid #ddd; border-left: 3px solid #c9a227; border-radius: 3px; padding: 7px 10px; margin-bottom: 8px; }
        .bill-box .caption { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #c9a227; margin-bottom: 2px; }
        .bill-box p { margin: 0; font-size: 9.5px; }
        .status-flag { float: right; font-size: 8.5px; font-weight: bold; text-transform: uppercase; padding: 2px 9px; border-radius: 3px; color: #fff; }
        .status-flag.paid { background: #198754; }
        .status-flag.partial { background: #c9a227; }
        .status-flag.unpaid { background: #dc3545; }
        .status-flag.cancelled { background: #6c757d; }
        .section-title {
            font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px;
            color: #1f2937; background: #f5f5f5; border-left: 3px solid #c9a227;
            padding: 4px 8px; margin: 10px 0 6px;
        }
        table { width: 100%; border-collapse: collapse; }
        .items-table th {
            background: #1f2937; color: #fff; font-size: 8.5px; text-transform: uppercase;
            letter-spacing: 0.4px; text-align: left; padding: 4px 7px; border-bottom: 1.5px solid #c9a227;
        }
        .items-table td { padding: 4px 7px; border-bottom: 1px solid #eee; font-size: 9px; }
        .items-table tr:nth-child(even) td { background: #fafaf7; }
        .items-table .num { text-align: right; }
        .sub { font-size: 8.5px; color: #666; }
        .sub b { color: #1f2937; }
        .summary { margin-top: 8px; }
        .summary table { width: 45%; margin-left: 55%; border-collapse: collapse; }
        .summary td { padding: 2px 0; font-size: 9.5px; }
        .summary td.label { color: #555; }
        .summary td.amount { text-align: right; font-weight: bold; }
        .summary .line td { border-top: 1px solid #ddd; }
        .summary .grand td { font-size: 11px; font-weight: bold; border-top: 1.5px solid #333; color: #1f2937; }
        .summary .grand td.amount { color: #c9a227; }
        .notes-box { margin-top: 8px; background: #fafafa; border-left: 3px solid #c9a227; padding: 5px 9px; font-size: 9px; color: #555; }
        .terms { margin-top: 10px; padding-top: 5px; border-top: 1px solid #ddd; font-size: 8.5px; color: #777; }
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
                <td class="doc-type" style="width:30%;">Invoice</td>
            </tr>
        </table>
    </div>

    <div class="status-flag {{ $flag }}">{{ $flagText }}</div>
    <div class="meta-box">
        <table>
            <tr><td class="label">Invoice No.</td><td>{{ $invoiceNo }}</td>
                <td class="label">Booking Ref</td><td>Booking {{ $booking->reference }}</td></tr>
            <tr><td class="label">Invoice Date</td><td>{{ now()->format('M d, Y') }}</td>
                <td class="label">Due Date</td><td>{{ \Carbon\Carbon::parse($booking->event_date)->copy()->subDays(3)->format('M d, Y') }}</td></tr>
            <tr><td class="label">Event Date</td><td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                <td class="label">Event Time</td><td>{{ $booking->time_slot ? ucfirst($booking->time_slot).' slot' : 'Flexible' }}</td></tr>
            <tr><td class="label">Event Type</td><td>{{ ucfirst($booking->event_type) }}</td>
                <td class="label">Guests</td><td>{{ $totalGuests ? number_format($totalGuests) : 'N/A' }}</td></tr>
            @if($cateringModes->isNotEmpty())
                <tr><td class="label">Catering</td><td colspan="3">{{ $cateringModes->map(fn($m) => $cateringLabels[$m] ?? ucfirst($m))->implode(', ') }}</td></tr>
            @endif
            @if($booking->price_negotiation_note)
                <tr><td class="label">Price Note</td><td colspan="3">{{ $booking->price_negotiation_note }}</td></tr>
            @endif
        </table>
    </div>

    <div class="bill-box">
        <div class="caption">From</div>
        <p><strong>{{ setting('site_name', config('app.name', 'BeeG Events')) }}</strong> · {{ setting('contact_email', '') }} · {{ setting('contact_phone', '') }}@if(setting('contact_address')) · {{ setting('contact_address') }}@endif</p>
    </div>

    <div class="bill-box">
        <div class="caption">Bill To</div>
        <p><strong>{{ $booking->customer->name ?? 'N/A' }}</strong> · {{ $booking->customer->email ?? '' }} · {{ $booking->customer->phone ?? '' }}</p>
    </div>

    <div class="section-title">Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th>Item</th>
                <th>Vendor</th>
                <th class="num" style="width:14%;">Amount</th>
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
                    <td class="num">PKR {{ number_format($item->price) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No items attached to this booking.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">PKR {{ number_format($itemsTotal) }}</td>
            </tr>
            @if($adjustment != 0)
                <tr>
                    <td class="label">Package adjustment</td>
                    <td class="amount">PKR {{ number_format($adjustment) }}</td>
                </tr>
            @endif
            <tr class="line">
                <td class="label">Total billed</td>
                <td class="amount">PKR {{ number_format($agreedPrice) }}</td>
            </tr>
            <tr>
                <td class="label">Amount paid</td>
                <td class="amount">PKR {{ number_format($paidAmount) }}</td>
            </tr>
            <tr class="grand">
                <td>Balance due</td>
                <td class="amount">PKR {{ number_format($balance) }}</td>
            </tr>
        </table>
    </div>

    @if($booking->notes || $booking->price_negotiation_note)
        <div class="notes-box">
            <strong style="color:#1f2937;">Notes:</strong>
            @if($booking->price_negotiation_note)
                <div>{{ $booking->price_negotiation_note }}</div>
            @endif
            @if($booking->notes)
                <div>{{ $booking->notes }}</div>
            @endif
        </div>
    @endif

    <div class="terms">
        <strong>Payment Terms:</strong> Please complete payment before the event date. This invoice relates to booking {{ $booking->reference }} on
        {{ setting('site_name', config('app.name', 'BeeG Events')) }}. For any queries, contact {{ setting('support_email', setting('contact_email', '')) }}.
    </div>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }} · {{ setting('site_name', config('app.name', 'BeeG Events')) }} · This is a system-generated invoice.
    </div>
</body>
</html>
