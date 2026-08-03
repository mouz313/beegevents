@extends('customer.layouts.master')

@section('title', 'Corporate Quotations')

@section('content')
<div class="container">
    <h2 class="mb-1">Corporate Quotations</h2>
    <p class="text-muted" style="font-size:13px;">Quotations prepared for your corporate event inquiries.</p>

    <div class="card mt-3">
        <div class="card-body">
            @if($quotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Quotation</th>
                                <th>Company</th>
                                <th>Event Date</th>
                                <th>Venue</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotations as $quotation)
                                <tr>
                                    <td><strong>{{ $quotation->quote_no }}</strong></td>
                                    <td>{{ $quotation->lead?->company_name ?? '—' }}</td>
                                    <td>{{ $quotation->event_date?->format('M d, Y') ?? 'TBC' }}</td>
                                    <td>{{ $quotation->venue ?? '—' }}</td>
                                    <td>PKR {{ number_format($quotation->amount ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quotation->status == 'accepted' ? 'success' : ($quotation->status == 'declined' ? 'danger' : ($quotation->status == 'sent' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($quotation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('corporate.quotations.show', $quotation->token) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">
                    No quotations yet.
                    <a href="{{ route('corporate.leads.create') }}">Submit a corporate inquiry</a> and our team will prepare a quotation for you.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
