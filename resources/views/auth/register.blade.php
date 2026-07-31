@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm mt-5" style="border:1px solid var(--border);border-radius:14px;border-top:3px solid var(--gold);">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div style="width:48px;height:48px;background:var(--gold);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:var(--charcoal);">B</div>
                        <h4 style="color:var(--charcoal);font-weight:700;margin-top:12px;">Create Account</h4>
                        <p style="color:var(--text-muted);font-size:13px;margin:2px 0 0;">Join BeeG Events today</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Full Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Phone (optional)</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Password</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Confirm Password</label>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">I want to join as</label>
                            <div class="d-flex gap-3">
                                <label class="d-flex align-items-center gap-2 p-3" style="border:2px solid var(--border);border-radius:10px;cursor:pointer;flex:1;transition:all 0.15s;{{ old('role', 'customer') == 'customer' ? 'border-color:var(--gold);background:var(--light-honey);' : '' }}" onclick="this.querySelector('input').checked=true;document.querySelectorAll('.role-option').forEach(e=>{e.style.borderColor='var(--border)';e.style.background='none'});this.style.borderColor='var(--gold)';this.style.background='var(--light-honey)'">
                                    <input class="form-check-input" type="radio" name="role" value="customer" {{ old('role', 'customer') == 'customer' ? 'checked' : '' }} style="display:none;">
                                    <i class="ti ti-user" style="font-size:20px;color:var(--gold-dark);"></i>
                                    <div><strong style="font-size:14px;color:var(--charcoal);display:block;">Customer</strong><span style="font-size:11px;color:var(--text-muted);">Book events &amp; services</span></div>
                                </label>
                                <label class="d-flex align-items-center gap-2 p-3 role-option" style="border:2px solid var(--border);border-radius:10px;cursor:pointer;flex:1;transition:all 0.15s;{{ old('role') == 'vendor' ? 'border-color:var(--gold);background:var(--light-honey);' : '' }}" onclick="this.querySelector('input').checked=true;document.querySelectorAll('.role-option').forEach(e=>{e.style.borderColor='var(--border)';e.style.background='none'});this.style.borderColor='var(--gold)';this.style.background='var(--light-honey)'">
                                    <input class="form-check-input" type="radio" name="role" value="vendor" {{ old('role') == 'vendor' ? 'checked' : '' }} style="display:none;">
                                    <i class="ti ti-building-store" style="font-size:20px;color:var(--gold-dark);"></i>
                                    <div><strong style="font-size:14px;color:var(--charcoal);display:block;">Vendor</strong><span style="font-size:11px;color:var(--text-muted);">List your services</span></div>
                                </label>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" style="background:var(--gold);border:none;color:var(--charcoal);padding:11px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='var(--gold-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--gold)';this.style.color='var(--charcoal)'">Create Account</button>
                        </div>
                    </form>
                    <p class="text-center mt-3 mb-0" style="font-size:13px;color:var(--text-muted);">
                        Already have an account? <a href="{{ route('login') }}" style="color:var(--gold-dark);font-weight:600;">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
