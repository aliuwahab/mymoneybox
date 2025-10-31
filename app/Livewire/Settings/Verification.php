<?php

namespace App\Livewire\Settings;

use App\Models\IdVerification;
use Livewire\Component;
use Livewire\WithFileUploads;

class Verification extends Component
{
    use WithFileUploads;

    public $idType = '';
    public $firstName = '';
    public $lastName = '';
    public $otherNames = '';
    public $idNumber = '';
    public $expiresAt = '';
    public $frontImage;
    public $backImage;

    public $currentVerification;
    public $showForm = false;

    public function mount()
    {
        $this->currentVerification = auth()->user()->currentVerification;
        $this->showForm = !$this->currentVerification;
    }

    protected function rules()
    {
        return [
            'idType' => 'required|in:passport,national_card,drivers_license',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'otherNames' => 'nullable|string|max:255',
            'idNumber' => 'nullable|string|max:255',
            'expiresAt' => 'required|date|after:today',
            'frontImage' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'backImage' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ];
    }

    protected $validationAttributes = [
        'frontImage' => 'front image',
        'backImage' => 'back image',
    ];

    public function submit()
    {
        $this->validate();

        $verification = IdVerification::create([
            'user_id' => auth()->id(),
            'id_type' => $this->idType,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'other_names' => $this->otherNames,
            'id_number' => $this->idNumber,
            'expires_at' => $this->expiresAt,
            'status' => 'pending',
        ]);

        // Upload front image
        $verification->addMedia($this->frontImage->getRealPath())
            ->usingName($this->frontImage->getClientOriginalName())
            ->toMediaCollection('front');

        // Upload back image if provided
        if ($this->backImage) {
            $verification->addMedia($this->backImage->getRealPath())
                ->usingName($this->backImage->getClientOriginalName())
                ->toMediaCollection('back');
        }

        session()->flash('success', 'ID verification submitted successfully! We will review it shortly.');
        
        return redirect()->route('settings.verification');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        $verifications = auth()->user()->idVerifications()->latest()->get();
        
        return view('livewire.settings.verification', [
            'verifications' => $verifications,
        ]);
    }
}
