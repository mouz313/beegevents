@php
    $participantNames = collect([$booking->customer?->name])
        ->merge($booking->bookingItems->map(fn ($bi) => $bi->vendorProfile?->user?->name))
        ->filter()
        ->unique()
        ->take(3)
        ->join(', ');
@endphp

<div class="mchat">
    <div class="mchat-head">
        <div class="mchat-head-left">
            <div class="mchat-head-icon"><i class="ti ti-message-circle-2"></i></div>
            <div>
                <div class="mchat-head-title">Chat · Booking #{{ $booking->id }}</div>
                <div class="mchat-head-sub">@if($participantNames){{ $participantNames }}@else{{ $booking->customer?->name }}@endif</div>
            </div>
        </div>
        <div class="mchat-live" id="mchat-live">
            <span class="mchat-live-dot"></span> <span id="mchat-live-text">Connecting…</span>
        </div>
    </div>

    <div class="mchat-body" id="message-thread">
        <div class="mchat-empty message-empty" style="{{ $booking->messages->isEmpty() ? '' : 'display:none;' }}">
            <i class="ti ti-message-2"></i>
            <p>No messages yet. Start the conversation!</p>
        </div>

        @foreach($booking->messages as $msg)
            @include('partials.message-bubble', ['msg' => $msg])
        @endforeach

        <button type="button" class="mchat-newpill hidden" id="mchat-newpill" onclick="mchatScrollDown()">
            <i class="ti ti-arrow-down"></i> New messages
        </button>
    </div>

    <form method="POST" action="{{ $route }}" class="mchat-form send-message-form">
        @csrf
        <input type="hidden" name="client_id" id="mchat-client-id" value="">
        <input type="text" name="message" placeholder="Type your message…" required autocomplete="off"
               class="mchat-input" maxlength="2000">
        <button type="submit" class="mchat-send"><i class="ti ti-send"></i> Send</button>
    </form>
</div>

<style>
.mchat{max-width:760px;margin:0 auto;background:var(--white,#fff);border:1px solid var(--border,#e5dfd4);border-radius:16px;box-shadow:0 10px 30px rgba(43,38,32,0.08);overflow:hidden;display:flex;flex-direction:column;}
.mchat-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border,#e5dfd4);background:var(--charcoal,#2b2620);color:#fff;}
.mchat-head-left{display:flex;align-items:center;gap:12px;min-width:0;}
.mchat-head-icon{width:38px;height:38px;border-radius:10px;background:var(--gold,#d4a017);color:var(--charcoal,#2b2620);display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0;}
.mchat-head-title{font-size:14px;font-weight:700;line-height:1.2;}
.mchat-head-sub{font-size:11px;color:rgba(255,255,255,0.7);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;}
.mchat-live{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:5px 10px;border-radius:20px;background:rgba(255,255,255,0.12);color:#ddd;white-space:nowrap;}
.mchat-live-dot{width:8px;height:8px;border-radius:50%;background:#aaa;flex-shrink:0;}
.mchat-live.on{color:#7ef0a6;background:rgba(126,240,166,0.14);}
.mchat-live.on .mchat-live-dot{background:#37d67a;box-shadow:0 0 0 3px rgba(55,214,122,0.25);animation:mchat-pulse 1.6s infinite;}
.mchat-live.off{color:#f2c14e;background:rgba(242,193,78,0.14);}
.mchat-live.off .mchat-live-dot{background:#f2c14e;}
@keyframes mchat-pulse{0%,100%{opacity:1}50%{opacity:.4}}
.mchat-body{position:relative;height:440px;overflow-y:auto;padding:18px;display:flex;flex-direction:column;background:var(--cream,#faf7ee);}
.mchat-empty{text-align:center;padding:50px 20px;color:var(--text-muted,#8a8378);margin:auto;}
.mchat-empty i{font-size:40px;opacity:.3;display:block;margin-bottom:10px;}
.mchat-empty p{margin:0;font-size:14px;}
.mchat-msg{display:flex;gap:10px;margin-bottom:16px;max-width:85%;}
.mchat-msg.mine{align-self:flex-end;flex-direction:row-reverse;}
.mchat-msg.mine .mchat-bubble{background:var(--gold,#d4a017);color:var(--charcoal,#2b2620);border-bottom-right-radius:4px;box-shadow:0 2px 8px rgba(212,160,23,0.25);}
.mchat-msg.theirs .mchat-bubble{background:#fff;color:var(--text-primary,#2b2620);border-bottom-left-radius:4px;border:1px solid var(--border,#e5dfd4);box-shadow:0 1px 4px rgba(43,38,32,0.06);}
.mchat-avatar{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;margin-top:2px;}
.mchat-msg.mine .mchat-avatar{background:var(--charcoal,#2b2620);color:var(--gold,#d4a017);}
.mchat-msg.theirs .mchat-avatar{background:var(--light-honey,#f4e3b0);color:var(--gold-dark,#9a7412);}
.mchat-bubble{padding:9px 14px;border-radius:14px;font-size:13.5px;line-height:1.45;max-width:100%;word-wrap:break-word;overflow-wrap:anywhere;}
.mchat-meta{font-size:10px;color:var(--text-muted,#8a8378);margin-top:3px;display:flex;gap:5px;align-items:center;}
.mchat-msg.mine .mchat-meta{justify-content:flex-end;}
.mchat-meta .mchat-name{font-weight:600;color:var(--gold-dark,#9a7412);}
.mchat-newpill{position:sticky;bottom:10px;margin:0 auto -6px;z-index:5;border:none;background:var(--charcoal,#2b2620);color:#fff;font-size:12px;font-weight:600;padding:7px 14px;border-radius:20px;cursor:pointer;box-shadow:0 4px 14px rgba(43,38,32,0.25);display:flex;align-items:center;gap:6px;}
.mchat-newpill.hidden{display:none;}
.mchat-form{display:flex;gap:10px;padding:14px 18px;border-top:1px solid var(--border,#e5dfd4);background:#fff;}
.mchat-input{flex:1;border:2px solid var(--border,#e5dfd4);border-radius:12px;padding:11px 16px;font-size:14px;outline:none;transition:border-color .2s;background:var(--cream,#faf7ee);color:var(--text-primary,#2b2620);}
.mchat-input:focus{border-color:var(--gold,#d4a017);}
.mchat-send{background:var(--gold,#d4a017);border:none;color:var(--charcoal,#2b2620);padding:11px 20px;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;}
.mchat-send:hover{background:var(--gold-dark,#9a7412);color:#fff;}
.mchat-send:disabled{opacity:.6;cursor:default;}
.mchat-enter{animation:mchat-in .25s ease;}
@keyframes mchat-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:576px){.mchat-body{height:52vh}.mchat-head-sub{max-width:120px}}
</style>

@push('scripts')
<script>
    var mchatThread = document.getElementById('message-thread');
    var mchatNewPill = document.getElementById('mchat-newpill');
    var mchatLive = document.getElementById('mchat-live');
    var mchatNearBottom = function () {
        return mchatThread.scrollHeight - mchatThread.scrollTop - mchatThread.clientHeight < 130;
    };

    function mchatScrollDown() {
        mchatThread.scrollTop = mchatThread.scrollHeight;
        if (mchatNewPill) mchatNewPill.classList.add('hidden');
    }

    function mchatSetLive(on) {
        if (!mchatLive) return;
        mchatLive.classList.remove('on', 'off');
        var txt = document.getElementById('mchat-live-text');
        if (on) {
            mchatLive.classList.add('on');
            if (txt) txt.textContent = 'Live';
        } else {
            mchatLive.classList.add('off');
            if (txt) txt.textContent = 'Offline';
        }
    }

    function mchatNotify(message, type) {
        if (typeof showToast === 'function') {
            if (showToast.length === 3) showToast(type, 'Message', message);
            else showToast(message, type);
        }
    }

    function mchatBubbleHtml(msg) {
        var mine = String(msg.user_id) === String(currentUserId);
        var initial = (msg.name || '?').charAt(0);
        var name = mine ? 'You' : (msg.name || '');
        var time = '';
        if (msg.created_at) {
            var d = new Date(Number(msg.created_at));
            if (!isNaN(d.getTime())) {
                var now = new Date();
                var sameDay = d.toDateString() === now.toDateString();
                time = sameDay
                    ? d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    : d.toLocaleString([], { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
            }
        }
        var nameMeta = (mine || !name) ? '' : '<span class="mchat-name">' + name + '</span>';
        return '<div data-msg-id="' + msg.id + '" class="mchat-msg ' + (mine ? 'mine' : 'theirs') + '">' +
            '<div class="mchat-avatar">' + initial + '</div>' +
            '<div style="max-width:100%;">' +
            '<div class="mchat-bubble">' + msg.message + '</div>' +
            '<div class="mchat-meta">' + nameMeta + '<span>' + time + '</span></div>' +
            '</div>' +
            '</div>';
    }

    function mchatAppend(msg) {
        if (!msg || !msg.id) return;
        if (document.querySelector('[data-msg-id="' + msg.id + '"]')) return;
        var empty = document.querySelector('#message-thread .mchat-empty');
        if (empty) empty.style.display = 'none';
        var atBottom = mchatNearBottom();
        mchatThread.insertAdjacentHTML('beforeend', mchatBubbleHtml(msg));
        var el = mchatThread.lastElementChild;
        if (el) el.classList.add('mchat-enter');
        if (atBottom) mchatScrollDown();
        else if (mchatNewPill) mchatNewPill.classList.remove('hidden');
    }

    mchatThread.addEventListener('scroll', function () {
        if (mchatNearBottom() && mchatNewPill) mchatNewPill.classList.add('hidden');
    });

    if (mchatNearBottom()) mchatScrollDown();
</script>
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-app.js";
    import { getDatabase, ref, query, orderByChild, onChildAdded } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-database.js";

    var bookingId = {{ $booking->id }};
    var currentUserId = {{ auth()->id() }};
    var fbEnabled = false;

    try {
        var firebaseConfig = {
            apiKey: @json(config('firebase.api_key')),
            authDomain: @json(config('firebase.auth_domain')),
            projectId: @json(config('firebase.project_id')),
            storageBucket: @json(config('firebase.storage_bucket')),
            messagingSenderId: @json(config('firebase.messaging_sender_id')),
            appId: @json(config('firebase.app_id')),
            measurementId: @json(config('firebase.measurement_id')),
            databaseURL: @json(config('firebase.database_url')),
        };

        if (firebaseConfig.databaseURL) {
            var app = initializeApp(firebaseConfig);
            var db = getDatabase(app);
            onChildAdded(query(ref(db, 'messages/' + bookingId), orderByChild('created_at')), function (snap) {
                var data = snap.val();
                if (!data) return;
                data.id = snap.key;
                mchatAppend(data);
            }, function (err) {
                console.warn('Firebase realtime read error:', err);
                fbEnabled = false;
                mchatSetLive(false);
            });
            fbEnabled = true;
            mchatSetLive(true);
        } else {
            mchatSetLive(false);
        }
    } catch (e) {
        console.warn('Firebase realtime chat unavailable:', e);
        mchatSetLive(false);
    }

    document.querySelector('.send-message-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        var input = form.querySelector('input[name="message"]');
        var clientIdInput = form.querySelector('input[name="client_id"]');
        var btn = form.querySelector('button');
        var text = input.value.trim();
        if (!text) return;

        btn.disabled = true;
        btn.innerHTML = '…';

        var clientMsgId = 'cm-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
        clientIdInput.value = clientMsgId;

        var csrfToken = '{{ csrf_token() }}';
        var endpoint = window.location.origin + window.location.pathname;
        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: JSON.stringify({ message: text, _token: csrfToken, client_id: clientMsgId })
        }).then(function (r) {
            var ct = (r.headers.get('content-type') || '');
            if (r.redirected && r.url.indexOf('/login') !== -1) {
                mchatNotify('Session expired — please login again', 'warning');
                setTimeout(function () { location.href = r.url; }, 1500);
                return null;
            }
            if (r.status === 419 || r.status === 401) {
                mchatNotify('Session expired — refreshing, please resend your message', 'warning');
                setTimeout(function () { location.reload(); }, 1200);
                return null;
            }
            if (r.redirected) {
                location.reload();
                return null;
            }
            if (!r.ok || ct.indexOf('application/json') === -1) {
                console.error('Message send failed:', r.status, ct);
                mchatNotify('Network error (HTTP ' + r.status + ', ' + ct.split(';')[0] + ')', 'error');
                return null;
            }
            return r.json();
        }).then(function (d) {
            if (!d) return;
            if (d.success) {
                input.value = '';
                if (d.message && d.message.id) {
                    mchatAppend(d.message);
                } else if (!fbEnabled) {
                    location.reload();
                }
            } else {
                mchatNotify('Failed to send message', 'error');
            }
        }).catch(function (err) {
            console.error('Message send fetch rejected:', err);
            mchatNotify('Network error — sending again without realtime', 'warning');
            setTimeout(function () {
                form.submit();
            }, 400);
        }).finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-send"></i> Send';
        });
    });
</script>
@endpush
