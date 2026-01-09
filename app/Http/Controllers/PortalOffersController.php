<?php

namespace App\Http\Controllers;

use App\Models\BudgetPosition;
use App\Models\Offer;
use App\Models\OfferPosition;
use App\Models\Order;
use App\Models\Task;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalOffersController extends Controller
{
    public function index(): View
    {
        $offers = Offer::where('organization_id', Auth::user()->organization_id)->latest()->get();

        return view('portal.offers.index', compact('offers'));
    }

    public function show(int $id): View
    {
        $offer = Offer::where('organization_id', Auth::user()->organization_id)->findOrFail($id);
        $positions = $offer->positions()->get();

        return view('portal.offers.show', compact('offer', 'positions'));
    }

    public function decide(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $position = OfferPosition::findOrFail($id);
        $offer = $position->offer;

        if (! Auth::user()->hasRole('customer_admin')) {
            abort(403);
        }

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
        ]);

        $position->update([
            'status' => $data['decision'],
            'approved_at' => $data['decision'] === 'approved' ? now() : null,
            'approved_by_user_id' => Auth::id(),
        ]);

        if ($data['decision'] === 'approved') {
            $order = Order::firstOrCreate([
                'tenant_id' => $offer->tenant_id,
                'organization_id' => $offer->organization_id,
                'status' => 'active',
                'title' => 'Auftrag aus Offerte #' . $offer->id,
                'created_by_user_id' => $offer->created_by_user_id,
            ]);

            $budget = BudgetPosition::create([
                'order_id' => $order->id,
                'title' => $position->title,
                'budget_hours' => $position->budget_hours,
                'budget_amount_cents' => $position->budget_amount_cents,
                'currency' => $position->currency,
                'source_offer_position_id' => $position->id,
            ]);

            Task::create([
                'tenant_id' => $offer->tenant_id,
                'organization_id' => $offer->organization_id,
                'order_id' => $order->id,
                'title' => 'Task aus Offertposition: ' . $position->title,
                'status' => 'open',
                'created_by_user_id' => $offer->created_by_user_id,
            ]);
        }

        $total = $offer->positions()->count();
        $approved = $offer->positions()->where('status', 'approved')->count();
        $rejected = $offer->positions()->where('status', 'rejected')->count();

        if ($approved === $total) {
            $offer->update(['status' => 'approved']);
        } elseif ($approved > 0 && $rejected > 0) {
            $offer->update(['status' => 'partially_approved']);
        } elseif ($rejected === $total) {
            $offer->update(['status' => 'rejected']);
        }

        $stream->log(
            Auth::user(),
            'order',
            $offer->id,
            'offer_position_decided',
            'public',
            ['offer_position_id' => $position->id, 'decision' => $data['decision']],
            $offer->organization_id
        );

        $audit->log(Auth::user(), 'offer_position_decided', 'offer_position', $position->id);

        return back()->with('status', 'Entscheidung gespeichert.');
    }
}
