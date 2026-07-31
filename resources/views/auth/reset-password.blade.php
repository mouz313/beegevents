@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm mt-5" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body p-4">
                    <div style="text-align:center;margin-bottom:20px;">
                        <div style="width:48px;height:48px;background:var(--light-honey);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="ti ti-key" style="font-size:24px;color:var(--gold-dark);"></i>
                        </div>
                        <h4 style="color:var(--charcoal);font-weight:700;margin-top:12px;">Reset Password</h4>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">New Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label" style="font-size:13px;font-weight:600;color:var(--charcoal);">Confirm Password</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required style="border-color:var(--border);border-radius:10px;padding:10px 14px;font-size:14px;">
                        </div>

                        <button type="submit" class="btn-gold" style="width:100%;padding:11px;font-size:14px;border:none;">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
