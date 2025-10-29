<?php

namespace App\Http\Controllers;

use App\Actions\CreateMoneyBoxAction;
use App\Models\Category;
use App\Models\MoneyBox;
use Illuminate\Http\Request;

class MoneyBoxController extends Controller
{
    /**
     * Display user's dashboard with their money boxes
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
     * Display a listing of the user's money boxes
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
     * Show the form for creating a new money box
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('money-boxes.create', compact('categories'));
    }

    /**
     * Store a newly created money box
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

        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'Money Box created successfully!');
    }

    /**
     * Display the specified money box
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
     * Show the form for editing the specified money box
     */
    public function edit(MoneyBox $moneyBox)
    {
        $this->authorize('update', $moneyBox);

        $categories = Category::active()->ordered()->get();
        return view('money-boxes.edit', compact('moneyBox', 'categories'));
    }

    /**
     * Update the specified money box
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
        ]);

        $moneyBox->update($validated);

        return redirect()->route('money-boxes.show', $moneyBox)
            ->with('success', 'Money Box updated successfully!');
    }

    /**
     * Remove the specified money box
     */
    public function destroy(MoneyBox $moneyBox)
    {
        $this->authorize('delete', $moneyBox);

        $moneyBox->delete();

        return redirect()->route('money-boxes.index')
            ->with('success', 'Money Box deleted successfully!');
    }

    /**
     * Display statistics for a money box
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
     * Display share options for a money box
     */
    public function share(MoneyBox $moneyBox)
    {
        $this->authorize('view', $moneyBox);

        return view('money-boxes.share', compact('moneyBox'));
    }

    /**
     * Generate QR code for a money box
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
}
