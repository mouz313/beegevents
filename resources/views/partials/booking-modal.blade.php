<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bm-modal">
            <div class="modal-header bm-header">
                <h5 class="modal-title" id="bookingModalLabel">
                    <span class="bm-header-icon"><i class="ti ti-calendar-plus"></i></span>
                    Book Your Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">

                {{-- Step 1: choose booking type --}}
                <div id="bmStep1" class="p-4">
                    <p class="bm-subtitle mb-4">Choose how you'd like to book your event.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" class="bm-card w-100" data-bm-card="custom">
                                <span class="bm-card-step">1</span>
                                <i class="ti ti-list-details"></i>
                                <strong>Custom</strong>
                                <span>Pick services, get a quote, then discuss</span>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="bm-card w-100" data-bm-card="budget">
                                <span class="bm-card-step">2</span>
                                <i class="ti ti-wallet"></i>
                                <strong>Budget</strong>
                                <span>Enter amount &amp; guests, we suggest a bundle</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Custom --}}
                <div id="bmStepCustom" class="d-none p-4">
                    <button type="button" class="btn btn-sm bm-btn-outline mb-3" data-bm-back><i class="ti ti-arrow-left"></i> Back</button>
                    <h6 class="bm-step-title">Choose a date, time &amp; services</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <input type="date" id="bmCustomDate" class="form-control bm-input">
                        </div>
                        <div class="col-md-6">
                            <select id="bmCustomTimeSlot" class="form-select bm-input">
                                <option value="noon">Noon</option>
                                <option value="evening">Evening</option>
                            </select>
                        </div>
                    </div>
                    <div id="bmHallSection" style="display:none;margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <strong style="font-weight:700;color:var(--charcoal);font-size:14px;"><i class="ti ti-building"></i> Hall Unit</strong>
                            <button type="button" class="btn btn-sm bm-btn-outline" id="bmHallReviewBtn" style="padding:3px 10px;font-size:11px;">
                                <i class="ti ti-eye"></i> Review hall services
                            </button>
                        </div>
                        <div id="bmHallReview" style="display:none;background:var(--cream);border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px;font-size:12px;color:var(--text-muted);"></div>
                        <select id="bmHallUnit" class="form-select bm-input mb-2"></select>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label bm-label">Number of Guests</label>
                                <input type="number" id="bmHallGuests" class="form-control bm-input" min="1" placeholder="e.g. 200">
                                <div id="bmHallGuestsHint" style="font-size:11px;color:var(--text-muted);"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label bm-label">Catering</label>
                                <select id="bmHallCatering" class="form-select bm-input">
                                    <option value="">Select catering option</option>
                                </select>
                            </div>
                        </div>
                        <div id="bmHallMenuWrap" style="display:none;margin-bottom:8px;">
                            <label class="form-label bm-label">Menu Set (optional)</label>
                            <select id="bmHallMenu" class="form-select bm-input"></select>
                        </div>
                        <div id="bmHallExtras" style="display:none;">
                            <label class="form-label bm-label">Extras (optional)</label>
                            <div id="bmHallExtrasList" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
                        </div>
                        <div id="bmHallSummary" style="display:none;margin-top:12px;background:var(--cream);border:1px solid var(--border);border-radius:10px;padding:12px;font-size:13px;"></div>
                    </div>
                    <div id="bmCategoryList">
                        <div class="bm-loading">Loading services...</div>
                    </div>
                    <div id="bmAgreement" class="mt-3" style="border:1px solid var(--border);border-radius:10px;padding:12px;background:var(--cream);">
                        <label class="form-check" style="margin:0;font-size:13px;">
                            <input type="checkbox" class="form-check-input" id="bmAgreeCheck" style="margin-right:6px;">
                            I agree to the <a href="#" id="bmAgreeToggle" style="color:var(--gold-dark);font-weight:600;" onclick="return false;">BeeG Events Booking Agreement</a>
                        </label>
                        <div id="bmAgreeText" style="display:none;margin-top:10px;font-size:12px;color:var(--text-muted);line-height:1.6;border-top:1px dashed var(--border);padding-top:10px;">
                            <strong style="color:var(--charcoal);">Booking Agreement</strong><br>
                            <ul style="margin:6px 0 0 16px;padding:0;">
                                <li>Your booking is a request until the vendor confirms and an advance payment is received.</li>
                                <li>Prices shown are estimates; the final agreed price is confirmed by the vendor.</li>
                                <li>Held slots auto-release after 24 hours if not confirmed.</li>
                                <li>Cancellations are subject to the platform's refund policy.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn bm-btn-outline flex-fill" id="bmCustomAddCart">Add Selected to Cart</button>
                        <button type="button" class="btn bm-btn-gold flex-fill" id="bmCustomQuote">Get Quote</button>
                    </div>
                </div>

                {{-- Step 2: Budget --}}
                <div id="bmStepBudget" class="d-none p-4">
                    <button type="button" class="btn btn-sm bm-btn-outline mb-3" data-bm-back><i class="ti ti-arrow-left"></i> Back</button>
                    <h6 class="bm-step-title">Find a bundle in your budget</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label bm-label">Budget (PKR)</label>
                            <input type="number" id="bmBudgetAmount" class="form-control bm-input" min="1" placeholder="e.g. 500000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label bm-label">Guests</label>
                            <input type="number" id="bmBudgetGuests" class="form-control bm-input" min="1" placeholder="e.g. 200">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label bm-label">City</label>
                            <select id="bmBudgetCity" class="form-select bm-input">
                                <option value="">Any</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label bm-label">Event Type</label>
                            <select id="bmBudgetEventType" class="form-select bm-input">
                                <option value="">Any</option>
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                                <option value="home">Home</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label bm-label">Event Time</label>
                            <select id="bmBudgetTimeSlot" class="form-select bm-input">
                                <option value="">Any</option>
                                <option value="noon">Noon</option>
                                <option value="evening">Evening</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label bm-label">Event Date</label>
                            <input type="date" id="bmBudgetDate" class="form-control bm-input">
                        </div>
                    </div>
                    <button type="button" class="btn bm-btn-gold w-100 mb-3" id="bmBudgetFind">Find Bundle</button>
                    <div id="bmBudgetResult"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.bm-modal { border:none; border-radius:16px; overflow:hidden; box-shadow:0 24px 60px rgba(43,38,32,0.35); }

.bm-header { background:linear-gradient(135deg, var(--charcoal) 0%, #3D342D 100%); border-bottom:none; }
.bm-header .modal-title { color:var(--white); font-weight:800; display:flex; align-items:center; gap:10px; font-size:17px; }
.bm-header-icon { width:36px; height:36px; background:var(--gold); border-radius:9px; display:inline-flex; align-items:center; justify-content:center; color:var(--charcoal); flex-shrink:0; }

.bm-subtitle { font-size:13px; color:var(--text-muted); }
.bm-step-title { font-weight:700; color:var(--charcoal); font-size:15px; margin-bottom:10px; }
.bm-label { font-size:12px; font-weight:600; color:var(--charcoal); }

.bm-input { border:1.5px solid var(--border); border-radius:10px; font-size:14px; }
.bm-input:focus { border-color:var(--gold); box-shadow:0 0 0 0.2rem rgba(212,160,23,0.15); }

.bm-loading { text-align:center; padding:28px 0; color:var(--text-muted); font-size:13px; }

.bm-card { position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; padding:26px 14px; border:1.5px solid var(--border); border-radius:14px; background:var(--cream); text-align:center; cursor:pointer; transition:all 0.2s ease; height:100%; }
.bm-card .bm-card-step { position:absolute; top:10px; left:12px; width:22px; height:22px; border-radius:50%; background:var(--charcoal); color:var(--gold); font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; }
.bm-card i { font-size:32px; color:var(--gold-dark); }
.bm-card strong { font-size:15px; color:var(--charcoal); }
.bm-card span { font-size:12px; color:var(--text-muted); }
.bm-card:hover, .bm-card:focus-visible { border-color:var(--gold); background:var(--light-honey); transform:translateY(-2px); box-shadow:0 8px 20px rgba(212,160,23,0.18); }

.bm-btn-gold { background:var(--gold); color:var(--charcoal); font-weight:700; border:none; border-radius:10px; }
.bm-btn-gold:hover { background:var(--gold-dark); color:var(--white); }
.bm-btn-outline { border:1.5px solid var(--gold); color:var(--gold-dark); font-weight:600; background:transparent; border-radius:10px; }
.bm-btn-outline:hover { background:var(--gold); color:var(--charcoal); }

.bm-package-item { display:flex; align-items:center; gap:12px; padding:14px; background:var(--cream); border:1px solid var(--border); border-radius:12px; margin-bottom:10px; }
.bm-package-item .bm-pkg-info { flex:1; min-width:0; }
.bm-package-item .bm-pkg-title { font-weight:700; font-size:14px; color:var(--charcoal); }
.bm-package-item .bm-pkg-meta { font-size:12px; color:var(--text-muted); }
.bm-pkg-price { font-weight:800; color:var(--gold-dark); font-size:14px; }

.bm-service-item { display:flex; align-items:center; gap:10px; padding:12px 14px; border-bottom:1px solid var(--border); }
.bm-service-item:last-child { border-bottom:none; }

.bm-accordion .accordion-button { color:var(--text-primary); background:var(--white); font-weight:600; font-size:14px; }
.bm-accordion .accordion-button:not(.collapsed) { background:var(--light-honey); color:var(--charcoal); box-shadow:none; }
.bm-accordion .accordion-button:focus { box-shadow:0 0 0 0.2rem rgba(212,160,23,0.15); }
.bm-accordion .accordion-item { border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:10px; }
.bm-badge { background:var(--gold); color:var(--charcoal); font-weight:600; }

.bm-bundle-box { border:1.5px solid var(--gold); border-radius:14px; padding:16px; background:var(--cream); }
.bm-bundle-box .bm-bundle-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.bm-bundle-box .bm-bundle-total { font-weight:800; color:var(--gold-dark); }

.bm-tag { font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:0.3px; }
.bm-tag.within_budget { background:#E6F7ED; color:var(--green); }
.bm-tag.slightly_above { background:#FFF3E0; color:var(--amber); }
.bm-tag.services_only { background:#E8EEF1; color:var(--blue-grey); }
.bm-tag.over_budget { background:#FDECEC; color:var(--red); }
</style>

@push('scripts')
<script>
(function () {
    var modalEl = document.getElementById('bookingModal');
    if (!modalEl) return;

    var isAuth = @json(auth()->check());
    var CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var bmHallId = null;
    var bmOptions = null;
    var bmHallUnitData = null;
    var bmSelected = {};

    var steps = {
        base: document.getElementById('bmStep1'),
        custom: document.getElementById('bmStepCustom'),
        budget: document.getElementById('bmStepBudget')
    };

    function showStep(name) {
        Object.keys(steps).forEach(function (k) {
            steps[k].classList.toggle('d-none', k !== name);
        });
    }

    function requireAuth() {
        if (isAuth) return true;
        showToast('Please login to continue booking.', 'warning');
        setTimeout(function () { window.location.href = '/login'; }, 900);
        return false;
    }

    function jsonHeaders(extra) {
        return Object.assign({
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF
        }, extra || {});
    }

    function handle401(res) {
        if (res.status === 401) {
            showToast('Your session expired. Please login again.', 'warning');
            setTimeout(function () { window.location.href = '/login'; }, 900);
            return true;
        }
        return false;
    }

    function loadOptions() {
        if (bmOptions) { renderCategoryList(); return; }
        var url = '{{ route("booking.options") }}';
        if (bmHallId) url += '?hall_id=' + bmHallId;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                bmOptions = data;
                renderCategoryList();
                populateCities();
            })
            .catch(function () {
                showToast('Could not load booking options.', 'error');
            });
    }

    function populateCities() {
        var sel = document.getElementById('bmBudgetCity');
        if (!sel || !bmOptions.cities || !bmOptions.cities.length) return;
        bmOptions.cities.forEach(function (c) {
            var opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            sel.appendChild(opt);
        });
    }

    /* ---------- Custom tab ---------- */
    function renderCategoryList() {
        var list = document.getElementById('bmCategoryList');
        if (!list) return;
        if (!bmOptions.categories.length) {
            list.innerHTML = '<div class="alert alert-info mb-0">No services available yet.</div>';
            return;
        }
        var html = '<div class="accordion bm-accordion" id="bmAccordion">';
        bmOptions.categories.forEach(function (cat, ci) {
            if (!cat.listings.length) return;
            html += '<div class="accordion-item">' +
                '<h2 class="accordion-header"><button class="accordion-button ' + (ci === 0 ? '' : 'collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#bmCat' + cat.id + '">' + cat.name + ' <span class="badge bm-badge ms-2">' + cat.listings.length + '</span></button></h2>' +
                '<div id="bmCat' + cat.id + '" class="accordion-collapse collapse ' + (ci === 0 ? 'show' : '') + '" data-bs-parent="#bmAccordion">' +
                '<div class="accordion-body p-0">';
            cat.listings.forEach(function (l) {
                html += '<div class="bm-service-item">' +
                    '<input type="checkbox" class="form-check-input bm-service-check" data-id="' + l.id + '" data-price="' + l.price + '">' +
                    '<div style="flex:1;">' +
                    '<div style="font-weight:600;font-size:13px;color:var(--charcoal);">' + l.title + '</div>' +
                    '<div style="font-size:11px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> ' + (l.vendor || '') + (l.city ? ' &middot; ' + l.city : '') + '</div>' +
                    '</div>' +
                    '<div style="font-size:13px;font-weight:700;color:var(--gold-dark);white-space:nowrap;">PKR ' + Number(l.price).toLocaleString() + '</div>' +
                    '</div>';
            });
            html += '</div></div></div>';
        });
        html += '</div>';
        list.innerHTML = html;
    }

    function selectedServices() {
        return Array.from(document.querySelectorAll('.bm-service-check:checked')).map(function (cb) {
            return { id: cb.dataset.id, price: parseFloat(cb.dataset.price) };
        });
    }

    /* ---------- Hall custom booking (from hall "Book Now") ---------- */
    function loadHallUnits() {
        var section = document.getElementById('bmHallSection');
        var hallSelect = document.getElementById('bmHallUnit');
        if (!section || !hallSelect || !bmHallId) { return; }
        section.style.display = 'block';
        hallSelect.innerHTML = '<option value="">Loading units...</option>';

        fetch('{{ route("booking.hall-options") }}?hall_id=' + bmHallId, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                bmHallUnitData = data;
                if (!data.units || !data.units.length) {
                    hallSelect.innerHTML = '<option value="">No units available</option>';
                    return;
                }
                hallSelect.innerHTML = '<option value="">Select a hall unit</option>' + data.units.map(function (u) {
                    return '<option value="' + u.id + '">' + u.name + ' (PKR ' + Number(u.base_price).toLocaleString() + ', ' + u.capacity + ' guests)</option>';
                }).join('');
            })
            .catch(function () {
                hallSelect.innerHTML = '<option value="">Could not load units</option>';
            });
    }

    function currentHallUnit() {
        if (!bmHallUnitData) return null;
        var hallSelect = document.getElementById('bmHallUnit');
        if (!hallSelect || !hallSelect.value) return null;
        var units = bmHallUnitData.units || [];
        return units.find(function (u) { return u.id == hallSelect.value; }) || null;
    }

    function cateringOptionsFor(unit) {
        var labels = {
            internal: 'In-house catering',
            external: 'Outside / third-party catering',
            both: 'Both (in-house or outside)',
            none: 'No catering (self-arrange)'
        };
        var modes = [];
        if (unit.catering_mode === 'internal' || unit.catering_mode === 'both') modes.push('internal');
        if (unit.catering_mode === 'external' || unit.catering_mode === 'both') modes.push('external');
        if (unit.catering_mode === 'none' || unit.catering_mode === 'both') modes.push('none');
        return modes.length ? modes : ['none'];
    }

    function populateHallUnit() {
        var unit = currentHallUnit();
        var menuWrap = document.getElementById('bmHallMenuWrap');
        var menuSelect = document.getElementById('bmHallMenu');
        var extrasWrap = document.getElementById('bmHallExtras');
        var extrasList = document.getElementById('bmHallExtrasList');
        var cateringSelect = document.getElementById('bmHallCatering');
        var guestsInput = document.getElementById('bmHallGuests');
        var guestsHint = document.getElementById('bmHallGuestsHint');
        var summary = document.getElementById('bmHallSummary');

        if (!unit) {
            menuWrap.style.display = 'none';
            extrasWrap.style.display = 'none';
            summary.style.display = 'none';
            cateringSelect.innerHTML = '<option value="">Select catering option</option>';
            guestsInput.value = '';
            guestsHint.textContent = '';
            document.getElementById('bmHallReview').style.display = 'none';
            return;
        }

        // Menu sets
        menuSelect.innerHTML = '<option value="">No menu set</option>' + (unit.menu_sets || []).map(function (s) {
            return '<option value="' + s.id + '">' + s.name + ' (+PKR ' + Number(s.price).toLocaleString() + ')</option>';
        }).join('');
        menuWrap.style.display = (unit.menu_sets && unit.menu_sets.length) ? 'block' : 'none';

        // Extras as toggleable pill cards
        extrasWrap.style.display = (unit.extras && unit.extras.length) ? 'block' : 'none';
        extrasList.innerHTML = (unit.extras || []).map(function (e) {
            return '<button type="button" class="bm-extra-pill" data-id="' + e.id + '" data-price="' + e.price + '" data-name="' + e.name.replace(/"/g, '&quot;') + '" style="border:1.5px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;background:var(--white);cursor:pointer;transition:all .15s ease;">' +
                '<i class="ti ti-circle" style="color:#c9c2b4;margin-right:4px;font-size:11px;"></i> ' + e.name + ' (+PKR ' + Number(e.price).toLocaleString() + ')</button>';
        }).join('');

        // Catering options from unit
        var catOptions = cateringOptionsFor(unit).map(function (m) {
            return '<option value="' + m + '">' + cateringLabel(m) + '</option>';
        }).join('');
        cateringSelect.innerHTML = '<option value="">Select catering option</option>' + catOptions;

        // Guests hint (capacity)
        guestsHint.textContent = 'Capacity: ' + unit.min_capacity + ' – ' + unit.max_capacity + ' guests';

        // Review hall services
        var rev = document.getElementById('bmHallReview');
        var lines = [];
        if (bmHallUnitData.hall && bmHallUnitData.hall.description) lines.push('<div><strong>Description:</strong> ' + bmHallUnitData.hall.description + '</div>');
        if (unit.amenities && unit.amenities.length) lines.push('<div><strong>Amenities:</strong> ' + unit.amenities.join(', ') + '</div>');
        if (unit.catering_mode) lines.push('<div><strong>Catering:</strong> ' + cateringLabel(unit.catering_mode) + '</div>');
        if (unit.food_service_style) lines.push('<div><strong>Food service:</strong> ' + foodServiceLabel(unit.food_service_style) + '</div>');
        if (unit.staff_male || unit.staff_female) lines.push('<div><strong>Staff:</strong> ' + unit.staff_male + ' male / ' + unit.staff_female + ' female</div>');
        if (unit.decor_type) lines.push('<div><strong>Decor:</strong> ' + unit.decor_type.charAt(0).toUpperCase() + unit.decor_type.slice(1) + '</div>');
        rev.innerHTML = lines.join('');

        updateHallSummary();
    }

    function cateringLabel(m) {
        var labels = {
            internal: 'In-house catering',
            external: 'Outside / third-party catering',
            both: 'Both (in-house or outside)',
            none: 'No catering (self-arrange)'
        };
        return labels[m] || m.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function foodServiceLabel(m) {
        var labels = {
            static_place: 'Static place / buffet stations',
            on_table: 'On-table service',
            both: 'Both on-table and static place'
        };
        return labels[m] || m.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function updateHallSummary() {
        var unit = currentHallUnit();
        var summary = document.getElementById('bmHallSummary');
        if (!unit) { summary.style.display = 'none'; return; }

        var menuPrice = 0;
        var menuName = null;
        var menuSel = document.getElementById('bmHallMenu');
        if (menuSel && menuSel.value) {
            var ms = (unit.menu_sets || []).find(function (s) { return s.id == menuSel.value; });
            if (ms) { menuPrice = ms.price; menuName = ms.name; }
        }

        var extrasSel = Array.from(document.querySelectorAll('.bm-extra-pill.selected'));
        var extrasTotal = extrasSel.reduce(function (s, p) { return s + parseFloat(p.dataset.price); }, 0);

        var grand = (parseFloat(unit.base_price) || 0) + menuPrice + extrasTotal;

        var rows = '';
        rows += '<div style="display:flex;justify-content:space-between;"><span>Hall unit (' + unit.name + ')</span><strong>PKR ' + Number(unit.base_price).toLocaleString() + '</strong></div>';
        if (menuName) rows += '<div style="display:flex;justify-content:space-between;"><span>Menu: ' + menuName + '</span><strong>+PKR ' + Number(menuPrice).toLocaleString() + '</strong></div>';
        if (extrasSel.length) {
            extrasSel.forEach(function (p) {
                rows += '<div style="display:flex;justify-content:space-between;"><span>· ' + p.dataset.name + '</span><span>+PKR ' + Number(p.dataset.price).toLocaleString() + '</span></div>';
            });
        }
        rows += '<div style="display:flex;justify-content:space-between;border-top:1px solid var(--border);margin-top:6px;padding-top:6px;font-weight:700;color:var(--charcoal);"><span>Estimated total</span><span>PKR ' + Number(grand).toLocaleString() + '</span></div>';

        summary.innerHTML = rows;
        summary.style.display = 'block';
        validateHallGuests();
    }

    function validateHallGuests() {
        var unit = currentHallUnit();
        var guestsInput = document.getElementById('bmHallGuests');
        var hint = document.getElementById('bmHallGuestsHint');
        if (!unit) return true;
        var v = parseInt(guestsInput.value, 10);
        if (!guestsInput.value || isNaN(v)) {
            hint.style.color = 'var(--amber)';
            hint.textContent = 'Capacity: ' + unit.min_capacity + ' – ' + unit.max_capacity + ' guests' + (guestsInput.value ? ' — enter a valid number.' : '');
            return guestsInput.value ? false : true;
        }
        if (v < unit.min_capacity || v > unit.max_capacity) {
            hint.style.color = 'var(--red)';
            hint.textContent = 'Please enter ' + unit.min_capacity + ' – ' + unit.max_capacity + ' guests for this unit.';
            return false;
        }
        hint.style.color = 'var(--text-muted)';
        hint.textContent = 'Capacity: ' + unit.min_capacity + ' – ' + unit.max_capacity + ' guests';
        return true;
    }

    function addHallUnitToCart(date) {
        var hallSelect = document.getElementById('bmHallUnit');
        var timeSlot = document.getElementById('bmCustomTimeSlot').value;
        if (!hallSelect || !hallSelect.value) return;

        var body = new URLSearchParams();
        body.append('type', 'hall_unit');
        body.append('id', hallSelect.value);
        body.append('date', date);
        body.append('time_slot', timeSlot);
        var guests = document.getElementById('bmHallGuests').value;
        if (guests) body.append('guests', guests);
        var catering = document.getElementById('bmHallCatering').value;
        if (catering) body.append('catering_mode', catering);
        var menu = document.getElementById('bmHallMenu').value;
        if (menu) body.append('menu_set_id', menu);
        Array.from(document.querySelectorAll('.bm-extra-pill.selected')).forEach(function (p) {
            body.append('extras[' + p.dataset.id + '][id]', p.dataset.id);
        });

        return fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        }).then(function (r) { return r.json(); });
    }

    function hallBookingValid() {
        var unit = currentHallUnit();
        if (unit && !validateHallGuests()) return false;
        if (!document.getElementById('bmAgreeCheck').checked) {
            showToast('Please accept the booking agreement to continue.', 'warning');
            return false;
        }
        return true;
    }

    document.getElementById('bmHallUnit')?.addEventListener('change', populateHallUnit);
    document.getElementById('bmHallMenu')?.addEventListener('change', updateHallSummary);
    document.getElementById('bmHallGuests')?.addEventListener('input', validateHallGuests);
    document.getElementById('bmHallCatering')?.addEventListener('change', function () {});
    document.addEventListener('click', function (e) {
        if (e.target.classList && e.target.classList.contains('bm-extra-pill')) {
            e.target.classList.toggle('selected');
            var icon = e.target.querySelector('.ti');
            if (icon) {
                icon.className = e.target.classList.contains('selected') ? 'ti ti-circle-check' : 'ti ti-circle';
                icon.style.color = e.target.classList.contains('selected') ? 'var(--green)' : '#c9c2b4';
                e.target.style.borderColor = e.target.classList.contains('selected') ? 'var(--green)' : 'var(--border)';
                e.target.style.background = e.target.classList.contains('selected') ? '#E6F7ED' : 'var(--white)';
            }
            updateHallSummary();
        }
    });

    document.getElementById('bmHallReviewBtn')?.addEventListener('click', function () {
        var rev = document.getElementById('bmHallReview');
        rev.style.display = rev.style.display === 'none' ? 'block' : 'none';
    });
    document.getElementById('bmAgreeToggle')?.addEventListener('click', function () {
        var t = document.getElementById('bmAgreeText');
        t.style.display = t.style.display === 'none' ? 'block' : 'none';
    });

    function addServiceToCart(id, date) {
        var body = new URLSearchParams();
        body.append('type', 'service_listing');
        body.append('id', id);
        body.append('date', date);
        body.append('time_slot', document.getElementById('bmCustomTimeSlot').value);
        return fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        }).then(function (r) { return r.json(); });
    }

    function customAdd() {
        var date = document.getElementById('bmCustomDate').value;
        if (!date) { showToast('Please choose an event date.', 'warning'); return; }
        if (!requireAuth()) return;

        var hallSelected = document.getElementById('bmHallUnit') && document.getElementById('bmHallUnit').value;

        var jobs = [];
        if (hallSelected) {
            if (!hallBookingValid()) return;
            jobs.push(addHallUnitToCart(date));
        } else if (!document.getElementById('bmAgreeCheck').checked) {
            showToast('Please accept the booking agreement to continue.', 'warning');
            return;
        }
        jobs = jobs.concat(selectedServices().map(function (i) { return addServiceToCart(i.id, date); }));

        var hasAnything = hallSelected || selectedServices().length > 0;
        if (!hasAnything) { showToast('Please select at least one service or hall unit.', 'warning'); return; }

        Promise.all(jobs)
            .then(function (results) {
                if (results.some(function (r) { return r && r.success; })) {
                    showToast('Added to cart!');
                } else {
                    showToast('Could not add items to cart.', 'error');
                }
            })
            .catch(function () {
                showToast('Could not add items to cart. Please try again.', 'error');
            });
    }

    function customQuote() {
        var date = document.getElementById('bmCustomDate').value;
        if (!date) { showToast('Please choose an event date.', 'warning'); return; }
        if (!requireAuth()) return;

        var hallSelected = document.getElementById('bmHallUnit') && document.getElementById('bmHallUnit').value;
        var jobs = [];
        if (hallSelected) {
            if (!hallBookingValid()) return;
            jobs.push(addHallUnitToCart(date));
        } else if (!document.getElementById('bmAgreeCheck').checked) {
            showToast('Please accept the booking agreement to continue.', 'warning');
            return;
        }
        jobs = jobs.concat(selectedServices().map(function (i) { return addServiceToCart(i.id, date); }));

        var hasAnything = hallSelected || selectedServices().length > 0;
        if (!hasAnything) { showToast('Please select at least one service or hall unit.', 'warning'); return; }

        Promise.all(jobs)
            .then(function (results) {
                if (results.some(function (r) { return r && r.success; })) {
                    window.location.href = '{{ route("customer.checkout") }}';
                } else {
                    showToast('Could not add items to cart.', 'error');
                }
            })
            .catch(function () {
                showToast('Could not add items to cart. Please try again.', 'error');
            });
    }

    /* ---------- Budget tab ---------- */
    document.getElementById('bmBudgetFind').addEventListener('click', function () {
        var budget = document.getElementById('bmBudgetAmount').value;
        var guests = document.getElementById('bmBudgetGuests').value;
        var eventType = document.getElementById('bmBudgetEventType').value;
        var city = document.getElementById('bmBudgetCity').value;
        var timeSlot = document.getElementById('bmBudgetTimeSlot').value;
        var date = document.getElementById('bmBudgetDate').value;

        if (!budget || !guests) { showToast('Please enter budget and guests.', 'warning'); return; }

        var body = new URLSearchParams();
        body.append('budget', budget);
        body.append('guest_count', guests);
        if (eventType) body.append('event_type', eventType);
        if (city) body.append('city', city);
        if (timeSlot) body.append('time_slot', timeSlot);
        if (date) body.append('date', date);

        var resultBox = document.getElementById('bmBudgetResult');
        resultBox.innerHTML = '<div class="bm-loading">Finding the best bundle...</div>';

        fetch('{{ route("booking.budget") }}', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; }); })
        .then(function (res) {
            if (res.data && res.data.bundle) {
                renderBundle(res.data.bundle);
            } else {
                showToast((res.data && res.data.message) || 'Could not find a bundle. Please try again.', 'error');
                resultBox.innerHTML = '';
            }
        })
        .catch(function () {
            showToast('Something went wrong. Please try again.', 'error');
            resultBox.innerHTML = '';
        });
    });

    function renderBundle(b) {
        var box = document.getElementById('bmBudgetResult');
        if (!b || b.status === 'none' || (!b.hall_unit && (!b.services || !b.services.length))) {
            box.innerHTML = '<div class="alert alert-warning mb-0">No bundle found within this budget. Try increasing your budget or guest details.</div>';
            return;
        }

        var tags = { within_budget: 'Within Budget', slightly_above: 'Slightly Above Budget', services_only: 'Services Only', over_budget: 'Over Budget' };
        var tagLabel = tags[b.tag] || b.tag;

        var html = '<div class="bm-bundle-box">' +
            '<div class="bm-bundle-head">' +
            '<strong style="color:var(--charcoal);"><i class="ti ti-package"></i> Suggested Bundle</strong>' +
            '<span class="bm-tag ' + b.tag + '">' + tagLabel + '</span>' +
            '</div>';

        if (b.hall_unit) {
            var unit = b.hall_unit;
            var hallName = (unit.hall && unit.hall.name) ? unit.hall.name : 'Hall';
            var hallCity = (unit.hall && unit.hall.vendor_profile && unit.hall.vendor_profile.city) ? unit.hall.vendor_profile.city : '';
            html += '<div class="bm-service-item" style="padding-left:0;padding-right:0;"><div style="flex:1;">' +
                '<div style="font-weight:600;font-size:13px;color:var(--charcoal);"><i class="ti ti-building"></i> ' + hallName + ' - ' + (unit.unit_name || '') + '</div>' +
                '<div style="font-size:11px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> Capacity: ' + unit.min_capacity + '-' + unit.max_capacity + ' guests' + (hallCity ? ' &middot; ' + hallCity : '') + '</div>' +
                '</div><div style="font-weight:700;color:var(--gold-dark);white-space:nowrap;">PKR ' + Number(unit.base_price).toLocaleString() + '</div></div>';
        }

        (b.services || []).forEach(function (s) {
            var sCity = (s.vendor_profile && s.vendor_profile.city) ? s.vendor_profile.city : '';
            html += '<div class="bm-service-item" style="padding-left:0;padding-right:0;"><div style="flex:1;font-size:13px;color:var(--charcoal);">' + s.title +
                (sCity ? '<div style="font-size:11px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> ' + sCity + '</div>' : '') +
                '</div>' +
                '<div style="font-size:13px;font-weight:700;color:var(--gold-dark);white-space:nowrap;">PKR ' + Number(s.price).toLocaleString() + '</div></div>';
        });

        html += '<div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--border);">' +
            '<div style="font-size:12px;color:var(--text-muted);">Budget: PKR ' + Number(b.budget).toLocaleString() + '</div>' +
            '<div class="bm-bundle-total">Total: PKR ' + Number(b.total).toLocaleString() + '</div>' +
            '</div>' +
            '<button type="button" class="btn bm-btn-gold w-100 mt-3" id="bmAddBundle">Add Bundle to Cart</button>' +
            '</div>';

        box.innerHTML = html;
        document.getElementById('bmAddBundle').addEventListener('click', function () { addBundle(b); });
    }

    function addBundle(b) {
        var date = document.getElementById('bmCustomDate').value;
        if (!date) {
            showToast('Please choose an event date on the Custom tab first.', 'warning');
            return;
        }
        if (!requireAuth()) return;

        var items = [];
        if (b.hall_unit) items.push({ type: 'hall_unit', id: b.hall_unit.id });
        (b.services || []).forEach(function (s) { items.push({ type: 'service_listing', id: s.id }); });

        var timeSlot = document.getElementById('bmBudgetTimeSlot').value
            || document.getElementById('bmCustomTimeSlot').value;

        var body = new URLSearchParams();
        body.append('date', date);
        if (timeSlot) body.append('time_slot', timeSlot);
        items.forEach(function (it, i) {
            body.append('items[' + i + '][type]', it.type);
            body.append('items[' + i + '][id]', it.id);
        });

        fetch('{{ route("customer.cart.add-bundle") }}', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; }); })
        .then(function (res) {
            if (handle401(res)) return;
            if (res.data.success) {
                bootstrap.Modal.getInstance(modalEl).hide();
                showToast('Bundle added to cart! (' + res.data.cart_count + ' item(s))');
                setTimeout(function () { window.location.href = '{{ route("customer.cart") }}'; }, 1200);
            } else {
                showToast(res.data.message || 'Could not add bundle to cart.', 'error');
            }
        })
        .catch(function () {
            showToast('Could not add bundle to cart. Please try again.', 'error');
        });
    }

    /* ---------- navigation ---------- */
    modalEl.addEventListener('show.bs.modal', function (e) {
        var trigger = e.relatedTarget;
        bmHallId = trigger ? (trigger.dataset.bookingHall || null) : null;
        var mode = trigger ? (trigger.dataset.bookingMode || null) : null;
        bmSelected = {};
        showStep(mode || 'base');

        // Pre-fill date / time slot when the trigger carries them (e.g. hall detail from search)
        var bookingDate = trigger ? (trigger.dataset.bookingDate || null) : null;
        var bookingSlot = trigger ? (trigger.dataset.bookingTimeSlot || null) : null;
        if (bookingDate) {
            var custDate = document.getElementById('bmCustomDate');
            if (custDate) custDate.value = bookingDate;
        }
        if (bookingSlot) {
            var custSlot = document.getElementById('bmCustomTimeSlot');
            if (custSlot) custSlot.value = bookingSlot;
        }

        if (mode) {
            loadOptions();
            if (mode === 'custom' && bmHallId) {
                loadHallUnits();
            }
            if (mode === 'budget') {
                document.getElementById('bmBudgetResult').innerHTML = '';
            }
        }
    });

    modalEl.querySelectorAll('[data-bm-card]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showStep(btn.dataset.bmCard);
            loadOptions();
        });
    });

    modalEl.querySelectorAll('[data-bm-back]').forEach(function (btn) {
        btn.addEventListener('click', function () { showStep('base'); });
    });

    document.getElementById('bmCustomAddCart').addEventListener('click', customAdd);
    document.getElementById('bmCustomQuote').addEventListener('click', customQuote);

    function tomorrowIso() {
        var d = new Date();
        d.setDate(d.getDate() + 1);
        return d.toISOString().split('T')[0];
    }
    var min = tomorrowIso();
    document.getElementById('bmCustomDate').min = min;
    document.getElementById('bmBudgetDate').min = min;
})();
</script>
@endpush
