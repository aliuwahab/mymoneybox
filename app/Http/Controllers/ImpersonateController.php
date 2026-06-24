<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(Request $request, User $user)
    {
        $admin = Auth::user();

        if (! $admin->isSuperAdmin()) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            abort(403, 'Cannot impersonate another super admin.');
        }

        $request->session()->put('impersonating_admin_id', $admin->id);

        \Log::info('Impersonation started', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_id' => $user->id,
            'target_email' => $user->email,
        ]);

        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard');
    }

    public function stop(Request $request)
    {
        $adminId = $request->session()->pull('impersonating_admin_id');

        if (! $adminId) {
            return redirect()->route('dashboard');
        }

        \Log::info('Impersonation stopped', [
            'admin_id' => $adminId,
            'was_user_id' => Auth::id(),
        ]);

        Auth::loginUsingId($adminId);

        return redirect('/admin');
    }
}