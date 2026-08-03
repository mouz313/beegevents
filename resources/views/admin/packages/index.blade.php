@extends('admin.layouts.master')

@section('title', 'Packages')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-box"></i> Packages ({{ $packages->count() }})</h5>
        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal">
            <i class="ti ti-plus"></i> Add Package
        </button>
    </div>
    <div class="card-body">
        @if($packages->count() > 0)
            <div class="row">
                @foreach($packages as $package)
                    <div class="col-md-4 mb-3">
                        <div class="admin-card h-100" style="margin:0;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-between align-center mb-2">
                                    <h5 style="font-size:15px;font-weight:600;margin:0;">{{ $package->title }}</h5>
                                    @if($package->is_active)
                                        <span class="status-badge status-active" style="font-size:10px;">Active</span>
                                    @else
                                        <span class="status-badge status-suspended" style="font-size:10px;">Inactive</span>
                                    @endif
                                </div>
                                <h4 style="color:var(--gold);font-weight:700;">PKR {{ number_format($package->total_price) }}</h4>
                                <div class="d-flex flex-wrap gap-1 mb-1" style="font-size:11px;">
                                    @if($package->boost_tier)
                                        <span class="status-badge status-featured" style="background:rgba(212,160,23,0.12);color:var(--gold);text-transform:capitalize;"><i class="ti ti-{{ $package->boost_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ $package->boost_tier }} boost</span>
                                    @else
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">No boost</span>
                                    @endif
                                    <span class="status-badge" style="background:rgba(46,139,87,0.12);color:var(--green);">{{ $package->duration_days }} days</span>
                                    @if($package->max_halls !== null)
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">{{ $package->max_halls }} halls</span>
                                    @else
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">Unlimited halls</span>
                                    @endif
                                    @if($package->max_listings !== null)
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">{{ $package->max_listings }} services</span>
                                    @else
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">Unlimited services</span>
                                    @endif
                                </div>
                                <p style="font-size:12px;color:var(--text-muted);flex:1;margin-bottom:8px;">{{ $package->description ?: 'No description.' }}</p>
                                <div class="d-flex justify-between align-center mb-2">
                                    <span style="font-size:11px;color:var(--text-muted);">
                                        <i class="ti ti-list"></i> {{ $package->packageItems->count() }} template item{{ $package->packageItems->count() != 1 ? 's' : '' }}
                                    </span>
                                    @if($package->created_at)
                                        <span style="font-size:11px;color:var(--text-muted);">{{ $package->created_at->format('M d, Y') }}</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2" style="border-top:1px solid var(--border);padding-top:10px;">
                                    <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-ghost btn-sm" title="View">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-gold btn-sm" title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?')" class="ms-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm" title="Delete"><i class="ti ti-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="ti ti-box" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No packages yet. Click "Add Package" to create one.</p>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;">
            <form id="packageForm">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px;">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600;">Add Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Title</label>
                        <input type="text" class="form-control-admin w-100" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Description</label>
                        <textarea class="form-control-admin w-100" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Total Price (PKR)</label>
                            <input type="number" step="0.01" class="form-control-admin w-100" name="total_price" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Duration (Days)</label>
                            <input type="number" class="form-control-admin w-100" name="duration_days" min="1" value="30" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Boost Tier</label>
                            <select class="form-control-admin w-100" name="boost_tier">
                                <option value="">No boost</option>
                                <option value="featured">Featured</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Max Halls (leave empty = unlimited)</label>
                            <input type="number" class="form-control-admin w-100" name="max_halls" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Max Services (leave empty = unlimited)</label>
                            <input type="number" class="form-control-admin w-100" name="max_listings" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block" style="font-size:12px;font-weight:600;">Status</label>
                            <div class="form-check form-switch" style="padding-top:6px;">
                                <input class="form-check-input" type="checkbox" name="is_active" id="packageActiveSwitch" checked>
                                <label class="form-check-label" for="packageActiveSwitch" style="font-size:12px;">Active (available for purchase)</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Hall Units <span style="font-weight:400;color:var(--text-muted);">(combo template)</span></label>
                        <select class="form-control-admin w-100" id="hallUnitSelect">
                            <option value="">Select a hall unit...</option>
                            @foreach($hallUnits as $unit)
                                <option value="hall_unit_{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }} (PKR {{ number_format($unit->base_price) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Services</label>
                        <select class="form-control-admin w-100" id="listingSelect">
                            <option value="">Select a service...</option>
                            @foreach($listings as $listing)
                                <option value="service_listing_{{ $listing->id }}">{{ $listing->title }} (PKR {{ number_format($listing->price) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="form-label" style="font-size:12px;font-weight:600;">Combo Template Items</label>
                    <div id="selectedItems" class="p-2" style="background:var(--cream);border-radius:8px;min-height:40px;">
                        <span class="text-muted" style="font-size:12px;">Template items the vendor will fill with their own items.</span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px;">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold" id="savePackageBtn">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = [];

const hallUnitMap = {};
document.querySelectorAll('#hallUnitSelect option').forEach(o => {
    const m = o.value.match(/^hall_unit_(\d+)$/);
    if (m) hallUnitMap[parseInt(m[1])] = o.text.split(' (PKR')[0];
});
const listingMap = {};
document.querySelectorAll('#listingSelect option').forEach(o => {
    const m = o.value.match(/^service_listing_(\d+)$/);
    if (m) listingMap[parseInt(m[1])] = o.text.split(' (PKR')[0];
});

function addSelectedItem(type, id) {
    const label = type === 'hall_unit' ? hallUnitMap[id] : listingMap[id];
    if (selectedItems.some(s => s.type === type && s.id === id)) {
        showToast('warning', 'Duplicate', label + ' is already in the package.');
        return;
    }
    selectedItems.push({ type, id, label });
    updateSelectedItems();
}

function updateSelectedItems() {
    if (selectedItems.length === 0) {
        document.getElementById('selectedItems').innerHTML = '<span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>';
        return;
    }
    let html = '<div class="d-flex flex-wrap gap-1">';
    selectedItems.forEach((item, i) => {
        const cls = item.type === 'hall_unit' ? 'background:var(--blue-grey);color:#fff;' : 'background:var(--green);color:#fff;';
        html += `<span style="display:inline-flex;align-items:center;gap:6px;${cls}padding:4px 10px;border-radius:14px;font-size:11px;font-weight:600;margin:2px;">
            <i class="ti ${item.type === 'hall_unit' ? 'ti-building' : 'ti-list-check'}"></i>
            ${item.label || ('#' + item.id)}
            <button type="button" onclick="selectedItems.splice(${i},1);updateSelectedItems();" style="background:none;border:none;color:inherit;cursor:pointer;line-height:1;padding:0;" title="Remove">&times;</button>
        </span>`;
    });
    html += '</div>';
    document.getElementById('selectedItems').innerHTML = html;
}

document.getElementById('hallUnitSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        addSelectedItem('hall_unit', parseInt(parts[2]));
        this.value = '';
    }
});

document.getElementById('listingSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        addSelectedItem('service_listing', parseInt(parts[2]));
        this.value = '';
    }
});

document.getElementById('packageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('savePackageBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader"></i> Saving...';

    const formData = new FormData(this);
    selectedItems.forEach((item, i) => {
        formData.append(`items[${i}][type]`, item.type);
        formData.append(`items[${i}][id]`, item.id);
    });

    fetch('{{ route("admin.packages.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Created', 'Package created successfully.');
            setTimeout(() => location.reload(), 700);
        } else {
            showToast('error', 'Error', data.message || 'Could not create package.');
            btn.disabled = false;
            btn.innerHTML = 'Save Package';
        }
    })
    .catch(() => {
        showToast('error', 'Error', 'Something went wrong. Please try again.');
        btn.disabled = false;
        btn.innerHTML = 'Save Package';
    });
});
</script>
@endpush
