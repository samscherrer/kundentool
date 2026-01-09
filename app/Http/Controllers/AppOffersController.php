<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\OfferPosition;
use App\Services\StreamService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppOffersController extends Controller
{
    public function index(): View
    {
        $offers = Offer::latest()->get();

        return view('app.offers.index', compact('offers'));
    }

    public function create(): View
    {
        return view('app.offers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer'],
            'title' => ['required', 'string'],
        ]);

        $offer = Offer::create([
            'tenant_id' => Auth::user()->tenant_id,
            'organization_id' => $data['organization_id'],
            'title' => $data['title'],
            'status' => 'draft',
            'version_number' => 1,
            'created_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('app.offers.edit', $offer->id);
    }

    public function edit(int $id): View
    {
        $offer = Offer::findOrFail($id);
        $positions = $offer->positions()->get();

        return view('app.offers.edit', compact('offer', 'positions'));
    }

    public function addPosition(Request $request, int $id): RedirectResponse
    {
        $offer = Offer::findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'pricing_type' => ['required', 'string'],
            'budget_hours' => ['nullable', 'numeric'],
            'budget_amount_cents' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $offer->positions()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'pricing_type' => $data['pricing_type'],
            'budget_hours' => $data['budget_hours'] ?? null,
            'budget_amount_cents' => $data['budget_amount_cents'] ?? null,
            'currency' => $data['currency'] ?? 'CHF',
            'status' => 'draft',
        ]);

        return back()->with('status', 'Position hinzugefügt.');
    }

    public function send(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $offer = Offer::findOrFail($id);

        $offer->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $stream->log(
            Auth::user(),
            'order',
            $offer->id,
            'offer_sent',
            'public',
            ['offer_id' => $offer->id, 'version' => $offer->version_number],
            $offer->organization_id
        );

        $audit->log(Auth::user(), 'offer_sent', 'offer', $offer->id);

        return redirect()->route('app.offers.index')->with('status', 'Offerte gesendet.');
    }
}
