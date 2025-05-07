<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with('causer')
            ->latest()
            ->paginate(10)
            ->through(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->description,
                    'description' => $activity->description,
                    'causer' => $activity->causer ? [
                        'name' => $activity->causer->name,
                        'email' => $activity->causer->email,
                    ] : null,
                    'subject_type' => $activity->subject_type,
                    'properties' => $activity->properties,
                    'date' => $activity->created_at->diffForHumans(),
                    'datetime' => $activity->created_at->toISOString(),
                ];
            });

        return Inertia::render('Admin/Activities/Index', [
            'activities' => $activities,
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
