<?php

namespace App\Http\Controllers;

use App\Models\CorporateLead;
use Illuminate\Http\Request;

class CorporateLeadController extends Controller
{
    public function create()
    {
        return view('corporate-leads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'requirement_notes' => 'nullable|string',
        ]);

        CorporateLead::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Thank you! We will contact you shortly.');
    }
}
