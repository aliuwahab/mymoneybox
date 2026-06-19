<?php

namespace App\Events;

use App\Models\EventBoxTicketRefund;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketRefundQueued
{
    use Dispatchable, SerializesModels;

    public function __construct(public EventBoxTicketRefund $refund) {}
}