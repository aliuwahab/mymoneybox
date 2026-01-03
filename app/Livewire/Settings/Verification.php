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
            'frontImage' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'backImage' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ];
    }

    protected $validationAttributes = [
        'frontImage' => 'front image',
        'backImage' => 'back image',
    ];

    public function submit()
    {
        // Debug: Log what we're receiving
        \Log::info('Verification submission attempt', [
            'frontImage_type' => gettype($this->frontImage),
            'frontImage_class' => is_object($this->frontImage) ? get_class($this->frontImage) : 'not_object',
            'frontImage_value' => $this->frontImage,
            'backImage_type' => gettype($this->backImage),
        ]);

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

        // Upload front image using Livewire temporary file
        if ($this->frontImage) {
            try {
                // Store the file directly using Livewire's store method
                // This works for both local and S3 temporary files
                $storedPath = $this->frontImage->store('id-verifications', config('media-library.private_disk', 's3'));
                
                // Add the stored file to media collection
                $verification->addMediaFromDisk($storedPath, config('media-library.private_disk', 's3'))
                    ->usingName($this->frontImage->getClientOriginalName())
                    ->toMediaCollection('front');
            } catch (\Exception $e) {
                \Log::error('Failed to upload front image', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        }

        // Upload back image if provided
        if ($this->backImage) {
            try {
                // Store the file directly using Livewire's store method
                $storedPath = $this->backImage->store('id-verifications', config('media-library.private_disk', 's3'));
                
                // Add the stored file to media collection
                $verification->addMediaFromDisk($storedPath, config('media-library.private_disk', 's3'))
                    ->usingName($this->backImage->getClientOriginalName())
                    ->toMediaCollection('back');
            } catch (\Exception $e) {
                \Log::error('Failed to upload back image', [
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
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
