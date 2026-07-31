<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()->latest()->take(5)->get();
        $inquiries = \App\Models\Inquiry::where('user_id', auth()->id())
            ->with('vendorProfile', 'inquiriable')
            ->latest()
            ->take(5)
            ->get();
        return view('customer.dashboard', compact('bookings', 'inquiries'));
    }
}
