<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MoneyBox;
use Illuminate\Http\Request;

class PublicBoxController extends Controller
{
    /**
     * Display the homepage with hero and featured money boxes
     */
    public function home()
    {
        // Get 6 featured public money boxes
        $featuredMoneyBoxes = MoneyBox::with(['category', 'user'])
            ->public()
            ->active()
            ->started()
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact('featuredMoneyBoxes'));
    }

    /**
     * Display the browse page with all public money boxes and filters
     */
    public function index(Request $request)
    {
        $query = MoneyBox::with(['category', 'user'])
            ->public()
            ->active()
            ->started();

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $moneyBoxes = $query->latest()->paginate(12);
        $categories = Category::active()->ordered()->get();

        return view('public.index', compact('moneyBoxes', 'categories'));
    }

    /**
     * Display a specific public money box
     */
    public function show(string $slug)
    {
        $moneyBox = MoneyBox::where('slug', $slug)
            ->with(['category', 'user', 'contributions' => function ($query) {
                $query->completed()->recent()->limit(10);
            }])
            ->firstOrFail();

        // Check if box is public or belongs to current user
        if ($moneyBox->visibility->value !== 'public' &&
            (!auth()->check() || auth()->id() !== $moneyBox->user_id)) {
            abort(404);
        }

        return view('public.show', compact('moneyBox'));
    }
}
