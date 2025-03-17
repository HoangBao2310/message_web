<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;

class UpdateLastSeen
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $this->userService->updateLastSeen($user);
            }
        }

        return $next($request);
    }
}