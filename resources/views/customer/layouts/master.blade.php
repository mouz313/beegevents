<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BeeG Events') }} | Customer | @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/browse.css') }}">
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @php
        $pendingBookingCount = \App\Models\Booking::where('customer_id', auth()->id())->whereIn('status', ['requested', 'pending'])->count();
        $cartCount = count(\Illuminate\Support\Facades\Session::get('cart', []));
    @endphp

    <aside class="sidebar" id="customerSidebar">
        <div class="sidebar-brand">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="sidebar-brand-logo">
            @else
                <div class="brand-icon">B</div>
                <div class="brand-text">Bee<span>G</span> <small style="font-size:11px;font-weight:500;color:rgba(255,255,255,0.5);">Customer</small></div>
            @endif
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <i class="ti ti-dashboard"></i> Dashboard
            </a>

            <div class="menu-label">Bookings</div>
            <a href="{{ route('customer.bookings.index') }}" class="nav-link {{ request()->routeIs('customer.bookings.*') || request()->routeIs('customer.messages.*') ? 'active' : '' }}">
                <i class="ti ti-calendar-event"></i> My Bookings
                @if($pendingBookingCount > 0)
                    <span class="badge bg-warning">{{ $pendingBookingCount }}</span>
                @endif
            </a>
            <a href="{{ route('customer.bookings.index') }}" class="nav-link">
                <i class="ti ti-wallet"></i> Payments
            </a>

            <div class="menu-label">Shopping</div>
            <a href="{{ route('customer.cart') }}" class="nav-link {{ request()->routeIs('customer.cart') || request()->routeIs('customer.checkout') ? 'active' : '' }}">
                <i class="ti ti-shopping-cart"></i> Cart
                @if($cartCount > 0)
                    <span class="badge bg-warning">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('customer.budget.match.index') }}" class="nav-link {{ request()->routeIs('customer.budget.*') ? 'active' : '' }}">
                <i class="ti ti-wand"></i> Budget Match
            </a>
            <a href="{{ route('customer.quotations.index') }}" class="nav-link {{ request()->routeIs('customer.quotations.*') ? 'active' : '' }}">
                <i class="ti ti-file-invoice"></i> Corporate Quotations
            </a>
            <a href="{{ route('browse.index') }}" class="nav-link">
                <i class="ti ti-building-arch"></i> Browse Venues
            </a>

            <div class="menu-label">Account</div>
            <a href="{{ route('browse.index') }}" class="nav-link" target="_blank">
                <i class="ti ti-external-link"></i> View Site
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('customer.dashboard') }}" class="user-info" style="text-decoration:none;color:inherit;">
                <div class="avatar" style="overflow:hidden;">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/'.auth()->user()->avatar_path) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Customer</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2 w-100">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm w-100" style="border:1px solid var(--border);">
                    <i class="ti ti-logout"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <header class="top-header">
            <div class="d-flex align-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="ti ti-menu-2"></i>
                </button>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="header-actions">
                <form class="header-search" action="{{ route('browse.search') }}" method="GET">
                    <i class="ti ti-search"></i>
                    <input type="text" name="q" placeholder="Search venues..." value="{{ request('q') }}">
                </form>
                @include('partials.notification-bell')
                <div class="cd-user-dropdown" style="position:relative;">
                    <button type="button" class="cd-dropdown-trigger" onclick="this.nextElementSibling.classList.toggle('show')" style="display:flex;align-items:center;gap:8px;background:none;border:none;cursor:pointer;padding:4px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--gold);color:var(--charcoal);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span style="font-size:13px;font-weight:600;color:var(--text-primary);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</span>
                        <i class="ti ti-chevron-down" style="font-size:11px;color:var(--text-muted);"></i>
                    </button>
                    <div class="cd-dropdown-menu" style="display:none;position:absolute;right:0;top:calc(100% + 6px);min-width:200px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 32px rgba(43,38,32,0.15);padding:6px;z-index:10000;">
                        <a href="{{ route('customer.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:13px;color:var(--text-primary);text-decoration:none;">
                            <i class="ti ti-dashboard" style="color:var(--gold-dark);"></i> Dashboard
                        </a>
                        <a href="{{ route('customer.bookings.index') }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:13px;color:var(--text-primary);text-decoration:none;">
                            <i class="ti ti-calendar-event" style="color:var(--gold-dark);"></i> My Bookings
                        </a>
                        <div style="border-top:1px solid var(--border);margin:6px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="display:flex;align-items:center;gap:10px;width:100%;padding:9px 12px;border:none;background:none;border-radius:8px;font-size:13px;color:var(--red);cursor:pointer;text-align:left;">
                                <i class="ti ti-logout"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if(!auth()->user()->hasVerifiedEmail())
            <div style="background:var(--light-honey);border-bottom:1px solid var(--gold);padding:10px 30px;text-align:center;font-size:13px;color:var(--charcoal);">
                <i class="ti ti-mail-warning" style="color:var(--gold-dark);"></i>
                Please verify your email address.
                <a href="{{ route('verification.notice') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:underline;margin-left:4px;">Resend verification</a>
            </div>
        @endif

        <div class="page-content">
            @yield('content')
        </div>

        <footer class="footer">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">BeeG Events</a>. All rights reserved.
        </footer>
    </div>

    <div id="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('customerSidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.cd-user-dropdown')) {
                document.querySelectorAll('.cd-dropdown-menu.show').forEach(function(m) { m.classList.remove('show'); });
            }
        });
    </script>
    <style>
        #toast-container { position: fixed; top: 80px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
        .toast-bee { background: var(--charcoal); color: var(--white); padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 500; box-shadow: 0 6px 20px rgba(43,38,32,0.2); display: flex; align-items: center; gap: 10px; animation: toastIn 0.3s ease; max-width: 360px; border-left: 4px solid var(--gold); }
        .toast-bee.success { border-left-color: var(--green); }
        .toast-bee.error { border-left-color: var(--red); }
        .toast-bee.warning { border-left-color: var(--amber); }
        .toast-bee i { font-size: 18px; }
        .toast-bee .toast-close { margin-left: auto; cursor: pointer; opacity: 0.5; background: none; border: none; color: var(--white); font-size: 16px; padding: 0 0 0 8px; }
        .toast-bee .toast-close:hover { opacity: 1; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(40px); } }
        .toast-bee.out { animation: toastOut 0.25s ease forwards; }
    </style>
    <script>
        function showToast(message, type) {
            type = type || 'success';
            var icons = { success: 'ti-circle-check-filled', error: 'ti-alert-circle-filled', warning: 'ti-alert-triangle-filled' };
            var c = document.getElementById('toast-container');
            var t = document.createElement('div');
            t.className = 'toast-bee ' + type;
            t.innerHTML = '<i class="ti ' + (icons[type] || icons.success) + '"></i><span>' + message + '</span><button class="toast-close">&times;</button>';
            c.appendChild(t);
            t.querySelector('.toast-close').onclick = function() { t.classList.add('out'); setTimeout(function() { t.remove(); }, 260); };
            setTimeout(function() { if (t.parentNode) { t.classList.add('out'); setTimeout(function() { t.remove(); }, 260); } }, 3500);
        }
    </script>
    @stack('scripts')
</body>
</html>
