@extends('admin.layouts.master')

@section('title', 'Vendor Detail')

@section('content')
<style>
.kyc-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.kyc-badge-verified { background: rgba(40,167,69,0.12); color: #28a745; }
.kyc-badge-pending { background: rgba(212,160,23,0.15); color: #b8860b; }
.kyc-badge-suspended, .kyc-badge-incomplete { background: rgba(220,53,69,0.12); color: #dc3545; }
</style>
<div class="row">
    {{-- Profile Sidebar --}}
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-body text-center py-4">
                @if($vendorProfile->logo_path)
                    <img src="{{ asset('storage/' . $vendorProfile->logo_path) }}"
                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 12px;"
                         alt="{{ $vendorProfile->business_name }}">
                @else
                    <div style="width:80px;height:80px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:32px;font-weight:700;color:white;">
                        {{ substr($vendorProfile->business_name, 0, 1) }}
                    </div>
                @endif
                <h5 style="font-weight:600;">{{ $vendorProfile->business_name }}</h5>
                <p class="text-muted" style="font-size:13px;">{{ $vendorProfile->user->name ?? 'N/A' }}</p>
                <span class="status-badge status-{{ $vendorProfile->status }}">{{ ucfirst($vendorProfile->status) }}</span>
                <div class="mt-3" style="background:var(--cream);border:1px solid var(--border);border-radius:10px;padding:10px 12px;text-align:left;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:6px;">
                        <i class="ti ti-shield-check"></i> KYC Status
                    </div>
                    <span class="kyc-badge {{ $vendorProfile->kycBadge()['class'] }}">{{ $vendorProfile->kycBadge()['label'] }}</span>
                    @php $kycMissing = $vendorProfile->kycMissing(); @endphp
                    <div style="font-size:12px;margin-top:6px;color:var(--text-muted);">
                        @if($kycMissing)
                            <strong style="color:#dc3545;">Missing:</strong> {{ implode(', ', $kycMissing) }}
                        @else
                            All required KYC documents and details provided.
                        @endif
                    </div>
                </div>
                <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                    @if($vendorProfile->status !== 'verified')
                        <button class="btn btn-gold btn-sm verify-vendor" data-id="{{ $vendorProfile->id }}">
                            <i class="ti ti-check"></i> Approve
                        </button>
                    @endif
                    @if($vendorProfile->status !== 'suspended')
                        <button class="btn btn-outline-danger btn-sm suspend-vendor" data-id="{{ $vendorProfile->id }}" style="border-color:#dc3545;color:#dc3545;">
                            <i class="ti ti-ban"></i> Suspend
                        </button>
                    @endif
                    @if($vendorProfile->status === 'blocked')
                        <button class="btn btn-success btn-sm unblock-vendor" data-id="{{ $vendorProfile->id }}">
                            <i class="ti ti-unlock"></i> Unblock + 3-day Trial
                        </button>
                    @endif
                    <a href="{{ route('admin.vendors.edit', $vendorProfile) }}" class="btn btn-outline-gold btn-sm">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-ghost btn-sm">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="border-top p-3" style="background:var(--cream);border-radius:0 0 12px 12px;">
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Phone</span>
                    <strong>{{ $vendorProfile->phone ?? '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Email</span>
                    <strong>{{ $vendorProfile->user->email ?? '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between" style="font-size:13px;">
                    <span class="text-muted">Member Since</span>
                    <strong>{{ $vendorProfile->created_at->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="col-lg-8">
        {{-- Vendor Info --}}
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-info-circle"></i> Vendor Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Business Name</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->business_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Owner</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->user->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Vendor Type</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize;">{{ str_replace('_', ' ', $vendorProfile->vendor_type) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">City</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->city }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Status</div>
                            <div style="margin-top:4px;"><span class="status-badge status-{{ $vendorProfile->status }}">{{ ucfirst($vendorProfile->status) }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Phone</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->phone ?? '—' }}</div>
                        </div>
                    </div>
                    @if($vendorProfile->address)
                    <div class="col-12">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Address</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->address }}</div>
                        </div>
                    </div>
                    @endif
                    @if($vendorProfile->cancellation_policy)
                    <div class="col-12">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Cancellation Policy</div>
                            <div style="font-size:13px;margin-top:4px;">{{ $vendorProfile->cancellation_policy }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Package & Subscription --}}
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-ticket"></i> Package &amp; Subscription</h5>
            </div>
            <div class="card-body">
                @php
                    $activePkg = $vendorProfile->activePackage();
                    $purchases = $vendorProfile->packagePurchases;
                @endphp
                @if($vendorProfile->status === 'blocked')
                    <div style="background:rgba(220,53,69,0.06);border:1px dashed rgba(220,53,69,0.35);border-radius:8px;padding:10px 14px;font-size:12px;color:#dc3545;margin-bottom:12px;">
                        <i class="ti ti-alert-triangle"></i> Vendor is blocked — no active package or expired trial.
                    </div>
                @endif
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Trial Ends</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">
                                @if($vendorProfile->trial_ends_at)
                                    {{ $vendorProfile->trial_ends_at->format('M d, Y') }}
                                    @if($vendorProfile->onTrial())<span class="status-badge status-verified" style="font-size:10px;margin-left:4px;">Active</span>@else<span class="status-badge status-cancelled" style="font-size:10px;margin-left:4px;">Expired</span>@endif
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Current Package</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $activePkg?->package->title ?? 'None' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Package Expires</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">
                                @if($activePkg)
                                    {{ $activePkg->ends_at->format('M d, Y') }}
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $activePkg->ends_at->diffForHumans() }}</div>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($activePkg)
                        <div class="col-md-4">
                            <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Hall Slots</div>
                                <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->halls->count() }} / {{ $activePkg->max_halls ?? '∞' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Listing Slots</div>
                                <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->serviceListings->count() }} / {{ $activePkg->max_listings ?? '∞' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Boost Tier</div>
                                <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize;">{{ $activePkg->boost_tier ?? 'Standard' }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($purchases->count() > 0)
                    <div style="margin-top:12px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:8px;">Purchase History</div>
                        <table class="table-admin">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Purchased</th>
                                    <th>Expires</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $p)
                                    <tr>
                                        <td><strong>{{ $p->package->title ?? '#' . $p->package_id }}</strong></td>
                                        <td>PKR {{ number_format($p->amount) }}</td>
                                        <td>{{ ucfirst($p->method) }}</td>
                                        <td>{{ $p->starts_at?->format('M d, Y') ?? '—' }}</td>
                                        <td>{{ $p->ends_at?->format('M d, Y') ?? '—' }}</td>
                                        <td><span class="status-badge status-{{ $p->status == 'active' ? 'verified' : ($p->status == 'expired' ? 'cancelled' : 'pending') }}">{{ ucfirst($p->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- KYC & Identity --}}
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-shield-lock"></i> KYC & Identity</h5>
                <span class="kyc-badge {{ $vendorProfile->kycBadge()['class'] }}">{{ $vendorProfile->kycBadge()['label'] }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">CNIC Front</div>
                            <div style="margin-top:6px;">
                                @if($vendorProfile->cnic_front_path)
                                    <a href="{{ asset('storage/' . $vendorProfile->cnic_front_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $vendorProfile->cnic_front_path) }}" style="height:70px;border-radius:6px;object-fit:cover;border:1px solid var(--border);" alt="CNIC Front">
                                    </a>
                                @else
                                    <span style="font-size:13px;color:var(--red);">Not uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">CNIC Back</div>
                            <div style="margin-top:6px;">
                                @if($vendorProfile->cnic_back_path)
                                    <a href="{{ asset('storage/' . $vendorProfile->cnic_back_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $vendorProfile->cnic_back_path) }}" style="height:70px;border-radius:6px;object-fit:cover;border:1px solid var(--border);" alt="CNIC Back">
                                    </a>
                                @else
                                    <span style="font-size:13px;color:var(--red);">Not uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Legal Document</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">
                                @if($vendorProfile->legal_doc_path)
                                    <a href="{{ asset('storage/' . $vendorProfile->legal_doc_path) }}" target="_blank" style="color:var(--gold-dark);"><i class="ti ti-file-download"></i> View Document</a>
                                @else
                                    <span style="font-size:13px;color:var(--red);">Not uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Bank Name</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->bank_name ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Account Title</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->bank_account_title ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Account Number</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->bank_account_number ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">IBAN</div>
                            <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->bank_iban ?? '—' }}</div>
                        </div>
                    </div>
                    @if($vendorProfile->kycMissing())
                    <div class="col-12">
                        <div style="background:rgba(220,53,69,0.06);border:1px dashed rgba(220,53,69,0.35);border-radius:8px;padding:10px 14px;font-size:12px;color:#dc3545;">
                            <i class="ti ti-alert-triangle"></i> Missing: {{ implode(', ', $vendorProfile->kycMissing()) }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Business Details (contact + specs) --}}
        @php $specRows = $vendorProfile->specDisplayList(); @endphp
        @if($vendorProfile->contact_person_name || $vendorProfile->contact_person_phone || $vendorProfile->legal_doc_path || count($specRows) > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-settings"></i> Business Details ({{ $vendorProfile->type_label }})</h5>
                </div>
                <div class="card-body">
                    @if($vendorProfile->contact_person_name || $vendorProfile->contact_person_phone || $vendorProfile->legal_doc_path)
                        <div class="row g-3 mb-3">
                            @if($vendorProfile->contact_person_name)
                                <div class="col-md-6">
                                    <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Contact Person</div>
                                        <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->contact_person_name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($vendorProfile->contact_person_phone)
                                <div class="col-md-6">
                                    <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Contact Person Number</div>
                                        <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $vendorProfile->contact_person_phone }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($vendorProfile->legal_doc_path)
                                <div class="col-md-6">
                                    <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Legal Document</div>
                                        <div style="font-size:14px;font-weight:600;margin-top:4px;">
                                            <a href="{{ asset('storage/' . $vendorProfile->legal_doc_path) }}" target="_blank" style="color:var(--gold-dark);">
                                                <i class="ti ti-file-download"></i> View Document
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(count($specRows) > 0)
                        <div class="row g-3">
                            @foreach($specRows as $row)
                                <div class="col-md-6">
                                    <div style="background:var(--cream);border-radius:8px;padding:12px 16px;">
                                        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">{{ $row['label'] }}</div>
                                        <div style="font-size:14px;font-weight:600;margin-top:4px;">{{ $row['value'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Food Menu --}}
        @php $menuCats = $vendorProfile->menuCategories()->with('menuItems')->get(); @endphp
        @if($menuCats->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-cookie"></i> Food Menu</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menuCats as $cat)
                                @foreach($cat->menuItems as $item)
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        <td><strong>{{ $item->name }}</strong></td>
                                        <td>{{ $item->price ? 'PKR ' . number_format($item->price) : '—' }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $item->is_available ? 'verified' : 'suspended' }}">
                                                {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Menu Sets & Hall Extras --}}
        @php $menuSets = $vendorProfile->menuSets()->withCount('items')->get(); @endphp
        @if($menuSets->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-menu-2"></i> Menu Sets ({{ $menuSets->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Items</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menuSets as $set)
                                <tr>
                                    <td><strong>{{ $set->name }}</strong></td>
                                    <td>{{ $set->items_count }}</td>
                                    <td>PKR {{ number_format($set->getTotalPriceAttribute()) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $set->is_active ? 'verified' : 'suspended' }}">
                                            {{ $set->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @php
            $hallExtras = $vendorProfile->halls->flatMap(fn($h) => $h->hallUnits)->flatMap(fn($u) => $u->extraServices);
        @endphp
        @if($hallExtras->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-tag"></i> Hall Extra Services ({{ $hallExtras->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hallExtras as $extra)
                                <tr>
                                    <td><strong>{{ $extra->name }}</strong></td>
                                    <td>PKR {{ number_format($extra->price) }} @if($extra->price_unit && $extra->price_unit !== 'flat')/{{ $extra->price_unit }}@endif</td>
                                    <td>{{ $extra->serviceable?->unit_name ?? '#' . $extra->serviceable_id }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Halls with Floors/Units + Gallery --}}
        @if($vendorProfile->halls->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-building"></i> Halls ({{ $vendorProfile->halls->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($vendorProfile->halls as $hall)
                        <div style="padding:20px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                            {{-- Hall Header --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <strong style="font-size:15px;">{{ $hall->name }}</strong>
                                    @if($hall->venue_type)
                                        <span class="status-badge status-pending" style="font-size:10px;margin-left:6px;text-transform:capitalize;">{{ str_replace('_', ' ', $hall->venue_type) }}</span>
                                    @endif
                                    <div style="font-size:12px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> {{ $hall->address }}</div>
                                    @if($hall->description)
                                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ Str::limit($hall->description, 100) }}</div>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:var(--text-muted);text-align:right;white-space:nowrap;">
                                    <div>{{ $hall->hallUnits->count() }} unit(s)</div>
                                    <div>PKR {{ number_format($hall->hallUnits->min('base_price') ?? 0) }}+</div>
                                </div>
                            </div>

                            {{-- Image Gallery Thumbnails --}}
                            <div class="mb-3">
                                @if($hall->hallImages->count() > 0)
                                    <div style="display:flex;gap:4px;overflow-x:auto;cursor:pointer;" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $hall->id }}">
                                        @foreach($hall->hallImages->take(5) as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                 style="width:80px;height:60px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                                        @endforeach
                                        @if($hall->hallImages->count() > 5)
                                            <div style="width:80px;height:60px;border-radius:6px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--text-muted);flex-shrink:0;">
                                                +{{ $hall->hallImages->count() - 5 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div style="width:100%;height:60px;border-radius:6px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--text-muted);">
                                        No images
                                    </div>
                                @endif
                            </div>

                            {{-- Floors Accordion --}}
                            <div class="accordion accordion-flush" id="floorAccordion{{ $hall->id }}">
                                @foreach($hall->floors as $f => $floor)
                                    <div class="accordion-item" style="border:1px solid var(--border);margin-bottom:6px;border-radius:8px;overflow:hidden;">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $f > 0 ? 'collapsed' : '' }}" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#floor-{{ $hall->id }}-{{ $floor->id }}"
                                                    style="font-size:13px;font-weight:600;padding:10px 14px;">
                                                {{ $floor->floor_label }}
                                                <span class="badge bg-secondary ms-2" style="font-size:10px;">{{ $floor->hallUnits->count() }} unit(s)</span>
                                            </button>
                                        </h2>
                                        <div id="floor-{{ $hall->id }}-{{ $floor->id }}" class="accordion-collapse collapse {{ $f === 0 ? 'show' : '' }}">
                                            <div class="accordion-body p-0">
                                                @if($floor->hallUnits->count() > 0)
                                                    <table class="table-admin" style="margin:0;">
                                                        <thead>
                                                            <tr>
                                                                <th>Unit</th>
                                                                <th>Capacity</th>
                                                                <th>Price</th>
                                                                <th>Details</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($floor->hallUnits as $unit)
                                                                <tr>
                                                                    <td><strong>{{ $unit->unit_name }}</strong></td>
                                                                    <td>{{ $unit->min_capacity ?? '—' }}–{{ $unit->max_capacity ?? '—' }}</td>
                                                                    <td>PKR {{ number_format($unit->base_price) }}</td>
                                                                    <td style="font-size:11px;color:var(--text-muted);">
                                                                        @if($unit->catering_mode)<span style="margin-right:6px;">Catering: {{ str_replace('_',' ', $unit->catering_mode) }}</span>@endif
                                                                        @if($unit->staff_male !== null || $unit->staff_female !== null)<span style="margin-right:6px;">Staff: {{ (int)$unit->staff_male + (int)$unit->staff_female }}</span>@endif
                                                                        @if(!empty($unit->amenities))<span>Amenities: {{ implode(', ', $unit->amenities) }}</span>@endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div style="padding:10px 14px;font-size:12px;color:var(--text-muted);">No units on this floor.</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($hall->floors->count() === 0)
                                <div style="font-size:12px;color:var(--text-muted);padding:4px 0;">No floors configured.</div>
                            @endif

                            {{-- Gallery Carousel Modal --}}
                            @if($hall->hallImages->count() > 0)
                                <div class="modal fade" id="galleryModal{{ $hall->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content" style="background:var(--charcoal);border:none;">
                                            <div class="modal-body p-0">
                                                <div id="hallCarousel{{ $hall->id }}" class="carousel slide">
                                                    <div class="carousel-indicators">
                                                        @foreach($hall->hallImages as $i => $img)
                                                            <button type="button" data-bs-target="#hallCarousel{{ $hall->id }}" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></button>
                                                        @endforeach
                                                    </div>
                                                    <div class="carousel-inner">
                                                        @foreach($hall->hallImages as $i => $img)
                                                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                                                <img src="{{ asset('storage/' . $img->image_path) }}" class="d-block w-100" style="max-height:75vh;object-fit:contain;">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#hallCarousel{{ $hall->id }}" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#hallCarousel{{ $hall->id }}" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Services --}}
        @if($vendorProfile->serviceListings->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-list-check"></i> Services ({{ $vendorProfile->serviceListings->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendorProfile->serviceListings as $listing)
                                <tr>
                                    <td><strong>{{ $listing->title }}</strong></td>
                                    <td>{{ $listing->serviceCategory->name ?? '—' }}</td>
                                    <td>PKR {{ number_format($listing->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.verify-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Verify this vendor?')) return;
        const ogText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        this.disabled = true;
        fetch(`/admin/vendors/${this.dataset.id}/verify`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Vendor Verified', 'Vendor has been approved successfully.');
                setTimeout(() => window.location.reload(), 1000);
            } else if (data.message) {
                this.innerHTML = ogText;
                this.disabled = false;
                showToast('error', 'KYC Incomplete', data.message);
            }
        });
    });
});

document.querySelectorAll('.suspend-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Suspend this vendor?')) return;
        const ogText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        this.disabled = true;
        fetch(`/admin/vendors/${this.dataset.id}/suspend`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('warning', 'Vendor Suspended', 'Vendor has been suspended.');
                setTimeout(() => window.location.reload(), 1000);
            }
        });
    });
});

document.querySelectorAll('.unblock-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Unblock this vendor and grant a fresh 3-day trial?')) return;
        const ogText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        this.disabled = true;
        fetch(`/admin/vendors/${this.dataset.id}/unblock`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Vendor Unblocked', 'Vendor is verified with a fresh 3-day trial.');
                setTimeout(() => window.location.reload(), 1000);
            }
        });
    });
});
</script>
@endpush
