<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmPassport;
use App\UserPassport;
use App\UserBank;
use App\UserServiceRecord;
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

class RecSvcWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year
        $qpArr = $request->all();
        $courseList = Course::where('status', '1')->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();

        $svcResposibilityList = ['0' => __('label.SELECT_SVC_RES')] + Common::getSvcResposibilityList();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('responsibility', '3')->where('status', '1')->pluck('code', 'id')->toArray();
        
        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.UNIT_FMN_INST'),
            '4' => __('label.RESPONSIBILITY'),
            '5' => __('label.APPT'),
            '6' => __('label.FROM'),
            '7' => __('label.TO')
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


            $dsRecSvcInfo = UserServiceRecord::join('user_basic_profile', 'user_basic_profile.id', 'user_service_record.user_basic_profile_id');

            $dsRecSvcInfo = $dsRecSvcInfo->select('user_service_record.user_basic_profile_id as ds_id', 'user_service_record.service_record_info as rec_svc_info')
                    ->get();


            if (!empty($dsRecSvcInfo)) {
                foreach ($dsRecSvcInfo as $recSvcInfo) {
                    $dsId = $recSvcInfo->ds_id;
                    $recSvcInfoArr = !empty($recSvcInfo->rec_svc_info) ? json_decode($recSvcInfo->rec_svc_info, true) : [];

                    if (!empty($recSvcInfoArr)) {
                        foreach ($recSvcInfoArr as $rsKey => $rsInfo) {
                            if (!empty($request->unit)) {
                                if (!empty($request->responsibility_id)) {
                                    if (!empty($request->appt)) {
                                        if (!empty($rsInfo['resp'])) {
                                            if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && !empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['resp'])) {
                                            if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && !empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->appt)) {
                                        if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    } else {
                                        if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst'])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    }
                                }
                            } else {
                                if (!empty($request->responsibility_id)) {
                                    if (!empty($request->appt)) {
                                        if (!empty($rsInfo['resp'])) {
                                            if (!empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['resp'])) {
                                            if (!empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp'])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->appt)) {
                                        if (preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    }
                                }
                            }

                            if (empty($request->unit) && empty($request->responsibility_id) && empty($request->appt)) {
                                $dsDetailArr[$dsId][$rsKey] = $this->pushSvcRec($rsInfo);
                            }
                        }
                    }
                }
            }

//            echo "<pre>";
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

//             echo '<pre>';
//            echo count($targetArr);
//            print_r($targetArr);
//            exit;
//            
            $fileName = 'Record_of_Service_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }




        if ($request->view == 'print') {
            return view('dsAnalytics.recSvcInfo.print.index')->with(compact('request', 'svcResposibilityList', 'targetArr', 'qpArr', 'sortByList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.recSvcInfo.print.index', compact('request', 'svcResposibilityList', 'activeTrainingYearList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.recSvcInfo.print.index', compact('request', 'svcResposibilityList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.recSvcInfo.index', compact('request', 'svcResposibilityList', 'dsDetailArr', 'nameArr', 'appointmentList','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    function filter(Request $request) {
        $rules = $messages = [];
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&appt_id=' . $request->appt_id 
                . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id 
                . '&unit=' . urlencode($request->unit) . '&responsibility_id=' . $request->responsibility_id . '&appt=' . urlencode($request->appt)
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('recSvcWiseDsAnalytics?generate=true&' . $url);
    }

    public function pushSvcRec($rsInfo) {
        $dsDetailArr['from'] = $rsInfo['from'] ?? '';
        $dsDetailArr['to'] = !empty($rsInfo['to']) ? $rsInfo['to'] : (!empty($rsInfo['year']) ? $rsInfo['year'] : '');
        $dsDetailArr['unit'] = $rsInfo['unit_fmn_inst'] ?? '';
        $dsDetailArr['responsibility'] = $rsInfo['resp'] ?? '';
        $dsDetailArr['appt'] = $rsInfo['appointment'] ?? '';

        return $dsDetailArr;
    }

}
