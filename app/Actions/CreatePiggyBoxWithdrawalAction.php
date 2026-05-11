<?php

namespace App\Actions;

use App\Data\WithdrawalRequestData;
use App\Events\WithdrawalRequested;
use App\Models\PiggyBox;
use App\Models\PiggyBoxWithdrawal;
use App\Models\WithdrawalAccount;
use Illuminate\Support\Facades\DB;

class CreatePiggyBoxWithdrawalAction
{
    public function __construct(
        protected CalculateWithdrawalFeeAction $calculateFee,
    ) {}

    public function execute(PiggyBox $piggyBox, WithdrawalRequestData $data): PiggyBoxWithdrawal
    {
        return DB::transaction(function () use ($piggyBox, $data) {
            // Lock the row so concurrent withdrawal requests see the same balance
            $piggyBox = PiggyBox::lockForUpdate()->findOrFail($piggyBox->id);

            $availableBalance = $piggyBox->getAvailableBalance();

            if ($data->amount > $availableBalance) {
                throw new \InvalidArgumentException(
                    "Insufficient balance. Available: {$availableBalance}, Requested: {$data->amount}"
                );
            }

            $account = WithdrawalAccount::findOrFail($data->withdrawalAccountId);
            $feeData = $this->calculateFee->execute($data->amount);

            $withdrawal = PiggyBoxWithdrawal::create([
                'piggy_box_id'          => $piggyBox->id,
                'user_id'               => $piggyBox->user_id,
                'withdrawal_account_id' => $account->id,
                'amount'                => $data->amount,
                'fee'                   => $feeData->fee,
                'net_amount'            => $feeData->netAmount,
                'currency_code'         => $piggyBox->currency_code,
                'status'                => 'pending',
                'reference'             => PiggyBoxWithdrawal::generateReference(),
                'payment_provider'      => 'trendipay',
                'user_note'             => $data->note,
            ]);

            event(new WithdrawalRequested($withdrawal));

            return $withdrawal;
        });
    }
}