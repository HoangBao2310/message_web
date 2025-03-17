<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use App\Events\FriendOffline;
use App\Models\User;

class UpdateLastSeenOnLogout
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
    public function handle(Logout $event)
    {
        $user = $event->user;

        // Kiểm tra nếu $user là một đối tượng của lớp User
        if ($user instanceof User) {
            $user->last_seen = now();
            $user->save();

            // Phát sự kiện thông báo bạn bè rằng người dùng đã ngoại tuyến
            broadcast(new FriendOffline($user))->toOthers();
        }
    }
}