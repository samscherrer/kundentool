<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalEstimatesController extends Controller
{
    public function index(): View
    {
        $estimates = Estimate::whereHas('ticket', function ($query) {
            $query->where('organization_id', Auth::user()->organization_id);
        })->latest()->get();

        return view('portal.estimates.index', compact('estimates'));
    }

    public function decide(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $estimate = Estimate::findOrFail($id);

        if (! Auth::user()->hasRole('customer_admin')) {
            abort(403);
        }

        $data = $request->validate([
            'decision' => ['required', 'in:approved,approved_with_changes,changes_requested'],
            'decision_note' => ['nullable', 'string'],
        ]);

        $estimate->update([
            'status' => $data['decision'],
            'decided_at' => now(),
            'decided_by_user_id' => Auth::id(),
            'decision_note' => $data['decision_note'] ?? null,
        ]);

        $stream->log(
            Auth::user(),
            'ticket',
            $estimate->ticket_id,
            'estimate_decided',
            'public',
            [
                'estimate_id' => $estimate->id,
                'decision' => $data['decision'],
                'decision_note' => $data['decision_note'] ?? null,
            ],
            $estimate->ticket->organization_id
        );

        $audit->log(Auth::user(), 'estimate_decided', 'estimate', $estimate->id);

        return back()->with('status', 'Entscheidung gespeichert.');
    }
}
