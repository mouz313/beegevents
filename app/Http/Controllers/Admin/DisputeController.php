<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index()
    {
        $disputes = Dispute::with('booking', 'raisedBy')->latest()->get();
        return view('admin.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load('booking', 'raisedBy');
        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate(['resolution_notes' => 'required|string']);
        $dispute->update(['status' => 'resolved', 'resolution_notes' => $request->resolution_notes]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Dispute resolved.');
    }

    public function reject(Request $request, Dispute $dispute)
    {
        $request->validate(['resolution_notes' => 'nullable|string']);
        $dispute->update(['status' => 'rejected', 'resolution_notes' => $request->resolution_notes]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Dispute rejected.');
    }
}
