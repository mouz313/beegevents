@extends('layouts.app')

@section('title', 'Availability Calendar')

@section('content')
<div class="container">
    <h2 class="mb-4" style="color:var(--charcoal);font-weight:700;">Availability Calendar</h2>

    @if($hallUnits->count() > 0)
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3" style="border:1px solid var(--border);border-radius:14px;">
                    <div class="card-body p-4">
                        <h5 style="font-weight:600;margin-bottom:16px;">Block Dates &amp; Hours</h5>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Hall Unit</label>
                            <select class="form-select" id="unitSelect" style="border-color:var(--border);border-radius:8px;">
                                @foreach($hallUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Date</label>
                            <input type="date" class="form-control" id="datePicker" min="{{ date('Y-m-d') }}" style="border-color:var(--border);border-radius:8px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Hour (24h)</label>
                            <select class="form-select" id="hourSlot" style="border-color:var(--border);border-radius:8px;">
                                <option value="">All Day / No specific hour</option>
                                @for($h = 0; $h < 24; $h++)
                                    <option value="{{ $h }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad(($h+1) % 24, 2, '0', STR_PAD_LEFT) }}:00</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:600;">Notes (optional)</label>
                            <textarea class="form-control" id="blockNotes" rows="2" style="border-color:var(--border);border-radius:8px;font-size:13px;" placeholder="e.g. Maintenance"></textarea>
                        </div>
                        <button class="btn-gold w-100" id="blockDate" style="border:none;padding:10px;">Mark as Unavailable</button>
                    </div>
                </div>

                <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                    <div class="card-body p-4">
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
                <div class="card" style="border:1px solid var(--border);border-radius:14px;">
                    <div class="card-body p-4">
                        <div id="slotInfo" class="text-center text-muted" style="padding:40px 0;">
                            <i class="ti ti-calendar-search" style="font-size:36px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                            Select a unit and date to see availability.
                        </div>
                        <div id="slotDetails" style="display:none;">
                            <div id="slotList" style="margin-bottom:12px;"></div>
                            <button class="btn btn-outline-secondary btn-sm" id="unblockDate" style="display:none;">
                                <i class="ti ti-unlock"></i> Unblock selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card" style="border:1px solid var(--border);border-radius:14px;padding:40px;text-align:center;">
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
const hourSlot = document.getElementById('hourSlot');
const slotInfo = document.getElementById('slotInfo');
const slotDetails = document.getElementById('slotDetails');
const slotList = document.getElementById('slotList');
const unblockBtn = document.getElementById('unblockDate');
const blockNotes = document.getElementById('blockNotes');

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

        const hours = [null];
        for (let h = 0; h < 24; h++) hours.push(h);

        hours.forEach(h => {
            const key = h === null ? 'all' : h;
            const slotData = data.slots ? data.slots.find(s => s.time_slot == h) : null;
            const status = slotData ? slotData.status : 'available';
            const label = h === null ? 'All Day' : String(h).padStart(2,'0') + ':00 - ' + String((h+1)%24).padStart(2,'0') + ':00';
            const statusColors = {
                available: {bg:'#E6F7ED', color:'var(--green)', icon:'ti-circle-check-filled', text:'Available'},
                held: {bg:'var(--light-honey)', color:'var(--gold-dark)', icon:'ti-clock', text:'Held'},
                booked: {bg:'#FDE8E8', color:'var(--red)', icon:'ti-circle-off', text:'Booked'},
                blocked_offline: {bg:'#E8EEF1', color:'var(--blue-grey)', icon:'ti-lock', text:'Blocked'}
            };
            const c = statusColors[status] || statusColors.available;

            if (status === 'blocked_offline') hasBlocked = true;

            html += `<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:${c.bg};border-radius:8px;margin-bottom:4px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <i class="ti ${c.icon}" style="color:${c.color};font-size:16px;"></i>
                    <span style="font-size:13px;font-weight:500;">${label}</span>
                </div>
                <span style="font-size:12px;font-weight:600;color:${c.color};">${c.text}</span>
            </div>`;
        });

        slotList.innerHTML = html;
        unblockBtn.style.display = hasBlocked ? 'inline-block' : 'none';
    });
}

unitSelect.addEventListener('change', loadSlots);
datePicker.addEventListener('change', loadSlots);

document.getElementById('blockDate')?.addEventListener('click', function() {
    const unitId = unitSelect.value;
    const date = datePicker.value;
    const hour = hourSlot.value || null;
    const notes = blockNotes.value.trim() || null;
    if (!unitId || !date) return showToast('Select unit and date', 'warning');

    fetch('{{ route("vendor.calendar.block") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify({ hall_unit_id: unitId, date, time_slot: hour, notes })
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
    const hour = hourSlot.value || null;
    if (!confirm('Unblock this slot?')) return;

    fetch('{{ route("vendor.calendar.unblock") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify({ hall_unit_id: unitId, date, time_slot: hour })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Slot unblocked!', 'success');
            loadSlots();
        }
    });
});
</script>
@endpush
