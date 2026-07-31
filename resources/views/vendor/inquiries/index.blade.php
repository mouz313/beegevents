@extends('layouts.app')

@section('title', 'Inquiries')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Inquiries</h2>
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left"></i> Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card" style="border:1px solid var(--border);border-radius:12px;">
        <div class="card-body" style="padding:0;">
            @if($inquiries->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Listing</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Customer</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Contact</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Date</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Status</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Message</th>
                                <th style="padding:12px 20px;font-size:11px;text-transform:uppercase;color:var(--text-muted);">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inquiries as $inq)
                                <tr>
                                    <td style="padding:12px 20px;font-size:13px;font-weight:600;">
                                        @php $item = $inq->inquiriable; @endphp
                                        {{ $item ? ($item->name ?? $item->title ?? 'N/A') : 'N/A' }}
                                    </td>
                                    <td style="padding:12px 20px;font-size:13px;">
                                        {{ $inq->user->name ?? $inq->name }}
                                    </td>
                                    <td style="padding:12px 20px;font-size:12px;">
                                        <div>{{ $inq->email }}</div>
                                        @if($inq->phone)
                                            <div style="color:var(--text-muted);">{{ $inq->phone }}</div>
                                        @endif
                                    </td>
                                    <td style="padding:12px 20px;font-size:13px;">{{ $inq->created_at->format('M d, Y') }}</td>
                                    <td style="padding:12px 20px;">
                                        <span style="display:inline-block;padding:2px 10px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $inq->status == 'replied' ? '#E6F7ED' : ($inq->status == 'closed' ? '#f5f3f0' : '#FFF3E0') }};color:{{ $inq->status == 'replied' ? 'var(--green)' : ($inq->status == 'closed' ? 'var(--text-muted)' : 'var(--amber)') }};">
                                            {{ ucfirst($inq->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:12px 20px;font-size:12px;color:var(--text-muted);max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $inq->message }}">
                                        {{ $inq->message }}
                                    </td>
                                    <td style="padding:12px 20px;">
                                        <div class="d-flex gap-1">
                                            @if($inq->status == 'pending')
                                                <form method="POST" action="{{ route('vendor.inquiries.update', $inq) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="replied">
                                                    <button type="submit" class="btn btn-sm btn-success" style="font-size:11px;padding:2px 10px;">Replied</button>
                                                </form>
                                            @endif
                                            @if($inq->status != 'closed')
                                                <form method="POST" action="{{ route('vendor.inquiries.update', $inq) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit" class="btn btn-sm btn-secondary" style="font-size:11px;padding:2px 10px;">Close</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:12px 20px;">
                    {{ $inquiries->links() }}
                </div>
            @else
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:13px;">
                    No inquiries yet. Inquiries from customers will appear here.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
