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
use Helper;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MaritalInfoWiseDsAnalyticsController extends Controller {

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
        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $spouseProf = ['0' => __('label.SELECT_SPOUSE_PROFESSION')] + Common::getSpouseProfessionList();
        
        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.DOB_SELF'),
            '4' => __('label.SPOUSE_BIRTH_DATE'),
            '5' => __('label.MARRIAGE_DATE'),
            '6' => __('label.SPOUSE_PROFESSION')
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
            'marriage_date_des' => __('label.MARRIAGE_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'marriage_date_asc' => __('label.MARRIAGE_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'spouse_dob_desc' => __('label.SPOUSE_BIRTH_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'spouse_dob_asc' => __('label.SPOUSE_BIRTH_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dob_asc' => __('label.DOB_SELF') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dob_desc' => __('label.DOB_SELF') . ' (' . __('label.DESCENDING_ORDER') . ')',
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


            if (!empty($request->marital_status)) {
                $dsArr = $dsArr->where('user_basic_profile.marital_status', $request->marital_status);
            }
            if (!empty($request->spouse_profession)) {
                $dsArr = $dsArr->where('user_basic_profile.spouse_occupation', $request->spouse_profession);
            }


            $marriageDateFrom = !empty($request->marriage_date_from) ? date("Y-m-d", strtotime($request->marriage_date_from)) : '';
            $marriageDateTo = !empty($request->marriage_date_to) ? date("Y-m-d", strtotime($request->marriage_date_to)) : '';
            if (!empty($marriageDateFrom) && !empty($marriageDateTo)) {
                $dsArr = $dsArr->whereBetween('user_basic_profile.date_of_marriage', [$marriageDateFrom, $marriageDateTo]);
            } else {

                if (!empty($marriageDateFrom)) {
                    $dsArr = $dsArr->where('user_basic_profile.date_of_marriage', '>=', $marriageDateFrom);
                }
                if (!empty($marriageDateTo)) {
                    $dsArr = $dsArr->where('user_basic_profile.date_of_marriage', '<=', $marriageDateTo);
                }
            }

            $spouseBirthDateFrom = !empty($request->spouse_birth_date_from) ? date("Y-m-d", strtotime($request->spouse_birth_date_from)) : '';
            $spouseBirthDateTo = !empty($request->spouse_birth_date_to) ? date("Y-m-d", strtotime($request->spouse_birth_date_to)) : '';
            if (!empty($spouseBirthDateFrom) && !empty($spouseBirthDateTo)) {
                $dsArr = $dsArr->whereBetween('user_basic_profile.spouse_dob', [$spouseBirthDateFrom, $spouseBirthDateTo]);
            } else {

                if (!empty($spouseBirthDateFrom)) {
                    $dsArr = $dsArr->where('user_basic_profile.spouse_dob', '>=', $spouseBirthDateFrom);
                }
                if (!empty($spouseBirthDateTo)) {
                    $dsArr = $dsArr->where('user_basic_profile.spouse_dob', '<=', $spouseBirthDateTo);
                }
            }







            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'users.photo', 'rank.code as rank', 'user_basic_profile.date_of_marriage', 'user_basic_profile.spouse_dob', 'user_basic_profile.id', 'appointment.code as appointment_name'
                            , 'user_basic_profile.spouse_occupation', 'user_basic_profile.date_of_birth', 'arms_service.code as arms_service_name', 'user_basic_profile.marital_status')
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
                } elseif ($request->sort == 'marriage_date_des') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.date_of_marriage', 'desc');
                } elseif ($request->sort == 'spouse_dob_desc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.spouse_dob', 'desc');
                } elseif ($request->sort == 'spouse_dob_asc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.spouse_dob', 'asc');
                } elseif ($request->sort == 'marriage_date_asc') {
                    $dsArr = $dsArr->orderBy('user_basic_profile.date_of_marriage', 'asc');
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

            $fileName = 'Marital_Info_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('dsAnalytics.maritalInfo.print.index')->with(compact('request', 'appointmentList', 'targetArr', 'qpArr', 'sortByList', 'spouseProf', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.maritalInfo.print.index', compact('request', 'activeTrainingYearList', 'appointmentList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.maritalInfo.print.index', compact('request', 'spouseProf', 'appointmentList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.maritalInfo.index', compact('request', 'appointmentList', 'nameArr', 'maritalStatusList', 'spouseProf','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&appt_id=' . $request->appt_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&marital_status=' . $request->marital_status . '&marriage_date_from=' . $request->marriage_date_from . '&marriage_date_to=' . $request->marriage_date_to . '&spouse_profession=' . $request->spouse_profession . '&spouse_birth_date_from=' . $request->spouse_birth_date_from . '&spouse_birth_date_to=' . $request->spouse_birth_date_to
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('maritalInfoWiseDsAnalytics?generate=true&' . $url);
    }

}
