<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Ds {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //START:: Access for SuperAdmin
        if (Auth::check()) {
            if (!in_array(Auth::user()->group_id, [4])) {
                return redirect('dashboard');
            }
        } else {
            return redirect('/');
        }
        //END:: Access for SuperAdmin

        return $next($request);
    }

}
