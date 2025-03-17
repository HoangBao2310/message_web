<?php

namespace App\Events;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        $this->message->load('sender');
        $this->message->sender->avatar_url = $this->message->sender->avatar ? asset($this->message->sender->avatar) 
        : asset('/assets/images/avatar_default.jpg');
        $this->message->time_diff = $this->formatTimeDiff($this->message->created_at, Carbon::now());


        $notification = $this->message->conversation;

        broadcast(new NotificationSentMessage($notification))->toOthers();

        return new PrivateChannel('chat.'.$this->message->conversation->id);
    }

    private function formatTimeDiff($latestTime, $now)
    {
        if ($latestTime->diffInSeconds($now) < 60) {
            return $latestTime->diffInSeconds($now) . __('messages.secondBefore');
        } elseif ($latestTime->diffInMinutes($now) < 60) {
            return $latestTime->diffInMinutes($now) . __('messages.minuteBefore');
        } elseif ($latestTime->diffInHours($now) < 24) {
            return $latestTime->diffInHours($now) . __('messages.hourBefore');
        } else {
            return $latestTime->diffInDays($now) . __('messages.dayBefore');
        }
    }
}
