<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\EventBoxTicket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Finds tickets whose payment is marked completed in payment_metadata
 * (i.e. webhook fired but code generation failed) and re-issues codes.
 * Also logs tickets that have been pending for more than 30 minutes.
 */
class RecoverPendingTickets extends Command
{
    protected $signature   = 'tickets:recover {--dry-run : Preview without making changes}';
    protected $description = 'Generate missing codes for tickets whose payment was completed';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Tickets marked completed but still missing their code
        $broken = EventBoxTicket::where('payment_status', PaymentStatus::Completed)
            ->whereNull('code')
            ->get();

        if ($broken->isEmpty()) {
            $this->info('No broken tickets found.');
        } else {
            $this->warn("Found {$broken->count()} completed ticket(s) without a code.");

            foreach ($broken as $ticket) {
                if ($dryRun) {
                    $this->line("  [dry-run] Would fix ticket #{$ticket->id} ({$ticket->buyer_email})");
                    continue;
                }

                $code = EventBoxTicket::generateCode();
                $ticket->update(['code' => $code, 'status' => 'unused']);

                event(new \App\Events\TicketIssued($ticket->fresh()));

                Log::info('tickets:recover — re-issued code', [
                    'ticket_id' => $ticket->id,
                    'code'      => $code,
                ]);

                $this->line("  Fixed ticket #{$ticket->id} → {$code}");
            }
        }

        // Log stale pending tickets (older than 30 min) for monitoring
        $stale = EventBoxTicket::where('payment_status', PaymentStatus::Pending)
            ->where('created_at', '<', now()->subMinutes(30))
            ->count();

        if ($stale > 0) {
            Log::warning("tickets:recover — {$stale} ticket(s) still pending after 30 minutes", [
                'count' => $stale,
            ]);
            $this->warn("{$stale} ticket(s) still pending after 30 minutes — check TrendiPay dashboard.");
        }

        return Command::SUCCESS;
    }
}