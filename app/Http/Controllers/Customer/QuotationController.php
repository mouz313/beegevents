<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CorporateQuotation;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = CorporateQuotation::with('lead')
            ->whereHas('lead', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->get();

        return view('customer.quotations.index', compact('quotations'));
    }
}
