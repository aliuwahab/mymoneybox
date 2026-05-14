<?php

use App\Models\IdVerification;
use App\Models\MoneyBox;
use App\Models\User;
use App\Models\WithdrawalAccount;

it('shows a withdraw action on money boxes with an available balance', function () {
    $user = User::factory()->create();

    IdVerification::create([
        'user_id' => $user->id,
        'id_type' => 'national_card',
        'first_name' => 'Test',
        'last_name' => 'User',
        'id_number' => 'GHA-000000000-0',
        'status' => 'approved',
    ]);

    WithdrawalAccount::create([
        'user_id' => $user->id,
        'account_type' => 'mobile_money',
        'account_name' => 'Test User',
        'account_number' => '0240000000',
        'mobile_network' => 'mtn',
        'is_default' => true,
        'is_active' => true,
    ]);

    $moneyBox = MoneyBox::create([
        'user_id' => $user->id,
        'title' => 'Test PiggyBox',
        'slug' => 'test-piggybox',
        'currency_code' => 'GHS',
        'visibility' => 'public',
        'contributor_identity' => 'user_choice',
        'amount_type' => 'variable',
        'total_contributions' => 50,
        'contribution_count' => 1,
        'is_active' => true,
        'is_ongoing' => true,
    ]);

    $this->actingAs($user)
        ->get(route('money-boxes.show', $moneyBox))
        ->assertOk()
        ->assertSee('Withdraw')
        ->assertSee(route('money-boxes.withdraw.create', $moneyBox), false);
});
