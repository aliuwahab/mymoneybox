<?php

use App\Events\TicketIssued;
use App\Listeners\SendTicketEmail;
use App\Mail\TicketMail;
use App\Models\EventBox;
use App\Models\EventBoxTicket;
use App\Models\EventBoxTicketRefund;
use App\Models\EventBoxTicketType;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

function createEventBoxTicketValidationFixture(): array
{
    $owner = User::factory()->create();

    $eventBox = EventBox::create([
        'user_id' => $owner->id,
        'title' => 'Launch Night',
        'slug' => 'launch-night-test',
        'event_date' => now()->addWeek(),
        'status' => 'active',
        'tickets_sold' => 1,
    ]);

    $ticketType = EventBoxTicketType::create([
        'event_box_id' => $eventBox->id,
        'name' => 'General',
        'price' => 25,
        'sold' => 1,
    ]);

    $ticket = EventBoxTicket::create([
        'event_box_id' => $eventBox->id,
        'ticket_type_id' => $ticketType->id,
        'ticket_type_name' => $ticketType->name,
        'buyer_name' => 'Ama Attendee',
        'buyer_email' => 'ama@example.com',
        'buyer_phone' => '0240000000',
        'amount' => 25,
        'payment_reference' => 'EVT-TEST-VALIDATE',
        'payment_status' => 'completed',
        'payment_account_number' => '0240000000',
        'payment_method' => 'mtn',
        'code' => 'TKT-ABCD-EFGH-JKLM',
        'status' => 'unused',
    ]);

    return compact('owner', 'eventBox', 'ticket', 'ticketType');
}

it('validates an unused paid event ticket for the owner', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $this->actingAs($owner)
        ->postJson(route('events.tickets.validate', $eventBox), [
            'code' => $ticket->code,
        ])
        ->assertOk()
        ->assertJson([
            'status' => 'valid',
            'ticket_id' => $ticket->id,
            'holder_name' => 'Ama Attendee',
        ]);
});

it('sends only one confirmation email for the same issued ticket', function () {
    ['ticket' => $ticket] = createEventBoxTicketValidationFixture();

    Mail::fake();

    $listener = app(SendTicketEmail::class);

    $listener->handle(new TicketIssued($ticket));
    $listener->handle(new TicketIssued($ticket->fresh()));

    Mail::assertSent(TicketMail::class, 1);

    expect($ticket->fresh()->ticket_email_sent_at)->not->toBeNull()
        ->and($ticket->fresh()->ticket_email_sending_at)->toBeNull();
});

it('redeems a validated event ticket and records who redeemed it', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $this->actingAs($owner)
        ->postJson(route('events.tickets.redeem', [$eventBox, $ticket]))
        ->assertOk()
        ->assertJson([
            'status' => 'redeemed',
        ]);

    $ticket->refresh();

    expect($ticket->status->value)->toBe('redeemed')
        ->and($ticket->redeemed_by)->toBe($owner->id)
        ->and($ticket->redeemed_at)->not->toBeNull();
});

it('shows event revenue, fee, and organizer payout reporting', function () {
    ['owner' => $owner, 'eventBox' => $eventBox] = createEventBoxTicketValidationFixture();

    $eventBox->update(['fee_percentage' => 10]);

    $this->actingAs($owner)
        ->get(route('events.dashboard', $eventBox))
        ->assertOk()
        ->assertSee('Revenue report')
        ->assertSee('Gross ticket sales')
        ->assertSee('Platform fee (10.00%)')
        ->assertSee('GH₵ 2.50')
        ->assertSee('Organizer payout')
        ->assertSee('GH₵ 22.50');
});

it('requires ticket code confirmation before voiding a ticket', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $this->actingAs($owner)
        ->postJson(route('events.tickets.void', [$eventBox, $ticket]), [
            'code_confirmation' => 'WRONG-CODE',
        ])
        ->assertStatus(422)
        ->assertJson([
            'status' => 'error',
        ]);

    expect($ticket->refresh()->status->value)->toBe('unused');
});

it('voids an unused paid ticket and queues a refund minus charges', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket, 'ticketType' => $ticketType] = createEventBoxTicketValidationFixture();

    $eventBox->update(['fee_percentage' => 10]);

    $this->actingAs($owner)
        ->postJson(route('events.tickets.void', [$eventBox, $ticket]), [
            'code_confirmation' => $ticket->code,
            'reason' => 'Buyer requested cancellation.',
        ])
        ->assertOk()
        ->assertJson([
            'status' => 'voided',
            'refund_amount' => 22.5,
        ]);

    $ticket->refresh();

    expect($ticket->status->value)->toBe('voided')
        ->and($ticket->payment_status->value)->toBe('refunded')
        ->and($ticket->voided_by)->toBe($owner->id)
        ->and($eventBox->refresh()->tickets_sold)->toBe(0)
        ->and($ticketType->refresh()->sold)->toBe(0);

    $refund = EventBoxTicketRefund::first();

    expect($refund)->not->toBeNull()
        ->and((float) $refund->gross_amount)->toBe(25.0)
        ->and((float) $refund->charge_amount)->toBe(2.5)
        ->and((float) $refund->refund_amount)->toBe(22.5)
        ->and($refund->recipient_account_number)->toBe('0240000000');
});

it('does not allow redeemed tickets to be voided', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $ticket->redeem($owner->id);

    $this->actingAs($owner)
        ->postJson(route('events.tickets.void', [$eventBox, $ticket]), [
            'code_confirmation' => $ticket->code,
        ])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Redeemed tickets cannot be voided.',
        ]);
});

it('shows attendee filters, csv export, void modal, and audit tab on the dashboard', function () {
    ['owner' => $owner, 'eventBox' => $eventBox] = createEventBoxTicketValidationFixture();

    $this->actingAs($owner)
        ->get(route('events.dashboard', $eventBox))
        ->assertOk()
        ->assertSee('Export CSV')
        ->assertSee('Search name, email, phone, or code')
        ->assertSee('Confirm ticket void')
        ->assertSee('Audit log');
});

it('exports attendees as a native streamed csv', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $response = $this->actingAs($owner)
        ->get(route('events.attendees.export', [
            'eventBox' => $eventBox,
            'q' => 'Ama',
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();

    expect($csv)->toContain('"Buyer name","Buyer email","Buyer phone"')
        ->and($csv)->toContain('"Ama Attendee",ama@example.com,0240000000')
        ->and($csv)->toContain($ticket->code);
});

it('submits pending ticket refunds through the scheduled refund command', function () {
    ['owner' => $owner, 'eventBox' => $eventBox, 'ticket' => $ticket] = createEventBoxTicketValidationFixture();

    $this->actingAs($owner)
        ->postJson(route('events.tickets.void', [$eventBox, $ticket]), [
            'code_confirmation' => $ticket->code,
        ])
        ->assertOk();

    Http::fake([
        '*' => Http::response([
            'success' => true,
            'data' => [
                'availableBalance' => 100000,
                'actualBalance' => 100000,
                'availableBalanceInMajorUnitsFormatted' => 'GHS 1,000.00',
                'externalId' => 'TP-REFUND-123',
                'status' => 'pending',
            ],
        ], 200),
    ]);

    $this->artisan('events:process-ticket-refunds')
        ->assertExitCode(0);

    $refund = EventBoxTicketRefund::first();

    expect($refund->refresh()->status->value)->toBe('processing')
        ->and($refund->transaction_reference)->toBe('TP-REFUND-123');
});

it('loads Alpine support on the public event ticket page', function () {
    ['eventBox' => $eventBox] = createEventBoxTicketValidationFixture();

    $this->get(route('events.show', $eventBox->slug))
        ->assertOk()
        ->assertSee('x-data="ticketPanel()"', false)
        ->assertSee('flux.min.js', false);
});
