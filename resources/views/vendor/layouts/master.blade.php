<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BeeG Events') }} | Vendor | @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/browse.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.css') }}">
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @php
        $vendorProfile = auth()->user()->vendorProfile;
        $pendingInquiries = $vendorProfile ? \App\Models\Inquiry::where('vendor_profile_id', $vendorProfile->id)->where('status', 'pending')->count() : 0;
        $pendingBookings = $vendorProfile ? \App\Models\BookingItem::where('vendor_profile_id', $vendorProfile->id)->whereHas('booking', fn($q) => $q->where('status', 'requested'))->count() : 0;
    @endphp

    <aside class="sidebar" id="vendorSidebar">
        <div class="sidebar-brand">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="sidebar-brand-logo">
            @else
                <div class="brand-icon">B</div>
                <div class="brand-text">Bee<span>G</span> <small style="font-size:11px;font-weight:500;color:rgba(255,255,255,0.5);">Vendor</small></div>
            @endif
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="ti ti-dashboard"></i> Dashboard
            </a>

            <div class="menu-label">Business</div>
            <a href="{{ route('vendor.halls.index') }}" class="nav-link {{ request()->routeIs('vendor.halls.*') ? 'active' : '' }}">
                <i class="ti ti-building"></i> Halls
            </a>
            <a href="{{ route('vendor.listings.index') }}" class="nav-link {{ request()->routeIs('vendor.listings.*') ? 'active' : '' }}">
                <i class="ti ti-list-check"></i> Services
            </a>
            <a href="{{ route('vendor.packages.index') }}" class="nav-link {{ request()->routeIs('vendor.packages.*') ? 'active' : '' }}">
                <i class="ti ti-gift"></i> Packages
            </a>
            <a href="{{ route('vendor.combos.index') }}" class="nav-link {{ request()->routeIs('vendor.combos.*') ? 'active' : '' }}">
                <i class="ti ti-package"></i> My Combo
            </a>
            <a href="{{ route('vendor.menu.index') }}" class="nav-link {{ request()->routeIs('vendor.menu.*') ? 'active' : '' }}">
                <i class="ti ti-cookie"></i> Menu
            </a>

            <div class="menu-label">Operations</div>
            <a href="{{ route('vendor.bookings.index') }}" class="nav-link {{ request()->routeIs('vendor.bookings.*') ? 'active' : '' }}">
                <i class="ti ti-calendar-event"></i> Bookings
                @if($pendingBookings > 0)
                    <span class="badge bg-warning">{{ $pendingBookings }}</span>
                @endif
            </a>
            <a href="{{ route('vendor.calendar') }}" class="nav-link {{ request()->routeIs('vendor.calendar') ? 'active' : '' }}">
                <i class="ti ti-calendar-plus"></i> Calendar
            </a>
            <a href="{{ route('vendor.inquiries.index') }}" class="nav-link {{ request()->routeIs('vendor.inquiries.*') ? 'active' : '' }}">
                <i class="ti ti-mail"></i> Inquiries
                @if($pendingInquiries > 0)
                    <span class="badge bg-warning">{{ $pendingInquiries }}</span>
                @endif
            </a>

            <div class="menu-label">Account</div>
            <a href="{{ route('vendor.profile.create') }}" class="nav-link {{ request()->routeIs('vendor.profile.*') ? 'active' : '' }}">
                <i class="ti ti-settings"></i> Business Profile
            </a>
            <a href="{{ route('browse.index') }}" class="nav-link" target="_blank">
                <i class="ti ti-external-link"></i> View Site
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('vendor.profile.create') }}" class="user-info" style="text-decoration:none;color:inherit;">
                <div class="avatar" style="overflow:hidden;">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/'.auth()->user()->avatar_path) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Vendor</div>
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
                    <input type="text" name="q" placeholder="Search site..." value="{{ request('q') }}">
                </form>
                @include('partials.notification-bell')
                <div class="vd-user-dropdown" style="position:relative;">
                    <button type="button" class="vd-dropdown-trigger" onclick="this.nextElementSibling.classList.toggle('show')" style="display:flex;align-items:center;gap:8px;background:none;border:none;cursor:pointer;padding:4px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--gold);color:var(--charcoal);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span style="font-size:13px;font-weight:600;color:var(--text-primary);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</span>
                        <i class="ti ti-chevron-down" style="font-size:11px;color:var(--text-muted);"></i>
                    </button>
                    <div class="vd-dropdown-menu" style="display:none;position:absolute;right:0;top:calc(100% + 6px);min-width:200px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 32px rgba(43,38,32,0.15);padding:6px;z-index:10000;">
                        <a href="{{ route('vendor.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:13px;color:var(--text-primary);text-decoration:none;">
                            <i class="ti ti-dashboard" style="color:var(--gold-dark);"></i> Dashboard
                        </a>
                        <a href="{{ route('vendor.profile.create') }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:13px;color:var(--text-primary);text-decoration:none;">
                            <i class="ti ti-settings" style="color:var(--gold-dark);"></i> Business Profile
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
            document.getElementById('vendorSidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.vd-user-dropdown')) {
                document.querySelectorAll('.vd-dropdown-menu.show').forEach(function(m) { m.classList.remove('show'); });
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
