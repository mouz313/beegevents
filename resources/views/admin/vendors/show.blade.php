@extends('admin.layouts.master')

@section('title', 'Vendor Detail')

@section('content')
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
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.vendors.edit', $vendorProfile) }}" class="btn btn-gold btn-sm">
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
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($floor->hallUnits as $unit)
                                                                <tr>
                                                                    <td><strong>{{ $unit->unit_name }}</strong></td>
                                                                    <td>{{ $unit->min_capacity ?? '—' }}–{{ $unit->max_capacity ?? '—' }}</td>
                                                                    <td>PKR {{ number_format($unit->base_price) }}</td>
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
