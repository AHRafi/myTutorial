<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use PDF;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CiDsProfileReportController extends Controller {

    private $controller = 'CiDsProfileReport';

    public function index(Request $request) {
        
        $qpArr = $request->all();

        $userArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->leftjoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->whereIn('users.group_id', ['3','4'])
                ->select('users.personal_no', 'users.full_name', 'users.official_name', 'appointment.code as appointment'
                        , 'users.photo', 'rank.code as rank', 'users.email', 'users.id','users.join_date'
                        , 'users.phone', 'commissioning_course.name as comm_course_name','arms_service.code as arms_service_name')
                ->orderBy('users.status', 'asc')
                ->orderBy('users.group_id', 'asc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();
        
        
        $targetArr=[];
        if (!$userArr->isEmpty()) {
            foreach ($userArr as $userInfo) {
                $targetArr[$userInfo->id] = $userInfo->toArray();
            }
        }

        $fileName = 'Ci_Ds_Profile';
        $fileName = Common::getFileFormatedName($fileName);



        if ($request->view == 'print') {
            return view('report.ciDsProfile.print.index')->with(compact('request', 'targetArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('report.ciDsProfile.print.index', compact('request', 'targetArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('report.ciDsProfile.print.index', compact('request', 'targetArr')), $fileName . '.xlsx');
        } else {

            return view('report.ciDsProfile.index', compact('request', 'targetArr'));
        }
    }

    public function profile(Request $request, $id) {
        $loadView = 'report.ciDsProfile.profile';
        $prinLloadView = 'report.ciDsProfile.print.profile';
        return Common::getCiDsProfile($request, $id, $loadView, $prinLloadView);
    }

}
