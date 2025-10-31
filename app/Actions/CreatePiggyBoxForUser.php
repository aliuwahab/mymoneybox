<?php

namespace App\Actions;

use App\Models\PiggyBox;
use App\Models\User;

class CreatePiggyBoxForUser
{
    public function execute(User $user): PiggyBox
    {
        // Check if user already has a piggy box
        if ($user->piggyBox) {
            return $user->piggyBox;
        }

        // Generate unique piggy code if user doesn't have one
        if (!$user->piggy_code) {
            $user->update([
                'piggy_code' => User::generateUniquePiggyCode(),
            ]);
        }

        // Create piggy box for user
        return PiggyBox::create([
            'user_id' => $user->id,
            'title' => "My Piggy Box",
            'description' => "Send me a gift!",
            'currency_code' => $user->country->currency_code ?? 'USD',
            'is_active' => true,
        ]);
    }
}
