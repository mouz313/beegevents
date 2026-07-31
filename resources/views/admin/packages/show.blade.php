@extends('admin.layouts.master')

@section('title', 'Package Detail')

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-box"></i> {{ $package->title }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-gold btn-sm">Edit</a>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-ghost btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <h4 style="color:var(--gold);font-weight:700;font-size:24px;">PKR {{ number_format($package->total_price) }}</h4>
                <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);font-size:11px;">{{ ucfirst($package->event_type) }}</span>
                @if($package->description)
                    <p class="mt-3" style="color:var(--text-muted);font-size:13px;">{{ $package->description }}</p>
                @endif
                <div class="mt-3 text-muted" style="font-size:12px;">
                    Created {{ $package->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-list"></i> Package Items ({{ $package->packageItems->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @if($package->packageItems->count() > 0)
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($package->packageItems as $item)
                                <tr>
                                    <td>
                                        <span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);font-size:11px;">
                                            {{ class_basename($item->itemable_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->itemable)
                                            @if(class_basename($item->itemable_type) == 'HallUnit')
                                                <strong>{{ $item->itemable->unit_name ?? 'Unit #'.$item->itemable_id }}</strong>
                                                @if($item->itemable->hall)
                                                    <br><span class="text-muted" style="font-size:11px;">{{ $item->itemable->hall->name }}</span>
                                                @endif
                                            @else
                                                <strong>{{ $item->itemable->title ?? 'Listing #'.$item->itemable_id }}</strong>
                                            @endif
                                        @else
                                            <span class="text-muted">Item #{{ $item->itemable_id }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No items in this package.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
