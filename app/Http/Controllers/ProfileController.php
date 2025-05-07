<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/Index', [
            'mustVerifyEmail' => $this->mustVerifyEmail(),
            'status' => session('status'),
        ]);
    }

    public function show($id): Response
    {
        $user = Auth::user();
        
        // Only admin can view other profiles
        if ($user->id != $id && !$user->hasRole('admin')) {
            return redirect()->route('profile.index');
        }

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'mustVerifyEmail' => $this->mustVerifyEmail(),
            'status' => session('status'),
        ]);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'address' => $request->user()->address,
                'avatar_url' => $request->user()->avatar_url,
                'email_verified_at' => $request->user()->email_verified_at,
            ],
            'mustVerifyEmail' => $this->mustVerifyEmail(),
            'status' => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        return Redirect::route('profile.edit')->with('success', 'Avatar updated successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function mustVerifyEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }
}
