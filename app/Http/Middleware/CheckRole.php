<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserAuth;
use App\Models\UserRole;
use App\Services\APIService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function __construct(protected APIService $apiService)
    {
    }


    public function handle(Request $request, Closure $next, string $role): Response
    {
        $authUser = User::where('id', $request->user()->id)->first();

        if (!$authUser) {
            return $this->apiService->sendNotFound('User not found!');
        }

        $existingRole = UserRole::where('id', $authUser->user_role_id)->first();

        if (!$existingRole || $existingRole->role_name !== $role) {
            return $this->apiService->sendForbidden('You are not allowed to access this page!');
        }

        return $next($request);
    }
}
