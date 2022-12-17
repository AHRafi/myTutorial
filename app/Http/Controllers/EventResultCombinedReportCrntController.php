<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\EventToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\EventAssessmentMarking;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\GradingSystem;
use App\CmToSyn;
use App\CriteriaWiseWt;
use App\CiObsnMarkingLock;
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

class EventResultCombinedReportCrntController extends Controller {

    private $controller = 'EventResultCombinedReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.EVENT_RESULT_COMBINED');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.EVENT_RESULT_COMBINED');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $eventList = ['0' => __('label.ALL_EVENT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $courseList->id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $sortByList = ['svc' => __('label.WING'), 'alphabatically' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'personal_no' => __('label.PERSONAL_NO')];
        if (empty($request->event_id)) {
            $sortByList = ['position' => __('label.POSITION')] + $sortByList;
        }

        $assignedObsnInfo = $gradeInfo = $comdtObsnLockInfo = $ciObsnLockInfo = 0;
        $eventMksWtArr = $cmArr = $rowSpanArr = $achieveMksWtArr = [];
        if ($request->generate == 'true') {
            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $fileName = 'Event_Result_Combined_Report' . $tyName . $courseName . $eventName;
            $fileName = Common::getFileFormatedName($fileName);


            // get assigned ci obsn wt
            $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')->where('course_id', $request->course_id)->first();

            // get grade system
            $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

            // get ci lock info
            $ciObsnLockInfo = CiObsnMarkingLock::select('id')->where('course_id', $request->course_id)->first();

            //event info
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->event_id)) {
                $eventInfo = $eventInfo->where('term_to_event.event_id', $request->event_id);
            }
            $eventInfo = $eventInfo->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                            , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event')
                    ->orderBy('event.event_code', 'asc')
                    ->get();
//            echo '<pre>';            print_r($eventInfo->toArray()); exit;

            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    $eventMksWtArr['event'][$ev->event_id]['name'] = $ev->event_code ?? '';
                    if ($ev->has_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['highest_mks_limit'] = !empty($ev->highest_mks_limit) ? $ev->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['lowest_mks_limit'] = !empty($ev->lowest_mks_limit) ? $ev->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
						$eventMksWtArr['total_event_wt'][$ev->event_id] = !empty($ev->wt) ? $ev->wt : 0;
                                

                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($ev->wt) ? $ev->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'] = $eventMksWtArr['total_wt'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'] = $eventMksWtArr['total_wt_after_ci'] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    }
                }
            }

            //sub event info
            $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_event_mks_wt', function($join) {
                        $join->on('sub_event_mks_wt.course_id', '=', 'term_to_sub_event.course_id');
                        $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->where('term_to_sub_event.course_id', $request->course_id);
            if (!empty($request->event_id)) {
                $subEventInfo = $subEventInfo->where('term_to_sub_event.event_id', $request->event_id);
            }
            $subEventInfo = $subEventInfo->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                            , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->get();


            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    $eventMksWtArr['event'][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                    $eventMksWtArr['event'][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';
                    $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                    if ($subEv->has_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['highest_mks_limit'] = !empty($subEv->highest_mks_limit) ? $subEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['lowest_mks_limit'] = !empty($subEv->lowest_mks_limit) ? $subEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_event_wt'][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'] = $eventMksWtArr['total_wt'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'] = $eventMksWtArr['total_wt_after_ci'] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    } else {
                        if ($subEv->avg_marking == '1') {
                            $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                            $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                            $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                            $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                            $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                            $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                            $eventMksWtArr['total_event_wt'][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->event_id] : 0;
                            $eventMksWtArr['total_event_wt'][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                            $eventMksWtArr['total_wt_after_ci'] = $eventMksWtArr['total_wt'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                            $eventMksWtArr['total_wt_after_comdt'] = $eventMksWtArr['total_wt_after_ci'] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                        }
                    }
                }
            }

            //sub sub event info
            $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_event_mks_wt.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->event_id)) {
                $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.event_id', $request->event_id);
            }
            $subSubEventInfo = $subSubEventInfo->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_event.event_code', 'asc')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    $eventMksWtArr['event'][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';
                    if ($subSubEv->has_sub_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['highest_mks_limit'] = !empty($subSubEv->highest_mks_limit) ? $subSubEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['lowest_mks_limit'] = !empty($subSubEv->lowest_mks_limit) ? $subSubEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                        if ($subSubEv->avg_marking == '0') {


                            $eventMksWtArr['total_event_wt'][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubEv->event_id] : 0;
                            $eventMksWtArr['total_event_wt'][$subSubEv->event_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;


                            $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                            $eventMksWtArr['total_wt'] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                            $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                            $eventMksWtArr['total_mks_limit'] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;


                            $eventMksWtArr['total_wt_after_ci'] = $eventMksWtArr['total_wt'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                            $eventMksWtArr['total_wt_after_comdt'] = $eventMksWtArr['total_wt_after_ci'] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                        }
                    }

                    if($subSubEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                    }
                }
            }

            //sub sub sub event info
            $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                    ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_sub_event_mks_wt.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->event_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.event_id', $request->event_id);
            }
            $subSubSubEventInfo = $subSubSubEventInfo->where('sub_sub_sub_event.status', '1')
                    ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                            , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                            , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code', 'event.event_code'
                            , 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_sub_event.event_code', 'asc')
                    ->get();


            if (!$subSubSubEventInfo->isEmpty()) {
                foreach ($subSubSubEventInfo as $subSubSubEv) {
                    $eventMksWtArr['event'][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['highest_mks_limit'] = !empty($subSubSubEv->highest_mks_limit) ? $subSubSubEv->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['lowest_mks_limit'] = !empty($subSubSubEv->lowest_mks_limit) ? $subSubSubEv->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                    if ($subSubSubEv->avg_marking == '0') {
                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                        $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'] = $eventMksWtArr['total_wt'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'] = $eventMksWtArr['total_wt_after_ci'] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    }
                }
            }

            $eventMksWtArr2 = [];
            if (!empty($eventMksWtArr['event'])) {
                foreach ($eventMksWtArr['event'] as $eventId => $evInfo) {
                    if (sizeof($evInfo) == 1) {
                        $subEventId = $subSubEventId = $subSubSubEventId = 0;
                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                    }

                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        if (is_int($subEventId)) {
                            if (sizeof($subEvInfo) == 1) {
                                $subSubEventId = $subSubSubEventId = 0;
                                $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                            }
                            foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                                if (is_int($subSubEventId)) {
                                    if (sizeof($subSubEvInfo) == 1) {
                                        $subSubSubEventId = 0;
                                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                    }
                                    foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                        if (is_int($subSubSubEventId)) {
                                            $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                            $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $eventMksWtArr['mks_wt'] = $eventMksWtArr2;
            
            
//            echo '<pre>';
//            print_r($subEventInfo->toArray());
//            print_r($eventMksWtArr['event']);
//            print_r($eventMksWtArr['mks_wt']);
//            exit;

            if (!empty($eventMksWtArr['mks_wt'])) {
                foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo) {
                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                $rowSpanArr['event'][$eventId] = !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 0;
                                $rowSpanArr['event'][$eventId] += 1;

                                $rowSpanArr['sub_event'][$eventId][$subEventId] = !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 0;
                                $rowSpanArr['sub_event'][$eventId][$subEventId] += 1;

                                $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] = !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 0;
                                $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] += 1;

                                $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = !empty($rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : 0;
                                $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] += 1;
                            }
                        }
                    }
                }
            }

//            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
//				->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
//                    ->where('cm_basic_profile.status', '1')
//                    ->where('cm_basic_profile.course_id', $request->course_id)
//                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
//                            , 'cm_basic_profile.full_name', 'rank.code as rank_name')
//					->orderBy('wing.order', 'asc')
//                    ->orderBy('rank.order', 'asc')
//                    ->orderBy('cm_basic_profile.personal_no', 'asc')
//                    ->get();
            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id');
//            if (!empty($request->sort) && $request->sort == 'syn') {
//                $cmDataArr = $cmDataArr->leftJoin('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
//                        ->leftJoin('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id');
//            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.course_id', $request->course_id);
//            if (in_array(Auth::user()->group_id, [4])) {
//                $cmDataArr = $cmDataArr->whereIn('cm_basic_profile.id', $dsCmArr);
//            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.status', '1')
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                    , 'cm_basic_profile.full_name', 'rank.code as rank_name');

            if (!empty($request->sort)) {
//                if ($request->sort == 'syn') {
//                    $cmDataArr = $cmDataArr->where('cm_group.order', '<=', '2')
//                            ->orderBy('cm_group.order', 'asc')
//                            ->orderBy('wing.order', 'asc')
//                            ->orderBy('rank.order', 'asc')
//                            ->orderBy('cm_basic_profile.personal_no', 'asc');
//                } 
                if ($request->sort == 'alphabatically') {
                    $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
                } else {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                }
            } else {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }
            $cmDataArr = $cmDataArr->get();
            if (!$cmDataArr->isEmpty()) {
                foreach ($cmDataArr as $cmData) {
                    $cmArr[$cmData->id] = $cmData->toArray();
                }
            }

            $eventWiseMksWtInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                        $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                        $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                        $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                        $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                    })
                    ->where('event_assessment_marking.course_id', $request->course_id)
                    ->select('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"), DB::raw("AVG(event_assessment_marking.wt) as avg_wt")
                            , DB::raw("AVG(event_assessment_marking.percentage) as avg_percentage"))
                    ->groupBy('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id')
                    ->get();
            $cmEventCountArr = [];
            if (!$eventWiseMksWtInfo->isEmpty()) {
                foreach ($eventWiseMksWtInfo as $eventMwInfo) {
                    if (!empty($eventMwInfo->avg_mks)) {
                        $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] = !empty($cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id]) ? $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] : 0;
                        $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] += 1;
                    }
                    $eventWiseMksWtArr[$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_mks'] = $eventMwInfo->avg_mks;
                    $eventWiseMksWtArr[$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_wt'] = $eventMwInfo->avg_wt;
                    $eventWiseMksWtArr[$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_percentage'] = $eventMwInfo->avg_percentage;
                }
            }
// ci moderation wise mks & wt 
            $ciModWiseMksWtInfo = CiModerationMarking::join('ci_moderation_marking_lock', function($join) {
                        $join->on('ci_moderation_marking_lock.course_id', 'ci_moderation_marking.course_id');
                        $join->on('ci_moderation_marking_lock.term_id', 'ci_moderation_marking.term_id');
                        $join->on('ci_moderation_marking_lock.event_id', 'ci_moderation_marking.event_id');
                        $join->on('ci_moderation_marking_lock.sub_event_id', 'ci_moderation_marking.sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_event_id', 'ci_moderation_marking.sub_sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id');
                        $join->on('ci_moderation_marking_lock.locked_by', 'ci_moderation_marking.updated_by');
                    })
                    ->where('ci_moderation_marking.course_id', $request->course_id)
                    ->select('ci_moderation_marking.term_id', 'ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                            , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.mks', 'ci_moderation_marking.wt', 'ci_moderation_marking.percentage', 'ci_moderation_marking.grade_id')
                    ->get();

            if (!$ciModWiseMksWtInfo->isEmpty()) {
                foreach ($ciModWiseMksWtInfo as $ciMwInfo) {
                    $eventWiseMksWtArr[$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_mks'] = $ciMwInfo->mks;
                    $eventWiseMksWtArr[$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_wt'] = $ciMwInfo->wt;
                    $eventWiseMksWtArr[$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_percentage'] = $ciMwInfo->percentage;
                }
            }
// comdt moderation wise mks & wt 
            $comdtModWiseMksWtInfo = ComdtModerationMarking::join('comdt_moderation_marking_lock', function($join) {
                        $join->on('comdt_moderation_marking_lock.course_id', 'comdt_moderation_marking.course_id');
                        $join->on('comdt_moderation_marking_lock.term_id', 'comdt_moderation_marking.term_id');
                        $join->on('comdt_moderation_marking_lock.event_id', 'comdt_moderation_marking.event_id');
                        $join->on('comdt_moderation_marking_lock.sub_event_id', 'comdt_moderation_marking.sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id');
                        $join->on('comdt_moderation_marking_lock.locked_by', 'comdt_moderation_marking.updated_by');
                    })
                    ->where('comdt_moderation_marking.course_id', $request->course_id)
                    ->select('comdt_moderation_marking.term_id', 'comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                            , 'comdt_moderation_marking.cm_id', 'comdt_moderation_marking.mks', 'comdt_moderation_marking.wt', 'comdt_moderation_marking.percentage', 'comdt_moderation_marking.grade_id')
                    ->get();
            if (!$comdtModWiseMksWtInfo->isEmpty()) {
                foreach ($comdtModWiseMksWtInfo as $comdtMwInfo) {
                    $eventWiseMksWtArr[$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_mks'] = $comdtMwInfo->mks;
                    $eventWiseMksWtArr[$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_wt'] = $comdtMwInfo->wt;
                    $eventWiseMksWtArr[$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_percentage'] = $comdtMwInfo->percentage;
                }
            }

            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $cmInfo) {
                    if (!empty($eventMksWtArr['mks_wt'])) {
                        foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {

                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $totalCount = 0;
                                        $comdtMks = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks'] : 0;
                                        $comdtWt = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt'] : 0;
                                        $comdtPercentage = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage'] : 0;

                                        $ciMks = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks'] : 0;
                                        $ciWt = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt'] : 0;
                                        $ciPercentage = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage'] : 0;

                                        $eventAvgMks = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks'] : 0;
                                        $eventAvgWt = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;
                                        $eventAvgPercentage = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage'] : 0;

                                        $TotalTermMks = !empty($comdtMks) ? $comdtMks : (!empty($ciMks) ? $ciMks : $eventAvgMks);
                                        $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                                        $TotalTermPercentage = !empty($comdtPercentage) ? $comdtPercentage : (!empty($ciPercentage) ? $ciPercentage : $eventAvgPercentage);

                                        $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks'] = $TotalTermMks;
                                        $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] = $TotalTermWt;
                                        $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['percentage'] = $TotalTermPercentage;
                                        
                                        $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                        
                                        //count average where avg marking is enabled
                                        if (!empty($cmEventCountArr[$cmId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($cmId, $cmEventCountArr)) {
                                                    $totalCount = $cmEventCountArr[$cmId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;
                                                $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;
                                                $subSubEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit'] : 0;
                                                $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt'] : 0;
                                                
                                                $mksLimit = !empty($subSubSubEventId) ? $subSubEventMksLimit : $subEventMksLimit;
                                                $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;
                                                
                                                $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;
                                                $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;
                                                
                                                if ($totalCount != 0 && $unitMksLimit != 0 && $unitWtLimit != 0) {
                                                    $assignedWt = $subEventWtLimit / $totalCount;
                                                    $TotalTermMks = ($TotalTermMks * $subEventMksLimit) / ($totalCount * $unitMksLimit);
                                                    $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                                }
                                            }
                                        }

                                        $cmArr[$cmId]['total_term_mks'] = !empty($cmArr[$cmId]['total_term_mks']) ? $cmArr[$cmId]['total_term_mks'] : 0;
                                        $cmArr[$cmId]['total_term_mks'] += $TotalTermMks;

                                        $cmArr[$cmId]['total_term_wt'] = !empty($cmArr[$cmId]['total_term_wt']) ? $cmArr[$cmId]['total_term_wt'] : 0;
                                        $cmArr[$cmId]['total_term_wt'] += $TotalTermWt;
                                        
                                        $cmArr[$cmId]['total_event_mks'][$eventId] = !empty($cmArr[$cmId]['total_event_mks'][$eventId]) ? $cmArr[$cmId]['total_event_mks'][$eventId] : 0;
                                        $cmArr[$cmId]['total_event_mks'][$eventId] += $TotalTermMks;

                                        $cmArr[$cmId]['total_event_wt'][$eventId] = !empty($cmArr[$cmId]['total_event_wt'][$eventId]) ? $cmArr[$cmId]['total_event_wt'][$eventId] : 0;
                                        $cmArr[$cmId]['total_event_wt'][$eventId] += $TotalTermWt;
                                        
                                        $cmArr[$cmId]['total_assigned_wt'] = !empty($cmArr[$cmId]['total_assigned_wt']) ? $cmArr[$cmId]['total_assigned_wt'] : 0;
                                        $cmArr[$cmId]['total_event_assigned_wt'][$eventId] = !empty($cmArr[$cmId]['total_event_assigned_wt'][$eventId]) ? $cmArr[$cmId]['total_event_assigned_wt'][$eventId] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $cmArr[$cmId]['total_assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                            $cmArr[$cmId]['total_event_assigned_wt'][$eventId] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }
                                        
                                        $cmArr[$cmId]['total_term_percent'] = ($cmArr[$cmId]['total_term_wt'] * 100) / (!empty($cmArr[$cmId]['total_assigned_wt']) ? $cmArr[$cmId]['total_assigned_wt'] : 1);

                                        $cmArr[$cmId]['total_event_percent'][$eventId] = ($cmArr[$cmId]['total_event_wt'][$eventId] * 100) / (!empty($cmArr[$cmId]['total_event_assigned_wt'][$eventId]) ? $cmArr[$cmId]['total_event_assigned_wt'][$eventId] : 1);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // get grade after term total
            $cmArr = Common::getGradeName($cmArr, $gradeInfo, 'total_term_percent', 'grade_after_term_total');

            // get postion after term total
            $cmArr = Common::getPosition($cmArr, 'total_term_percent', 'total_term_position');
            if (!empty($request->sort) && $request->sort == 'position') {
                if (!empty($cmArr)) {
                    usort($cmArr, function ($item1, $item2) {
                        if (!isset($item1['total_term_percent'])) {
                            $item1['total_term_percent'] = '';
                        }

                        if (!isset($item2['total_term_percent'])) {
                            $item2['total_term_percent'] = '';
                        }
                        return $item2['total_term_percent'] <=> $item1['total_term_percent'];
                    });
                }
            }


//            echo '<pre>';
//            print_r($rowSpanArr);
//            exit;
        }

        if ($request->view == 'print') {
            return view('reportCrnt.eventResultCombined.print.index')->with(compact('activeTrainingYearList', 'courseList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo', 'gradeInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.eventResultCombined.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo', 'gradeInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.eventResultCombined.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'eventList', 'sortByList', 'assignedObsnInfo', 'gradeInfo'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr')), $fileName . '.xlsx');
        }

        return view('reportCrnt.eventResultCombined.index', compact('activeTrainingYearList', 'courseList'
                        , 'eventList', 'sortByList', 'assignedObsnInfo', 'gradeInfo'
                        , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.eventResultCombined.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];

        if (empty($request->event_id)) {
            $request->sort = !empty($request->sort) ? $request->sort : 'position';
        }


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&event_id=' . $request->event_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('eventResultCombinedReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('eventResultCombinedReportCrnt?generate=true&' . $url);
    }

}
