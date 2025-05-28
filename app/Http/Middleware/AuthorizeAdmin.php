<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class AuthorizeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('login')) {
            return $next($request);
        }


        if (Auth::check()) {
            if (Auth::user()->role === 'client') {
                return redirect('/new-reservations');
            } elseif (Auth::user()->role === 'admin') {
                return $next($request);
            } 
        }
     
        return redirect('/login')->with('message', 'Please log in to access this page.'); 
    }
}