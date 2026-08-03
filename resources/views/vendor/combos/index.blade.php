@extends('vendor.layouts.master')

@section('title', 'My Combo')

@section('content')
<div class="vd-page-head">
    <div>
        <h2 class="vd-page-title"><i class="ti ti-package"></i> My Combo</h2>
        <p class="vd-page-sub">Fill each combo template with your own halls &amp; services. Customers can book the finished combo as one bundle.</p>
    </div>
</div>

@if($combos->isEmpty())
    <div class="admin-card">
        <div class="card-body text-center py-5">
            <i class="ti ti-package-off" style="font-size:40px;display:block;margin-bottom:12px;color:var(--text-muted);"></i>
            <h5 style="font-weight:700;color:var(--charcoal);">No combo available</h5>
            <p style="font-size:13px;color:var(--text-muted);">Buy a package to unlock a combo template your customers can book.</p>
            <a href="{{ route('vendor.packages.index') }}" class="btn bm-btn-gold mt-2">View Packages</a>
        </div>
    </div>
@endif

@foreach($combos as $combo)
    @php
        $slots = $combo->templateSlots();
        $filledBySlot = $combo->items->keyBy('slot_index');
        $filledCount = $combo->items->count();
    @endphp
    <div class="admin-card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                <h5 style="margin:0;"><i class="ti ti-package"></i> {{ $combo->package->title ?? 'Combo' }}</h5>
                <div>
                    @if($slots->count() > 0 && $filledCount >= $slots->count())
                        <span class="status-badge status-active">Complete</span>
                    @else
                        <span class="status-badge status-pending">{{ $filledCount }}/{{ $slots->count() }} filled</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:16px;">
                Customers pay the combined price of the items you choose below.
                @if($combo->packagePurchase && $combo->packagePurchase->ends_at)
                    Package valid until {{ $combo->packagePurchase->ends_at->format('M d, Y') }}.
                @endif
            </p>

            @if($slots->count() === 0)
                <div class="alert alert-warning mb-0" style="font-size:13px;border-radius:10px;">
                    This package has no combo template items yet. Ask the admin to add template items.
                </div>
            @else
                <form method="POST" action="{{ route('vendor.combos.save', $combo) }}">
                    @csrf
                    @foreach($slots as $slotIndex => $slot)
                        @php
                            $isHall = $slot->itemable_type === 'App\Models\HallUnit';
                            $templateItem = $slot->itemable;
                            $currentItem = $filledBySlot->get($slotIndex);
                            $currentId = $currentItem?->itemable_id;
                            if ($isHall) {
                                $options = \App\Models\HallUnit::whereHas('hall', fn ($q) => $q->where('vendor_profile_id', $vendor->id))
                                    ->with('hall')->orderBy('unit_name')->get();
                            } else {
                                $options = $vendor->serviceListings()->orderBy('title')->get();
                            }
                        @endphp
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">
                                {{ $slotIndex + 1 }}. {{ $templateItem->title ?? $templateItem->name ?? $templateItem->unit_name ?? 'Template item' }}
                                <span style="font-weight:400;text-transform:none;">
                                    <span class="status-badge status-pending" style="font-size:10px;padding:2px 8px;">
                                        {{ $isHall ? 'Hall unit' : 'Service' }}
                                    </span>
                                </span>
                            </label>
                            <select name="items[{{ $slotIndex }}]" class="form-select">
                                <option value="">— Choose one of your {{ $isHall ? 'hall units' : 'services' }} —</option>
                                @foreach($options as $option)
                                    <option value="{{ $option->id }}" @selected($currentId == $option->id)>
                                        @if($isHall)
                                            {{ $option->hall->name }} — {{ $option->unit_name }} (PKR {{ number_format($option->base_price) }})
                                        @else
                                            {{ $option->title }} (PKR {{ number_format($option->price) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($options->isEmpty())
                                <div style="font-size:11px;color:var(--amber);">
                                    You don't have any {{ $isHall ? 'hall units' : 'services' }} yet.
                                    <a href="{{ $isHall ? route('vendor.halls.index') : route('vendor.listings.index') }}">Add one</a>.
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3" style="border-top:1px solid var(--border);padding-top:16px;">
                        <div>
                            <span style="font-size:12px;color:var(--text-muted);">Combo total (sum of chosen items)</span>
                            <div style="font-size:22px;font-weight:800;color:var(--gold-dark);">PKR {{ number_format($combo->total_price) }}</div>
                        </div>
                        <button type="submit" class="btn bm-btn-gold" style="padding:10px 28px;">
                            <i class="ti ti-device-floppy"></i> Save Combo
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endforeach
@endsection
