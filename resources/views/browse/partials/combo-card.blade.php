@php
    $combo = $profile ? $profile->activeCombo() : null;
@endphp
@if($combo)
    <div class="detail-section mb-4">
        <div class="section-title mb-3"><i class="ti ti-package"></i> Combo Package</div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">
            Book this {{ strtolower($combo->package->title ?? 'combo') }} from {{ $profile->business_name }} in one request.
        </p>
        <div>
            @foreach($combo->items as $comboItem)
                @php
                    $item = $comboItem->itemable;
                    $isHall = $comboItem->itemable_type === 'App\Models\HallUnit';
                    $name = $isHall
                        ? ($item->hall->name ?? 'Hall').' — '.($item->unit_name ?? 'Unit')
                        : ($item->title ?? 'Service');
                    $price = (float) ($item->price ?? $item->base_price ?? 0);
                @endphp
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed var(--border);">
                    <span style="font-size:13px;color:var(--charcoal);">
                        <i class="ti {{ $isHall ? 'ti-building' : 'ti-tool' }}" style="color:var(--gold-dark);"></i>
                        {{ $name }}
                    </span>
                    <strong style="font-size:12px;color:var(--gold-dark);white-space:nowrap;">PKR {{ number_format($price) }}</strong>
                </div>
            @endforeach
            <div class="d-flex justify-content-between align-items-center mt-3">
                <strong style="font-size:14px;">Total</strong>
                <strong style="font-size:18px;color:var(--gold-dark);">PKR {{ number_format($combo->total_price) }}</strong>
            </div>
        </div>
        <a href="{{ route('combos.show', $combo) }}" class="btn-gold w-100 mt-3 text-center d-block" style="padding:12px;border-radius:10px;font-weight:600;">
            <i class="ti ti-calendar-plus"></i> Book This Combo
        </a>
    </div>
@endif
