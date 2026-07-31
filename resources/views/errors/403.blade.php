@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="padding:80px 0;">
        <div class="col-md-6 text-center">
            <div style="width:72px;height:72px;background:#FDE8E8;border-radius:18px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <i class="ti ti-lock" style="font-size:36px;color:var(--red);"></i>
            </div>
            <h1 style="font-size:48px;font-weight:800;color:var(--charcoal);margin-bottom:4px;">403</h1>
            <p style="font-size:16px;color:var(--text-muted);margin-bottom:24px;">You don't have permission to access this page.</p>
            <a href="{{ url('/') }}" class="btn-gold" style="padding:10px 24px;font-size:14px;text-decoration:none;display:inline-block;">Go Home</a>
        </div>
    </div>
</div>
@endsection
