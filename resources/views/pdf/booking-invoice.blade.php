@php
    $invoiceNo = 'INV-'.date('Y').'-'.str_pad($booking->id, 4, '0', STR_PAD_LEFT);
    $paidAmount = $booking->payments->whereIn('status', ['received'])->sum('amount');
    $agreedPrice = $booking->price();
    $itemsTotal = $booking->bookingItems->sum('price');
    $adjustment = $agreedPrice - $itemsTotal;
    $balance = max(0, $agreedPrice - $paidAmount);
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
    <title>Invoice {{ $invoiceNo }} — Booking #{{ $booking->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .invoice-head {
            border-bottom: 3px solid #c9a227;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand-name { font-size: 22px; font-weight: bold; color: #1f2937; }
        .brand-sub { font-size: 10px; color: #888; letter-spacing: 1px; text-transform: uppercase; }
        .invoice-title {
            text-align: right;
            font-size: 30px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .meta-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .meta-box table td { padding: 2px 0; font-size: 11px; }
        .meta-box td.label { color: #777; width: 34%; }
        .bill-box {
            border: 1px solid #ddd;
            border-left: 3px solid #c9a227;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .bill-box .caption {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c9a227;
            margin-bottom: 4px;
        }
        .bill-box p { margin: 1px 0; font-size: 12px; }
        .status-flag {
            float: right;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 3px;
            color: #fff;
        }
        .status-flag.paid { background: #198754; }
        .status-flag.partial { background: #c9a227; }
        .status-flag.unpaid { background: #dc3545; }
        .status-flag.cancelled { background: #6c757d; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th {
            background: #f5f5f5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 2px solid #ddd;
            color: #555;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .items-table .num { text-align: right; }
        .summary { margin-top: 14px; }
        .summary table { width: 55%; margin-left: 45%; border-collapse: collapse; }
        .summary td { padding: 4px 0; font-size: 12px; }
        .summary td.label { color: #555; }
        .summary td.amount { text-align: right; font-weight: bold; }
        .summary .line td { border-top: 1px solid #ddd; }
        .summary .grand td {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #333;
            color: #1f2937;
        }
        .summary .grand td.amount { color: #c9a227; }
        .terms {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #777;
        }
        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #aaa;
            text-align: center;
        }
        .empty { color: #aaa; font-style: italic; }
    </style>
</head>
<body>
    @php
        $flagText = [
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            'unpaid' => 'Unpaid',
            'cancelled' => 'Cancelled',
        ][$flag] ?? 'Unpaid';
    @endphp

    <div class="invoice-head">
        <table style="width:100%;">
            <tr>
                <td>
                    <div class="brand-name">{{ setting('site_name', config('app.name', 'BeeG Events')) }}</div>
                    <div class="brand-sub">{{ setting('site_tagline', 'Weddings & Events Platform') }}</div>
                </td>
                <td style="text-align:right;">
                    <div class="invoice-title">Invoice</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="bill-box">
        <div class="caption">From</div>
        <p><strong>{{ setting('site_name', config('app.name', 'BeeG Events')) }}</strong></p>
        <p>{{ setting('contact_email', '') }}</p>
        <p>{{ setting('contact_phone', '') }}</p>
        @if(setting('contact_address'))
            <p>{{ setting('contact_address') }}</p>
        @endif
    </div>

    <div class="status-flag {{ $flag }}">{{ $flagText }}</div>
    <div class="meta-box">
        <table style="width:60%;">
            <tr><td class="label">Invoice No.</td><td>{{ $invoiceNo }}</td></tr>
            <tr><td class="label">Invoice Date</td><td>{{ $booking->created_at->format('M d, Y') }}</td></tr>
            <tr><td class="label">Booking Ref</td><td>Booking #{{ $booking->id }}</td></tr>
            <tr><td class="label">Event Date</td><td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td></tr>
            <tr><td class="label">Due Date</td><td>{{ $booking->event_date->copy()->subDays(3)->format('M d, Y') }}</td></tr>
        </table>
    </div>

    <div class="bill-box">
        <div class="caption">Bill To</div>
        <p><strong>{{ $booking->customer->name ?? 'N/A' }}</strong></p>
        <p>{{ $booking->customer->email ?? '' }}</p>
        <p>{{ $booking->customer->phone ?? '' }}</p>
        <p style="margin-top:6px;">Event Type: <strong>{{ ucfirst($booking->event_type) }}</strong></p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th>Item</th>
                <th>Type</th>
                <th>Vendor</th>
                <th class="num" style="width:16%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($booking->bookingItems as $item)
                @php
                    $itemName = $item->itemable?->unit_name ?? $item->itemable?->title ?? ('#' . $item->itemable_id);
                    $itemType = str_replace('_', ' ', class_basename($item->itemable_type));
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $itemName }}</td>
                    <td>{{ $itemType }}</td>
                    <td>{{ $item->vendorProfile->business_name ?? 'N/A' }}</td>
                    <td class="num">PKR {{ number_format($item->price) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No items attached to this booking.</td></tr>
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

    <div class="terms">
        <strong>Payment Terms:</strong> Please complete payment before the event date. This invoice relates to booking #{{ $booking->id }} on
        {{ setting('site_name', config('app.name', 'BeeG Events')) }}. For any queries, contact {{ setting('support_email', setting('contact_email', '')) }}.
    </div>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }} · {{ setting('site_name', config('app.name', 'BeeG Events')) }} · This is a system-generated invoice.
    </div>
</body>
</html>
