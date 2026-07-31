<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BudgetMatchingService;
use Illuminate\Http\Request;

class BudgetMatchController extends Controller
{
    public function index()
    {
        return view('customer.budget-match');
    }

    public function match(Request $request, BudgetMatchingService $matcher)
    {
        $request->validate([
            'budget' => 'required|numeric|min:1',
            'guest_count' => 'required|integer|min:1',
            'event_type' => 'nullable|string',
        ]);

        $results = $matcher->match(
            $request->budget,
            $request->guest_count,
            $request->event_type
        );

        if ($request->ajax()) {
            return response()->json(['results' => $results]);
        }

        return view('customer.budget-match', compact('results'));
    }
}
