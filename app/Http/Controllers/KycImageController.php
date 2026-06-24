<?php

namespace App\Http\Controllers;

use App\Models\IdVerification;
use Illuminate\Support\Facades\Storage;

class KycImageController extends Controller
{
    public function show(IdVerification $verification, string $collection)
    {
        abort_unless(in_array($collection, ['front', 'back']), 404);
        abort_unless(auth()->user()?->isAdmin(), 403);

        $media = $verification->getFirstMedia($collection);

        abort_if(! $media, 404);

        $disk = Storage::disk($media->disk);
        $relativePath = $media->getPathRelativeToRoot();

        abort_unless($disk->exists($relativePath), 404);

        return response()->stream(function () use ($disk, $relativePath) {
            echo $disk->get($relativePath);
        }, 200, [
            'Content-Type'        => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
            'Cache-Control'       => 'private, no-store',
        ]);
    }
}