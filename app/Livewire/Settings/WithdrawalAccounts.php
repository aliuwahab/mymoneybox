<?php

namespace App\Livewire\Settings;

use App\Enums\MobileMoneyNetwork;
use App\Models\WithdrawalAccount;
use App\Payment\Providers\TrendiPayProvider;
use Livewire\Component;

class WithdrawalAccounts extends Component
{
    public $showForm = false;
    public $editingId = null;

    // Form fields
    public $accountName = '';
    public $accountNumber = '';
    public $mobileNetwork = '';
    public $isDefault = false;

    protected function rules(): array
    {
        return [
            'accountName'   => 'required|string|max:255',
            'accountNumber' => 'required|string|max:255',
            'mobileNetwork' => 'required|in:mtn,vodafone,airteltigo',
        ];
    }

    protected $validationAttributes = [
        'accountName'   => 'account name',
        'accountNumber' => 'account number',
        'mobileNetwork' => 'mobile network',
    ];

    public function mount()
    {
        if (request()->boolean('add')) {
            $this->showAddForm();
        }
    }

    public function showAddForm()
    {
        if (!auth()->user()->can('create', WithdrawalAccount::class)) {
            session()->flash('error', 'Please submit ID verification before adding a withdrawal account.');
            $this->redirectRoute('settings.verification', navigate: true);
            return;
        }

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($accountId)
    {
        $account = WithdrawalAccount::findOrFail($accountId);
        $this->authorize('update', $account);

        if (!$account->canBeModified()) {
            session()->flash('error', 'This account cannot be edited because it has received disbursements.');
            return;
        }

        $this->editingId      = $account->id;
        $this->accountName    = $account->account_name;
        $this->accountNumber  = $account->account_number;
        $this->mobileNetwork  = $account->mobile_network?->value ?? '';
        $this->isDefault      = $account->is_default;
        $this->showForm       = true;
    }

    public function verify($accountId)
    {
        $this->edit($accountId);
    }

    public function save()
    {
        $this->validate();

        $network      = MobileMoneyNetwork::from($this->mobileNetwork);
        $verification = app(TrendiPayProvider::class)->verifyAccountName(
            $this->accountNumber,
            $network->value
        );

        if (!$verification['success']) {
            $this->addError('accountNumber', $verification['message']);
            return;
        }

        $verifiedName = $verification['account_name'];

        if (!$this->namesMatch($this->accountName, $verifiedName)) {
            $this->addError('accountName', "The name you entered doesn't match the registered account name. Please check and try again.");
            return;
        }

        $data = [
            'user_id'        => auth()->id(),
            'account_type'   => 'mobile_money',
            'account_name'   => $verifiedName,
            'account_number' => $this->accountNumber,
            'mobile_network' => $this->mobileNetwork,
            'bank_name'      => null,
            'bank_branch'    => null,
            'is_default'     => $this->isDefault,
            'is_active'      => true,
            'is_verified'    => true,
        ];

        if ($this->editingId) {
            $account = WithdrawalAccount::findOrFail($this->editingId);
            $this->authorize('update', $account);
            $account->update($data);
            $message = 'Withdrawal account updated successfully!';
        } else {
            $this->authorize('create', WithdrawalAccount::class);
            $account = WithdrawalAccount::create($data);
            $message = 'Withdrawal account verified and added successfully!';
        }

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

        if (!$account->canBeModified()) {
            session()->flash('error', 'This account cannot be deleted because it has received disbursements.');
            return;
        }

        $account->delete();
        session()->flash('success', 'Withdrawal account deleted successfully!');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function namesMatch(string $userInput, string $verifiedName): bool
    {
        $normalize = fn(string $s) => strtolower(trim(preg_replace('/\s+/', ' ', $s)));

        $a = $normalize($userInput);
        $b = $normalize($verifiedName);

        if ($a === $b) {
            return true;
        }

        // Check if all words the user typed appear in the verified name
        $userWords     = explode(' ', $a);
        $verifiedWords = explode(' ', $b);
        $matches       = count(array_intersect($userWords, $verifiedWords));

        if ($matches / count($userWords) >= 0.6) {
            return true;
        }

        // Fallback: character-level similarity
        similar_text($a, $b, $percent);
        return $percent >= 60;
    }

    private function resetForm()
    {
        $this->editingId     = null;
        $this->accountName   = '';
        $this->accountNumber = '';
        $this->mobileNetwork = '';
        $this->isDefault     = false;
        $this->resetValidation();
    }

    public function render()
    {
        $accounts = auth()->user()->withdrawalAccounts()
            ->active()
            ->latest()
            ->get();

        return view('livewire.settings.withdrawal-accounts', [
            'accounts'      => $accounts,
            'mobileNetworks' => MobileMoneyNetwork::cases(),
        ]);
    }
}