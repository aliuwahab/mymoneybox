<?php

use App\Enums\PaymentStatus;
use App\Mail\ContributionThankYouMail;
use App\Models\Contribution;
use App\Models\MoneyBox;
use App\Models\User;
use App\Payment\PaymentManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

function createMoneyBoxContributionFixture(array $contributionOverrides = []): array
{
    $user = User::factory()->create();

    $moneyBox = MoneyBox::create([
        'user_id' => $user->id,
        'title' => 'Wedding Support',
        'slug' => 'wedding-support-test',
        'currency_code' => 'GHS',
        'visibility' => 'public',
        'contributor_identity' => 'user_choice',
        'amount_type' => 'variable',
        'total_contributions' => 0,
        'contribution_count' => 0,
        'is_active' => true,
        'is_ongoing' => true,
    ]);

    $contribution = Contribution::create(array_merge([
        'money_box_id' => $moneyBox->id,
        'contributor_name' => 'Ama Donor',
        'contributor_email' => 'ama@example.com',
        'contributor_phone' => '0240000000',
        'amount' => 25,
        'currency_code' => 'GHS',
        'is_anonymous' => false,
        'message' => 'Congrats',
        'payment_provider' => 'trendipay',
        'payment_method' => 'mtn',
        'payment_reference' => 'contrib_test_reference',
        'payment_status' => PaymentStatus::Pending,
    ], $contributionOverrides));

    return compact('user', 'moneyBox', 'contribution');
}

function trendiPayWebhookPayload(string $reference = 'contrib_test_reference', string $status = 'success'): array
{
    return [
        'data' => [
            'reference' => $reference,
            'status' => $status,
            'amount' => 2500,
            'rrn' => 'RRN-123',
            'internalId' => 'TP-123',
            'externalId' => 'EXT-123',
            'rSwitch' => 'mtn',
            'responseCode' => '000',
        ],
    ];
}

function signedTrendiPayWebhook(array $payload, string $secret, ?string $signature = null)
{
    $body = json_encode($payload);

    return test()->call(
        'PUT',
        route('trendipay.webhook'),
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_TRENDIPAY_SIGNATURE' => $signature ?? hash_hmac('sha256', $body, $secret),
        ],
        $body,
    );
}

function fakeTrendiPayContributionTransaction(string $reference = 'contrib_test_reference', int $amount = 2500, string $status = 'success', string $rrn = 'RRN-123'): void
{
    config([
        'payment.trendipay.api_key' => 'api-token',
        'payment.trendipay.merchant_external_id' => 'merchant-123',
        'payment.trendipay.api_base_url' => 'https://trendipay.test',
    ]);

    Http::fake([
        'https://trendipay.test/v1/merchants/merchant-123/transactions/'.$rrn => Http::response([
            'success' => true,
            'code' => '000',
            'data' => [
                'reference' => $reference,
                'rrn' => $rrn,
                'amount' => $amount,
                'status' => $status,
                'rSwitch' => 'mtn',
                'accountNumber' => '0240000000',
                'responseCode' => $status === 'success' ? '000' : '111',
                'reason' => $status === 'success' ? null : 'Transaction queued for processing.',
            ],
        ], 200),
    ]);
}

it('rejects invalid contribution webhook signatures and records the attempt', function () {
    ['contribution' => $contribution] = createMoneyBoxContributionFixture();

    config(['payment.trendipay.webhook_secret' => 'secret']);

    signedTrendiPayWebhook(trendiPayWebhookPayload(), 'secret', 'bad-signature')
        ->assertStatus(401);

    $contribution->refresh();

    expect($contribution->payment_status)->toBe(PaymentStatus::Pending)
        ->and($contribution->webhook_attempts)->toBe(1)
        ->and($contribution->webhook_last_signature_valid)->toBeFalse()
        ->and($contribution->webhook_last_status)->toBe('success');
});

it('audits duplicate contribution webhooks without double counting totals', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();
    config(['payment.trendipay.webhook_secret' => 'secret']);
    fakeTrendiPayContributionTransaction();

    signedTrendiPayWebhook(trendiPayWebhookPayload(), 'secret')
        ->assertOk();

    signedTrendiPayWebhook(trendiPayWebhookPayload(), 'secret')
        ->assertOk()
        ->assertJson(['message' => 'Already processed']);

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(25.0)
        ->and($moneyBox->contribution_count)->toBe(1)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Completed)
        ->and($contribution->webhook_attempts)->toBe(2)
        ->and($contribution->webhook_last_signature_valid)->toBeTrue();

    Mail::assertSent(ContributionThankYouMail::class, 1);
});

it('allows unsigned contribution webhooks when rrn verification succeeds', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();
    config(['payment.trendipay.webhook_secret' => 'secret']);
    fakeTrendiPayContributionTransaction();

    $this->putJson(route('trendipay.webhook'), trendiPayWebhookPayload())
        ->assertOk();

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(25.0)
        ->and($moneyBox->contribution_count)->toBe(1)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Completed);

    Mail::assertSent(ContributionThankYouMail::class, 1);
});

it('rejects completed contribution webhooks when the paid amount does not match', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();
    config(['payment.trendipay.webhook_secret' => 'secret']);

    $payload = trendiPayWebhookPayload();
    $payload['data']['amount'] = 1000;
    fakeTrendiPayContributionTransaction(amount: 1000);

    signedTrendiPayWebhook($payload, 'secret')->assertOk();

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(0.0)
        ->and($moneyBox->contribution_count)->toBe(0)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Failed)
        ->and($contribution->payment_metadata['amount_mismatch'])->toBeTrue();

    Mail::assertNothingSent();
});

it('does not complete contribution webhooks until the rrn is verified by trendipay', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();
    config(['payment.trendipay.webhook_secret' => 'secret']);

    $payload = trendiPayWebhookPayload();
    unset($payload['data']['rrn']);

    signedTrendiPayWebhook($payload, 'secret')
        ->assertStatus(202)
        ->assertJson(['message' => 'Payment verification pending']);

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(0.0)
        ->and($moneyBox->contribution_count)->toBe(0)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Pending);

    Mail::assertNothingSent();
});

it('exports owner contributions as csv', function () {
    ['user' => $user, 'moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture([
        'payment_status' => PaymentStatus::Completed,
    ]);

    $response = $this->actingAs($user)
        ->get(route('money-boxes.contributions.export', $moneyBox));

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();

    expect($csv)->toContain('"Contributor name","Contributor email","Contributor phone"')
        ->and($csv)->toContain('"Ama Donor",ama@example.com,0240000000')
        ->and($csv)->toContain($contribution->payment_reference);
});

it('lets owners manually verify a pending contribution', function () {
    ['user' => $user, 'moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();

    app()->instance(PaymentManager::class, new class extends PaymentManager
    {
        public function __construct() {}

        public function verifyPayment(string $reference, ?string $provider = null): array
        {
            return [
                'success' => true,
                'status' => 'completed',
                'amount' => 25,
                'reference' => $reference,
                'transaction_rrn' => 'RRN-VERIFY',
                'raw_data' => ['status' => 'success'],
            ];
        }
    });

    $this->actingAs($user)
        ->post(route('money-boxes.contributions.verify', [$moneyBox, $contribution]))
        ->assertRedirect();

    $moneyBox->refresh();
    $contribution->refresh();

    expect($contribution->payment_status)->toBe(PaymentStatus::Completed)
        ->and($contribution->transaction_rrn)->toBe('RRN-VERIFY')
        ->and((float) $moneyBox->total_contributions)->toBe(25.0)
        ->and($moneyBox->contribution_count)->toBe(1);

    Mail::assertSent(ContributionThankYouMail::class, 1);
});

it('does not manually complete a contribution when verification amount mismatches', function () {
    ['user' => $user, 'moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();

    app()->instance(PaymentManager::class, new class extends PaymentManager
    {
        public function __construct() {}

        public function verifyPayment(string $reference, ?string $provider = null): array
        {
            return [
                'success' => true,
                'status' => 'completed',
                'amount' => 10,
                'reference' => $reference,
                'transaction_rrn' => 'RRN-VERIFY',
                'raw_data' => ['status' => 'success'],
            ];
        }
    });

    $this->actingAs($user)
        ->post(route('money-boxes.contributions.verify', [$moneyBox, $contribution]))
        ->assertRedirect();

    $moneyBox->refresh();
    $contribution->refresh();

    expect($contribution->payment_status)->toBe(PaymentStatus::Failed)
        ->and((float) $moneyBox->total_contributions)->toBe(0.0)
        ->and($moneyBox->contribution_count)->toBe(0)
        ->and($contribution->payment_metadata['manual_verification']['amount_mismatch'])->toBeTrue();

    Mail::assertNothingSent();
});

it('lets owners resend a completed contribution receipt', function () {
    ['user' => $user, 'moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture([
        'payment_status' => PaymentStatus::Completed,
    ]);

    Mail::fake();

    $this->actingAs($user)
        ->post(route('money-boxes.contributions.resend-receipt', [$moneyBox, $contribution]))
        ->assertRedirect();

    $contribution->refresh();

    expect($contribution->receipt_sent_at)->not->toBeNull()
        ->and($contribution->receipt_resent_at)->not->toBeNull()
        ->and($contribution->receipt_resend_count)->toBe(1);

    Mail::assertSent(ContributionThankYouMail::class, 1);
});

it('prevents non owners from contribution operations', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture([
        'payment_status' => PaymentStatus::Completed,
    ]);
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('money-boxes.contributions.export', $moneyBox))
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->post(route('money-boxes.contributions.verify', [$moneyBox, $contribution]))
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->post(route('money-boxes.contributions.resend-receipt', [$moneyBox, $contribution]))
        ->assertForbidden();
});
