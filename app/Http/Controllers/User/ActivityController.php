<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return Inertia::render('Activities/Index', [
            'activities' => Activity::causedBy($user)
                ->latest()
                ->paginate(10)
                ->through(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'created_at' => $activity->created_at->diffForHumans(),
                        'properties' => $activity->properties,
                        'icon' => $this->getActivityIcon($activity->description),
                    ];
                }),
        ]);
    }

    private function getActivityIcon($description)
    {
        $icons = [
            'created' => 'plus-circle',
            'updated' => 'pencil',
            'deleted' => 'trash',
            'logged in' => 'login',
            'profile' => 'user',
            'password' => 'key',
            'default' => 'bell',
        ];

        foreach ($icons as $key => $icon) {
            if (str_contains(strtolower($description), $key)) {
                return $icon;
            }
        }

        return $icons['default'];
    }
} 