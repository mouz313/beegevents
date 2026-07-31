<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title" id="bookingModalLabel" style="font-weight:700;color:var(--charcoal);">Book Your Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">

                {{-- Step 1: choose booking type --}}
                <div id="bmStep1" class="p-4">
                    <p class="text-muted" style="font-size:13px;">Choose how you'd like to book your event.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button type="button" class="bm-card bm-card-package w-100" data-bm-card="package">
                                <i class="ti ti-gift"></i>
                                <strong>Package</strong>
                                <span>Ready-made packages by hall owners</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="bm-card w-100" data-bm-card="custom">
                                <i class="ti ti-list-details"></i>
                                <strong>Custom</strong>
                                <span>Pick services, get a quote, then discuss</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="bm-card w-100" data-bm-card="budget">
                                <i class="ti ti-wallet"></i>
                                <strong>Budget</strong>
                                <span>Enter amount &amp; guests, we suggest a bundle</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Package --}}
                <div id="bmStepPackage" class="d-none p-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" data-bm-back><i class="ti ti-arrow-left"></i> Back</button>
                    <h6 style="font-weight:700;">Choose a date &amp; package</h6>
                    <input type="date" id="bmPackageDate" class="form-control mb-3">
                    <div id="bmPackageList">
                        <div class="text-center py-4 text-muted" style="font-size:13px;">Loading packages...</div>
                    </div>
                </div>

                {{-- Step 2: Custom --}}
                <div id="bmStepCustom" class="d-none p-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" data-bm-back><i class="ti ti-arrow-left"></i> Back</button>
                    <h6 style="font-weight:700;">Choose a date &amp; services</h6>
                    <input type="date" id="bmCustomDate" class="form-control mb-3">
                    <div id="bmCategoryList">
                        <div class="text-center py-4 text-muted" style="font-size:13px;">Loading services...</div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary flex-fill" id="bmCustomAddCart">Add Selected to Cart</button>
                        <button type="button" class="btn btn-gold flex-fill" id="bmCustomQuote" style="color:var(--white);background:var(--gold-dark);border:none;">Get Quote</button>
                    </div>
                </div>

                {{-- Step 2: Budget --}}
                <div id="bmStepBudget" class="d-none p-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" data-bm-back><i class="ti ti-arrow-left"></i> Back</button>
                    <h6 style="font-weight:700;">Find a bundle in your budget</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Budget (PKR)</label>
                            <input type="number" id="bmBudgetAmount" class="form-control" min="1" placeholder="e.g. 500000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Guests</label>
                            <input type="number" id="bmBudgetGuests" class="form-control" min="1" placeholder="e.g. 200">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Event Type</label>
                            <select id="bmBudgetEventType" class="form-select">
                                <option value="">Any</option>
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                                <option value="home">Home</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mb-3" id="bmBudgetFind">Find Bundle</button>
                    <div id="bmBudgetResult"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.bm-card {
    display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
    padding:24px 12px;border:2px solid var(--border);border-radius:14px;background:var(--white);
    text-align:center;cursor:pointer;transition:all 0.2s ease;height:100%;
}
.bm-card i { font-size:30px;color:var(--gold-dark); }
.bm-card strong { font-size:15px;color:var(--charcoal); }
.bm-card span { font-size:12px;color:var(--text-muted); }
.bm-card:hover { border-color:var(--gold);transform:translateY(-2px);box-shadow:0 6px 16px rgba(212,160,23,0.15); }
.bm-package-item { display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--border);border-radius:10px;margin-bottom:8px; }
.bm-package-item .bm-pkg-info { flex:1; }
.bm-package-item .bm-pkg-title { font-weight:600;font-size:14px;color:var(--charcoal); }
.bm-package-item .bm-pkg-meta { font-size:12px;color:var(--text-muted); }
.bm-service-item { display:flex;align-items:center;gap:10px;padding:10px;border-bottom:1px solid var(--border); }
.bm-service-item:last-child { border-bottom:none; }
.bm-tag { font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.bm-tag.within_budget { background:#E6F7ED;color:var(--green); }
.bm-tag.slightly_above { background:#FFF3E0;color:var(--amber); }
.bm-tag.services_only { background:#E8EEF1;color:var(--blue-grey); }
.bm-tag.over_budget { background:#FDECEC;color:var(--red); }
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
    var bmSelected = {};

    var steps = {
        base: document.getElementById('bmStep1'),
        package: document.getElementById('bmStepPackage'),
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
        if (bmOptions) { renderPackageList(); renderCategoryList(); return; }
        var url = '{{ route("booking.options") }}';
        if (bmHallId) url += '?hall_id=' + bmHallId;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                bmOptions = data;
                renderPackageList();
                renderCategoryList();
            })
            .catch(function () {
                showToast('Could not load booking options.', 'error');
            });
    }

    /* ---------- Package tab ---------- */
    function renderPackageList() {
        var list = document.getElementById('bmPackageList');
        if (!list) return;
        if (!bmOptions.packages.length) {
            list.innerHTML = '<div class="alert alert-info mb-0">No packages available yet.</div>';
            return;
        }
        var html = '';
        bmOptions.packages.forEach(function (p) {
            html += '<div class="bm-package-item">' +
                '<div class="bm-pkg-info">' +
                '<div class="bm-pkg-title">' + p.title + '</div>' +
                '<div class="bm-pkg-meta">' + (p.vendor ? p.vendor.business_name : 'BeeG Events') +
                (p.event_type ? ' &middot; ' + p.event_type : '') +
                ' &middot; ' + p.items.length + ' item(s)</div>' +
                (p.description ? '<div class="bm-pkg-meta">' + p.description + '</div>' : '') +
                '</div>' +
                '<div class="text-end" style="white-space:nowrap;">' +
                '<div style="font-weight:700;color:var(--gold-dark);">PKR ' + Number(p.total_price).toLocaleString() + '</div>' +
                '<button type="button" class="btn btn-sm btn-primary mt-1" data-book-package="' + p.id + '">Book</button>' +
                '</div></div>';
        });
        list.innerHTML = html;
    }

    function bookPackage(pkgId) {
        var date = document.getElementById('bmPackageDate').value;
        if (!date) { showToast('Please choose an event date.', 'warning'); return; }
        if (!requireAuth()) return;

        var pkg = bmOptions.packages.find(function (p) { return p.id == pkgId; });
        var body = new URLSearchParams();
        body.append('event_date', date);
        body.append('event_type', pkg ? pkg.event_type : 'other');

        fetch('/customer/packages/' + pkgId + '/book', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; }); })
        .then(function (res) {
            if (handle401(res)) return;
            if (res.data.success) {
                bootstrap.Modal.getInstance(modalEl).hide();
                showToast('Booking requested! #' + res.data.booking_id);
                setTimeout(function () { window.location.href = '/customer/bookings/' + res.data.booking_id; }, 1200);
            } else {
                showToast(res.data.message || 'Booking failed.', 'error');
            }
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
        var html = '<div class="accordion" id="bmAccordion">';
        bmOptions.categories.forEach(function (cat, ci) {
            if (!cat.listings.length) return;
            html += '<div class="accordion-item">' +
                '<h2 class="accordion-header"><button class="accordion-button ' + (ci === 0 ? '' : 'collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#bmCat' + cat.id + '">' + cat.name + ' <span class="badge bg-secondary ms-2">' + cat.listings.length + '</span></button></h2>' +
                '<div id="bmCat' + cat.id + '" class="accordion-collapse collapse ' + (ci === 0 ? 'show' : '') + '" data-bs-parent="#bmAccordion">' +
                '<div class="accordion-body p-0">';
            cat.listings.forEach(function (l) {
                html += '<div class="bm-service-item">' +
                    '<input type="checkbox" class="form-check-input bm-service-check" data-id="' + l.id + '" data-price="' + l.price + '">' +
                    '<div style="flex:1;">' +
                    '<div style="font-weight:600;font-size:13px;">' + l.title + '</div>' +
                    '<div style="font-size:11px;color:var(--text-muted);">' + (l.vendor || '') + '</div>' +
                    '</div>' +
                    '<div style="font-size:13px;font-weight:600;white-space:nowrap;">PKR ' + Number(l.price).toLocaleString() + '</div>' +
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

    function addServiceToCart(id, date) {
        var body = new URLSearchParams();
        body.append('type', 'service_listing');
        body.append('id', id);
        body.append('date', date);
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
        var items = selectedServices();
        if (!items.length) { showToast('Please select at least one service.', 'warning'); return; }

        Promise.all(items.map(function (i) { return addServiceToCart(i.id, date); }))
            .then(function (results) {
                if (results.some(function (r) { return r.success; })) {
                    showToast('Added to cart!');
                } else {
                    showToast('Could not add services to cart.', 'error');
                }
            });
    }

    function customQuote() {
        var date = document.getElementById('bmCustomDate').value;
        if (!date) { showToast('Please choose an event date.', 'warning'); return; }
        if (!requireAuth()) return;
        var items = selectedServices();
        if (!items.length) { showToast('Please select at least one service.', 'warning'); return; }

        Promise.all(items.map(function (i) { return addServiceToCart(i.id, date); }))
            .then(function (results) {
                if (results.some(function (r) { return r.success; })) {
                    window.location.href = '{{ route("customer.checkout") }}';
                } else {
                    showToast('Could not add services to cart.', 'error');
                }
            });
    }

    /* ---------- Budget tab ---------- */
    document.getElementById('bmBudgetFind').addEventListener('click', function () {
        var budget = document.getElementById('bmBudgetAmount').value;
        var guests = document.getElementById('bmBudgetGuests').value;
        var eventType = document.getElementById('bmBudgetEventType').value;

        if (!budget || !guests) { showToast('Please enter budget and guests.', 'warning'); return; }

        var body = new URLSearchParams();
        body.append('budget', budget);
        body.append('guest_count', guests);
        if (eventType) body.append('event_type', eventType);

        var resultBox = document.getElementById('bmBudgetResult');
        resultBox.innerHTML = '<div class="text-center py-4 text-muted" style="font-size:13px;">Finding the best bundle...</div>';

        fetch('{{ route("booking.budget") }}', {
            method: 'POST',
            headers: jsonHeaders(),
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            renderBundle(data.bundle);
        });
    });

    function renderBundle(b) {
        var box = document.getElementById('bmBudgetResult');
        if (!b || b.status === 'none' || (!b.hall_unit && (!b.services || !b.services.length))) {
            box.innerHTML = '<div class="alert alert-warning mb-0">No bundle found within this budget. Try increasing your budget or guest details.</div>';
            return;
        }

        var tags = { within_budget: 'Within Budget', slightly_above: 'Slightly Above Budget', services_only: 'Services Only' };
        var tagLabel = tags[b.tag] || b.tag;

        var html = '<div class="border rounded-3 p-3" style="border-color:var(--gold)!important;">' +
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<strong style="color:var(--charcoal);">Suggested Bundle</strong>' +
            '<span class="bm-tag ' + b.tag + '">' + tagLabel + '</span>' +
            '</div>';

        if (b.hall_unit) {
            var unit = b.hall_unit;
            var hallName = (unit.hall && unit.hall.name) ? unit.hall.name : 'Hall';
            html += '<div class="bm-service-item"><div style="flex:1;">' +
                '<div style="font-weight:600;font-size:13px;"><i class="ti ti-building"></i> ' + hallName + ' - ' + (unit.unit_name || '') + '</div>' +
                '<div style="font-size:11px;color:var(--text-muted);">Capacity: ' + unit.min_capacity + '-' + unit.max_capacity + ' guests</div>' +
                '</div><div style="font-weight:600;white-space:nowrap;">PKR ' + Number(unit.base_price).toLocaleString() + '</div></div>';
        }

        (b.services || []).forEach(function (s) {
            html += '<div class="bm-service-item"><div style="flex:1;font-size:13px;">' + s.title + '</div>' +
                '<div style="font-size:13px;font-weight:600;white-space:nowrap;">PKR ' + Number(s.price).toLocaleString() + '</div></div>';
        });

        html += '<div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--border);">' +
            '<div style="font-size:12px;color:var(--text-muted);">Budget: PKR ' + Number(b.budget).toLocaleString() + '</div>' +
            '<div style="font-weight:800;color:var(--gold-dark);">Total: PKR ' + Number(b.total).toLocaleString() + '</div>' +
            '</div>' +
            '<button type="button" class="btn btn-gold w-100 mt-3" id="bmAddBundle" style="color:var(--white);background:var(--gold-dark);border:none;">Add Bundle to Cart</button>' +
            '</div>';

        box.innerHTML = html;
        document.getElementById('bmAddBundle').addEventListener('click', function () { addBundle(b); });
    }

    function addBundle(b) {
        var date = document.getElementById('bmCustomDate').value || document.getElementById('bmPackageDate').value;
        if (!date) {
            showToast('Please choose an event date on the Package or Custom tab first.', 'warning');
            return;
        }
        if (!requireAuth()) return;

        var items = [];
        if (b.hall_unit) items.push({ type: 'hall_unit', id: b.hall_unit.id });
        (b.services || []).forEach(function (s) { items.push({ type: 'service_listing', id: s.id }); });

        var body = new URLSearchParams();
        body.append('date', date);
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
        });
    }

    /* ---------- navigation ---------- */
    modalEl.addEventListener('show.bs.modal', function (e) {
        var trigger = e.relatedTarget;
        bmHallId = trigger ? (trigger.dataset.bookingHall || null) : null;
        var mode = trigger ? (trigger.dataset.bookingMode || null) : null;
        bmSelected = {};
        showStep(mode || 'base');
        if (mode) {
            loadOptions();
            if (mode === 'budget') {
                document.getElementById('bmBudgetResult').innerHTML = '';
            }
        }
    });

    modalEl.querySelectorAll('[data-bm-card]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showStep('bm' + btn.dataset.bmCard.charAt(0).toUpperCase() + btn.dataset.bmCard.slice(1));
            loadOptions();
        });
    });

    modalEl.querySelectorAll('[data-bm-back]').forEach(function (btn) {
        btn.addEventListener('click', function () { showStep('base'); });
    });

    document.getElementById('bmPackageList').addEventListener('click', function (e) {
        var btn = e.target.closest('[data-book-package]');
        if (btn) bookPackage(btn.dataset.bookPackage);
    });

    document.getElementById('bmCustomAddCart').addEventListener('click', customAdd);
    document.getElementById('bmCustomQuote').addEventListener('click', customQuote);

    function tomorrowIso() {
        var d = new Date();
        d.setDate(d.getDate() + 1);
        return d.toISOString().split('T')[0];
    }
    var min = tomorrowIso();
    document.getElementById('bmPackageDate').min = min;
    document.getElementById('bmCustomDate').min = min;
})();
</script>
@endpush
