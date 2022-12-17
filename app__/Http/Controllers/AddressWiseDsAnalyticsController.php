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
use App\UserPresentAddress;
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

class AddressWiseDsAnalyticsController extends Controller {

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
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.ADDRESS')
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
            'address_asc' => __('label.ADDRESS') . ' (' . __('label.DESCENDING_ORDER') . ')',
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $dsArr = UserPresentAddress::leftJoin('user_basic_profile', 'user_basic_profile.id', 'user_present_address.user_basic_profile_id')
                    ->leftJoin('users', 'users.id', 'user_basic_profile.user_id')
                    ->leftJoin('rank', 'rank.id', 'users.rank_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
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

            $address = $request->address;
            if (!empty($request->address)) {
                $dsArr = $dsArr->where('user_present_address.address_details', 'LIKE', '%' . $address . '%');
            }


            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'rank.code as rank',  'users.full_name as full_name' , 'users.personal_no as personal_no'
                            , 'users.photo', 'user_basic_profile.id', 'user_present_address.address_details', 'arms_service.code as arms_service_name'
                            , 'appointment.code as appointment_name')
                    ->where('users.status', '1');
 
            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $dsArr = $dsArr->orderBy('users.full_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $dsArr = $dsArr->orderBy('wing.order', 'asc')
                            ->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'address_asc') {
                    $dsArr = $dsArr->orderBy('user_present_address.address_details', 'asc');
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



            $fileName = 'Residence_Address_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('dsAnalytics.addressInfo.print.index')->with(compact('request', 'appointmentList', 'targetArr', 'qpArr', 'sortByList'
                    , 'armsServiceList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.addressInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.addressInfo.print.index', compact('request', 'appointmentList', 'armsServiceList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.addressInfo.index', compact('request', 'nameArr', 'columnArr'
                            , 'appointmentList', 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList'
                            , 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&appt_id=' . $request->appt_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&address=' . $request->address
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('addressWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('addressWiseDsAnalytics?generate=true&' . $url);
    }

}
