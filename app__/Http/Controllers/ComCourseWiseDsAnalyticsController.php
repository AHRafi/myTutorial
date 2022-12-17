<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmPassport;
use App\UserPassport;
use App\Term;
use App\CommissioningCourse;
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

class ComCourseWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();

        $commissionTypeList = array('0' => __('label.SELECT_COMMISSIONING_TYPE')) + Common::getCommissionType();
        $comCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE')) + CommissioningCourse::where('status', '1')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();


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
            '3' => __('label.JOINING_DATE'),
            '4' => __('label.COMMISSIONING_COURSE'),
            '5' => __('label.COMMISSIONING_DATE'),
            '6' => __('label.COMMISSIONING_TYPE')
        ];

        $nameArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->select('users.full_name')
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->get();


        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'seniority' => __('label.SENIORITY') ,
            'joining_asc' => __('label.JOINING_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'joining_desc' => __('label.JOINING_DATE') . ' (' . __('label.ASCENDING_ORDER') . ' )',
            'com_desc' => __('label.COMMISSIONING_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'com_asc' => __('label.COMMISSIONING_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $dsArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                    ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id');

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
            if (!empty($request->com_course_id)) {
                $dsArr = $dsArr->where('user_basic_profile.commissioning_course_id', $request->com_course_id);
            }
            if (!empty($request->com_type_id)) {
                $dsArr = $dsArr->where('user_basic_profile.commission_type', $request->com_type_id);
            }
            if (!empty($request->appt_id)) {
                $dsArr = $dsArr->where('appointment.id', $request->appt_id);
            }


            $commissionDateFrom = !empty($request->commissioning_date_from) ? date("Y-m-d", strtotime($request->commissioning_date_from)) : '';
            $commissionDateTo = !empty($request->commissioning_date_to) ? date("Y-m-d", strtotime($request->commissioning_date_to)) : '';
            if (!empty($commissionDateFrom) && !empty($commissionDateTo)) {
                $dsArr = $dsArr->whereBetween('user_basic_profile.commisioning_date', [$commissionDateFrom, $commissionDateTo]);
            } else {

                if (!empty($commissionDateFrom)) {
                    $dsArr = $dsArr->where('user_basic_profile.commisioning_date', '>=', $commissionDateFrom);
                }
                if (!empty($commissionDateTo)) {
                    $dsArr = $dsArr->where('user_basic_profile.commisioning_date', '<=', $commissionDateTo);
                }
            }

            $joiningDateFrom = !empty($request->joining_date_from) ? date("Y-m-d", strtotime($request->joining_date_from)) : '';
            $joiningDateTo = !empty($request->joining_date_to) ? date("Y-m-d", strtotime($request->joining_date_to)) : '';
            if (!empty($joiningDateFrom) && !empty($joiningDateTo)) {
                $dsArr = $dsArr->whereBetween('users.join_date', [$joiningDateFrom, $joiningDateTo]);
            } else {

                if (!empty($joiningDateFrom)) {
                    $dsArr = $dsArr->where('users.join_date', '>=', $joiningDateFrom);
                }
                if (!empty($joiningDateTo)) {
                    $dsArr = $dsArr->where('users.join_date', '<=', $joiningDateTo);
                }
            }


            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name', 'users.join_date'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'users.photo', 'rank.code as rank', 'user_basic_profile.commissioning_course_id', 'user_basic_profile.id', 'appointment.code as appointment_name'
                            , 'user_basic_profile.commisioning_date', 'user_basic_profile.commission_type', 'arms_service.code as arms_service_name')
                    ->where('users.status', '1');

//          
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
                } elseif ($request->sort == 'joining_desc') {
                    $dsArr = $dsArr->orderBy('users.join_date', 'desc')
                            ->orderBy('rank.order', 'desc')
                            ->orderBy('users.personal_no', 'desc');
                } elseif ($request->sort == 'joining_asc') {
                    $dsArr = $dsArr->orderBy('users.join_date', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'seniority') {
                    $dsArr = $dsArr->orderBy('rank.svc_order', 'asc')
                            ->orderBy('user_basic_profile.commisioning_date', 'asc')
                            ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'com_desc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.commisioning_date', 'desc');
                } elseif ($request->sort == 'com_asc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.commisioning_date', 'asc');
                }
            } else {
                $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc');
            }

            $dsArr = $dsArr->get();



            if (!$dsArr->isEmpty()) {
                foreach ($dsArr as $cmInfo) {
                    $targetArr[$cmInfo->id] = $cmInfo->toArray();
                }
            }



            $fileName = 'Commissioning_Course_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('dsAnalytics.comCourseInfo.print.index')->with(compact('request', 'comCourseList', 'commissionTypeList', 'targetArr', 'qpArr', 'sortByList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.comCourseInfo.print.index', compact('request', 'activeTrainingYearList', 'termList', 'comCourseList', 'commissionTypeList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.comCourseInfo.print.index', compact('request', 'comCourseList', 'commissionTypeList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.comCourseInfo.index', compact('request', 'nameArr', 'comCourseList', 'commissionTypeList', 'appointmentList','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&appt_id=' . $request->appt_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&com_course_id=' . $request->com_course_id . '&commissioning_date_from=' . $request->commissioning_date_from . '&commissioning_date_to=' . $request->commissioning_date_to . '&joining_date_from=' . $request->joining_date_from .
                '&joining_date_to=' . $request->joining_date_to . '&com_type_id=' . $request->com_type_id
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('comCourseWiseDsAnalytics?generate=true&' . $url);
    }

}
