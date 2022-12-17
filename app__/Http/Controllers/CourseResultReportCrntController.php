<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\Term;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\EventAssessmentMarking;
use App\GradingSystem;
use App\CmToSyn;
use App\CriteriaWiseWt;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\CiObsnMarkingLock;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
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

class CourseResultReportCrntController extends Controller {

    private $controller = 'CourseResultReportCrnt';

    public function index(Request $request) {
//get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.COURSE_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.COURSE_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        $sortByList = ['position' => __('label.POSITION'), 'svc' => __('label.WING'), 'alphabatically' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'personal_no' => __('label.PERSONAL_NO')];


        $assignedObsnInfo = $gradeInfo = $comdtObsnLockInfo = $ciObsnLockInfo = 0;
        $eventMksWtArr = $cmArr = $termDataArr = $eventWiseMksWtArr = $termTotalMksWtArr = $termMarkingArr = [];
        if ($request->generate == 'true') {


            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList->name) ? '_' . $activeTrainingYearList->name : '';
            $courseName = $request->course_id != '0' && !empty($courseList->name) ? '_' . $courseList->name : '';
            $fileName = 'Course_Result' . $tyName . $courseName;
            $fileName = Common::getFileFormatedName($fileName);

// get assigned ci obsn wt
            $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')->where('course_id', $request->course_id)->first();
            $assignedDsObsnInfo = DsObsnMarkingLimit::select('term_id', 'mks_limit', 'obsn')
                            ->where('course_id', $request->course_id)->get();
            $assignedDsObsnArr = [];
            if (!$assignedDsObsnInfo->isEmpty()) {
                foreach ($assignedDsObsnInfo as $dsObsn) {
                    $assignedDsObsnArr[$dsObsn->term_id]['mks_limit'] = $dsObsn->mks_limit;
                    $assignedDsObsnArr[$dsObsn->term_id]['obsn'] = $dsObsn->obsn;
                }
            }

// get grade system
            $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();
            $gradeArr = [];
            if (!$gradeInfo->isEmpty()) {
                foreach ($gradeInfo as $grade) {
                    $gradeArr[$grade->grade_name]['id'] = $grade->id;
                    $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                    $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
                }
            }

// get ci lock info
            $ciObsnLockInfo = CiObsnMarkingLock::select('id')->where('course_id', $request->course_id)->first();

// term info 
            if (!empty($request->term_id)) {
                $termInfo = Term::select('order')->where('id', $request->term_id)->first();
            }

            $termDataArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                    ->where('term_to_course.course_id', $request->course_id);
            if (!empty($termInfo)) {
                $termDataArr = $termDataArr->where('term.order', '<=', $termInfo->order);
            }
            $termIdList = $termDataArr->orderBy('term.order', 'asc')->pluck('term.id', 'term.id')
                    ->toArray();
            $termDataArr = $termDataArr->orderBy('term.order', 'asc')->pluck('term.name', 'term.id')
                    ->toArray();

//event info
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id)
                    ->whereIn('term_to_event.term_id', $termIdList)
                    ->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                            , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event'
                            , 'term_to_event.term_id')
                    ->get();

            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    if (empty($ev->has_sub_event)) {
                        $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['highest_mks_limit'] = !empty($ev->highest_mks_limit) ? $ev->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['lowest_mks_limit'] = !empty($ev->lowest_mks_limit) ? $ev->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
                        $eventMksWtArr['total_event_wt'][$ev->event_id] = !empty($ev->wt) ? $ev->wt : 0;


                        $eventMksWtArr['total_wt'][$ev->term_id] = !empty($eventMksWtArr['total_wt'][$ev->term_id]) ? $eventMksWtArr['total_wt'][$ev->term_id] : 0;
                        $eventMksWtArr['total_wt'][$ev->term_id] += !empty($ev->wt) ? $ev->wt : 0;
                        $eventMksWtArr['total_mks_limit'][$ev->term_id] = !empty($eventMksWtArr['total_mks_limit'][$ev->term_id]) ? $eventMksWtArr['total_mks_limit'][$ev->term_id] : 0;
                        $eventMksWtArr['total_mks_limit'][$ev->term_id] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'][$ev->term_id] = $eventMksWtArr['total_wt'][$ev->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'][$ev->term_id] = $eventMksWtArr['total_wt_after_ci'][$ev->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
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
                    ->join('sub_event_mks_wt', function($join) {
                        $join->on('sub_event_mks_wt.course_id', '=', 'term_to_sub_event.course_id');
                        $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->where('term_to_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_event.term_id', $termIdList)
                    ->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                            , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code', 'term_to_sub_event.term_id', 'event_to_sub_event.avg_marking')
                    ->get();

            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    $eventMksWtArr['event'][$subEv->term_id][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                    $eventMksWtArr['event'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';
                    $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                    if ($subEv->has_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['highest_mks_limit'] = !empty($subEv->highest_mks_limit) ? $subEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['lowest_mks_limit'] = !empty($subEv->lowest_mks_limit) ? $subEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                        $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;


                        $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                        $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                        $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] = $eventMksWtArr['total_wt'][$subEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'][$subEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    } else {
                        if ($subEv->avg_marking == '1') {
                            $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                            $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                            $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] : 0;
                            $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;


                            $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                            $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                            $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                            $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                            $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] = $eventMksWtArr['total_wt'][$subEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                            $eventMksWtArr['total_wt_after_comdt'][$subEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
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
                    ->where('term_to_sub_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_sub_event.term_id', $termIdList)
                    ->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code', 'term_to_sub_sub_event.term_id', 'event_to_sub_event.avg_marking')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';
                    if ($subSubEv->has_sub_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['highest_mks_limit'] = !empty($subSubEv->highest_mks_limit) ? $subSubEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['lowest_mks_limit'] = !empty($subSubEv->lowest_mks_limit) ? $subSubEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                        if ($subSubEv->avg_marking == '0') {

                            $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] : 0;
                            $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] : 0;
                            $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                            $eventMksWtArr['total_wt'][$subSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubEv->term_id] : 0;
                            $eventMksWtArr['total_wt'][$subSubEv->term_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                            $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] : 0;
                            $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                            $eventMksWtArr['total_wt_after_ci'][$subSubEv->term_id] = $eventMksWtArr['total_wt'][$subSubEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                            $eventMksWtArr['total_wt_after_comdt'][$subSubEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subSubEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                        }
                    }

                    if ($subSubEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                        $eventMksWtArr['avg_marking'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
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
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                    ->whereIn('term_to_sub_sub_sub_event.term_id', $termIdList)
                    ->where('sub_sub_sub_event.status', '1')
                    ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                            , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                            , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code', 'event.event_code'
                            , 'term_to_sub_sub_sub_event.term_id', 'event_to_sub_event.avg_marking')
                    ->get();


            if (!$subSubSubEventInfo->isEmpty()) {
                foreach ($subSubSubEventInfo as $subSubSubEv) {
                    $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                    $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['highest_mks_limit'] = !empty($subSubSubEv->highest_mks_limit) ? $subSubSubEv->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['lowest_mks_limit'] = !empty($subSubSubEv->lowest_mks_limit) ? $subSubSubEv->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                    if ($subSubSubEv->avg_marking == '0') {

                        $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                        $eventMksWtArr['total_wt'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubSubEv->term_id] : 0;
                        $eventMksWtArr['total_wt'][$subSubSubEv->term_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] : 0;
                        $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'][$subSubSubEv->term_id] = $eventMksWtArr['total_wt'][$subSubSubEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'][$subSubSubEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subSubSubEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    }
                }
            }
// event wise mks & wt
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
                    ->whereNotNull('event_assessment_marking.mks')
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
                        $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] = !empty($cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id]) ? $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] : 0;
                        $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] += 1;
                    }
                    $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_mks'] = $eventMwInfo->avg_mks;
                    $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_wt'] = $eventMwInfo->avg_wt;
                    $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_percentage'] = $eventMwInfo->avg_percentage;
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
                    if (!empty($ciMwInfo->mks) && empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id])) {
						$cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] = !empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id]) ? $cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] : 0;
						$cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] += 1;
					}
					$eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_mks'] = $ciMwInfo->mks;
                    $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_wt'] = $ciMwInfo->wt;
                    $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_percentage'] = $ciMwInfo->percentage;
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
                    $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_mks'] = $comdtMwInfo->mks;
                    $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_wt'] = $comdtMwInfo->wt;
                    $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_percentage'] = $comdtMwInfo->percentage;
                }
            }


//            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
//					->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
//                    ->where('cm_basic_profile.status', '1')
//                    ->where('cm_basic_profile.course_id', $request->course_id)
//                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
//                            , 'cm_basic_profile.full_name', 'rank.code as rank_name')
//					->orderBy('wing.order', 'asc')
//                    ->orderBy('rank.order', 'asc')
//                    ->orderBy('cm_basic_profile.personal_no', 'asc')
//                    ->get();
//            
//         


            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id');
            if (!empty($request->sort) && $request->sort == 'syn') {
                $cmDataArr = $cmDataArr->leftJoin('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                        ->leftJoin('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id');
            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.course_id', $request->course_id);
//            if (in_array(Auth::user()->group_id, [4])) {
//                $cmDataArr = $cmDataArr->whereIn('cm_basic_profile.id', $dsCmArr);
//            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.status', '1')
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                    , 'cm_basic_profile.full_name', 'rank.code as rank_name');

            if (!empty($request->sort)) {
                if ($request->sort == 'syn') {
                    $cmDataArr = $cmDataArr->orderBy('cm_group.order', 'asc')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
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




            //ds obsn marking info
            $dsObsnMksWtInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                        $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                        $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                        $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                    })
                    ->where('ds_obsn_marking.course_id', $request->course_id)
                    ->whereIn('ds_obsn_marking.term_id', $termIdList)
                    ->select('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id', DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks')
                            , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt'))
                    ->groupBy('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id')
                    ->get();
            $dsObsnMksWtArr = [];
            if (!$dsObsnMksWtInfo->isEmpty()) {
                foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                    $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['ds_obsn_mks'] = $dsObsnInfo->obsn_mks;
                    $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['ds_obsn_wt'] = $dsObsnInfo->obsn_wt;
                }
            }

            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $cmInfo) {
                    if (!empty($eventMksWtArr['mks_wt'])) {
                        foreach ($eventMksWtArr['mks_wt'] as $termId => $evInfo) {
                            foreach ($evInfo as $eventId => $subEvInfo) {
                                foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                    foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                        foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                            $comdtMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks'] : 0;
                                            $comdtWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt'] : 0;
                                            $comdtPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage'] : 0;

                                            $ciMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks'] : 0;
                                            $ciWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt'] : 0;
                                            $ciPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage'] : 0;

                                            $eventAvgMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks'] : 0;
                                            $eventAvgWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;
                                            $eventAvgPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage'] : 0;

                                            $TotalTermMks = !empty($comdtMks) ? $comdtMks : (!empty($ciMks) ? $ciMks : $eventAvgMks);
                                            $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                                            $TotalTermPercentage = !empty($comdtPercentage) ? $comdtPercentage : (!empty($ciPercentage) ? $ciPercentage : $eventAvgPercentage);

                                            //count average where avg marking is enabled
                                            $totalCount = 0;
                                            $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                            if (!empty($cmEventCountArr[$cmId][$termId][$eventId][$subEventId])) {
                                                if (!empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['avg_marking'] == '1') {
                                                    if (array_key_exists($cmId, $cmEventCountArr)) {
                                                        $totalCount = $cmEventCountArr[$cmId][$termId][$eventId][$subEventId];
                                                    }

                                                    $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['mks_limit'] : 0;
                                                    $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['wt'] : 0;
                                                    $subSubEventMksLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['mks_limit'] : 0;
                                                    $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

                                                    $mksLimit = !empty($subSubSubEventId) ? $subSubEventMksLimit : $subEventMksLimit;
                                                    $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                                    $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;
                                                    $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                                    if ($totalCount != 0 && $unitMksLimit != 0 && $unitWtLimit != 0) {
                                                        $assignedWt = $subEventWtLimit / $totalCount;
                                                        $TotalTermMks = ($TotalTermMks * $subEventMksLimit) / ($totalCount * $unitMksLimit);
                                                        $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                                    }
                                                }
                                            }
                                            //term wise total
                                            $cmArr[$cmId]['term_total'][$termId]['total_mks'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_mks']) ? $cmArr[$cmId]['term_total'][$termId]['total_mks'] : 0;
                                            $cmArr[$cmId]['term_total'][$termId]['total_mks'] += $TotalTermMks;
                                            $cmArr[$cmId]['term_total'][$termId]['total_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_wt'] : 0;
                                            $cmArr[$cmId]['term_total'][$termId]['total_wt'] += $TotalTermWt;
                                            $cmArr[$cmId]['term_total'][$termId]['percentage'] = 0;

                                            $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                                            if (!empty($TotalTermWt)) {
                                                $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                            }

                                            $cmArr[$cmId]['term_total'][$termId]['percentage'] = ($cmArr[$cmId]['term_total'][$termId]['total_wt'] * 100) / (!empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 1);

                                            $termMarkingArr[$termId][$cmId]['percentage'] = $cmArr[$cmId]['term_total'][$termId]['percentage'];
                                            $totalPercentage = $cmArr[$cmId]['term_total'][$termId]['percentage'];
                                            // grade
                                            if (!empty($totalPercentage)) {
                                                foreach ($gradeArr as $letter => $gradeRange) {
                                                    if ($totalPercentage == 100) {
                                                        $cmArr[$cmId]['term_total'][$termId]['total_grade'] = "A+";
                                                        $cmArr[$cmId]['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                                    }
                                                    if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                                        $cmArr[$cmId]['term_total'][$termId]['total_grade'] = $letter;
                                                        $cmArr[$cmId]['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //ds obsn marking count
            if (!empty($dsObsnMksWtArr)) {
                foreach ($dsObsnMksWtArr as $termId => $termInfo) {
                    foreach ($termInfo as $cmId => $info) {
                        $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                        $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);

                        $dsObsnWt = 0;
                        if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                            $dsObsnWt = (($info['ds_obsn_mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                        }
//term wise total
                        $cmArr[$cmId]['term_total'][$termId]['total_mks'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_mks']) ? $cmArr[$cmId]['term_total'][$termId]['total_mks'] : 0;
                        $cmArr[$cmId]['term_total'][$termId]['total_mks'] += $info['ds_obsn_mks'] ?? 0;
                        $cmArr[$cmId]['term_total'][$termId]['total_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_wt'] : 0;
                        $cmArr[$cmId]['term_total'][$termId]['total_wt'] += $dsObsnWt ?? 0;

                        $cmArr[$cmId]['term_total'][$termId]['percentage'] = ($cmArr[$cmId]['term_total'][$termId]['total_wt'] * 100) / (!empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 1);

                        $termMarkingArr[$termId][$cmId]['percentage'] = $cmArr[$cmId]['term_total'][$termId]['percentage'];
                        $totalPercentage = Helper::numberFormatDigit2($cmArr[$cmId]['term_total'][$termId]['percentage']);
                        // grade
                        if (!empty($totalPercentage)) {
                            foreach ($gradeArr as $letter => $gradeRange) {
                                if ($totalPercentage == 100) {
                                    $cmArr[$cmId]['term_total'][$termId]['total_grade'] = "A+";
                                    $cmArr[$cmId]['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                }
                                if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                    $cmArr[$cmId]['term_total'][$termId]['total_grade'] = $letter;
                                    $cmArr[$cmId]['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                }
                            }
                        }
                    }
                }
            }


            if (!empty($termMarkingArr)) {
                foreach ($termMarkingArr as $termId => $info) {
                    $termMarkingArr[$termId] = Common::getPosition($termMarkingArr[$termId], 'percentage', 'position');
                    foreach ($info as $cmId => $cminf) {
                        $cmArr[$cmId]['term_total'][$termId]['position'] = $termMarkingArr[$termId][$cmId]['position'];
                    }
                }
            }

//            Start:: Term Aggregate
            if (!empty($termDataArr)) {
                foreach ($termDataArr as $termId => $termName) {
                    $eventMksWtArr['total_wt'][$termId] = !empty($eventMksWtArr['total_wt'][$termId]) ? $eventMksWtArr['total_wt'][$termId] : 0;
                    $eventMksWtArr['total_wt'][$termId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                    $eventMksWtArr['total_mks_limit'][$termId] = !empty($eventMksWtArr['total_mks_limit'][$termId]) ? $eventMksWtArr['total_mks_limit'][$termId] : 0;
                    $eventMksWtArr['total_mks_limit'][$termId] += (!empty($assignedDsObsnArr[$termId]['mks_limit']) ? $assignedDsObsnArr[$termId]['mks_limit'] : 0);

                    $eventMksWtArr['agg_total_mks_limit'] = !empty($eventMksWtArr['agg_total_mks_limit']) ? $eventMksWtArr['agg_total_mks_limit'] : 0;
                    $eventMksWtArr['agg_total_mks_limit'] += $eventMksWtArr['total_mks_limit'][$termId];
                    $eventMksWtArr['agg_total_wt_limit'] = !empty($eventMksWtArr['agg_total_wt_limit']) ? $eventMksWtArr['agg_total_wt_limit'] : 0;
                    $eventMksWtArr['agg_total_wt_limit'] += $eventMksWtArr['total_wt'][$termId];
                }
            }

            if (!empty($termDataArr)) {
                foreach ($cmArr as $cmId => $cmInfo) {
                    if (!empty($termDataArr)) {
                        foreach ($termDataArr as $termId => $termName) {
                            $cmArr[$cmId]['agg_total_wt_limit'] = !empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 0;
                            $cmArr[$cmId]['agg_total_wt_limit'] += !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                            $cmArr[$cmId]['term_agg_total_mks'] = !empty($cmArr[$cmId]['term_agg_total_mks']) ? $cmArr[$cmId]['term_agg_total_mks'] : 0;
                            $cmArr[$cmId]['term_agg_total_mks'] += $cmArr[$cmId]['term_total'][$termId]['total_mks'];
                            $cmArr[$cmId]['term_agg_total_wt'] = !empty($cmArr[$cmId]['term_agg_total_wt']) ? $cmArr[$cmId]['term_agg_total_wt'] : 0;
                            $cmArr[$cmId]['term_agg_total_wt'] += $cmArr[$cmId]['term_total'][$termId]['total_wt'];
                            $cmArr[$cmId]['term_agg_percentage'] = ($cmArr[$cmId]['term_agg_total_wt'] * 100) / (!empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 1);
                        }
                    }
                }
            }

            // get grade after term total
            $cmArr = Common::getGradeName($cmArr, $gradeInfo, 'term_agg_percentage', 'term_agg_total_grade');

            // get postion after term total
            $cmArr = Common::getPosition($cmArr, 'term_agg_percentage', 'total_term_agg_position');


//            End:: Term Aggregate
//            get ci observation 
            $ciObsnDataArr = CiObsnMarking::join('ci_obsn_marking_lock', 'ci_obsn_marking_lock.course_id', 'ci_obsn_marking.course_id')
                    ->leftJoin('grading_system', 'grading_system.id', 'ci_obsn_marking.grade_id')
                    ->select('ci_obsn_marking.cm_id', 'ci_obsn_marking.ci_obsn', 'ci_obsn_marking.wt'
                            , 'ci_obsn_marking.percentage', 'grading_system.grade_name as after_ci_grade')
                    ->where('ci_obsn_marking.course_id', $request->course_id)
                    ->get();
            if (!$ciObsnDataArr->isEmpty()) {
                foreach ($ciObsnDataArr as $ciObsnData) {
                    $cmArr[$ciObsnData->cm_id]['ci_obsn'] = $ciObsnData->ci_obsn ?? 0;
                    $cmArr[$ciObsnData->cm_id]['total_wt_after_ci'] = $ciObsnData->wt ?? 0;
                    $cmArr[$ciObsnData->cm_id]['percent_after_ci'] = $ciObsnData->percentage ?? 0;
                    $cmArr[$ciObsnData->cm_id]['grade_after_ci'] = $ciObsnData->after_ci_grade ?? 0;
                }
            }

//            get comdt observation
            $comdtObsnDataArr = ComdtObsnMarking::join('comdt_obsn_marking_lock', 'comdt_obsn_marking_lock.course_id', 'comdt_obsn_marking.course_id')
                    ->leftJoin('grading_system', 'grading_system.id', 'comdt_obsn_marking.grade_id')
                    ->select('comdt_obsn_marking.cm_id', 'comdt_obsn_marking.comdt_obsn', 'comdt_obsn_marking.wt'
                            , 'comdt_obsn_marking.percentage', 'grading_system.grade_name as after_comdt_grade'
                            , 'grading_system.id as grade_id')
                    ->where('comdt_obsn_marking.course_id', $request->course_id)
                    ->get();

            if (!$comdtObsnDataArr->isEmpty()) {
                foreach ($comdtObsnDataArr as $comdtObsnData) {
                    $cmArr[$comdtObsnData->cm_id]['comdt_obsn'] = $comdtObsnData->comdt_obsn ?? '';
                    $cmArr[$comdtObsnData->cm_id]['total_wt_after_comdt'] = $comdtObsnData->wt ?? '';
                    $cmArr[$comdtObsnData->cm_id]['percent_after_comdt'] = $comdtObsnData->percentage ?? '';
                    $cmArr[$comdtObsnData->cm_id]['grade_after_comdt'] = $comdtObsnData->after_comdt_grade ?? '';
                    $cmArr[$comdtObsnData->cm_id]['grade_id_after_comdt'] = $comdtObsnData->grade_id ?? 0;
                }
            }

            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $cmInfo) {
                    $comdtWt = !empty($cmInfo['comdt_obsn']) ? $cmInfo['comdt_obsn'] : 0;
                    $ciWt = !empty($cmInfo['ci_obsn']) ? $cmInfo['ci_obsn'] : 0;
                    $termTotalWt = !empty($cmInfo['term_agg_total_wt']) ? $cmInfo['term_agg_total_wt'] : 0;
                    $cmArr[$cmId]['final_wt'] = $termTotalWt + $ciWt + $comdtWt;

                    $assignedCiObsnWt = !empty($ciWt) && !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0;
                    $assignedComdtObsnWt = !empty($comdtWt) && !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0;

                    $cmArr[$cmId]['agg_total_wt_limit'] = !empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 0;
                    $cmArr[$cmId]['final_assigned_wt'] = $cmArr[$cmId]['agg_total_wt_limit'] + $assignedCiObsnWt + $assignedComdtObsnWt;

                    $cmArr[$cmId]['final_percentage'] = ($cmArr[$cmId]['final_wt'] * 100) / (!empty($cmArr[$cmId]['final_assigned_wt']) ? $cmArr[$cmId]['final_assigned_wt'] : 1);
                }
            }

            $eventMksWtArr['final_wt'] = $eventMksWtArr['agg_total_wt_limit'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0) + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);

            $cmArr = Common::getPosition($cmArr, 'final_percentage', 'final_position');

            $cmArr = Common::getGradeName($cmArr, $gradeInfo, 'final_percentage', 'final_grade');


            if (empty($request->sort) || $request->sort == 'position') {
                if (!empty($cmArr)) {
                    usort($cmArr, function ($item1, $item2) {
                        $positioning = 'final_percentage';

                        if (!isset($item1[$positioning])) {
                            $item1[$positioning] = '';
                        }

                        if (!isset($item2[$positioning])) {
                            $item2[$positioning] = '';
                        }
                        return $item2[$positioning] <=> $item1[$positioning];
                    });
                }
            }

//            echo '<pre>';
//            print_r($cmArr);
//            exit;
        }



        if ($request->view == 'print') {
            return view('reportCrnt.courseResult.print.index')->with(compact('activeTrainingYearList', 'courseList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'termDataArr', 'request'
                                    , 'eventMksWtArr', 'cmArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.courseResult.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'termDataArr', 'request'
                                    , 'eventMksWtArr', 'cmArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.courseResult.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'termDataArr', 'request'
                                    , 'eventMksWtArr', 'cmArr')), $fileName . '.xlsx');
        }

        return view('reportCrnt.courseResult.index', compact('activeTrainingYearList', 'courseList'
                        , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'termDataArr', 'request'
                        , 'eventMksWtArr', 'cmArr'));
    }

    public function getCourse(Request $request) {
        $courseStatusArr = TermToCourse::join('course', 'course.id', 'term_to_course.course_id')
                        ->where('course.training_year_id', $request->training_year_id)
                        ->where('term_to_course.status', '2')
                        ->pluck('course.id', 'course.id')->toArray();

        $courseList = Course::where('training_year_id', $request->training_year_id);
        if (in_array(Auth::user()->group_id, [2])) {
            $courseList = $courseList->where('status', '2');
        } else {
            $courseList = $courseList->where('status', '<>', '0');
        }
        $courseList = $courseList->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('reportCrnt.courseResult.getCourse', compact('courseList'))->render();
        $html1 = view('reportCrnt.termResult.getCourseErr', compact('courseList'))->render();
        return Response::json(['html' => $html, 'html1' => $html1]);
    }

    public function filter(Request $request) {
//        echo '<pre>';            print_r($request->all()); exit;  

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];
        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('courseResultReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('courseResultReportCrnt?generate=true&' . $url);
    }

}
