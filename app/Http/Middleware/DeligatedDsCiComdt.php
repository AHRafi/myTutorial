<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Common;
use App\DeligateCiAcctToDs;

class DeligatedDsCiComdt {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $dsDeligationList = Common::getDsDeligationList();

        //START:: Access for SuperAdmin
        if (Auth::check()) {
            if (!in_array(Auth::user()->group_id, [2, 3])) {
                if (in_array(Auth::user()->group_id, [4])) {
                    if (!in_array(Auth::user()->id, $dsDeligationList)) {
                        return redirect('dashboard');
                    }
                } else {
                    return redirect('dashboard');
                }
            }
        } else {
            return redirect('/');
        }
        //END:: Access for SuperAdmin

        return $next($request);
    }

}
