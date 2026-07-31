<div style="max-width:700px;">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:14px;height:400px;overflow-y:auto;padding:20px;margin-bottom:16px;" id="message-thread">
        @forelse($booking->messages as $msg)
            <div data-msg-id="{{ $msg->id }}" style="display:flex;gap:10px;margin-bottom:16px;{{ $msg->user_id === auth()->id() ? 'flex-direction:row-reverse;' : '' }}">
                <div style="width:32px;height:32px;border-radius:50%;background:{{ $msg->user_id === auth()->id() ? 'var(--gold)' : 'var(--light-honey)' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $msg->user_id === auth()->id() ? 'var(--charcoal)' : 'var(--gold-dark)' }};flex-shrink:0;">
                    {{ substr($msg->user->name, 0, 1) }}
                </div>
                <div style="max-width:75%;">
                    <div style="background:{{ $msg->user_id === auth()->id() ? 'var(--gold)' : 'var(--cream)' }};border-radius:12px;padding:10px 14px;{{ $msg->user_id === auth()->id() ? 'border-bottom-right-radius:4px;' : 'border-bottom-left-radius:4px;' }}">
                        <p style="margin:0;font-size:13px;color:{{ $msg->user_id === auth()->id() ? 'var(--charcoal)' : 'var(--text-primary)' }};">{{ $msg->message }}</p>
                    </div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px;{{ $msg->user_id === auth()->id() ? 'text-align:right;' : '' }}">
                        {{ $msg->user->name }} · {{ $msg->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="message-empty" style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                <i class="ti ti-message-2" style="font-size:36px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                <p style="font-size:14px;">No messages yet. Start the conversation!</p>
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ $route }}" class="send-message-form" style="display:flex;gap:8px;">
        @csrf
        <input type="text" name="message" placeholder="Type your message..." required
               style="flex:1;border:2px solid var(--border);border-radius:10px;padding:10px 14px;font-size:13px;outline:none;transition:border-color 0.2s;"
               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border)'">
        <button type="submit" style="background:var(--gold);border:none;color:var(--charcoal);padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all 0.2s;"
                onmouseover="this.style.background='var(--gold-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--gold)';this.style.color='var(--charcoal)'">
            <i class="ti ti-send"></i> Send
        </button>
    </form>
</div>

@push('scripts')
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-app.js";
    import { getDatabase, ref, query, orderByChild, onChildAdded } from "https://www.gstatic.com/firebasejs/12.17.0/firebase-database.js";

    var bookingId = {{ $booking->id }};
    var currentUserId = {{ auth()->id() }};
    var currentUserName = @json(auth()->user()->name);
    var fbEnabled = false;

    var thread = document.getElementById('message-thread');

    function scrollBottom() {
        if (thread) thread.scrollTop = thread.scrollHeight;
    }
    if (thread) scrollBottom();

    function notify(message, type) {
        if (typeof showToast === 'function') {
            if (showToast.length === 3) {
                showToast(type, 'Message', message);
            } else {
                showToast(message, type);
            }
        }
    }

    function bubbleHtml(msg) {
        var mine = String(msg.user_id) === String(currentUserId);
        var initial = (msg.name || '?').charAt(0);
        var time = '';
        if (msg.created_at) {
            var d = new Date(Number(msg.created_at));
            if (!isNaN(d.getTime())) time = d.toLocaleString([], { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        }
        return '<div data-msg-id="' + msg.id + '" style="display:flex;gap:10px;margin-bottom:16px;' + (mine ? 'flex-direction:row-reverse;' : '') + '">' +
            '<div style="width:32px;height:32px;border-radius:50%;background:' + (mine ? 'var(--gold)' : 'var(--light-honey)') + ';display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:' + (mine ? 'var(--charcoal)' : 'var(--gold-dark)') + ';flex-shrink:0;">' + initial + '</div>' +
            '<div style="max-width:75%;">' +
            '<div style="background:' + (mine ? 'var(--gold)' : 'var(--cream)') + ';border-radius:12px;padding:10px 14px;' + (mine ? 'border-bottom-right-radius:4px;' : 'border-bottom-left-radius:4px;') + '">' +
            '<p style="margin:0;font-size:13px;color:' + (mine ? 'var(--charcoal)' : 'var(--text-primary)') + ';">' + msg.message + '</p>' +
            '</div>' +
            '<div style="font-size:10px;color:var(--text-muted);margin-top:2px;' + (mine ? 'text-align:right;' : '') + '">' + (msg.name || '') + (time ? ' · ' + time : '') + '</div>' +
            '</div>' +
            '</div>';
    }

    function appendMsg(msg) {
        if (!msg || !msg.id) return;
        if (document.querySelector('[data-msg-id="' + msg.id + '"]')) return;
        var empty = document.querySelector('#message-thread .message-empty');
        if (empty) empty.remove();
        thread.insertAdjacentHTML('beforeend', bubbleHtml(msg));
        scrollBottom();
    }

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
                appendMsg(data);
            });
            fbEnabled = true;
        }
    } catch (e) {
        console.warn('Firebase realtime chat unavailable:', e);
    }

    document.querySelector('.send-message-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var input = form.querySelector('input[name="message"]');
        var btn = form.querySelector('button');
        var text = input.value.trim();
        if (!text) return;

        btn.disabled = true;
        btn.innerHTML = '...';

        var csrfToken = '{{ csrf_token() }}';
        fetch(form.action, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
            body: JSON.stringify({message: text, _token: csrfToken})
        }).then(function(r) {
            var ct = (r.headers.get('content-type') || '');
            if (r.status === 419 || r.status === 401) {
                notify('Session expired — refreshing, please resend your message', 'warning');
                setTimeout(function(){ location.reload(); }, 1200);
                return null;
            }
            if (!r.ok || ct.indexOf('application/json') === -1) {
                notify('Network error', 'error');
                return null;
            }
            return r.json();
        }).then(function(d) {
            if (!d) return;
            if (d.success) {
                input.value = '';
                if (!fbEnabled) location.reload();
            } else {
                notify('Failed to send message', 'error');
            }
        }).catch(function() {
            notify('Network error', 'error');
        })
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="ti ti-send"></i> Send'; });
    });
</script>
@endpush
