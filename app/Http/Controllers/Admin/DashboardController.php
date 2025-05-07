<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $rolesCount = config('permission.models.role')::count();
        $activitiesCount = Activity::count();

        // Calculate percentage changes (dummy data for now)
        $stats = [
            [
                'name' => 'Total Users',
                'stat' => $totalUsers,
                'change' => '12%',
                'changeType' => 'increase',
                'icon' => 'UsersIcon',
                'href' => route('admin.users.index'),
            ],
            [
                'name' => 'Active Users',
                'stat' => $activeUsers,
                'change' => '5%',
                'changeType' => 'increase',
                'icon' => 'DocumentCheckIcon',
                'href' => route('admin.users.index'),
            ],
            [
                'name' => 'Roles',
                'stat' => $rolesCount,
                'change' => '3%',
                'changeType' => 'increase',
                'icon' => 'ShieldCheckIcon',
                'href' => route('admin.roles.index'),
            ],
            [
                'name' => 'Recent Activities',
                'stat' => $activitiesCount,
                'change' => '7%',
                'changeType' => 'increase',
                'icon' => 'ClockIcon',
                'href' => '#',
            ],
        ];

        // Get recent activities
        $activities = Activity::with('causer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->description,
                    'description' => $activity->description,
                    'target' => $activity->subject_type ?? 'System',
                    'time' => $activity->created_at->diffForHumans(),
                    'datetime' => $activity->created_at->toISOString(),
                ];
            });

        // Get recent users
        $recentUsers = User::latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'status' => $user->status,
                ];
            });

        return Inertia::render('Admin/Dashboard/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'stats' => $stats,
            'activities' => $activities,
            'recentUsers' => $recentUsers,
        ]);
    }

    private function getActivityIcon($description)
    {
        return match (true) {
            str_contains($description, 'created') => 'UserPlusIcon',
            str_contains($description, 'updated') => 'PencilSquareIcon',
            str_contains($description, 'deleted') => 'TrashIcon',
            str_contains($description, 'deactivated') => 'UserMinusIcon',
            default => 'ClockIcon',
        };
    }

    private function getActivityColor($description)
    {
        return match (true) {
            str_contains($description, 'created') => 'bg-green-500',
            str_contains($description, 'updated') => 'bg-blue-500',
            str_contains($description, 'deleted') => 'bg-red-500',
            str_contains($description, 'deactivated') => 'bg-yellow-500',
            default => 'bg-gray-500',
        };
    }
}
