<?php

namespace App\Http\Controllers;

use App\Actions\CalculateWithdrawalFeeAction;
use App\Actions\CreateMoneyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Models\MoneyBox;
use App\Models\WithdrawalAccount;
use Illuminate\Http\Request;

class MoneyBoxWithdrawalController extends Controller
{
    public function create(MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        // Check if user can withdraw
        $user = auth()->user();
        $availableBalance = $moneyBox->getAvailableBalance();
        $withdrawalAccounts = $user->withdrawalAccounts()->active()->get();

        // Redirect with appropriate message if requirements not met
        if (!$user->isVerified()) {
            return redirect()->route('settings.verification')
                ->with('error', 'You must verify your identity before making withdrawals.');
        }

        if ($withdrawalAccounts->count() === 0) {
            return redirect()->route('settings.withdrawal-accounts')
                ->with('error', 'Please add a withdrawal account before making withdrawals.');
        }

        if ($availableBalance < config('withdrawal.min_amount', 10)) {
            return redirect()->route('money-boxes.show', $moneyBox)
                ->with('error', 'Insufficient balance for withdrawal. Minimum amount: ' . $moneyBox->formatAmount(config('withdrawal.min_amount', 10)));
        }

        return view('money-boxes.withdraw', compact('moneyBox', 'withdrawalAccounts', 'availableBalance'));
    }

    public function store(Request $request, MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . config('withdrawal.min_amount', 10),
            'withdrawal_account_id' => 'required|exists:withdrawal_accounts,id',
            'note' => 'nullable|string|max:500',
        ], [
            'withdrawal_account_id.required' => 'Please select a withdrawal account.',
        ]);

        $availableBalance = $moneyBox->getAvailableBalance();

        // Validate amount
        $validator = app(ValidateWithdrawalAmountAction::class);
        $validation = $validator->execute((float) $validated['amount'], $availableBalance);

        if (!$validation->valid) {
            return back()->withErrors(['amount' => $validation->errors])->withInput();
        }

        $account = WithdrawalAccount::findOrFail($validated['withdrawal_account_id']);
        $this->authorize('view', $account);

        // Create withdrawal
        $action = app(CreateMoneyBoxWithdrawalAction::class);
        $withdrawal = $action->execute(
            $moneyBox,
            $account,
            (float) $validated['amount'],
            $validated['note'] ?? null
        );

        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'Withdrawal request submitted successfully! Reference: ' . $withdrawal->reference);
    }
}
