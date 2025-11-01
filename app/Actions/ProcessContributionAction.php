<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Events\ContributionProcessed;
use App\Models\Contribution;
use App\Models\MoneyBox;
use Illuminate\Support\Facades\DB;

class ProcessContributionAction
{
    public function __construct(
        protected UpdateMoneyBoxStatsAction $updateStatsAction
    ) {}

    public function execute(MoneyBox $moneyBox, array $data): Contribution
    {
        return DB::transaction(function () use ($moneyBox, $data) {
            // Validate contribution amount
            if (!$moneyBox->validateContributionAmount($data['amount'])) {
                throw new \InvalidArgumentException('Invalid contribution amount');
            }

            if (!$moneyBox->canAcceptContributions()) {
                throw new \InvalidArgumentException('Piggy box is not accepting contributions');
            }

            // Create contribution
            $contribution = Contribution::create([
                'money_box_id' => $moneyBox->id,
                'contributor_name' => $data['contributor_name'] ?? null,
                'contributor_email' => $data['contributor_email'] ?? null,
                'contributor_phone' => $data['contributor_phone'] ?? null,
                'amount' => $data['amount'],
                'currency_code' => $moneyBox->currency_code,
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'message' => $data['message'] ?? null,
                'payment_provider' => $data['payment_provider'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_status' => $data['payment_status'] ?? PaymentStatus::Pending,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update piggy box stats if payment is completed
            if ($contribution->payment_status === PaymentStatus::Completed) {
                $this->updateStatsAction->execute($moneyBox, $contribution);
            }

            // TODO: Enable when queue is configured
            // event(new ContributionProcessed($contribution, $moneyBox));

            return $contribution;
        });
    }
}
