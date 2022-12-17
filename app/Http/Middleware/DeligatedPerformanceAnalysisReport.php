<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Common;
use App\DeligateCiAcctToDs;
use App\DeligateReportsToDs;

class DeligatedPerformanceAnalysisReport {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $dsDeligationList = Common::getDsDeligationList();
        $reportDeligationList = Common::getReportDelegationList();
        $reportDeligationDsList = Common::getReportDelegationDsList();
//        
//        && ()
        //START:: Access for SuperAdmin
        if (Auth::check()) {
            if (!in_array(Auth::user()->group_id, [3])) {
                if (in_array(Auth::user()->group_id, [4])) {
                    if (!in_array(Auth::user()->id, $dsDeligationList)) {
                        if (empty($reportDeligationList) || (!empty($reportDeligationList) && !in_array(4, $reportDeligationList))) {
                            if (empty($reportDeligationDsList) || (!empty($reportDeligationDsList) && !in_array(Auth::user()->id, $reportDeligationDsList))) {
                                return redirect('dashboard');
                            }
                        }
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
