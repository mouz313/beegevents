@extends('admin.layouts.master')

@section('title', 'User Detail')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-body text-center py-4">
                <div style="width:72px;height:72px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px;font-weight:700;color:white;">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h5 style="font-weight:600;">{{ $user->name }}</h5>
                <p class="text-muted" style="font-size:13px;">{{ $user->email }}</p>
                <span class="status-badge status-{{ $user->role == 'admin' ? 'confirmed' : ($user->role == 'vendor' ? 'pending' : 'requested') }}">
                    {{ ucfirst($user->role) }}
                </span>
                <div class="mt-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-gold btn-sm">Edit</a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-info-circle"></i> User Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4 text-muted">User ID</div>
                    <div class="col-8"><strong>{{ $user->id }}</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Name</div>
                    <div class="col-8"><strong>{{ $user->name }}</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Email</div>
                    <div class="col-8">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Phone</div>
                    <div class="col-8">{{ $user->phone ?? '—' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Role</div>
                    <div class="col-8"><span class="status-badge status-{{ $user->role == 'admin' ? 'confirmed' : ($user->role == 'vendor' ? 'pending' : 'requested') }}">{{ ucfirst($user->role) }}</span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Registered</div>
                    <div class="col-8">{{ $user->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted">Last Updated</div>
                    <div class="col-8">{{ $user->updated_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                @if($user->vendorProfile)
                    <hr>
                    <h6 class="mb-3">Vendor Details</h6>
                    <div class="row mb-3">
                        <div class="col-4 text-muted">Business Name</div>
                        <div class="col-8"><strong>{{ $user->vendorProfile->business_name }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted">Vendor Type</div>
                        <div class="col-8">{{ ucfirst($user->vendorProfile->vendor_type) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted">City</div>
                        <div class="col-8">{{ $user->vendorProfile->city }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted">Status</div>
                        <div class="col-8"><span class="status-badge status-{{ $user->vendorProfile->status }}">{{ ucfirst($user->vendorProfile->status) }}</span></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
