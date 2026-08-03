@extends('vendor.layouts.master')

@section('title', 'Vendor Dashboard')

@push('styles')
<style>
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin: 8px 0 24px;
}
.dash-header h2 {
    font-weight: 800;
    font-size: 26px;
    color: var(--charcoal);
    margin: 0;
}
.dash-header h2 span { color: var(--gold); }
.dash-sub {
    font-size: 13px;
    color: var(--text-muted);
    margin: 4px 0 0;
}
.action-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-align: center;
    padding: 18px 10px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: white;
    color: var(--charcoal);
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.18s;
    height: 100%;
}
.action-tile i { font-size: 22px; color: var(--gold-dark); }
.action-tile:hover {
    background: var(--light-honey);
    border-color: var(--gold);
    color: var(--gold-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(212,160,23,0.12);
    text-decoration: none;
}
</style>
@endpush

@section('content')
<div>
    <div class="dash-header">
        <div>
            <h2>Vendor <span>Dashboard</span></h2>
            <p class="dash-sub">Welcome back{{ $profile ? ', ' . $profile->business_name : '' }} — manage your business from here.</p>
        </div>
        @if($profile)
            <a href="{{ route('vendor.profile.create') }}" class="btn-outline-gold" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:9px 20px;">
                <i class="ti ti-settings"></i> Edit Profile
            </a>
        @endif
    </div>

    @if(!$profile)
        <div class="alert alert-warning">
            <strong>Pending Verification!</strong> Create your vendor profile to get started.
            <a href="{{ route('vendor.profile.create') }}" class="alert-link">Complete your profile</a>
        </div>
    @elseif($profile->status == 'pending')
        @php $missing = $profile->kycMissing(); @endphp
        <div class="alert alert-warning">
            <strong>Pending Verification!</strong> Your vendor profile is being reviewed by admin.
            @if($missing)
                <div style="margin-top:6px;">
                    <strong>Complete KYC:</strong> {{ implode(', ', $missing) }}
                    <a href="{{ route('vendor.profile.create') }}" class="alert-link">Complete now</a>
                </div>
            @endif
        </div>
    @elseif($profile->status == 'blocked')
        <div class="alert alert-danger" style="border-radius:12px;">
            <strong><i class="ti ti-alert-triangle"></i> Your package is expired.</strong> Buy a package to show your listings on the website.
            <a href="{{ route('vendor.packages.index') }}" class="alert-link">Buy a package →</a>
        </div>
    @elseif(!$profile->activePackage() && $profile->onTrial())
        <div class="alert alert-info" style="border-radius:12px;">
            <strong><i class="ti ti-clock"></i> Free trial active.</strong> Your trial ends {{ $profile->trial_ends_at->format('M d, Y') }} ({{ $profile->trial_ends_at->diffForHumans() }}). Buy a package before it expires to keep your listings visible.
            <a href="{{ route('vendor.packages.index') }}" class="alert-link">Browse packages →</a>
        </div>
    @elseif(!$profile->activePackage())
        <div class="alert alert-warning" style="border-radius:12px;">
            <strong><i class="ti ti-box"></i> No active package.</strong> Your listings may be hidden. Buy a package to stay visible on the website.
            <a href="{{ route('vendor.packages.index') }}" class="alert-link">Buy a package →</a>
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

    <div class="row g-4">
        <div class="col-md-6">
            <div class="vendor-card h-100" style="padding:24px;background:white;border:1px solid var(--border);border-radius:16px;">
                <h6 style="font-weight:700;font-size:14px;margin-bottom:16px;color:var(--charcoal);">
                    <i class="ti ti-bolt" style="color:var(--gold);"></i> Quick Actions
                </h6>
                <div class="row g-2">
                    @if($profile)
                        <div class="col-6">
                            <a href="{{ route('vendor.halls.index') }}" class="action-tile"><i class="ti ti-building"></i><span>Manage Halls</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.listings.index') }}" class="action-tile"><i class="ti ti-list-check"></i><span>Manage Services</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.packages.index') }}" class="action-tile"><i class="ti ti-gift"></i><span>Manage Packages</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.menu.index') }}" class="action-tile"><i class="ti ti-cookie"></i><span>Manage Menu</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.calendar') }}" class="action-tile"><i class="ti ti-calendar-plus"></i><span>Availability</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.bookings.index') }}" class="action-tile"><i class="ti ti-calendar-event"></i><span>Bookings</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.inquiries.index') }}" class="action-tile"><i class="ti ti-mail"></i><span>Inquiries</span></a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('vendor.onboarding') }}" class="action-tile"><i class="ti ti-rocket"></i><span>Onboarding</span></a>
                        </div>
                    @else
                        <div class="col-12">
                            <a href="{{ route('vendor.profile.create') }}" class="action-tile" style="flex-direction:row;padding:20px;">
                                <i class="ti ti-plus-circle"></i><span>Create Vendor Profile</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($profile)
            <div class="col-md-6">
                <div class="vendor-card h-100" style="padding:24px;background:white;border:1px solid var(--border);border-radius:16px;">
                    <h6 style="font-weight:700;font-size:14px;margin-bottom:16px;color:var(--charcoal);">
                        <i class="ti ti-wallet" style="color:var(--gold);"></i> My Earnings
                    </h6>
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
        @endif
    </div>
</div>
@endsection
