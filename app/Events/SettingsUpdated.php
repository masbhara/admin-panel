<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SettingsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $settings;

    public function __construct(Collection $settings)
    {
        $this->settings = $settings;
    }

    public function broadcastOn()
    {
        return new Channel('settings');
    }

    public function broadcastAs()
    {
        return 'settings.updated';
    }

    public function broadcastWith()
    {
        return [
            'settings' => $this->settings
        ];
    }
} 