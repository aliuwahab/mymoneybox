<?php

namespace App\Livewire\Settings;

use App\Enums\AccountType;
use App\Enums\MobileMoneyNetwork;
use App\Models\WithdrawalAccount;
use Livewire\Component;

class WithdrawalAccounts extends Component
{
    public $showForm = false;
    public $editingId = null;
    
    // Form fields
    public $accountType = '';
    public $accountName = '';
    public $accountNumber = '';
    public $mobileNetwork = '';
    public $bankName = '';
    public $bankBranch = '';
    public $isDefault = false;

    protected function rules()
    {
        $rules = [
            'accountType' => 'required|in:mobile_money,bank_account',
            'accountName' => 'required|string|max:255',
            'accountNumber' => 'required|string|max:255',
        ];

        if ($this->accountType === 'mobile_money') {
            $rules['mobileNetwork'] = 'required|in:mtn,vodafone,airteltigo';
        } else {
            $rules['bankName'] = 'required|string|max:255';
            $rules['bankBranch'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    protected $validationAttributes = [
        'accountType' => 'account type',
        'accountName' => 'account name',
        'accountNumber' => 'account number',
        'mobileNetwork' => 'mobile network',
        'bankName' => 'bank name',
        'bankBranch' => 'bank branch',
    ];

    public function showAddForm()
    {
        $this->authorize('create', WithdrawalAccount::class);
        
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($accountId)
    {
        $account = WithdrawalAccount::findOrFail($accountId);
        $this->authorize('update', $account);

        $this->editingId = $account->id;
        $this->accountType = $account->account_type->value;
        $this->accountName = $account->account_name;
        $this->accountNumber = $account->account_number;
        $this->mobileNetwork = $account->mobile_network?->value ?? '';
        $this->bankName = $account->bank_name ?? '';
        $this->bankBranch = $account->bank_branch ?? '';
        $this->isDefault = $account->is_default;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'account_type' => $this->accountType,
            'account_name' => $this->accountName,
            'account_number' => $this->accountNumber,
            'is_default' => $this->isDefault,
            'is_active' => true,
        ];

        if ($this->accountType === 'mobile_money') {
            $data['mobile_network'] = $this->mobileNetwork;
            $data['bank_name'] = null;
            $data['bank_branch'] = null;
        } else {
            $data['bank_name'] = $this->bankName;
            $data['bank_branch'] = $this->bankBranch;
            $data['mobile_network'] = null;
        }

        if ($this->editingId) {
            $account = WithdrawalAccount::findOrFail($this->editingId);
            $this->authorize('update', $account);
            $account->update($data);
            $message = 'Withdrawal account updated successfully!';
        } else {
            $this->authorize('create', WithdrawalAccount::class);
            $account = WithdrawalAccount::create($data);
            $message = 'Withdrawal account added successfully!';
        }

        // If marked as default, update other accounts
        if ($this->isDefault) {
            $account->setAsDefault();
        }

        session()->flash('success', $message);
        $this->resetForm();
        $this->showForm = false;
    }

    public function setAsDefault($accountId)
    {
        $account = WithdrawalAccount::findOrFail($accountId);
        $this->authorize('update', $account);
        
        $account->setAsDefault();
        session()->flash('success', 'Default account updated!');
    }

    public function delete($accountId)
    {
        $account = WithdrawalAccount::findOrFail($accountId);
        $this->authorize('delete', $account);

        $account->delete();
        session()->flash('success', 'Withdrawal account deleted successfully!');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->accountType = '';
        $this->accountName = '';
        $this->accountNumber = '';
        $this->mobileNetwork = '';
        $this->bankName = '';
        $this->bankBranch = '';
        $this->isDefault = false;
        $this->resetValidation();
    }

    public function render()
    {
        $accounts = auth()->user()->withdrawalAccounts()
            ->active()
            ->latest()
            ->get();

        return view('livewire.settings.withdrawal-accounts', [
            'accounts' => $accounts,
            'accountTypes' => AccountType::cases(),
            'mobileNetworks' => MobileMoneyNetwork::cases(),
        ]);
    }
}
