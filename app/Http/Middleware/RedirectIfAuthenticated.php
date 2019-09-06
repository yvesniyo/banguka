<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // return redirect('/home');
            if (auth()->user()->level == "admin") {
                return redirect('/admin');
            }else if(auth()->user()->level == "parkingAdmin"){
                return redirect("/parkingAdmin");
            }else if(auth()->user()->level == "parkingAgent"){
                return redirect("/parkingAgent");
            }else{
                return redirect("/uknownUserType/");
            }
        }

        return $next($request);
    }
}
