<?php

namespace App\Console\Commands;

use App\Actions\ProcessEventBoxTicketRefundAction;
use App\Enums\RefundStatus;
use App\Models\EventBoxTicketRefund;
use Illuminate\Console\Command;

class ProcessEventBoxTicketRefunds extends Command
{
    protected $signature = 'events:process-ticket-refunds {--limit=100 : Maximum refunds to process}';

    protected $description = 'Process queued EventBox ticket refunds';

    public function handle(ProcessEventBoxTicketRefundAction $action): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $refunds = EventBoxTicketRefund::with('ticket.eventBox')
            ->where('status', RefundStatus::Pending->value)
            ->oldest()
            ->limit($limit)
            ->get();

        $this->info("Found {$refunds->count()} pending ticket refund(s).");

        $success = 0;
        $failed = 0;

        foreach ($refunds as $refund) {
            $this->line("Processing {$refund->reference} - {$refund->currency_code} {$refund->refund_amount}");

            $result = $action->execute($refund);

            if ($result['success'] ?? false) {
                $success++;
                $this->info("Submitted {$refund->reference}");
            } else {
                $failed++;
                $this->warn("Skipped {$refund->reference}: ".($result['message'] ?? 'Unknown error'));
            }
        }

        $this->info("Submitted: {$success}");
        $this->info("Skipped/failed: {$failed}");

        return Command::SUCCESS;
    }
}
