<?php

namespace App\Http\Controllers;

use App\Actions\CreatePiggyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Data\WithdrawalRequestData;
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

        $availableBalance = $piggyBox->getAvailableBalance();
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
            return redirect()->route('piggy.my-piggy-box')
                ->with('error', 'Insufficient balance for withdrawal. Minimum: ' . $piggyBox->formatAmount(config('withdrawal.min_amount', 10)));
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
            'amount'                => 'required|numeric|min:' . config('withdrawal.min_amount', 10),
            'withdrawal_account_id' => 'required|exists:withdrawal_accounts,id',
            'note'                  => 'nullable|string|max:500',
        ], [
            'withdrawal_account_id.required' => 'Please select a withdrawal account.',
        ]);

        // User-facing balance check before acquiring the DB lock
        $validation = app(ValidateWithdrawalAmountAction::class)
            ->execute((float) $validated['amount'], $piggyBox->getAvailableBalance());

        if (!$validation->valid) {
            return back()->withErrors(['amount' => $validation->errors])->withInput();
        }

        $this->authorize('view', WithdrawalAccount::findOrFail($validated['withdrawal_account_id']));

        try {
            $withdrawal = app(CreatePiggyBoxWithdrawalAction::class)->execute(
                $piggyBox,
                new WithdrawalRequestData(
                    amount: (float) $validated['amount'],
                    withdrawalAccountId: (int) $validated['withdrawal_account_id'],
                    note: $validated['note'] ?? null,
                ),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('piggy.my-piggy-box')
            ->with('success', 'Withdrawal request submitted! Reference: ' . $withdrawal->reference);
    }
}