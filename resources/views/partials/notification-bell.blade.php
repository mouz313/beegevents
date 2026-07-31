@php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count(); @endphp

<div class="nb-wrap" style="position:relative;display:inline-block;">
    <button type="button" class="nb-bell-btn" onclick="nbToggle(this)" aria-label="Notifications">
        <i class="ti ti-bell"></i>
        <span class="nb-badge" id="nb-badge" style="{{ $unreadCount ? '' : 'display:none;' }}">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
    </button>
    <div class="nb-panel" id="nb-panel" style="display:none;">
        <div class="nb-head">
            <span>Notifications</span>
            <button type="button" class="nb-readall" onclick="nbMarkAll()">Mark all read</button>
        </div>
        <div class="nb-list" id="nb-list">
            <div class="nb-empty">Loading...</div>
        </div>
    </div>
</div>

<style>
.nb-bell-btn{width:36px;height:36px;border-radius:8px;border:1px solid var(--border,rgba(0,0,0,0.12));background:var(--white,#fff);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted,#6b6b6b);position:relative;font-size:18px;transition:all .2s;}
.nb-bell-btn:hover{background:var(--cream,#f8f5ec);color:var(--text-primary,#2b2620);}
.nb-badge{position:absolute;top:-5px;right:-5px;min-width:18px;height:18px;border-radius:9px;background:var(--red,#d62839);color:#fff;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 4px;line-height:1;box-shadow:0 0 0 2px var(--white,#fff);}
.nb-panel{position:fixed;width:330px;max-width:calc(100vw - 16px);max-height:430px;background:#fff;border:1px solid rgba(0,0,0,0.1);border-radius:12px;box-shadow:0 12px 32px rgba(0,0,0,0.18);z-index:10000;overflow:hidden;display:flex;flex-direction:column;}
.nb-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid #eee;font-size:13px;font-weight:700;color:#2b2620;}
.nb-readall{border:none;background:none;color:var(--gold-dark,#9a7412);font-size:12px;cursor:pointer;font-weight:600;}
.nb-readall:hover{text-decoration:underline;}
.nb-list{overflow-y:auto;flex:1;}
.nb-item{display:block;padding:11px 14px;border-bottom:1px solid #f2f2f2;text-decoration:none;color:#2b2620;cursor:pointer;}
.nb-item:hover{background:#faf7ef;}
.nb-item.unread{background:#fdf7e3;}
.nb-i-title{font-size:13px;font-weight:600;margin-bottom:2px;}
.nb-i-body{font-size:12px;color:#6b6b6b;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.nb-i-time{font-size:10px;color:#a0a0a0;margin-top:3px;}
.nb-empty{padding:30px 14px;text-align:center;color:#a0a0a0;font-size:12px;}
</style>

@push('scripts')
<script>
    var nbCsrf = '{{ csrf_token() }}';
    var nbCount = {{ $unreadCount }};

    function nbSetBadge(n) {
        nbCount = n;
        var b = document.getElementById('nb-badge');
        if (b) { b.textContent = n > 99 ? '99+' : n; b.style.display = n > 0 ? 'flex' : 'none'; }
    }

    function nbToast(msg, type) {
        if (typeof showToast === 'function') {
            if (showToast.length === 3) showToast(type, 'Notification', msg);
            else showToast(msg, type);
        }
    }

    function nbToggle(btn) {
        var panel = document.getElementById('nb-panel');
        if (!panel) return;
        if (panel.style.display === 'block') { panel.style.display = 'none'; return; }
        var r = btn.getBoundingClientRect();
        panel.style.top = (r.bottom + 8) + 'px';
        panel.style.left = Math.max(8, r.right - 330) + 'px';
        panel.style.display = 'block';
        nbLoad();
    }

    function nbItemHtml(n) {
        var cls = n.is_read ? '' : ' unread';
        return '<a href="' + (n.action_url || '#') + '" class="nb-item' + cls + '" data-nid="' + n.id + '" onclick="nbMarkRead(' + n.id + ',this)">' +
            '<div class="nb-i-title">' + (n.title || '') + '</div>' +
            '<div class="nb-i-body">' + (n.body || '') + '</div>' +
            '<div class="nb-i-time">' + (n.time || '') + '</div>' +
            '</a>';
    }

    function nbRender(list) {
        var el = document.getElementById('nb-list');
        if (!el) return;
        if (!list || !list.length) { el.innerHTML = '<div class="nb-empty">No notifications yet</div>'; return; }
        el.innerHTML = list.map(nbItemHtml).join('');
    }

    function nbLoad() {
        fetch('/notifications', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d && d.notifications) nbRender(d.notifications); })
            .catch(function () {});
        fetch('/notifications/unread-count', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d) nbSetBadge(d.count); })
            .catch(function () {});
    }

    function nbMarkRead(id, el) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': nbCsrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function (r) { return r.json(); }).then(function (d) {
            if (d && d.success) {
                var it = document.querySelector('.nb-item[data-nid="' + id + '"]');
                if (it) it.classList.remove('unread');
                nbSetBadge(Math.max(0, nbCount - 1));
            }
        }).catch(function () {});
    }

    function nbMarkAll() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': nbCsrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function (r) { return r.json(); }).then(function (d) {
            if (d && d.success) {
                document.querySelectorAll('.nb-item.unread').forEach(function (it) { it.classList.remove('unread'); });
                nbSetBadge(0);
            }
        }).catch(function () {});
    }

    function nbOnNew(n) {
        if (!n || !n.id) return;
        var existing = document.querySelector('.nb-item[data-nid="' + n.id + '"]');
        if (!existing) {
            var list = document.getElementById('nb-list');
            if (list) {
                var empty = list.querySelector('.nb-empty');
                if (empty) empty.remove();
                list.insertAdjacentHTML('afterbegin', nbItemHtml(n));
            }
            nbSetBadge(nbCount + 1);
        }
        nbToast((n.title || 'New notification') + (n.body ? ': ' + n.body : ''), 'info');
    }

    document.addEventListener('click', function (e) {
        var panel = document.getElementById('nb-panel');
        if (panel && panel.style.display === 'block' && !panel.contains(e.target) && !e.target.closest('.nb-bell-btn')) {
            panel.style.display = 'none';
        }
    });

    setInterval(function () {
        fetch('/notifications/unread-count', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d && d.count !== nbCount) { nbSetBadge(d.count); nbLoad(); }
            })
            .catch(function () {});
    }, 45000);
</script>
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-app.js";
    import { getDatabase, ref, onChildAdded } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-database.js";

    var nbUserId = {{ auth()->id() }};
    try {
        var cfg = {
            apiKey: @json(config('firebase.api_key')),
            authDomain: @json(config('firebase.auth_domain')),
            projectId: @json(config('firebase.project_id')),
            storageBucket: @json(config('firebase.storage_bucket')),
            messagingSenderId: @json(config('firebase.messaging_sender_id')),
            appId: @json(config('firebase.app_id')),
            measurementId: @json(config('firebase.measurement_id')),
            databaseURL: @json(config('firebase.database_url')),
        };
        if (cfg.databaseURL) {
            var app = initializeApp(cfg, 'beegevents-notifications');
            var db = getDatabase(app);
            onChildAdded(ref(db, 'notifications/' + nbUserId), function (snap) {
                var data = snap.val();
                if (!data) return;
                data.id = snap.key;
                nbOnNew(data);
            });
        }
    } catch (e) {
        console.warn('Firebase notifications unavailable:', e);
    }
</script>
@endpush
