<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MoneyBox;
use Illuminate\Http\Request;

class PublicBoxController extends Controller
{
    /**
     * Display the homepage with hero and featured piggy boxes
     */
    public function home()
    {
        // Get 6 featured public piggy boxes
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
     * Display the browse page with all public piggy boxes and filters
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
     * Display a specific piggy box (public or private via direct link)
     * Private boxes are accessible via direct link for contributions/donations
     */
    public function show(string $slug)
    {
        $moneyBox = MoneyBox::where('slug', $slug)
            ->with(['category', 'user', 'contributions' => function ($query) {
                $query->completed()->recent()->limit(10);
            }])
            ->firstOrFail();

        // Both public and private boxes can be accessed via their direct link
        // Private boxes just won't be listed in the browse/homepage
        // This allows sharing private campaign links for contributions

        return view('public.show', compact('moneyBox'));
    }
}
