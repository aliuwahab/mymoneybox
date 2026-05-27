<?php

use App\Actions\VerifyPiggyDonationPaymentAction;
use App\Enums\PaymentStatus;
use App\Mail\PiggyDonationReceiptMail;
use App\Models\PiggyBox;
use App\Models\PiggyBoxWithdrawal;
use App\Models\PiggyDonation;
use App\Models\User;
use App\Models\WithdrawalAccount;
use App\Payment\PaymentManager;
use Illuminate\Support\Facades\Mail;

function createPiggyWalletFixture(array $donationOverrides = []): array
{
    $user = User::factory()->create([
        'name' => 'Wallet Owner',
        'piggy_code' => 'PIG123',
    ]);

    $piggyBox = PiggyBox::create([
        'user_id' => $user->id,
        'title' => 'Wallet Owner Piggy Wallet',
        'currency_code' => 'GHS',
        'total_received' => 0,
        'donation_count' => 0,
        'is_active' => true,
    ]);

    $donation = PiggyDonation::create(array_merge([
        'piggy_box_id' => $piggyBox->id,
        'donor_name' => 'Gift Sender',
        'donor_email' => 'sender@example.com',
        'donor_phone' => '0240000000',
        'amount' => 30,
        'currency_code' => 'GHS',
        'is_anonymous' => false,
        'message' => 'Enjoy this',
        'payment_provider' => 'trendipay',
        'payment_reference' => 'piggy_test_reference',
        'payment_status' => PaymentStatus::Pending,
    ], $donationOverrides));

    return compact('user', 'piggyBox', 'donation');
}

function piggyWebhookPayload(string $reference = 'piggy_test_reference', string $status = 'success', int $amount = 3000): array
{
    return [
        'data' => [
            'reference' => $reference,
            'status' => $status,
            'amount' => $amount,
            'rrn' => 'RRN-PIGGY-123',
            'internalId' => 'TP-PIGGY-123',
            'externalId' => 'EXT-PIGGY-123',
            'rSwitch' => 'mtn',
            'responseCode' => '000',
        ],
    ];
}

it('sends one receipt and credits the wallet once for duplicate piggy donation webhooks', function () {
    ['piggyBox' => $piggyBox, 'donation' => $donation] = createPiggyWalletFixture();

    Mail::fake();

    $this->putJson(route('piggy.webhook'), piggyWebhookPayload())->assertOk();
    $this->putJson(route('piggy.webhook'), piggyWebhookPayload())
        ->assertOk()
        ->assertJson(['message' => 'Already processed']);

    $piggyBox->refresh();
    $donation->refresh();

    expect((float) $piggyBox->total_received)->toBe(30.0)
        ->and($piggyBox->donation_count)->toBe(1)
        ->and($donation->payment_status)->toBe(PaymentStatus::Completed)
        ->and($donation->credited_at)->not->toBeNull();

    Mail::assertSent(PiggyDonationReceiptMail::class, 1);
});

it('does not credit mismatched piggy donation payments', function () {
    ['piggyBox' => $piggyBox, 'donation' => $donation] = createPiggyWalletFixture();

    Mail::fake();

    $this->putJson(route('piggy.webhook'), piggyWebhookPayload(amount: 1000))->assertOk();

    $piggyBox->refresh();
    $donation->refresh();

    expect((float) $piggyBox->total_received)->toBe(0.0)
        ->and($piggyBox->donation_count)->toBe(0)
        ->and($donation->payment_status)->toBe(PaymentStatus::Failed)
        ->and($donation->payment_metadata['webhook']['amount_mismatch'])->toBeTrue();

    Mail::assertNothingSent();
});

it('lets admins manually verify a stuck piggy donation', function () {
    ['piggyBox' => $piggyBox, 'donation' => $donation] = createPiggyWalletFixture([
        'payment_status' => PaymentStatus::Failed,
    ]);
    $admin = User::factory()->create();

    Mail::fake();

    app()->instance(PaymentManager::class, new class extends PaymentManager
    {
        public function __construct() {}

        public function verifyPayment(string $reference, ?string $provider = null): array
        {
            return [
                'success' => true,
                'status' => 'completed',
                'amount' => 30,
                'reference' => $reference,
                'transaction_rrn' => 'RRN-MANUAL-PIGGY',
                'raw_data' => ['status' => 'success'],
            ];
        }
    });

    app(VerifyPiggyDonationPaymentAction::class)->execute($donation, $admin->id);

    $piggyBox->refresh();
    $donation->refresh();

    expect((float) $piggyBox->total_received)->toBe(30.0)
        ->and($piggyBox->donation_count)->toBe(1)
        ->and($donation->payment_status)->toBe(PaymentStatus::Completed)
        ->and($donation->transaction_rrn)->toBe('RRN-MANUAL-PIGGY')
        ->and($donation->manual_verified_by)->toBe($admin->id);

    Mail::assertSent(PiggyDonationReceiptMail::class, 1);
});

it('shows a wallet ledger with gifts and withdrawals', function () {
    ['user' => $user, 'piggyBox' => $piggyBox, 'donation' => $donation] = createPiggyWalletFixture([
        'payment_status' => PaymentStatus::Completed,
        'credited_at' => now(),
    ]);

    $account = WithdrawalAccount::create([
        'user_id' => $user->id,
        'account_type' => 'mobile_money',
        'account_name' => 'Wallet Owner',
        'account_number' => '0240000000',
        'mobile_network' => 'mtn',
        'is_default' => true,
        'is_active' => true,
    ]);

    PiggyBoxWithdrawal::create([
        'piggy_box_id' => $piggyBox->id,
        'user_id' => $user->id,
        'withdrawal_account_id' => $account->id,
        'amount' => 10,
        'fee' => 2,
        'net_amount' => 8,
        'currency_code' => 'GHS',
        'status' => 'pending',
        'reference' => 'PB-WD-LEDGER',
        'payment_provider' => 'trendipay',
    ]);

    $this->actingAs($user)
        ->get(route('piggy.my-piggy-box'))
        ->assertOk()
        ->assertSee('Wallet ledger')
        ->assertSee($donation->payment_reference)
        ->assertSee('PB-WD-LEDGER');
});

it('rate limits public piggy code lookup attempts', function () {
    $bufferLevel = ob_get_level();

    try {
        for ($i = 0; $i < 6; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post(route('piggy.find'), ['piggy_code' => 'NOPE'.$i])
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('piggy.find'), ['piggy_code' => 'NOPE7'])
            ->assertStatus(429);
    } finally {
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
    }
});
