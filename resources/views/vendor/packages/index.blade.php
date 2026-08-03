@extends('vendor.layouts.master')

@section('title', 'Packages')

@section('content')
<div class="vd-page-head">
    <div>
        <h2 class="vd-page-title"><i class="ti ti-box"></i> Packages &amp; Subscription</h2>
        <p class="vd-page-sub">Buy a package to keep your listings visible on the website and get boosted.</p>
    </div>
</div>

@if($blocked)
    <div class="alert alert-danger d-flex align-items-center gap-3" style="border-radius:12px;">
        <i class="ti ti-alert-triangle" style="font-size:24px;"></i>
        <div>
            <strong>Your package has expired.</strong> Buy a package to show your listings on the website.
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="card-header">
                <h5><i class="ti ti-zap"></i> Current Status</h5>
            </div>
            <div class="card-body" style="font-size:14px;">
                @if($current)
                    <div class="mb-2">
                        <span class="status-badge status-active">Package Active</span>
                    </div>
                    <div><strong>{{ $current->package->title ?? 'Package' }}</strong></div>
                    <div class="text-muted" style="font-size:12px;">
                        Valid until {{ $current->ends_at->format('M d, Y') }}
                        ({{ $current->ends_at->diffForHumans() }})
                    </div>
                    @if($current->boost_tier)
                        <div class="mt-2" style="font-size:12px;color:var(--gold-dark);text-transform:capitalize;">
                            <i class="ti ti-{{ $current->boost_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ $current->boost_tier }} boost active
                        </div>
                    @endif
                @elseif($onTrial)
                    <div class="mb-2">
                        <span class="status-badge status-pending">Trial</span>
                    </div>
                    <div><strong>Free Trial</strong></div>
                    <div class="text-muted" style="font-size:12px;">
                        Trial ends {{ $vendor->trial_ends_at->format('M d, Y') }}
                        ({{ $vendor->trial_ends_at->diffForHumans() }})
                    </div>
                    <div class="mt-2" style="font-size:12px;color:var(--text-muted);">
                        Buy a package before the trial ends to stay listed.
                    </div>
                @else
                    <div class="mb-2">
                        <span class="status-badge status-suspended">No Package</span>
                    </div>
                    <div class="text-muted" style="font-size:13px;">
                        You currently have no active package or trial.
                        @if($vendor && $vendor->status === 'blocked')
                            Your listings are hidden until you buy a package.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="card-header">
                <h5><i class="ti ti-info-circle"></i> How It Works</h5>
            </div>
            <div class="card-body" style="font-size:13px;color:var(--text-muted);">
                <ul style="margin:0;padding-left:18px;line-height:2;">
                    <li>Choose a package and pay by card or bank transfer.</li>
                    <li>Your listings stay visible for the package duration.</li>
                    <li>Featured / Premium packages boost you to the top of search.</li>
                    <li>Each package includes hall &amp; service limits and a combo template.</li>
                    <li>Expired? Just buy again — no interruption when you renew before expiry.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<h4 style="font-size:16px;font-weight:700;margin-bottom:12px;">Choose a Package</h4>
<div class="row g-3 mb-4">
    @forelse($plans as $plan)
        <div class="col-md-6 col-lg-4">
            <div class="admin-card h-100" style="margin:0;border:{{ $current && $current->package_id === $plan->id ? '2px solid var(--gold)' : '1px solid var(--border)' }};">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-between align-center mb-1">
                        <h5 style="font-size:15px;font-weight:700;margin:0;">{{ $plan->title }}</h5>
                        @if($current && $current->package_id === $plan->id)
                            <span class="status-badge status-active" style="font-size:10px;">Current</span>
                        @endif
                    </div>
                    <h4 style="color:var(--gold);font-weight:800;">PKR {{ number_format($plan->total_price) }}</h4>
                    <div class="text-muted mb-2" style="font-size:12px;">per {{ $plan->duration_days }} days</div>
                    <p style="font-size:12px;color:var(--text-muted);flex:1;">{{ $plan->description ?: 'No description.' }}</p>
                    <ul style="font-size:12px;color:var(--text-muted);padding-left:18px;line-height:2;">
                        <li>
                            @if($plan->boost_tier)
                                <span style="text-transform:capitalize;"><i class="ti ti-{{ $plan->boost_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ $plan->boost_tier }} boost</span>
                            @else
                                No boost
                            @endif
                        </li>
                        <li>{{ $plan->max_halls !== null ? $plan->max_halls.' halls' : 'Unlimited halls' }}</li>
                        <li>{{ $plan->max_listings !== null ? $plan->max_listings.' services' : 'Unlimited services' }}</li>
                        <li>{{ $plan->packageItems->count() }} combo template item(s)</li>
                    </ul>
                    <a href="{{ route('vendor.packages.checkout', $plan) }}" class="btn bm-btn-gold w-100 mt-2">
                        {{ $current && $current->package_id === $plan->id ? 'Renew' : 'Buy Now' }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="admin-card">
                <div class="card-body text-center text-muted py-4">No packages available yet. Please check back later.</div>
            </div>
        </div>
    @endforelse
</div>

@if($purchases->count() > 0)
    <div class="admin-card">
        <div class="card-header">
            <h5><i class="ti ti-history"></i> Purchase History</h5>
        </div>
        <div class="card-body p-0">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                        <tr>
                            <td style="font-size:13px;"><strong>{{ $purchase->package->title ?? '#' . $purchase->package_id }}</strong></td>
                            <td style="font-size:13px;"><strong>PKR {{ number_format($purchase->amount) }}</strong></td>
                            <td style="font-size:12px;text-transform:capitalize;">{{ str_replace('_', ' ', $purchase->method) }}</td>
                            <td style="font-size:12px;">{{ $purchase->ends_at ? $purchase->ends_at->format('M d, Y') : '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
