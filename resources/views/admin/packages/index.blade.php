@extends('admin.layouts.master')

@section('title', 'Packages')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-box"></i> Packages</h5>
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
                            <div class="card-body">
                                <div class="d-flex justify-between align-center mb-2">
                                    <h5 style="font-size:15px;font-weight:600;">{{ $package->title }}</h5>
                                    <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);font-size:10px;">{{ $package->event_type }}</span>
                                </div>
                                <h4 style="color:var(--gold);font-weight:700;">PKR {{ number_format($package->total_price) }}</h4>
                                <p style="font-size:12px;color:var(--text-muted);">{{ $package->description }}</p>
                                <div class="d-flex justify-between align-center">
                                    <span style="font-size:11px;color:var(--text-muted);">{{ $package->packageItems->count() }} items</span>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm"><i class="ti ti-trash"></i></button>
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
                <p class="text-muted mt-2">No packages yet.</p>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Total Price</label>
                            <input type="number" step="0.01" class="form-control-admin w-100" name="total_price" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Event Type</label>
                            <select class="form-control-admin w-100" name="event_type">
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Hall Units</label>
                        <select class="form-control-admin w-100" id="hallUnitSelect">
                            <option value="">Select...</option>
                            @foreach($hallUnits as $unit)
                                <option value="hall_unit_{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }} (PKR {{ number_format($unit->base_price) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Services</label>
                        <select class="form-control-admin w-100" id="listingSelect">
                            <option value="">Select...</option>
                            @foreach($listings as $listing)
                                <option value="service_listing_{{ $listing->id }}">{{ $listing->title }} (PKR {{ number_format($listing->price) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="selectedItems" class="p-2" style="background:var(--cream);border-radius:8px;min-height:40px;">
                        <span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px;">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = [];

document.getElementById('hallUnitSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        selectedItems.push({ type: 'hall_unit', id: parseInt(parts[2]) });
        this.value = '';
        updateSelectedItems();
    }
});

document.getElementById('listingSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        selectedItems.push({ type: 'service_listing', id: parseInt(parts[2]) });
        this.value = '';
        updateSelectedItems();
    }
});

function updateSelectedItems() {
    let html = selectedItems.map((item, i) =>
        `<span style="display:inline-block;background:var(--gold);color:white;padding:2px 10px;border-radius:12px;font-size:11px;margin:2px;">${item.type} #${item.id} <button type="button" style="background:none;border:none;color:white;cursor:pointer;margin-left:4px;" onclick="selectedItems.splice(${i},1);updateSelectedItems();">&times;</button></span>`
    ).join('');
    document.getElementById('selectedItems').innerHTML = html || '<span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>';
}

document.getElementById('packageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
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
    .then(data => { if (data.success) location.reload(); });
});
</script>
@endpush
