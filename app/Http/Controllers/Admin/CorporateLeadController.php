<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateLead;
use Illuminate\Http\Request;

class CorporateLeadController extends Controller
{
    public function index()
    {
        $leads = CorporateLead::latest()->get();
        return view('admin.leads.index', compact('leads'));
    }

    public function show(CorporateLead $corporateLead)
    {
        return view('admin.leads.show', compact('corporateLead'));
    }

    public function updateStatus(Request $request, CorporateLead $corporateLead)
    {
        $request->validate(['status' => 'required|in:new,contacted,converted,closed']);
        $corporateLead->update(['status' => $request->status]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Lead status updated!');
    }

    public function destroy(CorporateLead $corporateLead)
    {
        $corporateLead->delete();
        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Lead deleted.');
    }
}
