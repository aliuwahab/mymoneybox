<?php

namespace App\Http\Controllers;

use App\Actions\ProcessContributionAction;
use App\Actions\UpdateMoneyBoxStatsAction;
use App\Enums\PaymentStatus;
use App\Models\MoneyBox;
use App\Payment\PaymentManager;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function __construct(
        protected PaymentManager $paymentManager,
        protected ProcessContributionAction $processContributionAction,
        protected UpdateMoneyBoxStatsAction $updateStatsAction
    ) {}

    /**
     * Store a new contribution and initialize payment
     */
    public function store(Request $request, string $slug)
    {
        $moneyBox = MoneyBox::where('slug', $slug)->firstOrFail();

        if (!$moneyBox->canAcceptContributions()) {
            return back()->with('error', 'This piggy box is not accepting contributions.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contributor_name' => 'required_if:is_anonymous,false|string|max:255',
            'contributor_email' => 'required|email',
            'contributor_phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
        ]);

        // Validate contribution amount based on piggy box rules
        if (!$moneyBox->validateContributionAmount($validated['amount'])) {
            return back()->with('error', 'Invalid contribution amount based on the piggy box rules.');
        }

        // Initialize payment
        $paymentData = [
            'email' => $validated['contributor_email'],
            'amount' => $validated['amount'],
            'currency' => $moneyBox->currency_code,
            'reference' => 'contrib_' . uniqid(),
            'callback_url' => route('contributions.callback'),
            'metadata' => [
                'money_box_id' => $moneyBox->id,
                'contributor_name' => $validated['contributor_name'] ?? 'Anonymous',
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'message' => $validated['message'] ?? null,
            ],
        ];

        $payment = $this->paymentManager->initializePayment($paymentData);

        dd($payment);

        if (!$payment['success']) {
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed.');
        }

        // Create contribution record with pending status
        $contributionData = [
            ...$validated,
            'payment_provider' => 'trendipay',
            'payment_reference' => $payment['reference'],
            'payment_status' => PaymentStatus::Pending,
        ];

        $this->processContributionAction->execute($moneyBox, $contributionData);

        // Redirect to payment page
        return redirect($payment['payment_url']);
    }

    /**
     * Handle payment callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('home')->with('error', 'Invalid payment reference.');
        }

        // Verify payment
        $verification = $this->paymentManager->verifyPayment($reference);

        if (!$verification['success']) {
            return redirect()->route('home')->with('error', 'Payment verification failed.');
        }

        // Find and update contribution
        $contribution = \App\Models\Contribution::where('payment_reference', $reference)->first();

        if (!$contribution) {
            return redirect()->route('home')->with('error', 'Contribution not found.');
        }

        if ($contribution->payment_status === PaymentStatus::Completed) {
            return redirect()->route('box.show', $contribution->moneyBox->slug)
                ->with('success', 'Thank you for your contribution!');
        }

        // Update contribution status
        $contribution->update([
            'payment_status' => PaymentStatus::Completed,
        ]);

        // Update piggy box stats
        $this->updateStatsAction->execute($contribution->moneyBox, $contribution);

        return redirect()->route('box.show', $contribution->moneyBox->slug)
            ->with('success', 'Thank you for your contribution!');
    }

    /**
     * Handle payment webhook
     */
    public function webhook(Request $request)
    {
        try {
            $this->paymentManager->handleWebhook($request->all());
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}

