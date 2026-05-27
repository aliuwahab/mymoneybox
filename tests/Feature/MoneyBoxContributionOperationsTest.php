<?php

use App\Enums\PaymentStatus;
use App\Mail\ContributionThankYouMail;
use App\Models\Contribution;
use App\Models\MoneyBox;
use App\Models\User;
use App\Payment\PaymentManager;
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

it('audits duplicate contribution webhooks without double counting totals', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();

    $this->putJson(route('trendipay.webhook'), trendiPayWebhookPayload())
        ->assertOk();

    $this->putJson(route('trendipay.webhook'), trendiPayWebhookPayload())
        ->assertOk()
        ->assertJson(['message' => 'Already processed']);

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(25.0)
        ->and($moneyBox->contribution_count)->toBe(1)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Completed)
        ->and($contribution->webhook_attempts)->toBe(2)
        ->and($contribution->webhook_last_status)->toBe('completed');

    Mail::assertSent(ContributionThankYouMail::class, 1);
});

it('processes unsigned contribution webhooks', function () {
    ['moneyBox' => $moneyBox, 'contribution' => $contribution] = createMoneyBoxContributionFixture();

    Mail::fake();

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

    $payload = trendiPayWebhookPayload();
    $payload['data']['amount'] = 1000;

    $this->putJson(route('trendipay.webhook'), $payload)->assertOk();

    $moneyBox->refresh();
    $contribution->refresh();

    expect((float) $moneyBox->total_contributions)->toBe(0.0)
        ->and($moneyBox->contribution_count)->toBe(0)
        ->and($contribution->payment_status)->toBe(PaymentStatus::Failed)
        ->and($contribution->payment_metadata['amount_mismatch'])->toBeTrue();

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
