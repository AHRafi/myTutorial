<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmPassport;
use App\UserPassport;
use App\Term;
use App\ArmsService;
use App\Appointment;
use App\Wing;
use App\Rank;
use App\User;
use App\UserBasicProfile;
use App\Religion;
use Response;
use PDF;
use Auth;
use File;
use DB;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class PassportInfoWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {

        $qpArr = $request->all();
        $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];


        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('responsibility', '3')->where('status', '1')->pluck('code', 'id')->toArray();

        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.PASSPORT_NO'),
            '4' => __('label.PLACE_OF_ISSUE'),
            '5' => __('label.DATE_OF_ISSUE'),
            '6' => __('label.DATE_OF_EXPIRY'),
            '7' => __('label.PHOTO_WITHOUT_UNIFORM')
        ];

        $nameArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select('users.full_name')
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->get();

//        echo "<pre>";
//        print_r($nameArr->toArray());
//        exit;
//        

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'issue_date_desc' => __('label.DATE_OF_ISSUE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'issue_date_asc' => __('label.DATE_OF_ISSUE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'expire_date_desc' => __('label.DATE_OF_EXPIRE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'expire_date_asc' => __('label.DATE_OF_EXPIRE') . ' (' . __('label.ASCENDING_ORDER') . ')',
        ];
        $synList = $targetArr = $subSynList = [];

        if ($request->generate == 'true') {
            $dsArr = UserPassport::leftJoin('user_basic_profile', 'user_basic_profile.id', '=', 'user_passport_details.user_basic_profile_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                    ->leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                    ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id');

            $name = $request->name;
            if (!empty($request->name)) {
                $dsArr->where(function($query) use ($name) {
                    $query->where('users.full_name', 'LIKE', '%' . $name . '%')
                            ->orWhere('users.official_name', 'LIKE', '%' . $name . '%');
                });
            }

            if (!empty($request->rank_id)) {
                $dsArr = $dsArr->where('users.rank_id', $request->rank_id);
            }
            if (!empty($request->wing_id)) {
                $dsArr = $dsArr->where('users.wing_id', $request->wing_id);
            }
            if (!empty($request->arms_service_id)) {
                $dsArr = $dsArr->where('user_basic_profile.arms_service_id', $request->arms_service_id);
            }
            if (!empty($request->appt_id)) {
                $dsArr = $dsArr->where('appointment.id', $request->appt_id);
            }

            $issueFrom = !empty($request->issue_from) ? date("Y-m-d", strtotime($request->issue_from)) : '';
            $issueTo = !empty($request->issue_to) ? date("Y-m-d", strtotime($request->issue_to)) : '';
            if (!empty($issueFrom) && !empty($issueTo)) {
                $dsArr = $dsArr->whereBetween('user_passport_details.date_of_issue', [$issueFrom, $issueTo]);
            } else {

                if (!empty($issueFrom)) {
                    $dsArr = $dsArr->where('user_passport_details.date_of_issue', '>=', $issueFrom);
                }
                if (!empty($issueTo)) {
                    $dsArr = $dsArr->where('user_passport_details.date_of_issue', '<=', $issueTo);
                }
            }

            $expireFrom = !empty($request->expire_from) ? date("Y-m-d", strtotime($request->expire_from)) : '';
            $expireTo = !empty($request->expire_to) ? date("Y-m-d", strtotime($request->expire_to)) : '';
            if (!empty($expireFrom) && !empty($expireTo)) {
                $dsArr = $dsArr->whereBetween('user_passport_details.date_of_expire', [$expireFrom, $expireTo]);
            } else {

                if (!empty($expireFrom)) {
                    $dsArr = $dsArr->where('user_passport_details.date_of_expire', '>=', $expireFrom);
                }
                if (!empty($expireTo)) {
                    $dsArr = $dsArr->where('user_passport_details.date_of_expire', '<=', $expireTo);
                }
            }

            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name', 'user_passport_details.pass_scan_copy'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name"), 'users.photo'
                            , 'user_passport_details.passport_no', 'user_passport_details.date_of_issue', 'user_passport_details.date_of_expire'
                            , 'user_passport_details.photo_without_uniform', 'users.id'
                            , 'user_passport_details.place_of_issue', 'arms_service.code as arms_service_name', 'appointment.code as appointment_name')
                    ->where('users.status', '1');


            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $dsArr = $dsArr->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $dsArr = $dsArr->orderBy('wing.order', 'asc')
                            ->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'issue_date_desc') {
                    $dsArr = $dsArr->orderBy('user_passport_details.date_of_issue', 'desc');
                } elseif ($request->sort == 'issue_date_asc') {
                    $dsArr = $dsArr->orderBy('user_passport_details.date_of_issue', 'asc');
                } elseif ($request->sort == 'expire_date_desc') {
                    $dsArr = $dsArr->orderBy('user_passport_details.date_of_expire', 'desc');
                } elseif ($request->sort == 'expire_date_asc') {
                    $dsArr = $dsArr->orderBy('user_passport_details.date_of_expire', 'asc');
                }
            } else {
                $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc');
            }

            $dsArr = $dsArr->get();



            if (!$dsArr->isEmpty()) {
                foreach ($dsArr as $dsInfo) {
                    $targetArr[$dsInfo->id] = $dsInfo->toArray();
                }
            }




            $fileName = 'Passport_Info_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }





        if ($request->view == 'print') {
            return view('dsAnalytics.passInfo.print.index')->with(compact('request', 'appointmentList', 'targetArr', 'qpArr', 'sortByList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.passInfo.print.index', compact('request', 'activeTrainingYearList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.passInfo.print.index', compact('request', 'appointmentList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.passInfo.index', compact('request', 'appointmentList', 'nameArr' ,'columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&appt_id=' . $request->appt_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&blood_group=' . $request->blood_group . '&birth_date_from=' . $request->birth_date_from . '&birth_date_to=' . $request->birth_date_to . '&religion=' . $request->religion . '&gender=' . $request->gender
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('passportInfoWiseDsAnalytics?generate=true&' . $url);
    }

}
