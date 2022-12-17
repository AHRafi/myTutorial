<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmBank;
use App\Term;
use App\ArmsService;
use App\Wing;
use App\Rank;
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

class BankInfoWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();
        $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];

//        print_r($qpArr);
//        exit;

        $courseList = Course::where('status', '<>', '0')
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('for_course_member', '1')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.BANK_NAME'),
            '4' => __('label.BANK_ACCOUNT_NO'),
            '5' => __('label.BRANCH'),
            '6' => __('label.ONLINE')
        ];

        $nameArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', 'cm_basic_profile.arms_service_id')
                ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                ->where('cm_basic_profile.status', '1')
                ->select('official_name')
                ->orderBy('course.id', 'desc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->get();

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
//            'rank' => __('label.RANK'),
//            'personal_no' => __('label.PERSONAL_NO'),
        ];
        $synList = $targetArr = $subSynList = [];
        $cmArr = $cmIdArr = $cmDetailArr = $targetArr = [];
        if ($request->generate == 'true') {

            $cmBankInfo = CmBank::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_bank.cm_basic_profile_id');
            if (!empty($explodeCourses)) {
                $cmBankInfo = $cmBankInfo->whereIn('cm_basic_profile.course_id', $explodeCourses);
            }
            $cmBankInfo = $cmBankInfo->select('cm_bank.cm_basic_profile_id as cm_id', 'cm_bank.bank_info as bank_info')
                    ->get();

//            echo '<pre>';
//            echo count($cmBankInfo);
//            print_r($cmBankInfo->toArray());
//            exit;

            if (!empty($cmBankInfo)) {
                foreach ($cmBankInfo as $bankInfo) {
                    $cmId = $bankInfo->cm_id;
                    $bankInfoArr = !empty($bankInfo->bank_info) ? json_decode($bankInfo->bank_info, true) : [];

                    if (!empty($bankInfoArr)) {
                        foreach ($bankInfoArr as $rsKey => $rsInfo) {
                            if (!empty($request->bank)) {
                                if (!empty($request->branch)) {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && preg_match("/" . $request->branch . "/i", $rsInfo['branch']) && $request->online_check == $rsInfo['is_online']) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && preg_match("/" . $request->branch . "/i", $rsInfo['branch'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->is_online)) {
                                        if (preg_match("/" . $request->bank . "/i", $rsInfo['name']) && $request->online_check == $rsInfo['is_online']) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                        }
                                    } else {
                                        if (preg_match("/" . $request->bank . "/i", $rsInfo['name'])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                        }
                                    }
                                }
                            } else {
                                if (!empty($request->branch)) {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if (preg_match("/" . $request->branch . "/i", $rsInfo['branch']) && $request->online_check == $rsInfo['is_online']) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['branch'])) {
                                            if (preg_match("/" . $request->branch . "/i", $rsInfo['branch'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->online_check)) {
                                        if (!empty($rsInfo['is_online'])) {
                                            if ($request->online_check == $rsInfo['is_online']) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                                            }
                                        }
                                    }
                                }
                            }
                            if (empty($request->bank) && empty($request->branch) && empty($request->online_check)) {
                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo);
                            }
                        }
                    }
                }
            }
            
            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                    ->where('cm_basic_profile.status', '1');

            if (!empty($explodeCourses)) {
                $cmArr = $cmArr->where('cm_basic_profile.course_id', $explodeCourses);
            }
            if (!empty($cmIdArr)) {
                $cmArr = $cmArr->whereIn('cm_basic_profile.id', $cmIdArr);
            }

            $name = $request->name;
            if (!empty($request->name)) {
                $cmArr->where(function($query) use ($name) {
                    $query->where('cm_basic_profile.full_name', 'LIKE', '%' . $name . '%')
                            ->orWhere('cm_basic_profile.official_name', 'LIKE', '%' . $name . '%');
                });
            }

            if (!empty($request->rank_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.rank_id', $request->rank_id);
            }

            if (!empty($request->wing_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.wing_id', $request->wing_id);
            }

            if (!empty($request->arms_service_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.arms_service_id', $request->arms_service_id);
            }

            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                            , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'course.name as course_name', 'rank.code as rank', 'arms_service.code as arms_service_name', 'cm_basic_profile.id')
                    ->orderBy('course.id', 'desc');
            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('cm_basic_profile.official_name', 'asc');
                }elseif ($request->sort == 'rank') {
                    $cmArr = $cmArr->orderBy('rank.order', 'asc');
                }elseif ($request->sort == 'personal_no') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.personal_no', 'asc');
                } 
            } else {
                $cmArr = $cmArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }
            $cmArr = $cmArr->get();


            if (!$cmArr->isEmpty()) {
                foreach ($cmArr as $cmInfo) {
                    $targetArr[$cmInfo->id] = $cmInfo->toArray();
                    if (!empty($cmDetailArr[$cmInfo->id])) {
                        $targetArr[$cmInfo->id]['rec_svc'] = $cmDetailArr[$cmInfo->id];
                        $targetArr[$cmInfo->id]['rec_svc_span'] = sizeof($cmDetailArr[$cmInfo->id]);
                    }
                }
            }
            
            $fileName = 'Bank_Info_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('cmAnalytics.bankInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.bankInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.bankInfo.print.index', compact('request', 'courseList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.bankInfo.index', compact('request', 'courseList', 'nameArr', 'cmDetailArr','printOptionList', 'columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id 
                . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id 
                . '&bank=' . $request->bank . '&branch=' . $request->branch . '&online_check=' . $request->online_check
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('bankInfoWiseCmAnalytics?generate=true&' . $url);
    }

    public function pushBankInfo($rsInfo) {
        $cmDetailArr['name'] = $rsInfo['name'] ?? '';
        $cmDetailArr['branch'] = $rsInfo['branch'] ?? '';
        $cmDetailArr['account'] = $rsInfo['account'] ?? '';
        $cmDetailArr['is_online'] = $rsInfo['is_online'] ?? '';


        return $cmDetailArr;
    }

}
