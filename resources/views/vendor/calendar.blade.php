@extends('vendor.layouts.master')

@section('title', 'Availability Calendar')

@section('content')
<div class="container vendor-page">
    <div class="vendor-page-head">
        <div>
            <h2 class="vendor-page-title">Availability Calendar</h2>
            <div class="vendor-page-sub">Block dates &amp; hours when your units are unavailable.</div>
        </div>
    </div>

    @if($hallUnits->count() > 0)
        <div class="row">
            <div class="col-md-4">
                <div class="profile-card mb-3">
                    <div class="card-body-custom">
                        <h5 style="font-weight:600;margin-bottom:16px;">Block Dates &amp; Hours</h5>
                        <div class="mb-3">
                            <label class="form-label-custom">Hall Unit</label>
                            <select class="form-select input-custom" id="unitSelect">
                                @foreach($hallUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Date</label>
                            <input type="date" class="form-control input-custom" id="datePicker" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Block Type</label>
                            <select class="form-select input-custom" id="blockType">
                                <option value="all_day">All Day (noon + evening)</option>
                                <option value="noon">Noon slot only</option>
                                <option value="evening">Evening slot only</option>
                                <option value="hour">Specific Hour</option>
                            </select>
                        </div>
                        <div class="mb-3" id="hourField" style="display:none;">
                            <label class="form-label-custom">Hour (24h)</label>
                            <select class="form-select input-custom" id="hourSlot">
                                @for($h = 0; $h < 24; $h++)
                                    <option value="{{ $h }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad(($h+1) % 24, 2, '0', STR_PAD_LEFT) }}:00</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Notes (optional)</label>
                            <textarea class="form-control input-custom" id="blockNotes" rows="2" placeholder="e.g. Maintenance"></textarea>
                        </div>
                        <button class="btn-gold w-100" id="blockDate" style="border:none;padding:10px;">Mark as Unavailable</button>
                    </div>
                </div>

                <div class="profile-card mb-3">
                    <div class="card-body-custom">
                        <h5 style="font-weight:600;margin-bottom:4px;">Add Manual Booking</h5>
                        <p style="font-size:11px;color:var(--text-muted);margin-bottom:16px;">Record an offline/on-site booking — the slot is locked so customers can't double-book it.</p>
                        <div class="mb-3">
                            <label class="form-label-custom">Hall Unit</label>
                            <select class="form-select input-custom" id="mbUnit">
                                @foreach($hallUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom">Date</label>
                                <input type="date" class="form-control input-custom" id="mbDate" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Slot</label>
                                <select class="form-select input-custom" id="mbSlot">
                                    <option value="all_day">All Day</option>
                                    <option value="noon">Noon</option>
                                    <option value="evening">Evening</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Event Type</label>
                            <select class="form-select input-custom" id="mbEventType">
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                                <option value="home">Home</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom">Client Name</label>
                                <input type="text" class="form-control input-custom" id="mbClient" placeholder="e.g. Ahmed Ali">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Client Phone</label>
                                <input type="text" class="form-control input-custom" id="mbPhone" placeholder="+92 3xx xxxxxxx">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Amount (PKR)</label>
                            <input type="number" class="form-control input-custom" id="mbAmount" min="0" step="0.01" placeholder="e.g. 500000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Notes (optional)</label>
                            <textarea class="form-control input-custom" id="mbNotes" rows="2" placeholder="Any details about this booking"></textarea>
                        </div>
                        <button class="btn-gold w-100" id="addManualBooking" style="border:none;padding:10px;">
                            <i class="ti ti-plus"></i> Add Manual Booking
                        </button>
                    </div>
                </div>

                <div class="profile-card">
                    <div class="card-body-custom">
                        <h5 style="font-weight:600;margin-bottom:12px;">Legend</h5>
                        <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;">
                            <span><span style="display:inline-block;width:12px;height:12px;background:#E6F7ED;border-radius:3px;margin-right:6px;"></span> Available</span>
                            <span><span style="display:inline-block;width:12px;height:12px;background:var(--light-honey);border-radius:3px;margin-right:6px;"></span> Held (pending)</span>
                            <span><span style="display:inline-block;width:12px;height:12px;background:#FDE8E8;border-radius:3px;margin-right:6px;"></span> Booked</span>
                            <span><span style="display:inline-block;width:12px;height:12px;background:#E8EEF1;border-radius:3px;margin-right:6px;"></span> Blocked by you</span>
                        </div>
                        <hr style="border-color:var(--border);margin:12px 0;">
                        <p style="font-size:11px;color:var(--text-muted);margin:0;">
                            <i class="ti ti-info-circle"></i> "Held" slots auto-release after 24 hours if not confirmed. Hourly granularity available.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="profile-card">
                    <div class="card-body-custom">
                        <div id="slotInfo" class="text-center text-muted" style="padding:40px 0;">
                            <i class="ti ti-calendar-search" style="font-size:36px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                            Select a unit and date to see availability.
                        </div>
                        <div id="slotDetails" style="display:none;">
                            <div id="slotList" style="margin-bottom:12px;"></div>
                            <button class="btn-outline-gold btn-sm" id="unblockDate" style="display:none;padding:6px 14px;font-size:12px;">
                                <i class="ti ti-unlock"></i> Unblock selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="profile-card" style="padding:40px;text-align:center;">
            <i class="ti ti-building-off" style="font-size:36px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:8px;"></i>
            <p style="color:var(--text-muted);">No hall units found. <a href="{{ route('vendor.halls.index') }}" style="color:var(--gold-dark);">Add halls and units first.</a></p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const unitSelect = document.getElementById('unitSelect');
const datePicker = document.getElementById('datePicker');
const blockTypeSelect = document.getElementById('blockType');
const hourSlot = document.getElementById('hourSlot');
const hourField = document.getElementById('hourField');
const slotInfo = document.getElementById('slotInfo');
const slotDetails = document.getElementById('slotDetails');
const slotList = document.getElementById('slotList');
const unblockBtn = document.getElementById('unblockDate');
const blockNotes = document.getElementById('blockNotes');

blockTypeSelect.addEventListener('change', function () {
    hourField.style.display = this.value === 'hour' ? 'block' : 'none';
});

function loadSlots() {
    const unitId = unitSelect.value;
    const date = datePicker.value;
    if (!unitId || !date) return;

    fetch('{{ route("vendor.calendar.slots") }}?hall_unit_id=' + unitId + '&date=' + date, {
        headers: {'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        slotInfo.style.display = 'none';
        slotDetails.style.display = 'block';
        let html = '';
        let hasBlocked = false;

        // Build rows: All Day, Noon, Evening, then 24 hourly rows
        const rows = [
            { key: 'date_full', label: 'All Day', match: s => s.time_slot == null && s.slot_type == null && s.status === 'blocked_offline' },
            { key: 'noon', label: 'Noon', match: s => s.slot_type === 'noon' },
            { key: 'evening', label: 'Evening', match: s => s.slot_type === 'evening' }
        ];
        for (let h = 0; h < 24; h++) {
            rows.push({ key: 'h_' + h, label: String(h).padStart(2,'0') + ':00 - ' + String((h+1)%24).padStart(2,'0') + ':00', match: s => s.time_slot == h });
        }

        const statusColors = {
            available: {bg:'#E6F7ED', color:'var(--green)', icon:'ti-circle-check-filled', text:'Available'},
            held: {bg:'var(--light-honey)', color:'var(--gold-dark)', icon:'ti-clock', text:'Held'},
            booked: {bg:'#FDE8E8', color:'var(--red)', icon:'ti-circle-off', text:'Booked'},
            blocked_offline: {bg:'#E8EEF1', color:'var(--blue-grey)', icon:'ti-lock', text:'Blocked'}
        };

        // All Day <=> a blocked slot with slot_type null + time_slot null
        const allDaySlot = (data.slots || []).find(s => s.time_slot == null && s.slot_type == null);

        rows.slice(1).forEach(row => {
            const slotData = (data.slots || []).find(row.match);
            // Noon/Evening inherit an All-Day block too
            const status = slotData ? slotData.status
                : ((row.key === 'noon' || row.key === 'evening') && allDaySlot ? allDaySlot.status : 'available');
            const c = statusColors[status] || statusColors.available;
            if (status === 'blocked_offline') {
                hasBlocked = true;
                // only count visible noon/evening for unblock bookkeeping
            }
            html += `<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:${c.bg};border-radius:8px;margin-bottom:4px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <i class="ti ${c.icon}" style="color:${c.color};font-size:16px;"></i>
                    <span style="font-size:13px;font-weight:500;">${row.label}</span>
                </div>
                <span style="font-size:12px;font-weight:600;color:${c.color};">${c.text}</span>
            </div>`;
        });

        slotList.innerHTML = html;
        unblockBtn.style.display = hasBlocked ? 'inline-block' : 'none';
        window.__calendarAllDayBlocked = !!allDaySlot;
    });
}

unitSelect.addEventListener('change', loadSlots);
datePicker.addEventListener('change', loadSlots);

document.getElementById('blockDate')?.addEventListener('click', function() {
    const unitId = unitSelect.value;
    const date = datePicker.value;
    const blockType = blockTypeSelect.value;
    const notes = blockNotes.value.trim() || null;
    if (!unitId || !date) return showToast('Select unit and date', 'warning');

    const payload = { hall_unit_id: unitId, date, notes };
    if (blockType === 'hour') {
        payload.time_slot = parseInt(hourSlot.value, 10);
        payload.slot_type = null;
    } else {
        payload.slot_type = blockType; // all_day | noon | evening
        payload.time_slot = null;
    }

    fetch('{{ route("vendor.calendar.block") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Slot blocked!', 'success');
            blockNotes.value = '';
            loadSlots();
        } else {
            showToast(data.message || 'Failed to block', 'error');
        }
    });
});

unblockBtn?.addEventListener('click', function() {
    const unitId = unitSelect.value;
    const date = datePicker.value;
    const blockType = blockTypeSelect.value;
    if (!confirm('Unblock selected slot?')) return;

    const payload = { hall_unit_id: unitId, date };
    if (blockType === 'hour') {
        payload.time_slot = typeof(hourSlot.value) === 'undefined' || hourSlot.value === '' ? null : parseInt(hourSlot.value, 10);
        payload.slot_type = null;
    } else {
        payload.slot_type = blockType;
        payload.time_slot = null;
    }

    fetch('{{ route("vendor.calendar.unblock") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Slot unblocked!', 'success');
            loadSlots();
        }
    });
});

document.getElementById('addManualBooking')?.addEventListener('click', function() {
    const payload = {
        hall_unit_id: document.getElementById('mbUnit').value,
        date: document.getElementById('mbDate').value,
        slot_type: document.getElementById('mbSlot').value,
        event_type: document.getElementById('mbEventType').value,
        client_name: document.getElementById('mbClient').value.trim(),
        client_phone: document.getElementById('mbPhone').value.trim() || null,
        amount: document.getElementById('mbAmount').value,
        notes: document.getElementById('mbNotes').value.trim() || null
    };

    if (!payload.hall_unit_id || !payload.date) {
        return showToast('Select unit and date', 'warning');
    }
    if (!payload.client_name) {
        return showToast('Enter client name', 'warning');
    }

    const btn = this;
    btn.disabled = true;
    fetch('{{ route("vendor.manual-bookings.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            showToast('Manual booking added!', 'success');
            document.getElementById('mbClient').value = '';
            document.getElementById('mbPhone').value = '';
            document.getElementById('mbAmount').value = '';
            document.getElementById('mbNotes').value = '';
            loadSlots();
        } else {
            showToast(data.message || 'Failed to add booking', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        showToast('Something went wrong', 'error');
    });
});
</script>
@endpush
