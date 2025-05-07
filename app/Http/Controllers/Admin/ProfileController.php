<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Admin/Profile/Edit', [
            'user' => $request->user(),
            'status' => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        
        $user->fill($request->validated());

        if ($request->hasFile('avatar')) {
            if ($user->hasMedia('avatar')) {
                $user->clearMediaCollection('avatar');
            }
            
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return Redirect::route('verification.notice');
        }
        
        $user->refresh();
        
        return Redirect::route('admin.profile.edit')->with([
            'success' => 'Profile updated successfully.',
            'user' => $user->only('name', 'email', 'avatar_url'),
        ]);
    }

    public function destroy(Request $request)
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

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return Redirect::route('admin.profile.edit')->with('success', 'Password updated successfully.');
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        $media = Media::findOrFail($request->media_id);

        if ($media->model_id === $user->id) {
            $media->delete();
            return response()->json(['message' => 'Avatar deleted successfully.']);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
