<?php

namespace App\Livewire;

use App\Actions\CalculateWithdrawalFeeAction;
use App\Actions\CreateMoneyBoxWithdrawalAction;
use App\Actions\ValidateWithdrawalAmountAction;
use App\Models\MoneyBox;
use App\Models\WithdrawalAccount;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MoneyBoxWithdrawalForm extends Component
{
    public MoneyBox $moneyBox;
    public $amount = '';
    public $withdrawalAccountId = '';
    public $note = '';
    public $showModal = false;
    public $feeData = null;

    public function mount(MoneyBox $moneyBox)
    {
        $this->moneyBox = $moneyBox;
        $this->authorize('update', $moneyBox);
    }

    protected function rules()
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

    public function updatedAmount($value)
    {
        if ($value && is_numeric($value) && $value > 0) {
            $this->calculateFee();
        } else {
            $this->feeData = null;
        }
    }

    public function calculateFee()
    {
        if (!$this->amount || !is_numeric($this->amount)) {
            return;
        }

        $calculator = app(CalculateWithdrawalFeeAction::class);
        $this->feeData = $calculator->execute((float) $this->amount);
    }

    public function openModal(): void
    {
        // All checks are already done by canWithdraw computed property
        // Just open the modal
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['amount', 'withdrawalAccountId', 'note', 'feeData']);
        $this->showModal = false;
        $this->resetValidation();
    }

    public function submit(): void
    {
        $this->validate();

        $availableBalance = $this->moneyBox->getAvailableBalance();

        // Validate amount
        $validator = app(ValidateWithdrawalAmountAction::class);
        $validation = $validator->execute((float) $this->amount, $availableBalance);

        if (!$validation->valid) {
            foreach ($validation->errors as $error) {
                $this->addError('amount', $error);
            }
            return;
        }

        $account = WithdrawalAccount::findOrFail($this->withdrawalAccountId);
        $this->authorize('view', $account);

        // Create withdrawal
        $action = app(CreateMoneyBoxWithdrawalAction::class);
        $withdrawal = $action->execute(
            $this->moneyBox,
            $account,
            (float) $this->amount,
            $this->note
        );

        session()->flash('success', 'Withdrawal request submitted successfully! Reference: ' . $withdrawal->reference);
        $this->closeModal();
        $this->dispatch('withdrawal-created');
    }

    #[Computed]
    public function availableBalance()
    {
        return $this->moneyBox->getAvailableBalance();
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
        return view('livewire.money-box-withdrawal-form');
    }
}
