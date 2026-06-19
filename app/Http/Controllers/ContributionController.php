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

        $identity = $moneyBox->contributor_identity->value;
        $isAnonymous = $identity === 'anonymous_allowed'
            || ($identity !== 'must_identify' && $request->boolean('is_anonymous'));

        $validated = $request->validate([
            'amount'             => 'required|numeric|min:0.01',
            'contributor_name'   => [$isAnonymous ? 'nullable' : 'required', 'string', 'max:255'],
            'contributor_email'  => [$isAnonymous ? 'nullable' : 'required', 'email', 'max:255'],
            'contributor_phone'  => 'nullable|string|max:20',
            'message'            => 'nullable|string|max:500',
            'is_anonymous'       => 'nullable|boolean',
        ]);

        if (!$moneyBox->validateContributionAmount($validated['amount'])) {
            return back()->with('error', 'Invalid contribution amount based on the PiggyBox rules.');
        }

        if (empty($validated['contributor_email'])) {
            $validated['contributor_email'] = 'noreply@mypiggybox.com';
        }

        $reference = 'contrib_' . uniqid();

        $payment = $this->paymentManager->initializePayment([
            'email'       => $validated['contributor_email'],
            'amount'      => $validated['amount'],
            'currency'    => $moneyBox->currency_code,
            'reference'   => $reference,
            'return_url'  => route('box.confirm', ['slug' => $moneyBox->slug, 'reference' => $reference]),
            'webhook_url' => route('trendipay.webhook'),
            'description' => "Contribution to {$moneyBox->title}",
            'metadata'    => [
                'money_box_id'    => $moneyBox->id,
                'money_box_title' => $moneyBox->title,
                'contributor_name' => $isAnonymous ? 'Anonymous' : $validated['contributor_name'],
                'is_anonymous'    => $isAnonymous,
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
            contributorName: $isAnonymous ? null : $validated['contributor_name'],
            contributorPhone: $validated['contributor_phone'] ?? null,
            isAnonymous: $isAnonymous,
            message: $validated['message'] ?? null,
            paymentProvider: 'trendipay',
            paymentReference: $payment['reference'],
            paymentStatus: PaymentStatus::Pending,
        );

        $this->processContributionAction->execute($moneyBox, $contributionData);

        return redirect($payment['payment_url']);
    }

    public function confirm(string $slug, string $reference)
    {
        $moneyBox = MoneyBox::query()->where('slug', $slug)->firstOrFail();
        $contribution = $moneyBox->contributions()->where('payment_reference', $reference)->first();
        $pending = $contribution && $contribution->payment_status === PaymentStatus::Pending;

        return view('contributions.confirmation', compact('moneyBox', 'contribution', 'reference', 'pending'));
    }

    public function status(string $slug, string $reference)
    {
        $moneyBox = MoneyBox::query()->where('slug', $slug)->firstOrFail();
        $contribution = $moneyBox->contributions()->where('payment_reference', $reference)->first();

        if (!$contribution) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status' => $contribution->payment_status->value,
            'amount' => $contribution->amount,
            'name'   => $contribution->getDisplayName(),
        ]);
    }
}
