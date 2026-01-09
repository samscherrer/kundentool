<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Ticket;
use App\Services\StreamService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppTicketsController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::latest()->get();

        return view('app.tickets.index', compact('tickets'));
    }

    public function show(int $id): View
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        $publicStream = \App\Models\StreamEvent::where('context_type', 'ticket')
            ->where('context_id', $ticket->id)
            ->where('visibility', 'public')
            ->latest()
            ->get();

        $internalStream = \App\Models\StreamEvent::where('context_type', 'ticket')
            ->where('context_id', $ticket->id)
            ->where('visibility', 'internal')
            ->latest()
            ->get();

        return view('app.tickets.show', compact('ticket', 'publicStream', 'internalStream'));
    }

    public function addPublicMessage(Request $request, int $id, StreamService $stream): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        $data = $request->validate(['body' => ['required', 'string']]);

        $stream->log(
            Auth::user(),
            'ticket',
            $ticket->id,
            'message_public',
            'public',
            ['body' => $data['body']],
            $ticket->organization_id
        );

        return back()->with('status', 'Nachricht gesendet.');
    }

    public function addInternalNote(Request $request, int $id, StreamService $stream): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        $data = $request->validate(['body' => ['required', 'string']]);

        $stream->log(
            Auth::user(),
            'ticket',
            $ticket->id,
            'note_internal',
            'internal',
            ['body' => $data['body']],
            $ticket->organization_id
        );

        return back()->with('status', 'Notiz gespeichert.');
    }

    public function showEstimateCreate(int $id): View
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        return view('app.tickets.estimate', compact('ticket'));
    }

    public function sendEstimate(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        $data = $request->validate([
            'hours_estimate' => ['required', 'numeric', 'min:0.25'],
            'amount_estimate_cents' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'size:3'],
            'scope_note' => ['nullable', 'string'],
        ]);

        $estimate = Estimate::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'tenant_id' => $ticket->tenant_id,
                'created_by_user_id' => Auth::id(),
                'hours_estimate' => $data['hours_estimate'],
                'amount_estimate_cents' => $data['amount_estimate_cents'] ?? null,
                'currency' => $data['currency'] ?? 'CHF',
                'scope_note' => $data['scope_note'] ?? null,
                'status' => 'sent',
                'sent_at' => now(),
            ]
        );

        $stream->log(
            Auth::user(),
            'ticket',
            $ticket->id,
            'estimate_sent',
            'public',
            [
                'estimate_id' => $estimate->id,
                'hours' => $estimate->hours_estimate,
                'amount_optional' => $estimate->amount_estimate_cents,
                'currency' => $estimate->currency,
                'scope_note' => $estimate->scope_note,
            ],
            $ticket->organization_id
        );

        $audit->log(Auth::user(), 'estimate_sent', 'estimate', $estimate->id);

        return redirect()->route('app.tickets.show', $ticket->id)->with('status', 'Schätzung gesendet.');
    }

    public function taskFromTicket(int $id): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        $task = \App\Models\Task::create([
            'tenant_id' => $ticket->tenant_id,
            'organization_id' => $ticket->organization_id,
            'order_id' => $ticket->order_id,
            'title' => 'Task aus Ticket: ' . $ticket->subject,
            'status' => 'open',
            'created_by_user_id' => Auth::id(),
        ]);

        return back()->with('status', 'Task erstellt (#' . $task->id . ').');
    }
}
