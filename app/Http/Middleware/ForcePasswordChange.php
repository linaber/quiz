<?php

namespace App\Http\Middleware;

use App\Filament\Pages\ChangePassword;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    public function handle(Request $request, Closure $next): Response
    {

        $isLogoutRequest = $request->routeIs('logout') || $request->path() === 'admin/logout';
        if (
            auth()->check() &&
            auth()->user()->shouldChangePassword() &&
            $request->path() !== "admin/change-password" &&
            !$isLogoutRequest
        ) {


            return redirect()->to(ChangePassword::getUrl());
        }

        return $next($request);
    }
}
