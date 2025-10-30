<?php

namespace App\Http\Controllers;

use App\Actions\CreateMoneyBoxAction;
use App\Models\Category;
use App\Models\MoneyBox;
use Illuminate\Http\Request;

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
                'message' => 'Piggy Box created successfully!'
            ]);
        }

        // Traditional redirect for non-AJAX
        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'Piggy Box created successfully!');
    }

    /**
     * Display the specified piggy box
     */
    public function show(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        $moneyBox->load(['category', 'contributions' => function ($query) {
            $query->completed()->recent()->limit(10);
        }]);

        return view('money-boxes.show', compact('moneyBox'));
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
            'end_date' => $validated['end_date'] ?? null,
            'is_ongoing' => $validated['is_ongoing'] ?? false,
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
            ->with('success', 'Piggy Box updated successfully!');
    }

    /**
     * Remove the specified piggy box
     */
    public function destroy(MoneyBox $moneyBox)
    {
        $this->authorize('delete', $moneyBox);

        $moneyBox->delete();

        return redirect()->route('money-boxes.index')
            ->with('success', 'Piggy Box deleted successfully!');
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
        $qrCodePath = $generateQRCodeAction->execute($moneyBox);

        $moneyBox->update(['qr_code_path' => $qrCodePath]);

        return redirect()->route('money-boxes.share', $moneyBox)
            ->with('success', 'QR Code generated successfully!');
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
                'message' => 'Images uploaded successfully!'
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
                'message' => 'Error uploading images: ' . $e->getMessage()
            ], 500);
        }
    }
}
