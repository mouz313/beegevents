<div style="max-width:700px;">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:14px;height:400px;overflow-y:auto;padding:20px;margin-bottom:16px;" id="message-thread">
        @forelse($booking->messages as $msg)
            <div style="display:flex;gap:10px;margin-bottom:16px;{{ $msg->user_id === auth()->id() ? 'flex-direction:row-reverse;' : '' }}">
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
            <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
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
<script>
    var thread = document.getElementById('message-thread');
    thread.scrollTop = thread.scrollHeight;

    document.querySelector('.send-message-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var input = form.querySelector('input[name="message"]');
        var btn = form.querySelector('button');
        var text = input.value.trim();
        if (!text) return;

        btn.disabled = true;
        btn.innerHTML = '...';

        fetch(form.action, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({message: text})
        }).then(r => r.json()).then(d => {
            if (d.success) {
                input.value = '';
                location.reload();
            } else {
                showToast('Failed to send message', 'error');
            }
        }).catch(() => showToast('Network error', 'error'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="ti ti-send"></i> Send'; });
    });
</script>
@endpush
