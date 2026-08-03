<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BeeG Events') }} | @yield('title', 'Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}" alt="{{ setting('site_name', 'BeeG Events') }}" class="sidebar-brand-logo">
            @else
                <div class="brand-icon">B</div>
                <div class="brand-text">Bee<span>G</span></div>
            @endif
        </div>

        <div class="sidebar-menu">
            @php
                $incompleteKycCount = \App\Models\VendorProfile::incompleteKyc()->count();
            @endphp
            <div class="menu-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ti ti-dashboard"></i> Dashboard
            </a>

            <div class="menu-label">Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="ti ti-users"></i> Users
            </a>
            <a href="{{ route('admin.vendors.index') }}" class="nav-link {{ request()->routeIs('admin.vendors.*') && ! request()->routeIs('admin.vendors.pending') && request('kyc') !== 'incomplete' ? 'active' : '' }}">
                <i class="ti ti-building-store"></i> Vendors
                @if(\App\Models\VendorProfile::where('status', 'pending')->count() > 0)
                    <span class="badge bg-warning">{{ \App\Models\VendorProfile::where('status', 'pending')->count() }}</span>
                @endif
                @if($incompleteKycCount > 0)
                    <span class="badge bg-danger" title="Incomplete KYC">{{ $incompleteKycCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.vendors.pending') }}" class="nav-link {{ request()->routeIs('admin.vendors.pending') ? 'active' : '' }}">
                <i class="ti ti-clock"></i> Pending Vendors
                @if(\App\Models\VendorProfile::where('status', 'pending')->count() > 0)
                    <span class="badge bg-warning">{{ \App\Models\VendorProfile::where('status', 'pending')->count() }}</span>
                @endif
                @if($incompleteKycCount > 0)
                    <span class="badge bg-danger" title="Incomplete KYC">{{ $incompleteKycCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.vendors.index', ['kyc' => 'incomplete']) }}" class="nav-link {{ request('kyc') === 'incomplete' ? 'active' : '' }}">
                <i class="ti ti-shield-check"></i> KYC
                @if($incompleteKycCount > 0)
                    <span class="badge bg-danger">{{ $incompleteKycCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i class="ti ti-calendar-event"></i> Bookings
                @if(\App\Models\Booking::where('status', 'requested')->count() > 0)
                    <span class="badge bg-warning">{{ \App\Models\Booking::where('status', 'requested')->count() }}</span>
                @endif
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="ti ti-category"></i> Categories
            </a>
            <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                <i class="ti ti-box"></i> Packages
            </a>
            <a href="{{ route('admin.package-purchases.index') }}" class="nav-link {{ request()->routeIs('admin.package-purchases.*') ? 'active' : '' }}">
                <i class="ti ti-receipt-2"></i> Package Purchases
                @if(\App\Models\VendorPackagePurchase::where('status', 'pending')->count() > 0)
                    <span class="badge bg-warning">{{ \App\Models\VendorPackagePurchase::where('status', 'pending')->count() }}</span>
                @endif
            </a>
            <a href="{{ route('admin.payouts.index') }}" class="nav-link {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">
                <i class="ti ti-wallet"></i> Payouts
                @if(\App\Models\Payout::where('status', 'pending')->count() > 0)
                    <span class="badge bg-warning">{{ \App\Models\Payout::where('status', 'pending')->count() }}</span>
                @endif
            </a>

            <div class="menu-label">Content</div>
            <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                <i class="ti ti-news"></i> Blog
            </a>
            <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
                <i class="ti ti-users-group"></i> Corporate Leads
            </a>

            <div class="menu-label">Support</div>
            <a href="{{ route('admin.disputes.index') }}" class="nav-link {{ request()->routeIs('admin.disputes.*') ? 'active' : '' }}">
                <i class="ti ti-alert-triangle"></i> Disputes
                @if(\App\Models\Dispute::where('status', 'open')->count() > 0)
                    <span class="badge bg-danger">{{ \App\Models\Dispute::where('status', 'open')->count() }}</span>
                @endif
            </a>

            <div class="menu-label">Site</div>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="ti ti-settings"></i> Settings
            </a>
            <a href="{{ route('browse.index') }}" class="nav-link" target="_blank">
                <i class="ti ti-external-link"></i> View Site
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('admin.profile.edit') }}" class="user-info" style="text-decoration:none;color:inherit;" title="Edit profile">
                <div class="avatar" style="overflow:hidden;">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/'.auth()->user()->avatar_path) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <i class="ti ti-settings" style="margin-left:auto;color:var(--text-muted);" title="Profile settings"></i>
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
                <div class="header-search">
                    <i class="ti ti-search"></i>
                    <input type="text" placeholder="Search anything..." id="adminSearch" onkeyup="if(event.key==='Enter') alert('Search: '+this.value)">
                </div>
                @include('partials.notification-bell')
            </div>
        </header>

        <div class="page-content">
            @yield('content')
        </div>

        <footer class="footer">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">BeeG Events</a>. All rights reserved. Crafted with <i class="ti ti-heart text-gold"></i>
        </footer>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);

        function showToast(type, title, message) {
            const icons = { success: 'ti ti-circle-check', error: 'ti ti-alert-circle', warning: 'ti ti-alert-triangle', info: 'ti ti-info-circle' };
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'admin-toast';
            toast.innerHTML = `
                <div class="toast-icon ${type}"><i class="${icons[type] || icons.info}"></i></div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.closest('.admin-toast').remove()">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => { toast.style.animation = 'slideOut 0.3s ease forwards'; setTimeout(() => toast.remove(), 300); }, 4000);
        }

        @if(session('success'))
            showToast('success', 'Success', '{!! addslashes(session('success')) !!}');
        @endif
        @if(session('error'))
            showToast('error', 'Error', '{!! addslashes(session('error')) !!}');
        @endif
        @if(session('warning'))
            showToast('warning', 'Warning', '{!! addslashes(session('warning')) !!}');
        @endif
        @if(session('info'))
            showToast('info', 'Info', '{!! addslashes(session('info')) !!}');
        @endif
    </script>
    @stack('scripts')
</body>
</html>
