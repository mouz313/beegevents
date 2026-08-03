<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CorporateLead;
use App\Models\Dispute;
use App\Models\Payment;
use App\Models\User;
use App\Models\VendorPackagePurchase;
use App\Models\VendorProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalVendors' => User::where('role', 'vendor')->count(),
            'pendingVendors' => VendorProfile::where('status', 'pending')->count(),
            'verifiedVendors' => VendorProfile::where('status', 'verified')->count(),
            'blockedVendors' => VendorProfile::where('status', 'blocked')->count(),
            'totalBookings' => Booking::count(),
            'requestedBookings' => Booking::where('status', 'requested')->count(),
            'confirmedBookings' => Booking::where('status', 'confirmed')->count(),
            'completedBookings' => Booking::where('status', 'completed')->count(),
            'cancelledBookings' => Booking::where('status', 'cancelled')->count(),
            'totalRevenue' => Payment::where('status', 'received')->sum('amount'),
            'packageRevenue' => VendorPackagePurchase::whereIn('status', ['active', 'expired'])->sum('amount'),
            'activePackages' => VendorPackagePurchase::where('status', 'active')->count(),
            'pendingPackagePayments' => VendorPackagePurchase::where('status', 'pending')->count(),
            'openDisputes' => Dispute::where('status', 'open')->count(),
            'newLeads' => CorporateLead::where('status', 'new')->count(),
        ];

        $recentBookings = Booking::with('customer')->latest()->take(5)->get();
        $recentVendors = VendorProfile::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentVendors'));
    }
}
