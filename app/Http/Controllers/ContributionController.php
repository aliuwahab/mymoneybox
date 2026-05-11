<?php

namespace App\Http\Controllers;

use App\Actions\ProcessContributionAction;
use App\Data\ContributionData;
use App\Enums\PaymentStatus;
use App\Models\MoneyBox;
use App\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContributionController extends Controller
{
    public function __construct(
        protected PaymentManager $paymentManager,
        protected ProcessContributionAction $processContributionAction,
    ) {}

    public function store(Request $request, string $slug)
    {
        $moneyBox = MoneyBox::query()->where('slug', $slug)->firstOrFail();

        if (!$moneyBox->canAcceptContributions()) {
            return back()->with('error', 'This box is not accepting contributions.');
        }

        $validated = $request->validate([
            'amount'             => 'required|numeric|min:0.01',
            'contributor_name'   => 'required_if:is_anonymous,false|nullable|string|max:255',
            'contributor_email'  => 'required|email|max:255',
            'contributor_phone'  => 'nullable|string|max:20',
            'message'            => 'nullable|string|max:500',
            'is_anonymous'       => 'boolean',
        ]);

        if (!$moneyBox->validateContributionAmount($validated['amount'])) {
            return back()->with('error', 'Invalid contribution amount based on the box rules.');
        }

        $reference = 'contrib_' . uniqid();

        $payment = $this->paymentManager->initializePayment([
            'email'       => $validated['contributor_email'],
            'amount'      => $validated['amount'],
            'currency'    => $moneyBox->currency_code,
            'reference'   => $reference,
            'return_url'  => route('box.show', $moneyBox->slug),
            'webhook_url' => route('trendipay.webhook'),
            'description' => "Contribution to {$moneyBox->title}",
            'metadata'    => [
                'money_box_id'    => $moneyBox->id,
                'money_box_title' => $moneyBox->title,
                'contributor_name' => $validated['contributor_name'] ?? 'Anonymous',
                'is_anonymous'    => $validated['is_anonymous'] ?? false,
                'message'         => $validated['message'] ?? null,
            ],
        ]);

        if (!$payment['success']) {
            Log::warning('Contribution: payment link failed', ['payment' => $payment, 'slug' => $slug]);
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed. Please try again.');
        }

        $contributionData = new ContributionData(
            amount: (float) $validated['amount'],
            contributorEmail: $validated['contributor_email'],
            contributorName: $validated['contributor_name'] ?? null,
            contributorPhone: $validated['contributor_phone'] ?? null,
            isAnonymous: (bool) ($validated['is_anonymous'] ?? false),
            message: $validated['message'] ?? null,
            paymentProvider: 'trendipay',
            paymentReference: $payment['reference'],
            paymentStatus: PaymentStatus::Pending,
        );

        $this->processContributionAction->execute($moneyBox, $contributionData);

        return redirect($payment['payment_url']);
    }
}