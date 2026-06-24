<?php

namespace App\Http\Controllers;

use App\Models\IdVerification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_type'    => 'required|in:passport,national_card,drivers_license',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'other_names'=> 'nullable|string|max:255',
            'id_number'  => 'nullable|string|max:255',
            'expires_at' => 'required|date|after:today',
            'front_image'=> 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'back_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $verification = IdVerification::create([
            'user_id'     => auth()->id(),
            'id_type'     => $request->id_type,
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'other_names' => $request->other_names,
            'id_number'   => $request->id_number,
            'expires_at'  => $request->expires_at,
            'status'      => 'pending',
        ]);

        $verification->addMediaFromRequest('front_image')
            ->toMediaCollection('front');

        if ($request->hasFile('back_image')) {
            $verification->addMediaFromRequest('back_image')
                ->toMediaCollection('back');
        }

        return redirect()->route('settings.verification')
            ->with('success', 'ID verification submitted successfully! We will review it shortly.');
    }
}