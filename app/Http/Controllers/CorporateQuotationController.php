<?php

namespace App\Http\Controllers;

use App\Models\CorporateQuotation;
use Illuminate\Http\Request;

class CorporateQuotationController extends Controller
{
    public function show(string $token)
    {
        $quotation = CorporateQuotation::with('lead')->where('token', $token)->firstOrFail();

        return view('corporate-quotations.show', compact('quotation'));
    }

    public function respond(Request $request, string $token)
    {
        $validated = $request->validate(['response' => 'required|in:accepted,declined']);

        $quotation = CorporateQuotation::with('lead')->where('token', $token)->firstOrFail();
        $quotation->update(['status' => $validated['response']]);

        if ($validated['response'] === 'accepted' && $quotation->lead) {
            $quotation->lead->update(['status' => 'converted']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Your response has been recorded. Thank you!');
    }
}
