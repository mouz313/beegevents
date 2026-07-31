@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Vendor Dashboard</h2>
        </div>
    </div>

    @if(!$profile || $profile->status == 'pending')
        <div class="alert alert-warning">
            <strong>Pending Verification!</strong> Your vendor profile is being reviewed by admin.
            @if(!$profile)
                <a href="{{ route('vendor.profile.create') }}" class="alert-link">Complete your profile</a>
            @endif
        </div>
    @endif

    @if($profile && !$profile->onboarding_completed)
        <div class="alert alert-info" style="background:var(--cream);border:1px solid var(--gold);border-radius:12px;padding:20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:var(--gold);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti ti-rocket" style="font-size:24px;color:var(--charcoal);"></i>
                </div>
                <div style="flex:1;">
                    <h5 style="margin:0 0 4px;font-weight:700;color:var(--charcoal);">Welcome to BeeG Events!</h5>
                    <p style="margin:0;font-size:13px;color:var(--text-muted);">Complete your onboarding to start receiving bookings.</p>
                </div>
                <a href="{{ route('vendor.onboarding') }}" class="btn-gold" style="text-decoration:none;white-space:nowrap;">
                    <i class="ti ti-arrow-right"></i> Continue
                </a>
            </div>
        </div>
    @endif

    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="vendor-card" style="border-left:4px solid var(--gold);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:var(--light-honey);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-building" style="font-size:20px;color:var(--gold-dark);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $stats['listings_count'] ?? 0 }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">My Listings</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="vendor-card" style="border-left:4px solid var(--green);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#E6F7ED;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-calendar-check" style="font-size:20px;color:var(--green);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $stats['active_bookings'] ?? 0 }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">Active Bookings</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="vendor-card" style="border-left:4px solid var(--amber);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FFF3E0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-clock-hour-4" style="font-size:20px;color:var(--amber);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $stats['pending_inquiries'] }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">Pending Inquiries</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="vendor-card" style="border-left:4px solid var(--blue-grey);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#E8EEF1;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-mail" style="font-size:20px;color:var(--blue-grey);"></i>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:var(--charcoal);">{{ $stats['total_inquiries'] }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">Total Inquiries</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($profile)
                            <a href="{{ route('vendor.halls.index') }}" class="btn btn-outline-primary">Manage Halls</a>
                            <a href="{{ route('vendor.listings.index') }}" class="btn btn-outline-success">Manage Services</a>
                            <a href="{{ route('vendor.packages.index') }}" class="btn btn-outline-dark">Manage Packages</a>
                            <a href="{{ route('vendor.calendar') }}" class="btn btn-outline-info">Update Availability</a>
                            <a href="{{ route('vendor.inquiries.index') }}" class="btn btn-outline-warning">View Inquiries</a>
                            <a href="{{ route('vendor.profile.create') }}" class="btn btn-outline-secondary">Edit Profile</a>
                        @else
                            <a href="{{ route('vendor.profile.create') }}" class="btn btn-primary">Create Vendor Profile</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($profile)
            <div class="col-md-6">
                <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                    <div class="card-header" style="background:var(--cream);border-radius:14px 14px 0 0;">
                        <h6 class="mb-0" style="font-weight:700;color:var(--charcoal);"><i class="ti ti-wallet"></i> My Earnings</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div style="font-size:12px;color:var(--text-muted);">Total Earned</div>
                                <div style="font-size:24px;font-weight:800;color:var(--gold-dark);">PKR {{ number_format($stats['total_earned']) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px;color:var(--text-muted);">Paid Out</div>
                                <div style="font-size:20px;font-weight:700;color:var(--green);">PKR {{ number_format($stats['payouts_paid']) }}</div>
                            </div>
                        </div>
                        <hr style="border-color:var(--border);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="font-size:12px;color:var(--text-muted);">Currently Due</div>
                                <div style="font-size:18px;font-weight:700;color:var(--charcoal);">PKR {{ number_format($stats['payout_due']) }}</div>
                            </div>
                            <span style="font-size:12px;color:var(--text-muted);">Payouts are processed after event completion.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
