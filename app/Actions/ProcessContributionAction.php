<?php

namespace App\Actions;

use App\Data\ContributionData;
use App\Models\Contribution;
use App\Models\MoneyBox;
use Illuminate\Support\Facades\DB;

class ProcessContributionAction
{
    public function execute(MoneyBox $moneyBox, ContributionData $data): Contribution
    {
        return DB::transaction(function () use ($moneyBox, $data) {
            if (!$moneyBox->validateContributionAmount($data->amount)) {
                throw new \InvalidArgumentException('Invalid contribution amount');
            }

            if (!$moneyBox->canAcceptContributions()) {
                throw new \InvalidArgumentException('This box is not accepting contributions');
            }

            return Contribution::create([
                'money_box_id'       => $moneyBox->id,
                'contributor_name'   => $data->contributorName,
                'contributor_email'  => $data->contributorEmail,
                'contributor_phone'  => $data->contributorPhone,
                'amount'             => $data->amount,
                'currency_code'      => $moneyBox->currency_code,
                'is_anonymous'       => $data->isAnonymous,
                'message'            => $data->message,
                'payment_provider'   => $data->paymentProvider,
                'payment_method'     => $data->paymentMethod,
                'payment_reference'  => $data->paymentReference,
                'payment_status'     => $data->paymentStatus,
                'ip_address'         => request()->ip(),
                'user_agent'         => request()->userAgent(),
            ]);
        });
    }
}