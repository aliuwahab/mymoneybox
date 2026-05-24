<?php

namespace App\Http\Controllers;

use App\Models\EventBox;
use App\Models\EventBoxTicket;
use App\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventBoxController extends Controller
{
    // ── Auth: owner's event list ──────────────────────────────────────────────

    public function index()
    {
        $eventBoxes = auth()->user()->eventBoxes()->latest()->paginate(12);

        return view('events.index', compact('eventBoxes'));
    }

    // ── Auth: create form ─────────────────────────────────────────────────────

    public function create()
    {
        return view('events.create');
    }

    // ── Auth: store new event ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'venue'        => ['nullable', 'string', 'max:255'],
            'event_date'   => ['required', 'date', 'after:now'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
            'capacity'     => ['nullable', 'integer', 'min:1'],
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);

        $eventBox = auth()->user()->eventBoxes()->create([
            ...$validated,
            'slug'   => $slug,
            'status' => 'draft',
        ]);

        return redirect()->route('events.show', $eventBox->slug)
            ->with('success', 'Event created successfully.');
    }

    // ── Auth: edit form ───────────────────────────────────────────────────────

    public function edit(EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        return view('events.edit', compact('eventBox'));
    }

    // ── Auth: update event ────────────────────────────────────────────────────

    public function update(Request $request, EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'venue'        => ['nullable', 'string', 'max:255'],
            'event_date'   => ['required', 'date'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
            'capacity'     => ['nullable', 'integer', 'min:1'],
        ]);

        $eventBox->update($validated);

        return redirect()->route('events.show', $eventBox->slug)
            ->with('success', 'Event updated successfully.');
    }

    // ── Auth: soft delete ─────────────────────────────────────────────────────

    public function destroy(EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $eventBox->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted.');
    }

    // ── Auth: owner dashboard ─────────────────────────────────────────────────

    public function eventDashboard(EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $tickets = $eventBox->tickets()->with('redeemedBy')->latest()->get();
        $revenue = $eventBox->tickets_sold * (float) $eventBox->ticket_price;

        return view('events.dashboard', compact('eventBox', 'tickets', 'revenue'));
    }

    // ── Auth: update status ───────────────────────────────────────────────────

    public function updateStatus(Request $request, EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:draft,active,ended,cancelled'],
        ]);

        $eventBox->update(['status' => $validated['status']]);

        return back()->with('success', 'Event status updated.');
    }

    // ── Public: list upcoming events ──────────────────────────────────────────

    public function publicIndex()
    {
        $eventBoxes = EventBox::active()->latest('event_date')->paginate(12);

        return view('events.public-index', compact('eventBoxes'));
    }

    // ── Public: event detail page ─────────────────────────────────────────────

    public function publicShow(string $slug)
    {
        $eventBox = EventBox::where('slug', $slug)->firstOrFail();

        return view('events.show', compact('eventBox'));
    }

    // ── Public: purchase ticket ───────────────────────────────────────────────

    public function purchase(Request $request, string $slug)
    {
        $eventBox = EventBox::where('slug', $slug)->where('status', 'active')->firstOrFail();

        if (!$eventBox->canPurchase()) {
            return back()->with('error', $eventBox->isSoldOut()
                ? 'This event is sold out.'
                : 'Tickets are not available.');
        }

        $validated = $request->validate([
            'buyer_name'  => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $reference = 'EVT-' . strtoupper(Str::random(16));

        $payment = app(PaymentManager::class)->initializePayment([
            'email'       => $validated['buyer_email'],
            'amount'      => $eventBox->ticket_price,
            'currency'    => 'GHS',
            'reference'   => $reference,
            'return_url'  => route('events.confirmation', [$slug, $reference]),
            'webhook_url' => route('trendipay.webhook'),
            'description' => "Ticket for {$eventBox->title}",
            'metadata'    => [
                'event_box_id' => $eventBox->id,
                'event_title'  => $eventBox->title,
            ],
        ]);

        if (!$payment['success']) {
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed.');
        }

        EventBoxTicket::create([
            'event_box_id'      => $eventBox->id,
            'buyer_name'        => $validated['buyer_name'],
            'buyer_email'       => $validated['buyer_email'],
            'buyer_phone'       => $validated['buyer_phone'] ?? null,
            'amount'            => $eventBox->ticket_price,
            'payment_reference' => $reference,
            'payment_status'    => 'pending',
        ]);

        return redirect($payment['payment_url']);
    }

    // ── Public: post-purchase confirmation ────────────────────────────────────

    public function confirmation(string $slug, string $reference)
    {
        $eventBox = EventBox::where('slug', $slug)->firstOrFail();
        $ticket   = EventBoxTicket::where('payment_reference', $reference)
            ->where('event_box_id', $eventBox->id)
            ->first();

        return view('events.confirmation', compact('eventBox', 'ticket'));
    }
}