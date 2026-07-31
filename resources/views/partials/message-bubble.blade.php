@php
    $mine = $msg->user_id === auth()->id();
    $sameDay = $msg->created_at->isSameDay(now());
    $time = $sameDay ? $msg->created_at->format('h:i A') : $msg->created_at->format('d M, h:i A');
@endphp
<div data-msg-id="{{ $msg->id }}" class="mchat-msg {{ $mine ? 'mine' : 'theirs' }}">
    <div class="mchat-avatar">{{ substr($msg->user->name, 0, 1) }}</div>
    <div style="max-width:100%;">
        <div class="mchat-bubble">{{ $msg->message }}</div>
        <div class="mchat-meta">
            @if(!$mine)<span class="mchat-name">{{ $msg->user->name }}</span>@endif
            <span>{{ $time }}</span>
        </div>
    </div>
</div>
