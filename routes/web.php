<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\User\SettingController as UserSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnifiedProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\ActivityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Public routes
Route::get('/', function () {
    return Inertia::render('Public/Home');
})->name('public.home');

Route::prefix('public')->name('public.')->group(function () {
    Route::get('/about', function () {
        return Inertia::render('Public/About');
    })->name('about');
    
    Route::get('/services', function () {
        return Inertia::render('Public/Services');
    })->name('services');
    
    Route::get('/contact', function () {
        return Inertia::render('Public/Contact');
    })->name('contact');
});

// Debug route - can be removed later
Route::get('/debug-permissions', function () {
    $user = auth()->user();
    return [
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can' => [
            'view_roles' => $user->can('view roles'),
            'create_roles' => $user->can('create roles'),
            'edit_roles' => $user->can('edit roles'),
            'delete_roles' => $user->can('delete roles'),
            'view_permissions' => $user->can('view permissions'),
            'create_permissions' => $user->can('create permissions'),
            'edit_permissions' => $user->can('edit permissions'),
            'delete_permissions' => $user->can('delete permissions'),
            'assign_permissions' => $user->can('assign permissions'),
        ],
    ];
})->middleware(['auth', 'verified']);

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Email Verification
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->intended();
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::middleware(['verified'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware(['auth', 'verified'])->group(function () {
            // User routes
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            // Activities route
            Route::get('/activities', [ActivityController::class, 'index'])->name('activities');
            
            // Notifications routes
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
            Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
            Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
            
            // Users resource routes
            Route::resource('users', UserController::class);
            
            // Profile routes
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
                Route::get('/edit', [UnifiedProfileController::class, 'edit'])->name('edit');
                Route::get('/{user}', [ProfileController::class, 'show'])->name('show');
                Route::patch('/', [UnifiedProfileController::class, 'update'])->name('update');
                Route::delete('/', [UnifiedProfileController::class, 'destroy'])->name('destroy');
                Route::put('/password', [UnifiedProfileController::class, 'updatePassword'])->name('password.update');
                Route::delete('/avatar', [UnifiedProfileController::class, 'deleteAvatar'])->name('avatar.delete');
            });

            // User settings routes
            Route::prefix('settings')->name('user.settings.')->group(function () {
                Route::get('/', [UserSettingController::class, 'index'])->name('index');
                Route::patch('/', [UserSettingController::class, 'update'])->name('update');
            });

            // Admin routes
            Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
                Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
                
                // Activities routes
                Route::get('/activities', [AdminActivityController::class, 'index'])->name('activities.index');
                
                // Components demo page
                Route::get('/components-demo', function () {
                    return Inertia::render('ComponentDemo');
                })->name('components-demo');
                
                // Tambahkan route profile admin yang mengarah ke controller terpadu
                Route::get('profile/edit', [UnifiedProfileController::class, 'edit'])->name('profile.edit');
                Route::patch('profile', [UnifiedProfileController::class, 'update'])->name('profile.update');
                Route::delete('profile', [UnifiedProfileController::class, 'destroy'])->name('profile.destroy');
                Route::put('password', [UnifiedProfileController::class, 'updatePassword'])->name('password.update');
                Route::delete('profile/avatar', [UnifiedProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
                
                // Users routes with middleware
                Route::middleware('permission:view users')->group(function () {
                    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
                    Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
                });
                
                Route::middleware('permission:create users')->group(function () {
                    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
                    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
                });
                
                Route::middleware('permission:edit users')->group(function () {
                    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
                    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
                    Route::patch('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
                });
                
                Route::middleware('permission:delete users')->group(function () {
                    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
                });
                
                Route::middleware('permission:manage user status')->group(function () {
                    Route::post('users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.updateStatus');
                });
                
                // Roles routes with middleware
                Route::middleware('permission:view roles')->group(function () {
                    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
                    Route::get('roles/matrix', [RoleController::class, 'matrix'])->name('roles.matrix');
                });

                Route::middleware('permission:create roles')->group(function () {
                    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
                    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
                });

                Route::middleware('permission:edit roles')->group(function () {
                    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
                    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
                    Route::post('roles/toggle-permission', [RoleController::class, 'togglePermission'])->name('roles.toggle-permission');
                });

                Route::middleware('permission:delete roles')->group(function () {
                    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
                });

                // Permissions routes with middleware
                Route::middleware('permission:view permissions')->group(function () {
                    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
                });

                Route::middleware('permission:create permissions')->group(function () {
                    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
                    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
                });

                Route::middleware('permission:edit permissions')->group(function () {
                    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
                    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
                });

                Route::middleware('permission:delete permissions')->group(function () {
                    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
                });
                
                // Impersonation routes
                Route::get('impersonate/{user}', [ImpersonateController::class, 'impersonate'])->name('impersonate');
                Route::get('impersonate-leave', [ImpersonateController::class, 'stopImpersonating'])->name('impersonate.leave');

                // Admin settings routes
                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::get('/', [SettingController::class, 'index'])->name('index');
                    Route::post('/general/update', [SettingController::class, 'updateGeneral'])->name('update');
                    Route::post('/{setting}', [SettingController::class, 'update'])->name('update.item');
                    Route::delete('/{setting}', [SettingController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });
});
