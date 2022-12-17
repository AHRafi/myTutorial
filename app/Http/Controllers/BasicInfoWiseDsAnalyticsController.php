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

class BasicInfoWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {

        $qpArr = $request->all();
        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('responsibility', '3')->where('status', '1')->pluck('code', 'id')->toArray();
        $bloodGroupList = ['0' => __('label.SELECT_BLOOD_GROUP')] + Common::getBloodGroup();
        $genderList = ['0' => __('label.SELECT_GENDER')] + Common::getGenderList();
        $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();

        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
         $columnArr = [
            '9' => __('label.FULL_NAME_BANGLA'),
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.EMAIL'),
            '4' => __('label.MOBILE'),
            '5' => __('label.BlOOD_GROUP'),
            '6' => __('label.DATE_OF_BIRTH'),
            '7' => __('label.RELIGION'),
            '8' => __('label.GENDER'),
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
        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'dob_desc' => __('label.DATE_OF_BIRTH') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'dob_asc' => __('label.DATE_OF_BIRTH') . ' (' . __('label.ASCENDING_ORDER') . ')',
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
            if (!empty($request->appt_id)) {
                $dsArr = $dsArr->where('appointment.id', $request->appt_id);
            }


            if (!empty($request->blood_group)) {
                $dsArr = $dsArr->where('user_basic_profile.blood_group', $request->blood_group);
            }
            if (!empty($request->religion)) {
                $dsArr = $dsArr->where('user_basic_profile.religion_id', $request->religion);
            }
            if (!empty($request->gender)) {
                $dsArr = $dsArr->where('user_basic_profile.gender', $request->gender);
            }


            $birthDateFrom = !empty($request->birth_date_from) ? date("Y-m-d", strtotime($request->birth_date_from)) : '';
            $birthDateTo = !empty($request->birth_date_to) ? date("Y-m-d", strtotime($request->birth_date_to)) : '';
            if (!empty($birthDateFrom) && !empty($birthDateTo)) {
                $dsArr = $dsArr->whereBetween('user_basic_profile.date_of_birth', [$birthDateFrom, $birthDateTo]);
            } else {

                if (!empty($birthDateFrom)) {
                    $dsArr = $dsArr->where('user_basic_profile.date_of_birth', '>=', $birthDateFrom);
                }
                if (!empty($birthDateTo)) {
                    $dsArr = $dsArr->where('user_basic_profile.date_of_birth', '<=', $birthDateTo);
                }
            }


            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'users.photo', 'rank.code as rank', 'users.email', 'user_basic_profile.blood_group', 'user_basic_profile.id', 'appointment.code as appointment_name'
                            , 'users.phone', 'user_basic_profile.date_of_birth', 'user_basic_profile.bn_name', 'arms_service.code as arms_service_name', 'user_basic_profile.religion_id', 'user_basic_profile.gender')
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
                } elseif ($request->sort == 'dob_desc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.date_of_birth', 'desc');
                } elseif ($request->sort == 'dob_asc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.date_of_birth', 'asc');
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
//    echo '<pre>';    count($targetArr); print_r($targetArr); exit;

            $fileName = 'Basic_Info_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('dsAnalytics.basicInfo.print.index')->with(compact('request', 'appointmentList', 'bloodGroupList', 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.basicInfo.print.index', compact('request', 'activeTrainingYearList', 'appointmentList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.basicInfo.print.index', compact('request', 'appointmentList', 'bloodGroupList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.basicInfo.index', compact('request', 'appointmentList', 'nameArr','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'bloodGroupList', 'religionList', 'genderList', 'printOptionList'));
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
        return redirect('basicInfoWiseDsAnalytics?generate=true&' . $url);
    }

}
