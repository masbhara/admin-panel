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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UnifiedProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil sesuai dengan role pengguna
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        
        // Tentukan view berdasarkan role
        $view = $isAdmin ? 'Admin/Profile/Edit' : 'Profile/Edit';
        
        return Inertia::render($view, [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'avatar_url' => $user->avatar_url,
                'email_verified_at' => $user->email_verified_at,
            ],
            'mustVerifyEmail' => $this->mustVerifyEmail(),
            'status' => session('status'),
        ]);
    }

    /**
     * Update profil pengguna
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        
        // Update data user
        $user->fill($request->validated());

        // Handle avatar jika ada
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->hasMedia('avatar')) {
                $user->clearMediaCollection('avatar');
            }
            
            // Upload avatar baru
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        // Reset email verification jika email berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Tentukan route untuk redirect berdasarkan role
        $redirectRoute = $isAdmin ? 'admin.profile.edit' : 'profile.edit';

        return Redirect::route($redirectRoute)->with('success', 'Profile updated successfully.');
    }

    /**
     * Hapus akun pengguna
     */
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

    /**
     * Update password pengguna
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        // Tentukan route untuk redirect berdasarkan role
        $redirectRoute = $isAdmin ? 'admin.profile.edit' : 'profile.edit';

        return Redirect::route($redirectRoute)->with('success', 'Password updated successfully.');
    }

    /**
     * Hapus avatar pengguna
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        
        // Jika media_id disediakan
        if ($request->has('media_id')) {
            $media = Media::findOrFail($request->media_id);
            
            if ($media->model_id === $user->id) {
                $media->delete();
                return response()->json(['message' => 'Avatar deleted successfully.']);
            }
            
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
        // Jika tidak ada media_id, hapus semua avatar
        if ($user->hasMedia('avatar')) {
            $user->clearMediaCollection('avatar');
        }
        
        // Tentukan route untuk redirect berdasarkan role dan tipe request
        $redirectRoute = $isAdmin ? 'admin.profile.edit' : 'profile.edit';
        
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Avatar deleted successfully.']);
        }
        
        return Redirect::route($redirectRoute)->with('success', 'Avatar deleted successfully.');
    }

    /**
     * Periksa apakah email perlu diverifikasi
     */
    protected function mustVerifyEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }
} 