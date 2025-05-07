<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImpersonateController extends Controller
{
    public function impersonate(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Store the admin's ID in the session
        session()->put('admin_id', auth()->id());
        
        // Set the impersonation
        session()->put('impersonate', $user->id);

        return redirect()->route('dashboard');
    }

    public function stopImpersonating()
    {
        // Get the admin's ID from session
        $adminId = session()->get('admin_id');

        // Clear the impersonation
        session()->forget(['impersonate', 'admin_id']);

        if ($adminId) {
            auth()->loginUsingId($adminId);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
