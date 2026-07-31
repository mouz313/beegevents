@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm mt-5" style="border:1px solid var(--border);border-radius:14px;border-top:3px solid var(--gold);">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div style="width:48px;height:48px;background:var(--gold);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:var(--charcoal);">B</div>
                        <h4 style="color:var(--charcoal);font-weight:700;margin-top:12px;">Welcome Back</h4>
                        <p style="color:var(--text-muted);font-size:13px;margin:2px 0 0;">Login to your BeeG account</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember" style="font-size:13px;">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--gold-dark);">Forgot password?</a>
                        </div>
                        <div class="d-grid">
                            <button type="submit" style="background:var(--gold);border:none;color:var(--charcoal);padding:11px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='var(--gold-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--gold)';this.style.color='var(--charcoal)'">Login</button>
                        </div>
                    </form>
                    <p class="text-center mt-3 mb-0" style="font-size:13px;color:var(--text-muted);">
                        Don't have an account? <a href="{{ route('register') }}" style="color:var(--gold-dark);font-weight:600;">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
