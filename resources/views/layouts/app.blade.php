<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'BeeG Events — Pakistan\'s trusted event planning platform. Find verified halls, farmhouses, decor, catering, photography, and more for your perfect event.')">
    <meta name="keywords" content="@yield('meta_keywords', 'event planning, wedding halls, farmhouses, decor, catering, photography, Pakistan events, BeeG Events')">
    <meta name="author" content="BeeG Events">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'BeeG Events'))">
    <meta property="og:description" content="@yield('og_description', 'Pakistan\'s trusted event planning platform connecting customers with verified venues and services.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="BeeG Events">
    <meta name="twitter:card" content="summary_large_image">
    <title>@yield('title', 'Welcome') - {{ config('app.name', 'BeeG Events') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/browse.css') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @stack('styles')
    @stack('structured-data')
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "BeeG Events",
        "url": "{{ url('/') }}",
        "description": "Pakistan's trusted event planning platform connecting customers with verified venues and services.",
        "foundingDate": "2026",
        "areaServed": "PK"
    }
    </script>
    @endverbatim
</head>
<body>

<div id="preloader">
    <div class="pl-ring"></div>
    @if(setting('site_logo'))
        <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="pl-logo-img">
    @else
        <div class="pl-logo">B</div>
    @endif
    <div class="pl-text">{{ setting('site_name', 'BeeG Events') }}</div>
</div>

<header class="app-header">
    <div style="display:flex;align-items:center;gap:12px;">
        <button class="mobile-nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('show')">
            <i class="ti ti-menu-2"></i>
        </button>
        <a href="{{ url('/') }}" class="logo">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="header-logo-img">
            @else
                <div class="logo-icon">B</div>
                <div class="logo-text">Bee<span>G</span></div>
            @endif
        </a>
    </div>

    <ul class="nav-links">
        <li><a href="{{ route('browse.index') }}" class="{{ request()->routeIs('browse.*') ? 'active' : '' }}"><i class="ti ti-building-arch"></i> Browse</a></li>
        <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}"><i class="ti ti-news"></i> Blog</a></li>
        @auth
            @php $role = auth()->user()->role; @endphp
            <li><a href="{{ route($role . '.dashboard') }}"><i class="ti ti-dashboard"></i> Dashboard</a></li>
            @if($role == 'customer')
                <li><a href="{{ route('customer.cart') }}"><i class="ti ti-shopping-cart"></i> Cart</a></li>
                <li><a href="{{ route('customer.budget.match.index') }}"><i class="ti ti-wand"></i> Budget Match</a></li>
            @endif
            @if($role == 'vendor')
                <li><a href="{{ route('vendor.halls.index') }}"><i class="ti ti-building"></i> Halls</a></li>
                <li><a href="{{ route('vendor.listings.index') }}"><i class="ti ti-list-check"></i> Services</a></li>
                <li><a href="{{ route('vendor.packages.index') }}"><i class="ti ti-gift"></i> Packages</a></li>
                <li><a href="{{ route('vendor.bookings.index') }}"><i class="ti ti-calendar-event"></i> Bookings</a></li>
                <li><a href="{{ route('vendor.inquiries.index') }}" class="{{ request()->routeIs('vendor.inquiries.*') ? 'active' : '' }}"><i class="ti ti-mail"></i> Inquiries</a></li>
            @endif
            @if($role == 'admin')
                <li><a href="{{ route('admin.vendors.pending') }}"><i class="ti ti-building-store"></i> Vendors</a></li>
                <li><a href="{{ route('admin.bookings.index') }}"><i class="ti ti-calendar-event"></i> Bookings</a></li>
            @endif
        @else
            <li><a href="{{ route('corporate.leads.create') }}"><i class="ti ti-building"></i> Corporate</a></li>
        @endauth
    </ul>

    <div class="header-right">
        <button type="button" class="btn-header" data-bs-toggle="modal" data-bs-target="#bookingModal" style="border:none;cursor:pointer;">
            <i class="ti ti-calendar-plus"></i> Book Now
        </button>
        @auth
            @include('partials.notification-bell')
            <div class="user-dropdown">
                <div class="dropdown-trigger" onclick="this.nextElementSibling.classList.toggle('show')">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <i class="ti ti-chevron-down" style="font-size:12px;"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="{{ route($role . '.dashboard') }}"><i class="ti ti-dashboard"></i> Dashboard</a>
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('logout') }}" style="display:block;">
                        @csrf
                        <button type="submit"><i class="ti ti-logout"></i> Logout</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-ghost-header">Login</a>
        @endauth
    </div>
</header>

<div id="toast-container"></div>

@auth
    @if(!auth()->user()->hasVerifiedEmail())
        <div style="background:var(--light-honey);border-bottom:1px solid var(--gold);padding:10px 20px;text-align:center;font-size:13px;color:var(--charcoal);">
            <i class="ti ti-mail-warning" style="color:var(--gold-dark);"></i>
            Please verify your email address.
            <a href="{{ route('verification.notice') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:underline;margin-left:4px;">Resend verification</a>
        </div>
    @endif
@endauth

@yield('hero')

<main class="app-main">
    @yield('content')
</main>

<footer class="app-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="footer-logo-img">
            @else
                <div class="logo-text">Bee<span>G</span></div>
            @endif
            <p>Pakistan's trusted event planning platform connecting customers with verified venues and services for weddings, engagements, and corporate events.</p>
            <div class="footer-social">
                <a href="#"><i class="ti ti-brand-facebook"></i></a>
                <a href="#"><i class="ti ti-brand-instagram"></i></a>
                <a href="#"><i class="ti ti-brand-twitter"></i></a>
                <a href="#"><i class="ti ti-brand-linkedin"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h5>Platform</h5>
            <a href="{{ route('browse.index') }}">Browse Services</a>
            <a href="{{ route('register') }}">Become a Vendor</a>
            <a href="{{ route('corporate.leads.create') }}">Corporate Inquiry</a>
        </div>
        <div class="footer-col">
            <h5>Categories</h5>
            <a href="{{ route('browse.category', 'hall') }}">Halls &amp; Farmhouses</a>
            <a href="{{ route('browse.category', 'decor') }}">Decor</a>
            <a href="{{ route('browse.category', 'catering') }}">Catering</a>
            <a href="{{ route('browse.category', 'photography') }}">Photography</a>
            <a href="{{ route('browse.category', 'dj') }}">DJ / Sound</a>
            <a href="{{ route('blog.index') }}">Blog</a>
        </div>
        <div class="footer-col">
            <h5>Account</h5>
            @auth
                <a href="{{ route(auth()->user()->role . '.dashboard') }}">Dashboard</a>
                <a href="#" onclick="event.preventDefault();document.querySelector('#logout-form-footer').submit();">Logout</a>
                <form id="logout-form-footer" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} <a href="{{ url('/') }}">BeeG Events</a>. All rights reserved.</span>
        <span>Crafted with <i class="ti ti-heart" style="color:var(--gold);"></i> in Pakistan</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown-menu').forEach(function(m) {
        if (!m.parentElement.contains(e.target)) m.classList.remove('show');
    });
});
// Preloader
(function() {
    var p = document.getElementById('preloader');
    if (p) {
        window.addEventListener('load', function() { setTimeout(function() { p.classList.add('done'); }, 400); });
        setTimeout(function() { if (!p.classList.contains('done')) p.classList.add('done'); }, 4000);
    }
})();
// Scroll animations
(function() {
    var els = document.querySelectorAll('[data-animate]');
    if (els.length && 'IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('animate-visible'); obs.unobserve(e.target); } });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function(el) { obs.observe(el); });
    } else {
        els.forEach(function(el) { el.classList.add('animate-visible'); });
    }
})();
</script>
<style>
:root { --gold-glow: rgba(212,160,23,0.08); }
.app-main { min-height:60vh; }
#toast-container { position:fixed; top:80px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.toast-bee { background:var(--charcoal); color:var(--white); padding:12px 20px; border-radius:10px; font-size:13px; font-weight:500; box-shadow:0 6px 20px rgba(43,38,32,0.2); display:flex; align-items:center; gap:10px; animation:toastIn 0.3s ease; max-width:360px; border-left:4px solid var(--gold); }
.toast-bee.success { border-left-color:var(--green); }
.toast-bee.error { border-left-color:var(--red); }
.toast-bee.warning { border-left-color:var(--amber); }
.toast-bee i { font-size:18px; }
.toast-bee .toast-close { margin-left:auto; cursor:pointer; opacity:0.5; background:none; border:none; color:var(--white); font-size:16px; padding:0 0 0 8px; }
.toast-bee .toast-close:hover { opacity:1; }
@keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }
.toast-bee.out { animation:toastOut 0.25s ease forwards; }
</style>
<script>
function showToast(message, type) {
    type = type || 'success';
    var icons = { success:'ti-circle-check-filled', error:'ti-alert-circle-filled', warning:'ti-alert-triangle-filled' };
    var c = document.getElementById('toast-container');
    var t = document.createElement('div');
    t.className = 'toast-bee ' + type;
    t.innerHTML = '<i class="ti ' + (icons[type]||icons.success) + '"></i><span>' + message + '</span><button class="toast-close">&times;</button>';
    c.appendChild(t);
    t.querySelector('.toast-close').onclick = function(){ t.classList.add('out'); setTimeout(function(){ t.remove(); }, 260); };
    setTimeout(function(){ if (t.parentNode) { t.classList.add('out'); setTimeout(function(){ t.remove(); }, 260); } }, 3500);
}

(function () {
    var header = document.querySelector('.app-header');
    if (header) {
        var onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > 10);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();
</script>
@include('partials.booking-modal')

@stack('scripts')
</body>
</html>
