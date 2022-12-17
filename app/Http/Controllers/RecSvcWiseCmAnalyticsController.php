<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmOthers;
use App\CmServiceRecord;
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

class RecSvcWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year
        $qpArr = $request->all();
        $courseList = Course::where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('for_course_member', '1')
                        ->where('status', '1')->pluck('code', 'id')->toArray();

        $svcResposibilityList = ['0' => __('label.SELECT_SVC_RES')] + Common::getSvcResposibilityList();

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
        
        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.UNIT_FMN_INST'),
            '4' => __('label.RESPONSIBILITY'),
            '5' => __('label.APPT'),
            '6' => __('label.FROM'),
            '7' => __('label.TO')
        ];


        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
        ];
        $synList = $targetArr = $subSynList = [];
        $cmArr = $cmIdArr = $cmDetailArr = $targetArr = [];

        if ($request->generate == 'true') {
            $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : 0;

            $cmRecSvcInfo = CmServiceRecord::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_service_record.cm_basic_profile_id');
            if (!empty($explodeCourses)) {
                $cmRecSvcInfo = $cmRecSvcInfo->whereIn('cm_basic_profile.course_id', $explodeCourses);
            }
            $cmRecSvcInfo = $cmRecSvcInfo->select('cm_service_record.cm_basic_profile_id as cm_id', 'cm_service_record.service_record_info as rec_svc_info')
                    ->get();


            if (!empty($cmRecSvcInfo)) {
                foreach ($cmRecSvcInfo as $recSvcInfo) {
                    $cmId = $recSvcInfo->cm_id;
                    $recSvcInfoArr = !empty($recSvcInfo->rec_svc_info) ? json_decode($recSvcInfo->rec_svc_info, true) : [];

                    if (!empty($recSvcInfoArr)) {
                        foreach ($recSvcInfoArr as $rsKey => $rsInfo) {
                            if (!empty($request->unit)) {
                                if (!empty($request->responsibility_id)) {
                                    if (!empty($request->appt)) {
                                        if (!empty($rsInfo['resp'])) {
                                            if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && !empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['resp'])) {
                                            if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && !empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->appt)) {
                                        if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    } else {
                                        if (preg_match("/" . $request->unit . "/i", $rsInfo['unit_fmn_inst'])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    }
                                }
                            } else {
                                if (!empty($request->responsibility_id)) {
                                    if (!empty($request->appt)) {
                                        if (!empty($rsInfo['resp'])) {
                                            if (!empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp']) && preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    } else {
                                        if (!empty($rsInfo['resp'])) {
                                            if (!empty($rsInfo['resp']) && ($request->responsibility_id == $rsInfo['resp'])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($request->appt)) {
                                        if (preg_match("/" . $request->appt . "/i", $rsInfo['appointment'])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                                        }
                                    }
                                }
                            }

                            if (empty($request->unit) && empty($request->responsibility_id) && empty($request->appt)) {
                                $cmDetailArr[$cmId][$rsKey] = $this->pushSvcRec($rsInfo);
                            }
                        }
                    }
                }
            }

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
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
                            , 'course.name as course_name', 'cm_basic_profile.photo', 'rank.code as rank', 'arms_service.code as arms_service_name', 'cm_basic_profile.id')
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
            
            $fileName = 'Record_of_Service_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }




        if ($request->view == 'print') {
            return view('cmAnalytics.recSvcInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList','svcResposibilityList',
                    'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.recSvcInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'printOptionList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.recSvcInfo.print.index', compact('request', 'courseList','svcResposibilityList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'printOptionList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.recSvcInfo.index', compact('request', 'courseList', 'svcResposibilityList', 'cmDetailArr', 'nameArr','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&unit=' . $request->unit . '&responsibility_id=' . $request->responsibility_id . '&appt=' . $request->appt
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('recSvcWiseCmAnalytics?generate=true&' . $url);
    }

    public function pushSvcRec($rsInfo) {
        $cmDetailArr['from'] = $rsInfo['from'] ?? '';
        $cmDetailArr['to'] = !empty($rsInfo['to']) ? $rsInfo['to'] : (!empty($rsInfo['year']) ? $rsInfo['year'] : '');
        $cmDetailArr['unit'] = $rsInfo['unit_fmn_inst'] ?? '';
        $cmDetailArr['responsibility'] = $rsInfo['resp'] ?? '';
        $cmDetailArr['appt'] = $rsInfo['appointment'] ?? '';

        return $cmDetailArr;
    }

}
