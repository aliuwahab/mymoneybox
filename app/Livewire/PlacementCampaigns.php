<?php

namespace App\Livewire;

use App\Models\MoneyBox;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PlacementCampaigns extends Component
{
    #[Computed]
    public function campaigns(): Collection
    {
        return MoneyBox::with(['user', 'category'])
            ->public()
            ->active()
            ->where('is_featured', false)
            ->latest()
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.placement-campaigns');
    }
}
