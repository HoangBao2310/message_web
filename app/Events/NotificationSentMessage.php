<?php

namespace App\Events;

use App\Models\notificationUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationSentMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // notification thực chất là một đối tượng conversation
    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {

        $this->notification->latestMessage = $this->notification->messageLast;
        $this->notification->latestMessage->sender = $this->notification->messageLast->sender;

        if (!$this->notification->is_group) {
            $this->notification->friend = $this->notification->users->first();
        }
        
        return new PrivateChannel('notifications.' . $this->notification->id);
    }

}
