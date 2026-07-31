@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm mt-5" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <div style="text-align:center;margin-bottom:20px;">
                        <div style="width:48px;height:48px;background:var(--light-honey);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="ti ti-lock-question" style="font-size:24px;color:var(--gold-dark);"></i>
                        </div>
                        <h4 style="color:var(--charcoal);font-weight:700;margin-top:12px;">Forgot Password?</h4>
                        <p style="color:var(--text-muted);font-size:13px;margin:4px 0 0;">Enter your email and we'll send you a reset link.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert" style="background:var(--light-honey);color:var(--gold-dark);border:1px solid var(--gold);border-radius:10px;font-size:13px;">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-gold" style="width:100%;padding:11px;font-size:14px;border:none;">
                            Send Reset Link
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0" style="font-size:13px;color:var(--text-muted);">
                        <a href="{{ route('login') }}" style="color:var(--gold-dark);">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
