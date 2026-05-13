<?php

namespace App\Livewire;

use App\Actions\CalculateWithdrawalFeeAction;
use App\Actions\CreatePiggyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Data\WithdrawalRequestData;
use App\Models\WithdrawalAccount;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PiggyBoxWithdrawalForm extends Component
{
    public $amount = '';
    public $withdrawalAccountId = '';
    public $note = '';
    public $showModal = false;
    public $feeData = null;

    protected function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:' . config('withdrawal.min_amount', 10),
            'withdrawalAccountId' => 'required|exists:withdrawal_accounts,id',
            'note' => 'nullable|string|max:500',
        ];
    }

    protected $validationAttributes = [
        'withdrawalAccountId' => 'withdrawal account',
    ];

    public function updatedAmount($value): void
    {
        if ($value && is_numeric($value) && $value > 0) {
            $this->calculateFee();
        } else {
            $this->feeData = null;
        }
    }

    public function calculateFee(): void
    {
        if (!$this->amount || !is_numeric($this->amount)) {
            return;
        }

        $piggyBox = auth()->user()->piggyBox;
        $this->feeData = app(CalculateWithdrawalFeeAction::class)
            ->execute((float) $this->amount, $piggyBox?->getEffectiveFeePercentage());
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset(['amount', 'withdrawalAccountId', 'note', 'feeData']);
        $this->showModal = false;
        $this->resetValidation();
    }

    public function submit(): void
    {
        $this->validate();

        $piggyBox = auth()->user()->piggyBox;

        if (!$piggyBox) {
            $this->addError('amount', 'No piggy box found.');
            return;
        }

        $availableBalance = $piggyBox->getAvailableBalance();

        $validation = app(ValidateWithdrawalAmountAction::class)
            ->execute((float) $this->amount, $availableBalance);

        if (!$validation->valid) {
            foreach ($validation->errors as $error) {
                $this->addError('amount', $error);
            }
            return;
        }

        $this->authorize('view', WithdrawalAccount::findOrFail($this->withdrawalAccountId));

        try {
            $withdrawal = app(CreatePiggyBoxWithdrawalAction::class)->execute(
                $piggyBox,
                new WithdrawalRequestData(
                    amount: (float) $this->amount,
                    withdrawalAccountId: (int) $this->withdrawalAccountId,
                    note: $this->note ?: null,
                ),
            );
        } catch (\InvalidArgumentException $e) {
            $this->addError('amount', $e->getMessage());
            return;
        }

        session()->flash('success', 'Withdrawal request submitted! Reference: ' . $withdrawal->reference);
        $this->closeModal();
        $this->dispatch('withdrawal-created');
    }

    #[Computed]
    public function piggyBox()
    {
        return auth()->user()->piggyBox;
    }

    #[Computed]
    public function availableBalance(): float
    {
        return (float) ($this->piggyBox?->getAvailableBalance() ?? 0);
    }

    #[Computed]
    public function withdrawalAccounts()
    {
        return auth()->user()->withdrawalAccounts()->active()->get();
    }

    #[Computed]
    public function canWithdraw(): bool
    {
        return auth()->user()->isVerified()
            && $this->availableBalance >= config('withdrawal.min_amount', 10)
            && $this->withdrawalAccounts->count() > 0;
    }

    public function render()
    {
        return view('livewire.piggy-box-withdrawal-form');
    }
}
