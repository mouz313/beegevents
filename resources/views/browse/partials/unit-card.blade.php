@php
    $unitExtras = $unit->extraServices()->get();
    $amenityLabels = [
        'parking' => 'Parking', 'wheelchair' => 'Wheelchair Access', 'ac' => 'AC / Heating',
        'sound_system' => 'Sound System', 'generator' => 'Generator Backup', 'bridal_room' => 'Bridal Room',
        'stage' => 'Stage', 'washrooms' => 'Washrooms', 'waiting_area' => 'Waiting Area', 'dining_tables' => 'Dining Tables',
    ];
    $cateringModes = [];
    if (in_array($unit->catering_mode, ['internal', 'both'])) $cateringModes['internal'] = 'In-house catering';
    if (in_array($unit->catering_mode, ['external', 'both'])) $cateringModes['external'] = 'Outside / third-party';
    if (in_array($unit->catering_mode, ['none', 'both'])) $cateringModes['none'] = 'Self-arrange (no catering)';
@endphp
<div class="unit-card" data-unit-id="{{ $unit->id }}">
    <div class="d-flex align-items-start gap-3">
        <div class="unit-icon">
            <i class="ti ti-building"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="unit-name mb-0">{{ $unit->unit_name }}</h5>
                <span class="unit-status avail" id="status-{{ $unit->id }}">Available</span>
            </div>
            <div class="unit-detail mt-1">
                <span><i class="ti ti-users"></i> {{ $unit->min_capacity }}-{{ $unit->max_capacity }} guests</span>
                @if($unit->floor)
                    <span><i class="ti ti-layers"></i> {{ $unit->floor->floor_label }}</span>
                @endif
                <span><i class="ti ti-palette"></i> {{ ucfirst($unit->decor_type) }} decor</span>
                @if($unit->catering_mode)
                    <span><i class="ti ti-cooking-pot"></i> {{ ucwords(str_replace('_', ' ', $unit->catering_mode)) }} catering</span>
                @endif
                @if($unit->staff_male > 0 || $unit->staff_female > 0)
                    <span><i class="ti ti-users-group"></i> Staff: {{ $unit->staff_male }} M / {{ $unit->staff_female }} F</span>
                @endif
                @if($unit->amenities)
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @foreach($unit->amenities as $am)
                            <span class="amenity-chip">{{ $amenityLabels[$am] ?? ucfirst(str_replace('_', ' ', $am)) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="text-end flex-shrink-0">
            <div class="unit-price">PKR {{ number_format($unit->base_price) }}</div>
            @auth
                @if(auth()->user()->role == 'customer')
                    <button class="btn-gold btn-sm add-to-cart mt-2" data-type="hall_unit" data-id="{{ $unit->id }}">
                        <i class="ti ti-shopping-cart"></i> Add
                    </button>
                @endif
            @endauth
        </div>
    </div>

    @auth
        @if(auth()->user()->role == 'customer')
            <div class="unit-booking-options mt-3" style="border-top:1px dashed var(--border);padding-top:12px;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Event Time</label>
                        <div class="d-flex gap-3" style="font-size:13px;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="time_slot" id="slot_noon_{{ $unit->id }}" value="noon" checked>
                                <label class="form-check-label" for="slot_noon_{{ $unit->id }}">Noon</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="time_slot" id="slot_evening_{{ $unit->id }}" value="evening">
                                <label class="form-check-label" for="slot_evening_{{ $unit->id }}">Evening</label>
                            </div>
                        </div>
                    </div>

                    @if(isset($menuSets) && $menuSets->count() > 0)
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Menu Set</label>
                            <select class="form-select form-select-sm" name="menu_set_id" style="font-size:13px;border:1px solid var(--border);border-radius:8px;">
                                <option value="">No menu set</option>
                                @foreach($menuSets as $set)
                                    <option value="{{ $set->id }}" data-price="{{ $set->items->sum('price') }}">{{ $set->name }} (PKR {{ number_format($set->items->sum('price')) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($unitExtras->count() > 0)
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Extra Charges</label>
                            <div style="max-height:110px;overflow-y:auto;">
                                @foreach($unitExtras as $extra)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="extras[]" value="{{ $extra->id }}"
                                            data-name="{{ $extra->name }}" data-price="{{ $extra->price }}" data-price-unit="{{ $extra->price_unit }}"
                                            id="extra_{{ $unit->id }}_{{ $extra->id }}">
                                        <label class="form-check-label" for="extra_{{ $unit->id }}_{{ $extra->id }}" style="font-size:12px;">
                                            {{ $extra->name }} (+PKR {{ number_format($extra->price) }}/{{ str_replace('_', ' ', $extra->price_unit) }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Number of Guests</label>
                        <input type="number" class="form-control form-control-sm guests-input" min="1" value="{{ $unit->min_capacity }}"
                            data-min="{{ $unit->min_capacity }}" data-max="{{ $unit->max_capacity }}">
                        <small class="guests-hint" style="font-size:11px;color:var(--text-muted);">Capacity: {{ $unit->min_capacity }}–{{ $unit->max_capacity }} guests</small>
                    </div>
                    @if(count($cateringModes) > 0)
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Catering</label>
                            <select class="form-select form-select-sm catering-select" name="catering_mode" style="font-size:13px;border:1px solid var(--border);border-radius:8px;">
                                <option value="">Select option</option>
                                @foreach($cateringModes as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);">Estimate</label>
                        <div class="unit-mini-summary" data-unit-id="{{ $unit->id }}" data-base-price="{{ $unit->base_price }}" style="font-size:12px;color:var(--charcoal);background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
                            <div>Base: <strong>PKR {{ number_format($unit->base_price) }}</strong></div>
                            <div>Menu: <strong>+PKR 0</strong></div>
                            <div>Extras: <strong>+PKR 0</strong></div>
                            <div style="border-top:1px solid var(--border);margin-top:4px;padding-top:4px;font-weight:700;">Total: PKR {{ number_format($unit->base_price) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
