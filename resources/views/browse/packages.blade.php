@extends('layouts.app')

@section('title', 'Package Deals')
@section('meta_description', 'Save with BeeG Events package deals — bundled hall, decor, catering, and photography packages for weddings, engagements, corporate events, and more in Pakistan.')

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="hero-badge"><i class="ti ti-gift"></i> Curated Bundles</div>
        <h1>Save with <span>Package Deals</span></h1>
        <p>Hand-picked combinations of hall, decor, catering, and more — at a bundled price.</p>
    </div>
</div>

<div class="container" style="margin-top:-20px;padding-bottom:60px;">
    @forelse($packages as $package)
        <div class="card mb-4" style="border:1px solid var(--border);border-radius:14px;overflow:hidden;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h3 style="color:var(--charcoal);font-weight:700;margin:0 0 4px;">{{ $package->title }}</h3>
                        <span style="display:inline-block;background:var(--light-honey);color:var(--gold-dark);padding:2px 10px;border-radius:6px;font-size:11px;font-weight:600;text-transform:capitalize;">{{ $package->event_type }}</span>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:24px;font-weight:800;color:var(--gold-dark);">PKR {{ number_format($package->total_price) }}</div>
                        <span style="font-size:12px;color:var(--text-muted);">total package price</span>
                    </div>
                </div>

                @if($package->description)
                    <p style="color:var(--text-muted);font-size:13px;margin:12px 0 0;">{{ $package->description }}</p>
                @endif

                @if($package->packageItems->count() > 0)
                    <hr style="border-color:var(--border);margin:16px 0;">
                    <h6 style="font-weight:600;color:var(--charcoal);margin-bottom:10px;">Includes</h6>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($package->packageItems as $item)
                            <span style="display:inline-flex;align-items:center;gap:4px;background:var(--cream);padding:4px 12px;border-radius:8px;font-size:12px;color:var(--charcoal);">
                                <i class="ti ti-circle-check-filled" style="color:var(--green);font-size:14px;"></i>
                                {{ $item->itemable ? ($item->itemable->name ?? $item->itemable->title ?? class_basename($item->itemable_type)) : class_basename($item->itemable_type) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('browse.index') }}?event_type={{ $package->event_type }}" class="btn-gold" style="padding:8px 20px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                        <i class="ti ti-search"></i> Browse Similar
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:60px 20px;">
            <div style="width:64px;height:64px;background:var(--cream);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <i class="ti ti-gift" style="font-size:32px;color:var(--text-muted);opacity:0.4;"></i>
            </div>
            <h4 style="color:var(--charcoal);font-weight:600;">No Packages Yet</h4>
            <p style="color:var(--text-muted);font-size:14px;">Check back soon for curated package deals.</p>
        </div>
    @endforelse
</div>
@endsection
