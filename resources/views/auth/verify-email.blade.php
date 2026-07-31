@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm mt-5" style="border:1px solid var(--border);border-radius:14px;">
                <div class="card-body text-center p-5">
                    <div style="width:56px;height:56px;background:var(--light-honey);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <i class="ti ti-mail-check" style="font-size:28px;color:var(--gold-dark);"></i>
                    </div>
                    <h4 style="color:var(--charcoal);font-weight:700;">Verify Your Email</h4>
                    <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px;">
                        A verification link has been sent to <strong>{{ auth()->user()->email }}</strong>.<br>
                        Click the link in the email to activate your account.
                    </p>

                    @if(session('success'))
                        <div class="alert" style="background:var(--light-honey);color:var(--gold-dark);border:1px solid var(--gold);border-radius:10px;font-size:13px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn-gold" style="padding:10px 24px;font-size:14px;border:none;">
                            Resend Verification Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" style="background:none;border:none;color:var(--text-muted);font-size:13px;cursor:pointer;text-decoration:underline;">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
