<?php

use App\Actions\CalculateWithdrawalFeeAction;
use App\Actions\CreateMoneyBoxWithdrawalAction;
use App\Data\WithdrawalRequestData;
use App\Models\MoneyBox;
use App\Models\MoneyBoxWithdrawal;
use App\Models\User;
use App\Models\WithdrawalAccount;

function createWithdrawalFixture(): array
{
    $user = User::factory()->create();

    $account = WithdrawalAccount::create([
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
        'title' => 'School Fees',
        'slug' => 'school-fees-test',
        'currency_code' => 'GHS',
        'visibility' => 'public',
        'contributor_identity' => 'user_choice',
        'amount_type' => 'variable',
        'total_contributions' => 20,
        'contribution_count' => 1,
        'is_active' => true,
        'is_ongoing' => true,
    ]);

    $withdrawal = MoneyBoxWithdrawal::create([
        'money_box_id' => $moneyBox->id,
        'user_id' => $user->id,
        'withdrawal_account_id' => $account->id,
        'amount' => 10,
        'fee' => 2,
        'net_amount' => 8,
        'currency_code' => 'GHS',
        'status' => 'pending',
        'reference' => 'MWD-TEST-123',
        'payment_provider' => 'trendipay',
        'user_note' => 'Please process this quickly.',
    ]);

    return compact('user', 'account', 'moneyBox', 'withdrawal');
}

it('lists user withdrawals with source, status, fee, and net payout', function () {
    ['user' => $user] = createWithdrawalFixture();

    $this->actingAs($user)
        ->get(route('withdrawals.index'))
        ->assertOk()
        ->assertSee('MWD-TEST-123')
        ->assertSee('School Fees')
        ->assertSee('Pending')
        ->assertSee('10.00')
        ->assertSee('2.00')
        ->assertSee('8.00');
});

it('shows withdrawal details and lets the owner add a comment', function () {
    ['user' => $user, 'withdrawal' => $withdrawal] = createWithdrawalFixture();

    $withdrawal->notes()->create([
        'user_id' => $user->id,
        'note' => 'Admin is reviewing this request.',
        'is_admin' => true,
    ]);

    $this->actingAs($user)
        ->get(route('withdrawals.show', ['money-box', $withdrawal->id]))
        ->assertOk()
        ->assertSee('Admin is reviewing this request.')
        ->assertSee('MyPiggyBox platform fee');

    $this->actingAs($user)
        ->post(route('withdrawals.notes.store', ['money-box', $withdrawal->id]), [
            'note' => 'Thanks, adding context from my side.',
        ])
        ->assertRedirect(route('withdrawals.show', ['money-box', $withdrawal->id]));

    expect($withdrawal->notes()->where('is_admin', false)->where('note', 'Thanks, adding context from my side.')->exists())->toBeTrue();
});

it('takes the platform fee from the requested withdrawal amount', function () {
    config([
        'withdrawal.fee_percentage' => 2.5,
        'withdrawal.min_fee' => 2,
        'withdrawal.max_fee' => 20,
    ]);

    $fee = app(CalculateWithdrawalFeeAction::class)->execute(10);

    expect($fee->amount)->toBe(10.0)
        ->and($fee->fee)->toBe(2.0)
        ->and($fee->netAmount)->toBe(8.0);
});

it('reserves the gross withdrawal amount while paying out the net amount', function () {
    config([
        'withdrawal.fee_percentage' => 2.5,
        'withdrawal.min_fee' => 2,
        'withdrawal.max_fee' => 20,
    ]);

    $user = User::factory()->create();
    $account = WithdrawalAccount::create([
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
        'title' => 'Fee Test',
        'slug' => 'fee-test',
        'currency_code' => 'GHS',
        'visibility' => 'public',
        'contributor_identity' => 'user_choice',
        'amount_type' => 'variable',
        'total_contributions' => 20,
        'contribution_count' => 1,
        'is_active' => true,
        'is_ongoing' => true,
    ]);

    $withdrawal = app(CreateMoneyBoxWithdrawalAction::class)->execute(
        $moneyBox,
        new WithdrawalRequestData(amount: 10, withdrawalAccountId: $account->id),
    );

    expect((float) $withdrawal->amount)->toBe(10.0)
        ->and((float) $withdrawal->fee)->toBe(2.0)
        ->and((float) $withdrawal->net_amount)->toBe(8.0)
        ->and($moneyBox->refresh()->getAvailableBalance())->toBe(10.0);
});
