<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\FriendOnline;
use App\Models\User;

class BroadcastFriendOnline
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $friends = User::whereHas('friends', function ($query) use ($user) {
            $query->where('friend_id', $user->id)
                  ->orWhere('user_id', $user->id);
        })->get();

        foreach ($friends as $friend) {
            broadcast(new FriendOnline($friend));
        }
    }
}
