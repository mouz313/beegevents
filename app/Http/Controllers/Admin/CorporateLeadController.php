<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CorporateQuotationMail;
use App\Models\CorporateLead;
use App\Models\CorporateQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CorporateLeadController extends Controller
{
    public function index()
    {
        $leads = CorporateLead::withCount('quotations')->latest()->get();
        return view('admin.leads.index', compact('leads'));
    }

    public function show(CorporateLead $corporateLead)
    {
        $corporateLead->load('quotations.creator');
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

    public function storeQuotation(Request $request, CorporateLead $corporateLead)
    {
        $validated = $request->validate([
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'seating_capacity' => 'nullable|integer|min:1',
            'budget' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'inclusions' => 'nullable|string',
            'notes' => 'nullable|string',
            'valid_until' => 'nullable|date|after_or_equal:today',
        ]);

        $quotation = $corporateLead->quotations()->create(array_merge($validated, [
            'token' => Str::random(40),
            'created_by' => auth()->id(),
            'status' => 'draft',
        ]));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'quotation' => $quotation]);
        }

        return redirect()->back()->with('success', 'Quotation '.$quotation->quote_no.' created.');
    }

    public function updateQuotation(Request $request, CorporateQuotation $quotation)
    {
        $validated = $request->validate([
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'seating_capacity' => 'nullable|integer|min:1',
            'budget' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'inclusions' => 'nullable|string',
            'notes' => 'nullable|string',
            'valid_until' => 'nullable|date|after_or_equal:today',
        ]);

        $quotation->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Quotation updated.');
    }

    public function updateQuotationStatus(Request $request, CorporateQuotation $quotation)
    {
        $request->validate(['status' => 'required|in:draft,sent,accepted,declined']);

        $quotation->update(['status' => $request->status]);

        if ($request->status === 'sent') {
            $lead = $quotation->lead;
            if ($lead && $lead->email) {
                try {
                    Mail::to($lead->email)->send(new CorporateQuotationMail($quotation));
                } catch (\Exception $e) {
                    \Log::warning('Corporate quotation mail failed: '.$e->getMessage());
                }
            }
        }

        if ($request->status === 'accepted' && $quotation->lead) {
            $quotation->lead->update(['status' => 'converted']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Quotation status updated.');
    }

    public function destroyQuotation(CorporateQuotation $quotation)
    {
        $quotation->delete();
        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Quotation deleted.');
    }
}
