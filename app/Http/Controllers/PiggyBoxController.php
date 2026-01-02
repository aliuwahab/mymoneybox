<?php

namespace App\Http\Controllers;

use App\Actions\CreatePiggyBoxForUser;
use App\Enums\PaymentStatus;
use App\Models\PiggyBox;
use App\Models\PiggyDonation;
use App\Models\User;
use App\Payment\PaymentManager;
use Illuminate\Http\Request;

class PiggyBoxController extends Controller
{
    public function __construct(
        protected PaymentManager $paymentManager
    ) {}

    /**
     * Show piggy code lookup form
     */
    public function lookup()
    {
        return view('piggy.lookup');
    }

    /**
     * Find piggy box by code and show donation form
     */
    public function findByCode(Request $request)
    {
        $request->validate([
            'piggy_code' => 'required|string|max:10',
        ]);

        $code = strtoupper(trim($request->piggy_code));
        
        $user = User::where('piggy_code', $code)
            ->with(['piggyBox', 'country'])
            ->first();

        if (!$user || !$user->piggyBox) {
            return back()->with('error', 'Invalid piggy code. Please check and try again.');
        }

        if (!$user->piggyBox->canReceiveDonations()) {
            return back()->with('error', 'This piggy box is not currently accepting donations.');
        }

        return view('piggy.donate', [
            'user' => $user,
            'piggyBox' => $user->piggyBox,
        ]);
    }

    /**
     * Process piggy box donation
     */
    public function donate(Request $request, User $user)
    {
        $piggyBox = $user->piggyBox;

        if (!$piggyBox || !$piggyBox->canReceiveDonations()) {
            return back()->with('error', 'This piggy box is not accepting donations.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email',
            'donor_phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
        ]);

        // Set default email if not provided
        if (empty($validated['donor_email'])) {
            $validated['donor_email'] = 'noreply@mymoneybox.com';
        }

        // Initialize payment
        $paymentData = [
            'email' => $validated['donor_email'],
            'amount' => $validated['amount'],
            'currency' => $piggyBox->currency_code,
            'reference' => 'piggy_' . uniqid(),
            'return_url' => route('piggy.callback'),
            'webhook_url' => route('piggy.webhook'),
            'metadata' => [
                'piggy_box_id' => $piggyBox->id,
                'user_id' => $user->id,
                'donor_name' => $validated['donor_name'] ?? 'Anonymous',
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'message' => $validated['message'] ?? null,
            ],
        ];

        $payment = $this->paymentManager->initializePayment($paymentData);

        if (!$payment['success']) {
            return back()->with('error', $payment['message'] ?? 'Payment initialization failed.');
        }

        // Create donation record with pending status
        PiggyDonation::create([
            'piggy_box_id' => $piggyBox->id,
            'donor_name' => $validated['donor_name'] ?? 'Anonymous',
            'donor_email' => $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'amount' => $validated['amount'],
            'currency_code' => $piggyBox->currency_code,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'message' => $validated['message'] ?? null,
            'payment_provider' => 'trendipay',
            'payment_reference' => $payment['reference'],
            'payment_status' => PaymentStatus::Pending,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect to payment page
        return redirect($payment['payment_url']);
    }

    /**
     * Handle payment callback for piggy donations
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

        // Find and update donation
        $donation = PiggyDonation::where('payment_reference', $reference)->first();

        if (!$donation) {
            return redirect()->route('home')->with('error', 'Donation not found.');
        }

        if ($donation->payment_status === PaymentStatus::Completed) {
            return redirect()->route('home')
                ->with('success', 'Thank you for your gift!');
        }

        // Update donation status
        $donation->update([
            'payment_status' => PaymentStatus::Completed,
        ]);

        // Update piggy box stats
        $piggyBox = $donation->piggyBox;
        $piggyBox->increment('total_received', $donation->amount);
        $piggyBox->increment('donation_count');

        return redirect()->route('home')
            ->with('success', 'Thank you for your gift to ' . $piggyBox->user->name . '!');
    }

    /**
     * Show user's own piggy box dashboard
     */
    public function myPiggyBox()
    {
        $user = auth()->user();
        
        // Auto-create piggy box if user doesn't have one
        $piggyBox = app(CreatePiggyBoxForUser::class)->execute($user);

        $recentDonations = $piggyBox->donations()
            ->completed()
            ->recent()
            ->limit(20)
            ->get();

        // Generate shareable URL
        $shareUrl = route('piggy.show', $user->piggy_code);

        return view('piggy.my-piggy-box', [
            'piggyBox' => $piggyBox,
            'recentDonations' => $recentDonations,
            'shareUrl' => $shareUrl,
        ]);
    }

    /**
     * Show piggy box by code (direct URL access)
     */
    public function showByCode(string $code)
    {
        $code = strtoupper(trim($code));
        
        $user = User::where('piggy_code', $code)
            ->with(['piggyBox', 'country'])
            ->first();

        if (!$user || !$user->piggyBox) {
            return redirect()->route('piggy.lookup')
                ->with('error', 'Invalid piggy code. Please check and try again.');
        }

        if (!$user->piggyBox->canReceiveDonations()) {
            return redirect()->route('piggy.lookup')
                ->with('error', 'This piggy box is not currently accepting donations.');
        }

        return view('piggy.donate', [
            'user' => $user,
            'piggyBox' => $user->piggyBox,
        ]);
    }

    /**
     * Generate QR code for user's piggy box
     */
    public function generateQrCode()
    {
        $user = auth()->user();
        $piggyBox = $user->piggyBox;

        if (!$piggyBox) {
            return redirect()->back()->with('error', 'Piggy box not found.');
        }

        $generateQRCodeAction = app(\App\Actions\GeneratePiggyQRCodeAction::class);
        $generateQRCodeAction->execute($piggyBox);

        return redirect()->route('piggy.my-piggy-box')
            ->with('success', 'QR Code generated successfully!');
    }

    /**
     * Download QR code for user's piggy box
     */
    public function downloadQrCode()
    {
        $user = auth()->user();
        $piggyBox = $user->piggyBox;

        if (!$piggyBox) {
            return redirect()->back()->with('error', 'Piggy box not found.');
        }

        // Generate QR code if it doesn't exist
        if (!$piggyBox->hasQrCode()) {
            $generateQRCodeAction = app(\App\Actions\GeneratePiggyQRCodeAction::class);
            $generateQRCodeAction->execute($piggyBox);
        }

        $media = $piggyBox->getFirstMedia('qr_code');
        
        if (!$media) {
            return redirect()->back()->with('error', 'QR Code not found.');
        }

        return response()->download($media->getPath(), "piggybox-{$user->piggy_code}-qr.png");
    }
}
