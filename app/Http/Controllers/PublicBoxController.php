<?php

namespace App\Http\Controllers;

use App\Actions\GenerateQRCodeAction;
use App\Models\Category;
use App\Models\EventBox;
use App\Models\MoneyBox;
use App\Models\TrustedLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $trustedLogos = TrustedLogo::active()
            ->ordered()
            ->get();

        $featuredEvents = EventBox::with(['ticketTypes'])
            ->where('status', 'active')
            ->where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->limit(3)
            ->get();

        return view('home', compact('featuredMoneyBoxes', 'trustedLogos', 'featuredEvents'));
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

    /**
     * Public QR code download — no auth required, accessible from the public box page.
     */
    public function downloadQr(string $slug)
    {
        $moneyBox = MoneyBox::where('slug', $slug)->firstOrFail();

        if (! $moneyBox->hasQrCode()) {
            app(GenerateQRCodeAction::class)->execute($moneyBox);
        }

        $media = $moneyBox->getFirstMedia('qr_code');

        if (! $media) {
            abort(404, 'QR Code not found.');
        }

        $filename = "piggybox-{$moneyBox->slug}-qr.png";

        if ($media->getDiskDriverName() === 's3') {
            $contents = Storage::disk($media->disk)->get($media->getPath());

            return response($contents, 200)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        return response()->download($media->getPath(), $filename);
    }

    /**
     * Render the embeddable contribution widget for a box.
     * Served in an <iframe> — no navigation, no sidebar.
     */
    public function embed(string $slug)
    {
        $moneyBox = MoneyBox::where('slug', $slug)
            ->with(['user'])
            ->firstOrFail();

        return response()
            ->view('public.embed', compact('moneyBox'))
            ->withoutHeader('X-Frame-Options')
            ->header('Content-Security-Policy', "frame-ancestors *");
    }
}
