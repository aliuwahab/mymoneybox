<?php

namespace App\Livewire;

use App\Models\MoneyBox;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FeaturedCampaign extends Component
{
    #[Computed]
    public function campaign(): ?MoneyBox
    {
        return MoneyBox::with(['user.currentVerification', 'category'])
            ->public()
            ->active()
            ->where('is_featured', true)
            ->first()
            ?? MoneyBox::with(['user.currentVerification', 'category'])
                ->public()
                ->active()
                ->latest()
                ->first();
    }

    public function render()
    {
        return view('livewire.featured-campaign');
    }
}
