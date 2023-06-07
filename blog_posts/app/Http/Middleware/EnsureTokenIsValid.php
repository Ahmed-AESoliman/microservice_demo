<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->userService->get('user');
        if(!empty($user)){
            return $next($request);
        }
        return abort(403);
    }
}
