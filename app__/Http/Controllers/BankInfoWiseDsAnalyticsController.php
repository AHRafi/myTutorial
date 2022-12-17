<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmPassport;
use App\UserPassport;
use App\UserBank;
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

class BankInfoWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();


//        print_r($qpArr);
//        exit;

        $courseList = Course::where('status', '1')
                ->where('status', '<>', '0')
                ->orderBy('training_year_id', 'desc')
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
            '3' => __('label.BANK_NAME'),
            '4' => __('label.BANK_ACCOUNT_NO'),
            '5' => __('label.BRANCH'),
            '6' => __('label.ONLINE')
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
        ];
        $synList = $targetArr = $subSynList = [];
        $dsArr = $dsIdArr = $dsDetailArr = $targetArr = [];


        if ($request->generate == 'true') {

            $dsBankInfo = UserBank::join('user_basic_profile', 'user_basic_profile.id', 'user_bank.user_basic_profile_id');

            $dsBankInfo = $dsBankInfo->select('user_bank.user_basic_profile_id as ds_id', 'user_bank.bank_info as bank_info')
                    ->get();

//            echo '<pre>';
//            echo count($dsBankInfo);
//            print_r($dsBankInfo->toArray());
//            exit;

            if (!empty($dsBankInfo)) {
                foreach ($dsBankInfo as $bankInfo) {
                    $dsId = $bankInfo->ds_id;
                    $bankInfoArr = !empty($bankInfo->bank_info) ? json_decode($bankInfo->bank_info, true) : [];

                    if (!empty($bankInfoArr)) {
                        foreach ($bankInfoArr as $rsKey => $rsInfo) {
                            if (!empty($request->bank)) {
                                if (!empty($request->branch)) {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && preg_match("/" . $request->branch . "/i", $rsInfo['branch']) && $request->online_check == $rsInfo['is_online']) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && preg_match("/" . $request->branch . "/i", $rsInfo['branch'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->is_online)) {
                                        if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && $request->online_check == $rsInfo['is_online']) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                        }
                                    } else {
                                        if (preg_match("/" . $request->bank . "/i", $rsInfo['name'])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                        }
                                    }
                                }
                            } else {
                                if (!empty($request->branch)) {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->branch . "/i", $rsInfo['branch']) && $request->online_check == $rsInfo['is_online']) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['branch'])) {
                                            if (preg_match("/" . $request->branch . "/i", $rsInfo['branch'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if ($request->online_check == $rsInfo['is_online']) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                }
                            }
                            if (empty($request->bank) && empty($request->branch) && empty($request->online_check)) {
                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo);
                            }
                        }
                    }
                }
            }


//            echo '<pre>';
//            echo count($dsDetailArr);
//            print_r($dsDetailArr);
//            exit;

            $dsArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                    ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id');

            if (!empty($dsIdArr)) {
                $dsArr = $dsArr->whereIn('user_basic_profile.id', $dsIdArr);
            }

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

            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name', 'appointment.code as appointment_name'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'rank.code as rank',  'users.full_name as full_name' , 'users.personal_no as personal_no'
                            , 'users.photo', 'rank.code as rank', 'arms_service.code as arms_service_name', 'user_basic_profile.id')
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
                }
            } else {
                $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc');
            }
            $dsArr = $dsArr->get();

//            echo '<pre>';
//            echo count($dsArr->toArray());
//            print_r($dsArr->toArray());
//            exit;


            if (!$dsArr->isEmpty()) {
                foreach ($dsArr as $dsInfo) {
                    $targetArr[$dsInfo->id] = $dsInfo->toArray();
                    if (!empty($dsDetailArr[$dsInfo->id])) {
                        $targetArr[$dsInfo->id]['rec_svc'] = $dsDetailArr[$dsInfo->id];
                        $targetArr[$dsInfo->id]['rec_svc_span'] = sizeof($dsDetailArr[$dsInfo->id]);
                    }
                }
            }

//            echo '<pre>';
//            echo count($targetArr);
//            print_r($targetArr);
//            exit;

            $fileName = 'Bank_Info_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('dsAnalytics.bankInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.bankInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.bankInfo.print.index', compact('request', 'courseList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.bankInfo.index', compact('request', 'nameArr', 'dsDetailArr', 'appointmentList','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&rank_id=' . $request->rank_id
                . '&appt_id=' . $request->appt_id . '&arms_service_id=' . $request->arms_service_id
                . '&bank=' . $request->bank . '&branch=' . $request->branch . '&online_check=' . $request->online_check
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('bankInfoWiseDsAnalytics?generate=true&' . $url);
    }

    public function pushBankInfo($rsInfo) {
        $dsDetailArr['name'] = $rsInfo['name'] ?? '';
        $dsDetailArr['branch'] = $rsInfo['branch'] ?? '';
        $dsDetailArr['account'] = $rsInfo['account'] ?? '';
        $dsDetailArr['is_online'] = $rsInfo['is_online'] ?? '';


        return $dsDetailArr;
    }

}
