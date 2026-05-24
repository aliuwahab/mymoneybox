<?php

namespace App\Events;

use App\Models\EventBoxTicket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketIssued
{
    use Dispatchable, SerializesModels;

    public function __construct(public EventBoxTicket $ticket) {}
}