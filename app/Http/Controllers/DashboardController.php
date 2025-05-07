<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activities = Activity::causedBy($user)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => [
                        'name' => $activity->description,
                        'icon' => $this->getActivityIcon($activity->description),
                        'bgColor' => $this->getActivityColor($activity->description),
                    ],
                    'content' => $activity->description,
                    'date' => $activity->created_at->diffForHumans(),
                    'datetime' => $activity->created_at->toISOString(),
                ];
            });

        return Inertia::render('User/Dashboard', [
            'auth' => [
                'user' => $user
            ],
            'userActivities' => $activities,
        ]);
    }

    private function getActivityIcon($description)
    {
        return match (true) {
            str_contains($description, 'profile') => 'PencilIcon',
            str_contains($description, 'document') => 'DocumentTextIcon',
            default => 'ClockIcon',
        };
    }

    private function getActivityColor($description)
    {
        return match (true) {
            str_contains($description, 'profile') => 'bg-blue-500',
            str_contains($description, 'document') => 'bg-green-500',
            default => 'bg-gray-500',
        };
    }
}
