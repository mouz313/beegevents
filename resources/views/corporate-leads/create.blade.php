@extends('layouts.app')

@section('title', 'Corporate Inquiry')

@section('content')
<div class="browse-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="ti ti-building"></i> Corporate Events</div>
                <h1>Let Us Plan Your <span>Corporate Event</span></h1>
                <p>Tell us your requirements and we'll connect you with the best venues and services for your corporate gathering.</p>
            </div>
            <div class="col-lg-5">
                <div class="hero-stats" style="justify-content:flex-end;">
                    <span><i class="ti ti-building-arch"></i> 12+ venues</span>
                    <span><i class="ti ti-users"></i> 50+ vendors</span>
                    <span><i class="ti ti-star"></i> Trusted by leading companies</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container detail-main">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="vendor-card">
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="width:56px;height:56px;background:var(--light-honey);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="ti ti-building" style="font-size:26px;color:var(--gold-dark);"></i>
                    </div>
                    <h5 style="font-weight:700;margin:0;">Corporate Event Inquiry</h5>
                    <p class="text-muted" style="font-size:13px;margin:4px 0 0;">Fill in the details and we'll get back to you within 24 hours.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" style="background:#E6F7ED;border:none;color:var(--green);font-size:13px;border-radius:8px;">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('corporate.leads.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Company Name</label>
                        <input type="text" class="form-control" name="company_name" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="Acme Corp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Contact Person</label>
                        <input type="text" class="form-control" name="contact_person" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="John Doe">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Email</label>
                            <input type="email" class="form-control" name="email" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="john@acme.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Phone</label>
                            <input type="text" class="form-control" name="phone" required style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="+92 300 1234567">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Requirements</label>
                        <textarea class="form-control" name="requirement_notes" rows="4" style="border:2px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;" placeholder="Describe your event — type, expected guests, preferred dates, budget range..."></textarea>
                    </div>
                    <button type="submit" class="btn-gold w-100" style="padding:12px;font-size:14px;">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
