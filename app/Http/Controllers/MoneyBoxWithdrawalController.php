<?php

namespace App\Http\Controllers;

use App\Actions\CreateMoneyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Data\WithdrawalRequestData;
use App\Models\MoneyBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoneyBoxWithdrawalController extends Controller
{
    public function create(MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $user = auth()->user();
        $availableBalance = $moneyBox->getAvailableBalance();
        $withdrawalAccounts = $user->withdrawalAccounts()->active()->get();

        if (!$user->isVerified()) {
            return redirect()->route('settings.verification')
                ->with('error', 'You must verify your identity before making withdrawals.');
        }

        if ($withdrawalAccounts->isEmpty()) {
            return redirect()->route('settings.withdrawal-accounts')
                ->with('error', 'Please add a withdrawal account before making withdrawals.');
        }

        if ($availableBalance < config('withdrawal.min_amount', 10)) {
            return redirect()->route('money-boxes.show', $moneyBox)
                ->with('error', 'Insufficient balance for withdrawal. Minimum: ' . $moneyBox->formatAmount(config('withdrawal.min_amount', 10)));
        }

        return view('money-boxes.withdraw', compact('moneyBox', 'withdrawalAccounts', 'availableBalance'));
    }

    public function store(Request $request, MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $validated = $request->validate([
            'amount'                => 'required|numeric|min:' . config('withdrawal.min_amount', 10),
            'withdrawal_account_id' => 'required|exists:withdrawal_accounts,id',
            'note'                  => 'nullable|string|max:500',
        ], [
            'withdrawal_account_id.required' => 'Please select a withdrawal account.',
        ]);

        // User-facing balance check before acquiring the DB lock
        $validation = app(ValidateWithdrawalAmountAction::class)
            ->execute((float) $validated['amount'], $moneyBox->getAvailableBalance());

        if (!$validation->valid) {
            return back()->withErrors(['amount' => $validation->errors])->withInput();
        }

        $this->authorize('view', \App\Models\WithdrawalAccount::findOrFail($validated['withdrawal_account_id']));

        try {
            $withdrawal = app(CreateMoneyBoxWithdrawalAction::class)->execute(
                $moneyBox,
                new WithdrawalRequestData(
                    amount: (float) $validated['amount'],
                    withdrawalAccountId: (int) $validated['withdrawal_account_id'],
                    note: $validated['note'] ?? null,
                ),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'Withdrawal request submitted! Reference: ' . $withdrawal->reference);
    }
}