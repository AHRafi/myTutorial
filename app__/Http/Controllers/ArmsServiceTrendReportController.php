<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\ArmsService;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\EventAssessmentMarking;
use App\GradingSystem;
use App\CmToSyn;
use App\CriteriaWiseWt;
use App\CiObsnMarkingLock;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\User;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ArmsServiceTrendReportController extends Controller {

    private $controller = 'ArmsServiceTrendReport';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::where('status', '2')
                        ->orderBy('start_date', 'desc')
                        ->pluck('name', 'id')->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();

        $armsServiceList = CmBasicProfile::leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->orderBy('arms_service.order', 'asc')
                ->pluck('arms_service.code', 'arms_service.id')
                ->toArray();

        $eventList = TermToEvent::leftJoin('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id)
                ->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $sortByList = ['personal_no' => __('label.PERSONAL_NO'), 'position' => __('label.POSITION')];
        $assignedObsnInfo = $comdtObsnLockInfo = $ciObsnLockInfo = 0;
        $eventMksWtArr = $cmArr = $rowSpanArr = $achieveMksWtArr = [];
        if ($request->generate == 'true') {
            $activeTrainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::where('status', '2')
                            ->orderBy('start_date', 'desc')
                            ->pluck('name', 'id')->toArray();
            $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                            ->where('status', '<>', '0')
                            ->orderBy('training_year_id', 'desc')
                            ->orderBy('id', 'desc')
                            ->pluck('name', 'id')
                            ->toArray();
            $armsServiceList = CmBasicProfile::leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->orderBy('arms_service.order', 'asc')
                    ->pluck('arms_service.code', 'arms_service.id')
                    ->toArray();
            $eventList = TermToEvent::leftJoin('event', 'event.id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id)
                    ->where('event.status', '1')
                    ->orderBy('event.event_code', 'asc')
                    ->pluck('event.event_code', 'event.id')
                    ->toArray();
            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $fileName = 'Arms_Service_Trend_Report' . $tyName . $courseName;


            // get assigned ci obsn wt
            $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')->where('course_id', $request->course_id)->first();

            // get ci lock info
            $ciObsnLockInfo = CiObsnMarkingLock::select('id')->where('course_id', $request->course_id)->first();


            //requested arms/service & Event
            $requestArmsServiceId = !empty($request->arms_service_id) ? explode(',', $request->arms_service_id) : '';
            $requestEventId = !empty($request->event_id) ? explode(',', $request->event_id) : '';

//            echo '<pre>';
//            print_r($armsServiceId);
//            print_r($eventId);
//            exit;
            //event info
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id)
                    ->whereIn('term_to_event.event_id', $requestEventId)
                    ->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                            , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event')
                    ->orderBy('event.event_code', 'asc')
                    ->get();

            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {
                            if (empty($ev->has_sub_event)) {

                                $eventMksWtArr[$ev->event_id]['total_mks_limit'] = !empty($eventMksWtArr[$ev->event_id]['total_mks_limit']) ? $eventMksWtArr[$ev->event_id]['total_mks_limit'] : 0;
                                $eventMksWtArr[$ev->event_id]['total_mks_limit'] += ($evId == $ev->event_id) ? (!empty($ev->mks_limit) ? $ev->mks_limit : 0) : 0;
                            }
                        }
                    }
                }
            }


            //sub event info
            $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_event_mks_wt', function($join) {
                        $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->where('term_to_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_event.event_id', $requestEventId)
                    ->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                            , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->get();


            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {
                            if (empty($subEv->has_sub_sub_event)) {
                                $eventMksWtArr[$subEv->event_id]['total_mks_limit'] = !empty($eventMksWtArr[$subEv->event_id]['total_mks_limit']) ? $eventMksWtArr[$subEv->event_id]['total_mks_limit'] : 0;
                                $eventMksWtArr[$subEv->event_id]['total_mks_limit'] += ($evId == $subEv->event_id) ? (!empty($subEv->mks_limit) ? $subEv->mks_limit : 0) : 0;
                            }
                        }
                    }
                }
            }
//            echo '<pre>';
//            print_r($eventMksWtArr);
//            exit;
            //sub sub event info
            $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->leftJoin('sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_sub_event.event_id', $requestEventId)
                    ->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {
                            if (empty($subSubEv->has_sub_sub_sub_event)) {
                                $eventMksWtArr[$subSubEv->event_id]['total_mks_limit'] = !empty($eventMksWtArr[$subSubEv->event_id]['total_mks_limit']) ? $eventMksWtArr[$subSubEv->event_id]['total_mks_limit'] : 0;
                                $eventMksWtArr[$subSubEv->event_id]['total_mks_limit'] += ($evId == $subSubEv->event_id) ? (!empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0) : 0;
                            }
                        }
                    }
                }
            }

            //sub sub sub event info
            $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                    ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_sub_sub_event.event_id', $requestEventId)
                    ->where('sub_sub_sub_event.status', '1')
                    ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                            , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                            , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code', 'event.event_code')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_sub_event.event_code', 'asc')
                    ->get();


            if (!$subSubSubEventInfo->isEmpty()) {
                foreach ($subSubSubEventInfo as $subSubSubEv) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {
                            $eventMksWtArr[$subSubSubEv->event_id]['total_mks_limit'] = !empty($eventMksWtArr[$subSubSubEv->event_id]['total_mks_limit']) ? $eventMksWtArr[$subSubSubEv->event_id]['total_mks_limit'] : 0;
                            $eventMksWtArr[$subSubSubEv->event_id]['total_mks_limit'] += ($evId == $subSubSubEv->event_id) ? (!empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0) : 0;
                        }
                    }
                }
            }

            // event wise mks & wt
            $achieveEventMksWtDataArr = EventAssessmentMarking::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'event_assessment_marking.cm_id')
                    ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id'
                            , 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id', 'event_assessment_marking.mks'
                            , 'event_assessment_marking.wt', 'event_assessment_marking.percentage'
                            , 'cm_basic_profile.arms_service_id')
                    ->where('event_assessment_marking.course_id', $request->course_id)
                    ->whereIn('event_assessment_marking.event_id', $requestEventId)
                    ->get();

            if (!$achieveEventMksWtDataArr->isEmpty()) {
                foreach ($achieveEventMksWtDataArr as $mwInfo) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {
                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] = !empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0;
                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] += ($evId == $mwInfo->event_id) ? $mwInfo->mks : 0;

                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] = !empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 0;
                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] += 1;

                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['avg_mks'] = (!empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0) / (!empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 1);
                        }
                    }
                }
            }


            // ci moderation wise mks & wt 
            $ciModWiseMksWtInfo = CiModerationMarking::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'ci_moderation_marking.cm_id')
                    ->where('ci_moderation_marking.course_id', $request->course_id)
                    ->select('ci_moderation_marking.term_id', 'ci_moderation_marking.event_id'
                            , 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id'
                            , 'ci_moderation_marking.sub_sub_sub_event_id', 'ci_moderation_marking.cm_id'
                            , 'ci_moderation_marking.mks', 'ci_moderation_marking.wt', 'ci_moderation_marking.percentage'
                            , 'cm_basic_profile.arms_service_id')
                    ->whereIn('ci_moderation_marking.event_id', $requestEventId)
                    ->get();

            if (!$ciModWiseMksWtInfo->isEmpty()) {
                foreach ($ciModWiseMksWtInfo as $mwInfo) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {

                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] = !empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0;
                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] += ($evId == $mwInfo->event_id) ? $mwInfo->mks : 0;

                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] = !empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 0;
                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] += 1;

                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['avg_mks'] = (!empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0) / (!empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 1);
                        }
                    }
                }
            }

            // comdt moderation wise mks & wt 
            $comdtModWiseMksWtInfo = ComdtModerationMarking::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'comdt_moderation_marking.cm_id')
                    ->where('comdt_moderation_marking.course_id', $request->course_id)
                    ->whereIn('comdt_moderation_marking.event_id', $requestEventId)
                    ->select('comdt_moderation_marking.term_id', 'comdt_moderation_marking.event_id'
                            , 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id'
                            , 'comdt_moderation_marking.sub_sub_sub_event_id', 'comdt_moderation_marking.cm_id'
                            , 'comdt_moderation_marking.mks', 'comdt_moderation_marking.wt'
                            , 'comdt_moderation_marking.percentage', 'cm_basic_profile.arms_service_id')
                    ->get();

            if (!$comdtModWiseMksWtInfo->isEmpty()) {
                foreach ($comdtModWiseMksWtInfo as $mwInfo) {
                    if (!empty($requestEventId)) {
                        foreach ($requestEventId as $evId) {

                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] = !empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0;
                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] += ($evId == $mwInfo->event_id) ? $mwInfo->mks : 0;

                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] = !empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 0;
                            $cmArr[$mwInfo->arms_service_id]['marked_cm'] += 1;

                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['avg_mks'] = (!empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_mks'] : 0) / (!empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 1);
//                            $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['avg_wt'] = (!empty($cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_wt']) ? $cmArr[$mwInfo->arms_service_id][$mwInfo->event_id]['total_event_wt'] : 0) / (!empty($cmArr[$mwInfo->arms_service_id]['marked_cm']) ? $cmArr[$mwInfo->arms_service_id]['marked_cm'] : 1);
                        }
                    }
                }
            }
            $assignedMksArr = [];
            if (!empty($cmArr)) {
                foreach ($cmArr as $armsServiceId => $evInfo) {
                    foreach ($evInfo as $eventId => $info) {
                        $assignedMksArr[$armsServiceId][$eventId]['total_mks_limit'] = !empty($assignedMksArr[$armsServiceId][$eventId]['total_mks_limit']) ? $assignedMksArr[$armsServiceId][$eventId]['total_mks_limit'] : 0;
                        $assignedMksArr[$armsServiceId][$eventId]['total_mks_limit'] += !empty($eventMksWtArr[$eventId]['total_mks_limit']) ? $eventMksWtArr[$eventId]['total_mks_limit'] : 0;
                    }
                }
            }
//            echo '<pre>';
//            print_r($assignedMksArr);
////            print_r($eventMksWtArr);
//            exit;
        }

        if ($request->view == 'print') {
            return view('report.trend.armsServiceTrend.print.index')->with(compact('activeTrainingYearList', 'courseList', 'armsServiceList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('report.trend.armsServiceTrend.print.index', compact('activeTrainingYearList', 'courseList', 'armsServiceList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('report.trend.armsServiceTrend.print.index', compact('activeTrainingYearList', 'courseList', 'armsServiceList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr')), $fileName . '.xlsx');
        }

        return view('report.trend.armsServiceTrend.index', compact('activeTrainingYearList', 'courseList', 'armsServiceList'
                        , 'eventList', 'sortByList', 'assignedObsnInfo'
                        , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('report.trend.armsServiceTrend.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getArmsServiceEvent(Request $request) {
        $armsServiceList = CmBasicProfile::leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->orderBy('arms_service.order', 'asc')
                ->pluck('arms_service.code', 'arms_service.id')
                ->toArray();

        $eventList = TermToEvent::leftJoin('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id)
                ->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

//        echo '<pre>';        print_r($armsServiceList); exit;
        $html = view('report.trend.armsServiceTrend.getArmsService', compact('armsServiceList'))->render();
        $html2 = view('report.trend.armsServiceTrend.getEvent', compact('eventList'))->render();
        return Response::json(['html' => $html, 'html2' => $html2]);
    }

    public function filter(Request $request) {
//        echo '<pre>';
//        print_r($request->all());
//        exit;

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'arms_service_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'arms_service_id.required' => __('label.THE_ARMS_SERVICE_FIELD_IS_REQUIRED'),
            'event_id.required' => __('label.THE_EVENT_FIELD_IS_REQUIRED'),
        ];
        $armsServiceId = !empty($request->arms_service_id) ? implode(',', $request->arms_service_id) : '';
        $eventId = !empty($request->event_id) ? implode(',', $request->event_id) : '';
        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&arms_service_id=' . $armsServiceId . '&event_id=' . $eventId . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('armsServiceTrendReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('armsServiceTrendReport?generate=true&' . $url);
    }

}
