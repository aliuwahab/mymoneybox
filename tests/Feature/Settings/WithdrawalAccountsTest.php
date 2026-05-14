<?php

use App\Models\IdVerification;
use App\Models\User;

it('opens the add withdrawal account form from the fallback URL', function () {
    $user = User::factory()->create();

    IdVerification::create([
        'user_id' => $user->id,
        'id_type' => 'national_card',
        'first_name' => 'Test',
        'last_name' => 'User',
        'id_number' => 'GHA-000000000-0',
        'status' => 'approved',
    ]);

    $this->actingAs($user)
        ->get(route('settings.withdrawal-accounts', ['add' => 1]))
        ->assertOk()
        ->assertSee('Add Withdrawal Account')
        ->assertSee('Account Type');
});
