<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking #{{ $booking->id }} Receipt</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .brand-bar {
            border-bottom: 3px solid #c9a227;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .brand-name { font-size: 22px; font-weight: bold; color: #1f2937; }
        .brand-sub { font-size: 10px; color: #888; letter-spacing: 1px; text-transform: uppercase; }
        .title-row {
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }
        .title-row h1 {
            font-size: 16px; margin: 0; color: #1f2937;
            display: inline-block;
        }
        .status-box {
            float: right;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 3px;
            background: #c9a227;
            color: #fff;
        }
        .status-box.cancelled { background: #dc3545; }
        .status-box.completed { background: #198754; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c9a227;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
            margin: 16px 0 8px;
        }
        table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 3px 0; font-size: 12px; }
        .meta-table td.label { color: #777; width: 40%; }
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
        .pricing-table td {
            padding: 4px 0;
            font-size: 12px;
            border-bottom: 1px solid #f5f5f5;
        }
        .pricing-table td.label { color: #555; }
        .pricing-table td.amount { text-align: right; font-weight: bold; }
        .pricing-table .grand td {
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: none;
            color: #1f2937;
        }
        .pricing-table .grand td.amount { color: #c9a227; }
        .notes-box {
            background: #fafafa;
            border-left: 3px solid #c9a227;
            padding: 8px 12px;
            font-size: 11px;
            color: #555;
        }
        .payments-table th {
            background: #f5f5f5;
            font-size: 10px;
            text-transform: uppercase;
            text-align: left;
            padding: 5px 8px;
            border-bottom: 2px solid #ddd;
            color: #555;
        }
        .payments-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .payments-table .num { text-align: right; }
        .footer {
            margin-top: 24px;
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
    <div class="brand-bar">
        <div class="brand-name">{{ setting('site_name', config('app.name', 'BeeG Events')) }}</div>
        <div class="brand-sub">{{ setting('site_tagline', 'Weddings & Events Platform') }}</div>
    </div>

    <div class="title-row">
        <h1>Booking Receipt #{{ $booking->id }}</h1>
        <span class="status-box {{ $booking->status == 'cancelled' ? 'cancelled' : ($booking->status == 'completed' ? 'completed' : '') }}">{{ ucfirst($booking->status) }}</span>
    </div>

    <div class="section-title">Booking Details</div>
    <table class="meta-table">
        <tr><td class="label">Customer</td><td>{{ $booking->customer->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $booking->customer->email ?? 'N/A' }}</td></tr>
        <tr><td class="label">Phone</td><td>{{ $booking->customer->phone ?? 'N/A' }}</td></tr>
        <tr><td class="label">Event Date</td><td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td></tr>
        <tr><td class="label">Event Type</td><td>{{ ucfirst($booking->event_type) }}</td></tr>
        <tr><td class="label">Booking Type</td><td>{{ ucfirst($booking->booking_type) }}</td></tr>
        <tr><td class="label">Requested On</td><td>{{ $booking->created_at->format('M d, Y') }}</td></tr>
    </table>

    <div class="section-title">Services &amp; Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th>Item</th>
                <th>Type</th>
                <th>Vendor</th>
                <th>Status</th>
                <th class="num" style="width:16%;">Price</th>
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
                    <td>{{ ucfirst($item->vendor_status) }}</td>
                    <td class="num">PKR {{ number_format($item->price) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No items attached to this booking.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Pricing</div>
    <table class="pricing-table">
        @php
            $paidAmount = $booking->payments->whereIn('status', ['received'])->sum('amount');
            $agreedPrice = $booking->price();
            $remaining = max(0, $agreedPrice - $paidAmount);
        @endphp
        <tr>
            <td class="label">Original total</td>
            <td class="amount">PKR {{ number_format($booking->total_price) }}</td>
        </tr>
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
