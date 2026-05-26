<?php

namespace App\Http\Controllers;

use App\Models\EventBox;
use App\Models\EventBoxTicket;
use App\Models\EventBoxTicketType;
use App\Payment\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventBoxController extends Controller
{
    // ── Auth: owner's event list ──────────────────────────────────────────────

    public function index()
    {
        $eventBoxes = auth()->user()->eventBoxes()->with('media')->latest()->paginate(12);

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
            'title'                       => ['required', 'string', 'max:255'],
            'tagline'                     => ['nullable', 'string', 'max:180'],
            'description'                 => ['nullable', 'string'],
            'venue'                       => ['nullable', 'string', 'max:255'],
            'organizer_name'              => ['nullable', 'string', 'max:255'],
            'contact_email'               => ['nullable', 'email', 'max:255'],
            'contact_phone'               => ['nullable', 'string', 'max:30'],
            'accent_color'                => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'event_date'                  => ['required', 'date', 'after:now'],
            'capacity'                    => ['nullable', 'integer', 'min:1'],
            'cover_image'                 => ['nullable', 'image', 'max:5120'],
            'ticket_types'                => ['required', 'array', 'min:1'],
            'ticket_types.*.name'         => ['required', 'string', 'max:100'],
            'ticket_types.*.price'        => ['required', 'numeric', 'min:0'],
            'ticket_types.*.capacity'     => ['nullable', 'integer', 'min:1'],
            'ticket_types.*.description'  => ['nullable', 'string', 'max:255'],
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);

        $eventBox = auth()->user()->eventBoxes()->create([
            'title'          => $validated['title'],
            'tagline'        => $validated['tagline'] ?? null,
            'description'    => $validated['description'] ?? null,
            'venue'          => $validated['venue'] ?? null,
            'organizer_name' => $validated['organizer_name'] ?? null,
            'contact_email'  => $validated['contact_email'] ?? null,
            'contact_phone'  => $validated['contact_phone'] ?? null,
            'accent_color'   => $validated['accent_color'] ?? null,
            'event_date'     => $validated['event_date'],
            'capacity'       => $validated['capacity'] ?? null,
            'slug'           => $slug,
            'status'         => 'draft',
        ]);

        if ($request->hasFile('cover_image')) {
            $eventBox->addMediaFromRequest('cover_image')->toMediaCollection('cover');
        }

        foreach ($validated['ticket_types'] as $index => $typeData) {
            $eventBox->ticketTypes()->create([
                'name'        => $typeData['name'],
                'description' => $typeData['description'] ?? null,
                'price'       => $typeData['price'],
                'capacity'    => $typeData['capacity'] ?? null,
                'sort_order'  => $index,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['id' => $eventBox->id, 'slug' => $eventBox->slug]);
        }

        return redirect()->route('events.dashboard', $eventBox)
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
            'title'                       => ['required', 'string', 'max:255'],
            'tagline'                     => ['nullable', 'string', 'max:180'],
            'description'                 => ['nullable', 'string'],
            'venue'                       => ['nullable', 'string', 'max:255'],
            'organizer_name'              => ['nullable', 'string', 'max:255'],
            'contact_email'               => ['nullable', 'email', 'max:255'],
            'contact_phone'               => ['nullable', 'string', 'max:30'],
            'accent_color'                => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'event_date'                  => ['required', 'date'],
            'capacity'                    => ['nullable', 'integer', 'min:1'],
            'cover_image'                 => ['nullable', 'image', 'max:5120'],
            'gallery_images'              => ['nullable', 'array', 'max:20'],
            'gallery_images.*'            => ['image', 'max:5120'],
            'ticket_types'                => ['required', 'array', 'min:1'],
            'ticket_types.*.id'           => ['nullable', 'integer'],
            'ticket_types.*.name'         => ['required', 'string', 'max:100'],
            'ticket_types.*.price'        => ['required', 'numeric', 'min:0'],
            'ticket_types.*.capacity'     => ['nullable', 'integer', 'min:1'],
            'ticket_types.*.description'  => ['nullable', 'string', 'max:255'],
        ]);

        $eventBox->update([
            'title'          => $validated['title'],
            'tagline'        => $validated['tagline'] ?? null,
            'description'    => $validated['description'] ?? null,
            'venue'          => $validated['venue'] ?? null,
            'organizer_name' => $validated['organizer_name'] ?? null,
            'contact_email'  => $validated['contact_email'] ?? null,
            'contact_phone'  => $validated['contact_phone'] ?? null,
            'accent_color'   => $validated['accent_color'] ?? null,
            'event_date'     => $validated['event_date'],
            'capacity'       => $validated['capacity'] ?? null,
        ]);

        if ($request->boolean('remove_cover')) {
            $eventBox->clearMediaCollection('cover');
        } elseif ($request->hasFile('cover_image')) {
            $eventBox->addMediaFromRequest('cover_image')->toMediaCollection('cover');
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $eventBox->addMedia($image)->toMediaCollection('gallery');
            }
        }

        // Smart ticket type update: preserve types that have sales
        $submittedIds = collect($validated['ticket_types'])
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values();

        // Delete only types not submitted AND with zero sales
        $eventBox->ticketTypes()
            ->when($submittedIds->isNotEmpty(), fn($q) => $q->whereNotIn('id', $submittedIds))
            ->where('sold', 0)
            ->delete();

        foreach ($validated['ticket_types'] as $index => $typeData) {
            $id = isset($typeData['id']) ? (int) $typeData['id'] : null;

            if ($id && $existing = $eventBox->ticketTypes()->find($id)) {
                $existing->update([
                    'name'        => $typeData['name'],
                    'description' => $typeData['description'] ?? null,
                    'price'       => $typeData['price'],
                    'capacity'    => $typeData['capacity'] ?? null,
                    'sort_order'  => $index,
                ]);
            } else {
                $eventBox->ticketTypes()->create([
                    'name'        => $typeData['name'],
                    'description' => $typeData['description'] ?? null,
                    'price'       => $typeData['price'],
                    'capacity'    => $typeData['capacity'] ?? null,
                    'sort_order'  => $index,
                ]);
            }
        }

        return redirect()->route('events.dashboard', $eventBox)
            ->with('success', 'Event updated successfully.');
    }

    // ── Auth: soft delete ─────────────────────────────────────────────────────

    public function destroy(EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);
        abort_if($eventBox->tickets_sold > 0, 422, 'Cannot delete an event with purchased tickets.');

        $eventBox->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted.');
    }

    // ── Auth: remove single gallery image ─────────────────────────────────────

    public function removeGalleryImage(EventBox $eventBox, int $mediaId): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $media = $eventBox->getMedia('gallery')->firstWhere('id', $mediaId);
        abort_if(!$media, 404);
        $media->delete();

        return response()->json(['status' => 'ok']);
    }

    // ── Auth: owner dashboard ─────────────────────────────────────────────────

    public function eventDashboard(EventBox $eventBox)
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $tickets          = $eventBox->tickets()->with(['redeemedBy', 'ticketType'])->latest()->get();
        $ticketTypes      = $eventBox->ticketTypes()->get();
        $completedTickets = $tickets->where('payment_status.value', 'completed');
        $pendingTickets   = $tickets->where('payment_status.value', 'pending');
        $voidedTickets    = $tickets->where('status.value', 'voided');
        $redeemedTickets  = $tickets->where('status.value', 'redeemed');
        $revenue          = (float) $completedTickets->sum('amount');
        $feePercentage    = $eventBox->getEffectiveFeePercentage();
        $platformFee      = round($revenue * ($feePercentage / 100), 2);
        $netPayout        = max(0, round($revenue - $platformFee, 2));
        $coverUrl         = $eventBox->getCoverImageUrl();
        $gallery          = $eventBox->getGalleryUrls();

        return view('events.dashboard', compact(
            'eventBox',
            'tickets',
            'ticketTypes',
            'completedTickets',
            'pendingTickets',
            'voidedTickets',
            'redeemedTickets',
            'revenue',
            'feePercentage',
            'platformFee',
            'netPayout',
            'coverUrl',
            'gallery',
        ));
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

    public function publicIndex(Request $request)
    {
        $query = EventBox::with(['ticketTypes', 'media'])
            ->active()
            ->orderBy('event_date', 'asc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('venue', 'like', '%' . $request->search . '%');
            });
        }

        $eventBoxes = $query->paginate(12);

        return view('events.public-index', compact('eventBoxes'));
    }

    // ── Public: event detail page ─────────────────────────────────────────────

    public function publicShow(string $slug)
    {
        $eventBox = EventBox::with('ticketTypes')->where('slug', $slug)->firstOrFail();

        return view('events.show', compact('eventBox'));
    }

    // ── Public: purchase ticket(s) ────────────────────────────────────────────

    public function purchase(Request $request, string $slug)
    {
        $eventBox = EventBox::with('ticketTypes')->where('slug', $slug)->where('status', 'active')->firstOrFail();

        if (!$eventBox->canPurchase()) {
            return back()->with('error', $eventBox->isSoldOut()
                ? 'This event is sold out.'
                : 'Tickets are not available.');
        }

        $validated = $request->validate([
            'ticket_type_id' => ['required', 'integer'],
            'quantity'       => ['nullable', 'integer', 'min:1', 'max:10'],
            'buyer_name'     => ['required', 'string', 'max:255'],
            'buyer_email'    => ['required', 'email', 'max:255'],
            'buyer_phone'    => ['nullable', 'string', 'max:30'],
        ]);

        $quantity   = (int) ($validated['quantity'] ?? 1);
        $ticketType = $eventBox->ticketTypes()->findOrFail($validated['ticket_type_id']);

        if (!$ticketType->isAvailable()) {
            return back()->with('error', "'{$ticketType->name}' tickets are sold out.");
        }

        // Ensure enough capacity remains for the requested quantity
        if ($ticketType->capacity !== null) {
            $remaining = $ticketType->availableCount();
            if ($quantity > $remaining) {
                return back()->with('error', "Only {$remaining} ticket(s) remaining for '{$ticketType->name}'.");
            }
        }

        if ($eventBox->capacity !== null) {
            $remaining = $eventBox->capacity - $eventBox->tickets_sold;
            if ($quantity > $remaining) {
                return back()->with('error', "Only {$remaining} ticket(s) remaining for this event.");
            }
        }

        $totalAmount    = $ticketType->price * $quantity;
        $groupReference = 'EVT-' . strtoupper(Str::random(16));

        $payment = app(PaymentManager::class)->initializePayment([
            'email'       => $validated['buyer_email'],
            'amount'      => $totalAmount,
            'currency'    => 'GHS',
            'reference'   => $groupReference,
            'return_url'  => route('events.confirmation', [$slug, $groupReference]),
            'webhook_url' => route('trendipay.webhook'),
            'description' => "{$quantity}× {$ticketType->name} ticket for {$eventBox->title}",
            'metadata'    => [
                'event_box_id' => $eventBox->id,
                'event_title'  => $eventBox->title,
                'ticket_type'  => $ticketType->name,
                'quantity'     => $quantity,
            ],
        ]);

        if (!$payment['success']) {
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed.');
        }

        for ($i = 1; $i <= $quantity; $i++) {
            // For a single ticket, payment_reference == group_reference for backward compat
            $ticketRef = $quantity > 1 ? $groupReference . '-' . $i : $groupReference;

            EventBoxTicket::create([
                'event_box_id'      => $eventBox->id,
                'ticket_type_id'    => $ticketType->id,
                'ticket_type_name'  => $ticketType->name,
                'buyer_name'        => $validated['buyer_name'],
                'buyer_email'       => $validated['buyer_email'],
                'buyer_phone'       => $validated['buyer_phone'] ?? null,
                'amount'            => $ticketType->price,
                'payment_reference' => $ticketRef,
                'payment_group'     => $groupReference,
                'quantity'          => 1,
                'payment_status'    => 'pending',
            ]);
        }

        return redirect($payment['payment_url']);
    }

    // ── Public: post-purchase confirmation ────────────────────────────────────

    public function confirmation(string $slug, string $reference)
    {
        $eventBox = EventBox::where('slug', $slug)->firstOrFail();

        // Support both single (payment_reference) and multi-ticket (payment_group) lookups
        $tickets = EventBoxTicket::where('event_box_id', $eventBox->id)
            ->where(function ($q) use ($reference) {
                $q->where('payment_reference', $reference)
                  ->orWhere('payment_group', $reference);
            })
            ->get();

        $ticket  = $tickets->first();
        $pending = $ticket && $ticket->payment_status->value === 'pending';

        return view('events.confirmation', compact('eventBox', 'ticket', 'tickets', 'pending', 'reference'));
    }

    // ── Public: ticket status check (for confirmation page polling) ───────────

    public function ticketStatus(string $slug, string $reference): JsonResponse
    {
        $eventBox = EventBox::where('slug', $slug)->firstOrFail();

        $tickets = EventBoxTicket::where('event_box_id', $eventBox->id)
            ->where(function ($q) use ($reference) {
                $q->where('payment_reference', $reference)
                  ->orWhere('payment_group', $reference);
            })
            ->get();

        if ($tickets->isEmpty()) {
            return response()->json(['status' => 'not_found']);
        }

        $allCompleted = $tickets->every(fn($t) => $t->payment_status->value === 'completed');
        $anyFailed    = $tickets->contains(fn($t) => $t->payment_status->value === 'failed');

        return response()->json([
            'status'     => $allCompleted ? 'completed' : ($anyFailed ? 'failed' : 'pending'),
            'code_ready' => $allCompleted && $tickets->first()?->code !== null,
            'email'      => $tickets->first()?->buyer_email,
            'quantity'   => $tickets->count(),
        ]);
    }
}
