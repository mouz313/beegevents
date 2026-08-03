@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Users</span>
            <div class="stat-icon" style="background: rgba(212,160,23,0.12); color: var(--gold);">
                <i class="ti ti-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['totalUsers'] }}</div>
        <div class="stat-change">
            <span class="text-muted">{{ $stats['totalCustomers'] }} customers, {{ $stats['totalVendors'] }} vendors</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Vendors</span>
            <div class="stat-icon" style="background: rgba(46,139,87,0.12); color: var(--green);">
                <i class="ti ti-building-store"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['verifiedVendors'] }}</div>
        <div class="stat-change">
            @if($stats['pendingVendors'] > 0)
                <span class="down">{{ $stats['pendingVendors'] }} pending verification</span>
            @else
                <span class="up">All verified</span>
            @endif
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Bookings</span>
            <div class="stat-icon" style="background: rgba(74,101,114,0.12); color: var(--blue-grey);">
                <i class="ti ti-calendar-event"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['totalBookings'] }}</div>
        <div class="stat-change">
            <span>{{ $stats['confirmedBookings'] }} confirmed, {{ $stats['completedBookings'] }} completed</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Revenue</span>
            <div class="stat-icon" style="background: rgba(245,166,35,0.12); color: var(--amber);">
                <i class="ti ti-coin"></i>
            </div>
        </div>
        <div class="stat-value">PKR {{ number_format($stats['totalRevenue']) }}</div>
        <div class="stat-change">
            <span class="up">Package sales: PKR {{ number_format($stats['packageRevenue']) }}</span>
        </div>
    </div>
</div>

<div class="stats-grid mb-4" style="grid-template-columns:repeat(2,1fr);">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Active Packages</span>
            <div class="stat-icon" style="background: rgba(212,160,23,0.12); color: var(--gold);">
                <i class="ti ti-zap"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['activePackages'] }}</div>
        <div class="stat-change">
            <a href="{{ route('admin.package-purchases.index') }}" style="color:var(--gold);font-weight:600;font-size:12px;">View purchases →</a>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Pending Package Payments</span>
            <div class="stat-icon" style="background: rgba(245,166,35,0.12); color: var(--amber);">
                <i class="ti ti-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['pendingPackagePayments'] }}</div>
        <div class="stat-change">
            @if($stats['pendingPackagePayments'] > 0)
                <a href="{{ route('admin.package-purchases.index', ['status' => 'pending']) }}" style="color:var(--amber);font-weight:600;font-size:12px;">{{ $stats['pendingPackagePayments'] }} to verify →</a>
            @else
                <span class="up">All verified</span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-clock"></i> Recent Bookings</h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->reference }}</strong></td>
                                <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                                <td>{{ ucfirst($booking->event_type) }}</td>
                                <td>PKR {{ number_format($booking->total_price) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No bookings yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-building-store"></i> Recent Vendors</h5>
                <a href="{{ route('admin.vendors.pending') }}" class="btn btn-ghost btn-sm">Manage</a>
            </div>
            <div class="card-body p-0">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVendors as $vendor)
                            <tr>
                                <td>
                                    <strong>{{ $vendor->business_name }}</strong>
                                    <div class="text-muted" style="font-size:11px;">{{ $vendor->user->name ?? '' }}</div>
                                </td>
                                <td><span class="text-muted">{{ ucfirst($vendor->vendor_type) }}</span></td>
                                <td>
                                    <span class="status-badge status-{{ $vendor->status }}">
                                        {{ ucfirst($vendor->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No vendors yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-alert-triangle"></i> Quick Overview</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Pending Bookings</span>
                    <span class="status-badge status-requested">{{ $stats['requestedBookings'] }}</span>
                </div>
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Confirmed</span>
                    <span class="status-badge status-confirmed">{{ $stats['confirmedBookings'] }}</span>
                </div>
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Completed</span>
                    <span class="status-badge status-completed">{{ $stats['completedBookings'] }}</span>
                </div>
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Cancelled</span>
                    <span class="status-badge status-cancelled">{{ $stats['cancelledBookings'] }}</span>
                </div>
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Blocked Vendors</span>
                    <a href="{{ route('admin.vendors.index', ['status' => 'blocked']) }}" style="color:var(--danger,#dc3545);font-weight:600;">{{ $stats['blockedVendors'] }}</a>
                </div>
                <div class="d-flex justify-between">
                    <span class="text-muted">Open Disputes</span>
                    <span class="status-badge status-pending">{{ $stats['openDisputes'] }}</span>
                </div>
                @if($stats['pendingVendors'] > 0 || $stats['newLeads'] > 0)
                    <hr>
                    @if($stats['pendingVendors'] > 0)
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Vendors to verify</span>
                            <a href="{{ route('admin.vendors.pending') }}" style="color:var(--gold);font-weight:600;">{{ $stats['pendingVendors'] }}</a>
                        </div>
                    @endif
                    @if($stats['newLeads'] > 0)
                        <div class="d-flex justify-between">
                            <span class="text-muted">New leads</span>
                            <a href="{{ route('admin.leads.index') }}" style="color:var(--gold);font-weight:600;">{{ $stats['newLeads'] }}</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
