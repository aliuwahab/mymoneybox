<?php

namespace App\Livewire\Settings;

use App\Models\IdVerification;
use Livewire\Component;

class Verification extends Component
{
    public $currentVerification;

    public function mount()
    {
        $this->currentVerification = auth()->user()->currentVerification;
    }

    public function render()
    {
        $verifications = auth()->user()->idVerifications()->latest()->get();

        return view('livewire.settings.verification', [
            'verifications' => $verifications,
        ]);
    }
}