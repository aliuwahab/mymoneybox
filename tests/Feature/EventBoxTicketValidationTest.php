<?php

use App\Models\EventBox;
use App\Models\EventBoxTicket;
use App\Models\User;

function createEventBoxTicketValidationFixture(): array
{
    $owner = User::factory()->create();

    $eventBox = EventBox::create([
        'user_id'      => $owner->id,
        'title'        => 'Launch Night',
        'slug'         => 'launch-night-test',
        'event_date'   => now()->addWeek(),
        'status'       => 'active',
        'tickets_sold' => 1,
    ]);

    $ticket = EventBoxTicket::create([
        'event_box_id'      => $eventBox->id,
        'buyer_name'        => 'Ama Attendee',
        'buyer_email'       => 'ama@example.com',
        'amount'            => 25,
        'payment_reference' => 'EVT-TEST-VALIDATE',
        'payment_status'    => 'completed',
        'code'              => 'TKT-ABCD-EFGH-JKLM',
        'status'            => 'unused',
    ]);

    return compact('owner', 'eventBox', 'ticket');
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
