@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Welcome, {{ auth()->user()->name }}!</h2>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="vendor-card" style="border-left:4px solid var(--gold);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:var(--light-honey);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-calendar-event" style="font-size:20px;color:var(--gold-dark);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $bookings->count() }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">My Bookings</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="vendor-card" style="border-left:4px solid var(--blue-grey);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#E8EEF1;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-mail" style="font-size:20px;color:var(--blue-grey);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $inquiries->count() }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">My Inquiries</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="vendor-card" style="border-left:4px solid var(--green);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#E6F7ED;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-building-arch" style="font-size:20px;color:var(--green);"></i>
                    </div>
                    <div>
                        <a href="{{ route('browse.index') }}" style="text-decoration:none;">
                            <div style="font-size:14px;font-weight:600;color:var(--green);">Browse Events →</div>
                        </a>
                        <div style="font-size:12px;color:var(--text-muted);">Find halls & services</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4" style="border:1px solid var(--border);border-radius:12px;">
        <div class="card-header" style="background:var(--white);border-bottom:1px solid var(--border);padding:16px 20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:600;font-size:15px;">Recent Bookings</span>
                @if($bookings->count() > 0)
                    <a href="{{ route('customer.bookings.index') }}" class="btn-outline-gold btn-sm">View All</a>
                @endif
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">ID</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Event Date</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Type</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Status</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td style="padding:12px 20px;font-size:13px;">#{{ $booking->id }}</td>
                                    <td style="padding:12px 20px;font-size:13px;">{{ $booking->event_date->format('M d, Y') }}</td>
                                    <td style="padding:12px 20px;font-size:13px;">{{ ucfirst($booking->event_type) }}</td>
                                    <td style="padding:12px 20px;">
                                        <span style="display:inline-block;padding:2px 10px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $booking->status == 'confirmed' ? '#E6F7ED' : ($booking->status == 'cancelled' ? '#FDE8E8' : '#FFF3E0') }};color:{{ $booking->status == 'confirmed' ? 'var(--green)' : ($booking->status == 'cancelled' ? 'var(--red)' : 'var(--amber)') }};">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:12px 20px;font-size:13px;font-weight:600;">PKR {{ number_format($booking->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:13px;">
                    No bookings yet. Start by <a href="{{ route('browse.index') }}" style="color:var(--gold-dark);">browsing venues</a>!
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="border:1px solid var(--border);border-radius:12px;">
        <div class="card-header" style="background:var(--white);border-bottom:1px solid var(--border);padding:16px 20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:600;font-size:15px;">My Inquiries</span>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            @if($inquiries->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Listing</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Vendor</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Date</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Status</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inquiries as $inq)
                                <tr>
                                    <td style="padding:12px 20px;font-size:13px;font-weight:600;">
                                        @php $item = $inq->inquiriable; @endphp
                                        {{ $item ? ($item->name ?? $item->title ?? 'N/A') : 'N/A' }}
                                    </td>
                                    <td style="padding:12px 20px;font-size:13px;">{{ $inq->vendorProfile->business_name ?? 'N/A' }}</td>
                                    <td style="padding:12px 20px;font-size:13px;">{{ $inq->created_at->format('M d, Y') }}</td>
                                    <td style="padding:12px 20px;">
                                        <span style="display:inline-block;padding:2px 10px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $inq->status == 'replied' ? '#E6F7ED' : ($inq->status == 'closed' ? '#f5f3f0' : '#FFF3E0') }};color:{{ $inq->status == 'replied' ? 'var(--green)' : ($inq->status == 'closed' ? 'var(--text-muted)' : 'var(--amber)') }};">
                                            {{ ucfirst($inq->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:12px 20px;font-size:12px;color:var(--text-muted);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $inq->message }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:13px;">
                    No inquiries yet. Send an inquiry from any hall or service page!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
