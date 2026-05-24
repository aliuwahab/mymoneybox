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
            'title'                       => ['required', 'string', 'max:255'],
            'tagline'                     => ['nullable', 'string', 'max:180'],
            'description'                 => ['nullable', 'string'],
            'venue'                       => ['nullable', 'string', 'max:255'],
            'organizer_name'              => ['nullable', 'string', 'max:255'],
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
            'accent_color'                => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'event_date'                  => ['required', 'date'],
            'capacity'                    => ['nullable', 'integer', 'min:1'],
            'cover_image'                 => ['nullable', 'image', 'max:5120'],
            'gallery_images'              => ['nullable', 'array', 'max:20'],
            'gallery_images.*'            => ['image', 'max:5120'],
            'ticket_types'                => ['required', 'array', 'min:1'],
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

        $eventBox->ticketTypes()->delete();
        foreach ($validated['ticket_types'] as $index => $typeData) {
            $eventBox->ticketTypes()->create([
                'name'        => $typeData['name'],
                'description' => $typeData['description'] ?? null,
                'price'       => $typeData['price'],
                'capacity'    => $typeData['capacity'] ?? null,
                'sort_order'  => $index,
            ]);
        }

        return redirect()->route('events.dashboard', $eventBox)
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

        $tickets     = $eventBox->tickets()->with(['redeemedBy', 'ticketType'])->latest()->get();
        $ticketTypes = $eventBox->ticketTypes()->get();
        $revenue     = $tickets->where('payment_status', 'completed')->sum('amount');

        return view('events.dashboard', compact('eventBox', 'tickets', 'ticketTypes', 'revenue'));
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

    // ── Public: purchase ticket ───────────────────────────────────────────────

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
            'buyer_name'     => ['required', 'string', 'max:255'],
            'buyer_email'    => ['required', 'email', 'max:255'],
            'buyer_phone'    => ['nullable', 'string', 'max:30'],
        ]);

        $ticketType = $eventBox->ticketTypes()->findOrFail($validated['ticket_type_id']);

        if (!$ticketType->isAvailable()) {
            return back()->with('error', "'{$ticketType->name}' tickets are sold out.");
        }

        $reference = 'EVT-' . strtoupper(Str::random(16));

        $payment = app(PaymentManager::class)->initializePayment([
            'email'       => $validated['buyer_email'],
            'amount'      => $ticketType->price,
            'currency'    => 'GHS',
            'reference'   => $reference,
            'return_url'  => route('events.confirmation', [$slug, $reference]),
            'webhook_url' => route('trendipay.webhook'),
            'description' => "{$ticketType->name} ticket for {$eventBox->title}",
            'metadata'    => [
                'event_box_id'    => $eventBox->id,
                'event_title'     => $eventBox->title,
                'ticket_type'     => $ticketType->name,
            ],
        ]);

        if (!$payment['success']) {
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed.');
        }

        EventBoxTicket::create([
            'event_box_id'      => $eventBox->id,
            'ticket_type_id'    => $ticketType->id,
            'ticket_type_name'  => $ticketType->name,
            'buyer_name'        => $validated['buyer_name'],
            'buyer_email'       => $validated['buyer_email'],
            'buyer_phone'       => $validated['buyer_phone'] ?? null,
            'amount'            => $ticketType->price,
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