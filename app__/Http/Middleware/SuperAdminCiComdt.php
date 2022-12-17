<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class SuperAdminCiComdt {

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
            if (!in_array(Auth::user()->group_id, [1,2,3])) {
                return redirect('dashboard');
            }
        } else {
            return redirect('/');
        }
        //END:: Access for SuperAdmin

        return $next($request);
    }

}