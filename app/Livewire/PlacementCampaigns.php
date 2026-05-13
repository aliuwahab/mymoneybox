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
        $base = fn () => MoneyBox::with(['user', 'category'])
            ->public()
            ->active();

        $campaigns = $base()
            ->where('is_featured', false)
            ->latest()
            ->take(3)
            ->get();

        if ($campaigns->isEmpty()) {
            $campaigns = $base()
                ->latest()
                ->take(3)
                ->get();
        }

        return $campaigns;
    }

    public function render()
    {
        return view('livewire.placement-campaigns');
    }
}
