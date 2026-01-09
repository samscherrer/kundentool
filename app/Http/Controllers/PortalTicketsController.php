<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Document;
use App\Models\StreamEvent;
use App\Models\Ticket;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalTicketsController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::where('organization_id', Auth::user()->organization_id)
            ->latest()
            ->get();

        return view('portal.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('portal.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string'],
            'priority' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $ticket = Ticket::create([
            'tenant_id' => Auth::user()->tenant_id,
            'organization_id' => Auth::user()->organization_id,
            'subject' => $data['subject'],
            'status' => 'new',
            'priority' => $data['priority'],
            'created_by_user_id' => Auth::id(),
        ]);

        StreamEvent::create([
            'tenant_id' => Auth::user()->tenant_id,
            'organization_id' => Auth::user()->organization_id,
            'context_type' => 'ticket',
            'context_id' => $ticket->id,
            'event_type' => 'message_public',
            'visibility' => 'public',
            'actor_user_id' => Auth::id(),
            'payload_json' => ['body' => $data['message']],
        ]);

        return redirect()->route('portal.tickets.show', $ticket->id);
    }

    public function show(int $id): View
    {
        $ticket = Ticket::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);

        $publicStream = StreamEvent::where('context_type', 'ticket')
            ->where('context_id', $ticket->id)
            ->where('visibility', 'public')
            ->latest()
            ->get();

        $estimate = Estimate::where('ticket_id', $ticket->id)->first();

        $documents = Document::where('linked_context_type', 'ticket')
            ->where('linked_context_id', $ticket->id)
            ->get();

        return view('portal.tickets.show', compact('ticket', 'publicStream', 'estimate', 'documents'));
    }

    public function reply(Request $request, int $id, StreamService $stream): RedirectResponse
    {
        $ticket = Ticket::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);

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

        return back()->with('status', 'Antwort gesendet.');
    }
}
