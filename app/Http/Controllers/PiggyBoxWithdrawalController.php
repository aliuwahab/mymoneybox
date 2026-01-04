<?php

namespace App\Http\Controllers;

use App\Actions\CalculateWithdrawalFeeAction;
use App\Actions\CreatePiggyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Models\WithdrawalAccount;
use Illuminate\Http\Request;

class PiggyBoxWithdrawalController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $piggyBox = $user->piggyBox;

        if (!$piggyBox) {
            return redirect()->route('piggy.my-piggy-box')
                ->with('error', 'You do not have a piggy box yet.');
        }

        // Check if user can withdraw
        $availableBalance = $piggyBox->getAvailableBalance();
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
            return redirect()->route('piggy.my-piggy-box')
                ->with('error', 'Insufficient balance for withdrawal. Minimum amount: ' . $piggyBox->formatAmount(config('withdrawal.min_amount', 10)));
        }

        return view('piggy-box.withdraw', compact('piggyBox', 'withdrawalAccounts', 'availableBalance'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $piggyBox = $user->piggyBox;

        if (!$piggyBox) {
            return redirect()->route('piggy.my-piggy-box')
                ->with('error', 'You do not have a piggy box yet.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . config('withdrawal.min_amount', 10),
            'withdrawal_account_id' => 'required|exists:withdrawal_accounts,id',
            'note' => 'nullable|string|max:500',
        ], [
            'withdrawal_account_id.required' => 'Please select a withdrawal account.',
        ]);

        $availableBalance = $piggyBox->getAvailableBalance();

        // Validate amount
        $validator = app(ValidateWithdrawalAmountAction::class);
        $validation = $validator->execute((float) $validated['amount'], $availableBalance);

        if (!$validation->valid) {
            return back()->withErrors(['amount' => $validation->errors])->withInput();
        }

        $account = WithdrawalAccount::findOrFail($validated['withdrawal_account_id']);
        $this->authorize('view', $account);

        // Create withdrawal
        $action = app(CreatePiggyBoxWithdrawalAction::class);
        $withdrawal = $action->execute(
            $piggyBox,
            $account,
            (float) $validated['amount'],
            $validated['note'] ?? null
        );

        return redirect()->route('piggy.my-piggy-box')
            ->with('success', 'Withdrawal request submitted successfully! Reference: ' . $withdrawal->reference);
    }
}
