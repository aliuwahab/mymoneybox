<?php

namespace App\Http\Controllers;

use App\Actions\CreateMoneyBoxAction;
use App\Enums\PaymentStatus;
use App\Events\ContributionProcessed;
use App\Mail\ContributionThankYouMail;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\MoneyBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MoneyBoxController extends Controller
{
    /**
     * Display user's dashboard with their piggy boxes
     */
    public function dashboard()
    {
        $moneyBoxes = auth()->user()->moneyBoxes()
            ->with(['category', 'contributions'])
            ->withCount('contributions')
            ->latest()
            ->get();

        return view('money-boxes.dashboard', compact('moneyBoxes'));
    }

    /**
     * Display a listing of the user's piggy boxes
     */
    public function index()
    {
        $moneyBoxes = auth()->user()->moneyBoxes()
            ->with('category')
            ->withCount('contributions')
            ->latest()
            ->paginate(12);

        return view('money-boxes.index', compact('moneyBoxes'));
    }

    /**
     * Show the form for creating a new piggy box
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();

        return view('money-boxes.create-steps', compact('categories'));
    }

    /**
     * Store a newly created piggy box
     */
    public function store(Request $request, CreateMoneyBoxAction $createMoneyBoxAction)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'visibility' => 'required|in:public,private',
            'contributor_identity' => 'required|in:anonymous_allowed,must_identify,user_choice',
            'amount_type' => 'required|in:fixed,variable,minimum,maximum,range',
            'fixed_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'goal_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_ongoing' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['currency_code'] = auth()->user()->country->currency_code;
        $validated['is_active'] = true;

        $moneyBox = $createMoneyBoxAction->execute($validated);

        // Return JSON for AJAX requests (multi-step form)
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => $moneyBox->id,
                'message' => 'PiggyBox created successfully!',
            ]);
        }

        // Traditional redirect for non-AJAX
        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'PiggyBox created successfully!');
    }

    /**
     * Display the specified piggy box
     */
    public function show(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        $moneyBox->load([
            'category',
            'contributions' => function ($query) {
                $query->recent()->limit(50);
            },
            'withdrawals' => function ($query) {
                $query->with(['withdrawalAccount', 'processedBy'])
                    ->latest()
                    ->limit(10);
            },
        ]);

        return view('money-boxes.show', compact('moneyBox'));
    }

    public function exportContributions(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        $filename = Str::slug($moneyBox->title).'-contributions-'.now()->format('Ymd-His').'.csv';

        return $this->streamContributionCsv($moneyBox->contributions(), $filename);
    }

    public function exportAllContributions()
    {
        $boxIds = auth()->user()->moneyBoxes()->pluck('id');
        $filename = 'piggybox-contributions-'.now()->format('Ymd-His').'.csv';

        return $this->streamContributionCsv(
            Contribution::query()->whereIn('money_box_id', $boxIds)->with('moneyBox'),
            $filename,
            includeBox: true,
        );
    }

    private function streamContributionCsv($query, string $filename, bool $includeBox = false)
    {
        return response()->streamDownload(function () use ($query, $includeBox) {
            $handle = fopen('php://output', 'w');

            $headings = [
                'Contributor name',
                'Contributor email',
                'Contributor phone',
            ];

            if ($includeBox) {
                $headings[] = 'PiggyBox';
            }

            fputcsv($handle, array_merge($headings, [
                'Amount',
                'Currency',
                'Status',
                'Payment method',
                'Payment reference',
                'Transaction RRN',
                'Message',
                'Anonymous',
                'Webhook attempts',
                'Last webhook at',
                'Last webhook status',
                'Last signature valid',
                'Receipt sent at',
                'Receipt resent at',
                'Created at',
            ]), ',', '"', '\\', "\n");

            $query
                ->reorder()
                ->chunkById(500, function ($contributions) use ($handle, $includeBox) {
                    foreach ($contributions as $contribution) {
                        $row = [
                            $contribution->getDisplayName(),
                            $contribution->contributor_email,
                            $contribution->contributor_phone,
                        ];

                        if ($includeBox) {
                            $row[] = $contribution->moneyBox?->title;
                        }

                        fputcsv($handle, array_merge($row, [
                            (float) $contribution->amount,
                            $contribution->currency_code,
                            $contribution->payment_status->value,
                            $contribution->payment_method,
                            $contribution->payment_reference,
                            $contribution->transaction_rrn,
                            $contribution->message,
                            $contribution->is_anonymous ? 'yes' : 'no',
                            $contribution->webhook_attempts,
                            $contribution->webhook_last_received_at?->toDateTimeString(),
                            $contribution->webhook_last_status,
                            is_bool($contribution->webhook_last_signature_valid) ? ($contribution->webhook_last_signature_valid ? 'yes' : 'no') : '',
                            $contribution->receipt_sent_at?->toDateTimeString(),
                            $contribution->receipt_resent_at?->toDateTimeString(),
                            $contribution->created_at?->toDateTimeString(),
                        ]), ',', '"', '\\', "\n");
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function verifyContribution(MoneyBox $moneyBox, Contribution $contribution)
    {
        $this->authorize('view', $moneyBox);
        abort_if($contribution->money_box_id !== $moneyBox->id, 404);

        if ($contribution->payment_status === PaymentStatus::Completed) {
            return back()->with('success', 'Contribution is already completed.');
        }

        $verification = app(\App\Payment\PaymentManager::class)->verifyPayment($contribution->payment_reference);
        $previousStatus = $contribution->payment_status;
        $paymentStatus = match ($verification['status'] ?? 'failed') {
            'completed' => PaymentStatus::Completed,
            'pending' => PaymentStatus::Pending,
            default => PaymentStatus::Failed,
        };

        $amountMatches = $contribution->matchesPaidAmount($verification['amount'] ?? null);

        if ($paymentStatus === PaymentStatus::Completed && ! $amountMatches) {
            Log::warning('Contribution recovery amount mismatch', [
                'contribution_id' => $contribution->id,
                'reference' => $contribution->payment_reference,
                'expected_amount' => (float) $contribution->amount,
                'verified_amount' => (float) $verification['amount'],
            ]);

            $paymentStatus = PaymentStatus::Failed;
        }

        $contribution->update([
            'payment_status' => $paymentStatus,
            'transaction_rrn' => $verification['transaction_rrn'] ?? $contribution->transaction_rrn,
            'payment_metadata' => array_merge($contribution->payment_metadata ?? [], [
                'manual_verification' => [
                    'at' => now()->toDateTimeString(),
                    'status' => $verification['status'] ?? null,
                    'success' => $verification['success'] ?? null,
                    'message' => $verification['message'] ?? null,
                    'raw_data' => $verification['raw_data'] ?? null,
                    'amount_mismatch' => ! $amountMatches,
                ],
            ]),
        ]);

        if ($previousStatus !== PaymentStatus::Completed && $paymentStatus === PaymentStatus::Completed) {
            app(\App\Actions\UpdateMoneyBoxStatsAction::class)->execute($moneyBox, $contribution->fresh());
            event(new ContributionProcessed($contribution->fresh(), $moneyBox));
        }

        return back()->with(
            $paymentStatus === PaymentStatus::Completed ? 'success' : 'error',
            $paymentStatus === PaymentStatus::Completed
                ? 'Contribution verified and marked completed.'
                : 'Contribution is still not completed.'
        );
    }

    public function resendContributionReceipt(MoneyBox $moneyBox, Contribution $contribution)
    {
        $this->authorize('view', $moneyBox);
        abort_if($contribution->money_box_id !== $moneyBox->id, 404);

        if ($contribution->payment_status !== PaymentStatus::Completed) {
            return back()->with('error', 'Receipts can only be sent for completed contributions.');
        }

        if (! $contribution->contributor_email || $contribution->contributor_email === 'noreply@mypiggybox.com') {
            return back()->with('error', 'This contribution does not have a donor email.');
        }

        Mail::to($contribution->contributor_email)
            ->send(new ContributionThankYouMail($contribution, $moneyBox));

        $contribution->forceFill([
            'receipt_sent_at' => $contribution->receipt_sent_at ?? now(),
            'receipt_resent_at' => now(),
            'receipt_resend_count' => $contribution->receipt_resend_count + 1,
        ])->save();

        return back()->with('success', 'Receipt resent to donor.');
    }

    /**
     * Show the form for editing the specified piggy box
     */
    public function edit(MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $categories = Category::active()->ordered()->get();

        return view('money-boxes.edit', compact('moneyBox', 'categories'));
    }

    /**
     * Update the specified piggy box
     */
    public function update(Request $request, MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'visibility' => 'required|in:public,private',
            'contributor_identity' => 'required|in:anonymous_allowed,must_identify,user_choice',
            'amount_type' => 'required|in:fixed,variable,minimum,maximum,range',
            'fixed_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'goal_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_ongoing' => 'boolean',
            'is_active' => 'boolean',
            // Media validation
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remove_main_image' => 'nullable|in:0,1',
            'remove_gallery_images' => 'nullable|string',
        ]);

        // Update only non-media fields
        $isOngoing = $validated['is_ongoing'] ?? false;

        $moneyBox->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'visibility' => $validated['visibility'],
            'contributor_identity' => $validated['contributor_identity'],
            'amount_type' => $validated['amount_type'],
            'fixed_amount' => $validated['fixed_amount'] ?? null,
            'minimum_amount' => $validated['minimum_amount'] ?? null,
            'maximum_amount' => $validated['maximum_amount'] ?? null,
            'goal_amount' => $validated['goal_amount'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $isOngoing ? null : ($validated['end_date'] ?? null),
            'is_ongoing' => $isOngoing,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Handle main image removal
        if ($request->filled('remove_main_image') && $request->remove_main_image == '1') {
            $moneyBox->clearMediaCollection('main');
        }

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $moneyBox->clearMediaCollection('main');
            $moneyBox->addMediaFromRequest('main_image')
                ->toMediaCollection('main');
        }

        // Handle gallery images removal
        if ($request->filled('remove_gallery_images')) {
            $mediaIds = explode(',', $request->remove_gallery_images);
            foreach ($mediaIds as $mediaId) {
                $media = $moneyBox->getMedia('gallery')->firstWhere('id', $mediaId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $moneyBox->addMedia($image)
                    ->toMediaCollection('gallery');
            }
        }

        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'PiggyBox updated successfully!');
    }

    /**
     * Remove the specified piggy box
     */
    public function destroy(MoneyBox $moneyBox)
    {
        $this->authorize('delete', $moneyBox);

        $moneyBox->delete();

        return redirect()->route('money-boxes.index')
            ->with('success', 'PiggyBox deleted successfully!');
    }

    /**
     * Display contributors across all user's piggy boxes
     */
    public function contributors()
    {
        $user = auth()->user();
        $boxIds = $user->moneyBoxes()->pluck('id');

        $contributors = \App\Models\Contribution::whereIn('money_box_id', $boxIds)
            ->completed()
            ->selectRaw('
                COALESCE(contributor_name, "Anonymous") as name,
                contributor_email as email,
                COUNT(*) as contributions_count,
                COUNT(DISTINCT money_box_id) as boxes_count,
                SUM(amount) as total_amount,
                MAX(created_at) as last_contributed_at
            ')
            ->groupBy('contributor_name', 'contributor_email')
            ->orderByDesc('total_amount')
            ->get();

        $totalContributors = $contributors->count();
        $repeatContributors = $contributors->where('contributions_count', '>', 1)->count();
        $largestGift = \App\Models\Contribution::whereIn('money_box_id', $boxIds)
            ->completed()
            ->orderByDesc('amount')
            ->first();

        return view('money-boxes.contributors', compact(
            'contributors', 'totalContributors', 'repeatContributors', 'largestGift'
        ));
    }

    /**
     * Display analytics across all user's piggy boxes
     */
    public function analytics()
    {
        $user = auth()->user();
        $boxIds = $user->moneyBoxes()->pluck('id');

        $totalRaised = $user->moneyBoxes()->sum('total_contributions');
        $totalContributors = $user->moneyBoxes()->sum('contribution_count');

        // Daily contributions for the last 14 days
        $dailyContributions = \App\Models\Contribution::whereIn('money_box_id', $boxIds)
            ->completed()
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing days
        $daily = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $dailyContributions->firstWhere('date', $date);
            $daily->push([
                'date' => $date,
                'label' => now()->subDays($i)->format('M j'),
                'total' => $found ? (float) $found->total : 0,
                'count' => $found ? (int) $found->count : 0,
            ]);
        }

        // Top referral sources (placeholder - based on contribution metadata if available)
        $topSources = collect([
            ['source' => 'WhatsApp', 'percentage' => 48],
            ['source' => 'Direct link', 'percentage' => 26],
            ['source' => 'QR code', 'percentage' => 14],
            ['source' => 'Facebook', 'percentage' => 8],
            ['source' => 'Twitter / X', 'percentage' => 4],
        ]);

        return view('money-boxes.analytics', compact(
            'totalRaised', 'totalContributors', 'daily', 'topSources'
        ));
    }

    /**
     * Display statistics for a piggy box
     */
    public function statistics(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        $moneyBox->load(['contributions' => function ($query) {
            $query->completed()->recent();
        }]);

        $stats = [
            'total_amount' => $moneyBox->total_contributions,
            'total_count' => $moneyBox->contribution_count,
            'progress_percentage' => $moneyBox->getProgressPercentage(),
            'recent_contributions' => $moneyBox->contributions()->completed()->recent()->limit(20)->get(),
        ];

        return view('money-boxes.statistics', compact('moneyBox', 'stats'));
    }

    /**
     * Display share options for a piggy box
     */
    public function share(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        return view('money-boxes.share', compact('moneyBox'));
    }

    /**
     * Generate QR code for a piggy box
     */
    public function generateQrCode(MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $generateQRCodeAction = app(\App\Actions\GenerateQRCodeAction::class);
        $generateQRCodeAction->execute($moneyBox);

        return redirect()->route('money-boxes.share', $moneyBox)
            ->with('success', 'QR Code generated successfully!');
    }

    /**
     * Download QR code for a piggy box
     */
    public function downloadQrCode(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        // Generate QR code if it doesn't exist
        if (! $moneyBox->hasQrCode()) {
            $generateQRCodeAction = app(\App\Actions\GenerateQRCodeAction::class);
            $generateQRCodeAction->execute($moneyBox);
        }

        $media = $moneyBox->getFirstMedia('qr_code');

        if (! $media) {
            return redirect()->back()->with('error', 'QR Code not found.');
        }

        $filename = "moneybox-{$moneyBox->slug}-qr.png";

        // For S3/remote files, stream the content
        if ($media->getDiskDriverName() === 's3') {
            $contents = \Storage::disk($media->disk)->get($media->getPath());

            return response($contents, 200)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        // For local files, use regular download
        return response()->download($media->getPath(), $filename);
    }

    /**
     * Upload media (main image and gallery) for a piggy box
     */
    public function uploadMedia(Request $request, MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $request->validate([
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB each
        ]);

        try {
            // Upload main image
            if ($request->hasFile('main_image')) {
                $moneyBox->clearMediaCollection('main');
                $moneyBox->addMediaFromRequest('main_image')
                    ->toMediaCollection('main');
            }

            // Upload gallery images
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $moneyBox->addMedia($image)
                        ->toMediaCollection('gallery');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Media upload error', [
                'money_box_id' => $moneyBox->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'has_main_image' => $request->hasFile('main_image'),
                'has_gallery' => $request->hasFile('gallery'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: '.$e->getMessage(),
            ], 500);
        }
    }
}
