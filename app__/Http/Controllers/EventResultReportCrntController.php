<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\CiComdtModerationMarkingLimit;
use App\EventAssessmentMarking;
use App\GradingSystem;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsMarkingGroup;
use App\CmMarkingGroup;
use App\CmToSyn;
use App\User;
use Response;
use PDF;
use Auth;
use File;
use DB;
use Helper;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class EventResultReportCrntController extends Controller {

    private $controller = 'EventResultReportCrnt';

    public function index(Request $request) {
//get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.EVENT_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.EVENT_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();
        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $subEventList = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();
        $hasSubEvent = !empty($subEventList) ? 1 : 0;
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;

        $subSubEventList = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();
        $hasSubSubEvent = !empty($subSubEventList) ? 1 : 0;
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;

        $subSubSubEventList = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();
        $hasSubSubSubEvent = !empty($subSubSubEventList) ? 1 : 0;
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;

        $sortByList = ['position' => __('label.POSITION'), 'svc' => __('label.WING'), 'syn' => __('label.SYN'), 'alphabatically' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'personal_no' => __('label.PERSONAL_NO')];


        $cmArr = $assingedMksWtInfo = $dsMksWtArr = $prevMksWtArr = $comdtMksWtArr = $dsDataList = $avgDsMksWtArr = $ciMksWtArr = [];
        $numOfDs = $comdtMksInfo = $gradeInfo = 0;
        if ($request->generate == 'true') {

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $subEventName = $request->sub_event_id != '0' && !empty($subEventList[$request->sub_event_id]) ? '_' . $subEventList[$request->sub_event_id] : '';
            $subSubEventName = $request->sub_sub_event_id != '0' && !empty($subSubEventList[$request->sub_sub_event_id]) ? '_' . $subSubEventList[$request->sub_sub_event_id] : '';
            $subSubSubEventName = $request->sub_sub_sub_event_id != '0' && !empty($subSubSubEventList[$request->sub_sub_sub_event_id]) ? '_' . $subSubSubEventList[$request->sub_sub_sub_event_id] : '';
            $fileName = 'Event_Result_Report' . $tyName . $courseName . $termName . $eventName . $subEventName . $subSubEventName . $subSubSubEventName;
            $fileName = Common::getFileFormatedName($fileName);
            $dsDeligationList = Common::getDsDeligationList();
            $deligatedDs = !empty($dsDeligationList[$request->course_id]) ? $dsDeligationList[$request->course_id] : 0;

//            Start::Event Result Data
            $dsDataInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                    ->join('users', 'users.id', 'ds_marking_group.ds_id')->join('rank', 'rank.id', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', 'users.wing_id')
                    ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', 'ds_marking_group.ds_appt_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (Auth::user()->group_id == 4 && (empty($dsDeligationList) || !in_array(Auth::user()->id, $dsDeligationList))) {
                $dsDataInfo = $dsDataInfo->where('ds_marking_group.ds_id', Auth::user()->id);
            }
            if (!empty($request->sub_event_id)) {
                $dsDataInfo = $dsDataInfo->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $dsDataInfo = $dsDataInfo->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $dsDataInfo = $dsDataInfo->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $dsDataInfo = $dsDataInfo->select('appointment.code as appt', 'users.id as ds_id', 'users.photo'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name) as ds_name"), 'users.personal_no')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('appointment.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc')
                    ->get();

            if (!$dsDataInfo->isEmpty()) {
                foreach ($dsDataInfo as $ds) {
                    $dsDataList[$ds->ds_id] = $ds->toArray();
                }
            }

//        $dsDataList = $dsDataArr->pluck('ds_marking_group.ds_id', 'ds_marking_group.ds_id')->toArray();
            $numOfDs = !empty($dsDataList) ? sizeof($dsDataList) : '0';
//        $dsAppoinmentList = $dsDataArr->pluck('appointment.name', 'ds_marking_group.ds_id')->toArray();
//cm List

            $dsCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $dsCmArr = $dsCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $dsCmArr = $dsCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $dsCmArr = $dsCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $dsCmArr = $dsCmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('ds_marking_group.ds_id', '<>', $deligatedDs)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_basic_profile.id', 'cm_basic_profile.id')
                    ->toArray();


            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id');
            if (!empty($request->sort) && $request->sort == 'syn') {
                $cmDataArr = $cmDataArr->leftJoin('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                        ->leftJoin('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id');
            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.course_id', $request->course_id);
            if (in_array(Auth::user()->group_id, [4]) && (empty($dsDeligationList) || !in_array(Auth::user()->id, $dsDeligationList))) {
                $cmDataArr = $cmDataArr->whereIn('cm_basic_profile.id', $dsCmArr);
            }
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
// CI Marking Information
            $comdtMksInfo = CiComdtModerationMarkingLimit::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->select('comdt_mod')->first();

// get ds marking data
            $dsMksWtDataArr = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                        $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                        $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                        $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                        $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                    })
                    ->leftJoin('grading_system', 'grading_system.id', 'event_assessment_marking.grade_id')
                    ->where('event_assessment_marking.course_id', $request->course_id)
                    ->where('event_assessment_marking.term_id', $request->term_id)
//                    ->whereNotNull('event_assessment_marking.mks')
                    ->where('event_assessment_marking.event_id', $request->event_id);

            if (!empty($request->sub_event_id)) {
                $dsMksWtDataArr = $dsMksWtDataArr->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $dsMksWtDataArr = $dsMksWtDataArr->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $dsMksWtDataArr = $dsMksWtDataArr->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }


            $totalDsMarkingList = $dsMksWtDataArr->pluck('event_assessment_marking.updated_by', 'event_assessment_marking.updated_by')
                    ->toArray();

            $dsMksWtDataArr = $dsMksWtDataArr->select('event_assessment_marking.cm_id', 'event_assessment_marking.mks'
                            , 'event_assessment_marking.wt', 'event_assessment_marking.percentage', 'grading_system.grade_name'
                            , 'grading_system.id as grade_id', 'event_assessment_marking.updated_by', 'event_assessment_marking.remarks')
                    ->get();
            $dsMksSum = 0;
            if (!$dsMksWtDataArr->isEmpty()) {
                foreach ($dsMksWtDataArr as $dsMksWtData) {
                    $dsMksWtArr[$dsMksWtData->updated_by][$dsMksWtData->cm_id] = $dsMksWtData->toArray();
                }
            }

            $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

            $gradeArr = [];
            if (!$gradeInfo->isEmpty()) {
                foreach ($gradeInfo as $grade) {
                    $gradeArr[$grade->grade_name]['id'] = $grade->id;
                    $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                    $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
                }
            }



// get ci moderation data
            $ciMksWtDataArr = CiModerationMarking::join('ci_moderation_marking_lock', function($join) {
                        $join->on('ci_moderation_marking_lock.course_id', 'ci_moderation_marking.course_id');
                        $join->on('ci_moderation_marking_lock.term_id', 'ci_moderation_marking.term_id');
                        $join->on('ci_moderation_marking_lock.event_id', 'ci_moderation_marking.event_id');
                        $join->on('ci_moderation_marking_lock.sub_event_id', 'ci_moderation_marking.sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_event_id', 'ci_moderation_marking.sub_sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id');
                        $join->on('ci_moderation_marking_lock.locked_by', 'ci_moderation_marking.updated_by');
                    })
                    ->join('grading_system', 'grading_system.id', 'ci_moderation_marking.grade_id')
                    ->where('ci_moderation_marking.course_id', $request->course_id)
                    ->where('ci_moderation_marking.term_id', $request->term_id)
                    ->where('ci_moderation_marking.event_id', $request->event_id);

            if (!empty($request->sub_event_id)) {
                $ciMksWtDataArr = $ciMksWtDataArr->where('ci_moderation_marking.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $ciMksWtDataArr = $ciMksWtDataArr->where('ci_moderation_marking.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $ciMksWtDataArr = $ciMksWtDataArr->where('ci_moderation_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $ciMksWtDataArr = $ciMksWtDataArr->select('ci_moderation_marking.cm_id', 'ci_moderation_marking.ci_moderation', 'ci_moderation_marking.mks'
                            , 'ci_moderation_marking.wt', 'ci_moderation_marking.percentage', 'grading_system.grade_name'
                            , 'grading_system.id as grade_id', 'ci_moderation_marking.updated_by')
                    ->get();
            $ciMksWtArr = [];
            if (!$ciMksWtDataArr->isEmpty()) {
                foreach ($ciMksWtDataArr as $ciMksWtData) {
                    $ciMksWtArr[$ciMksWtData->cm_id] = $ciMksWtData->toArray();
                }
            }

//        Start:: Calculate After CI Moderation
            $assignedMksWtModel = !empty($request->sub_sub_sub_event_id) ? 'SubSubSubEventMksWt' : (!empty($request->sub_sub_event_id) ? 'SubSubEventMksWt' : (!empty($request->sub_event_id) ? 'SubEventMksWt' : 'EventMksWt'));

            $namespacedModel = '\\App\\' . $assignedMksWtModel;
            $assingedMksWtInfo = $namespacedModel::where('course_id', $request->course_id)
                    ->where('event_id', $request->event_id);

            if (!empty($request->sub_event_id)) {
                $assingedMksWtInfo = $assingedMksWtInfo->where('sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $assingedMksWtInfo = $assingedMksWtInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $assingedMksWtInfo = $assingedMksWtInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $assingedMksWtInfo = $assingedMksWtInfo->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'wt')
                    ->first();
//        End:: Calculate After CI Moderation  
//        
// get previous data
            $prevMksWtDataArr = ComdtModerationMarking::join('comdt_moderation_marking_lock', function($join) {
                        $join->on('comdt_moderation_marking_lock.course_id', 'comdt_moderation_marking.course_id');
                        $join->on('comdt_moderation_marking_lock.term_id', 'comdt_moderation_marking.term_id');
                        $join->on('comdt_moderation_marking_lock.event_id', 'comdt_moderation_marking.event_id');
                        $join->on('comdt_moderation_marking_lock.sub_event_id', 'comdt_moderation_marking.sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id');
                        $join->on('comdt_moderation_marking_lock.locked_by', 'comdt_moderation_marking.updated_by');
                    })
                    ->join('grading_system', 'grading_system.id', 'comdt_moderation_marking.grade_id')
                    ->where('comdt_moderation_marking.course_id', $request->course_id)
                    ->where('comdt_moderation_marking.term_id', $request->term_id)
                    ->where('comdt_moderation_marking.event_id', $request->event_id);

            if (!empty($request->sub_event_id)) {
                $prevMksWtDataArr = $prevMksWtDataArr->where('comdt_moderation_marking.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $prevMksWtDataArr = $prevMksWtDataArr->where('comdt_moderation_marking.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $prevMksWtDataArr = $prevMksWtDataArr->where('comdt_moderation_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $prevMksWtDataArr = $prevMksWtDataArr->where('comdt_moderation_marking.updated_by', Auth::user()->id)
                    ->select('comdt_moderation_marking.cm_id', 'comdt_moderation_marking.comdt_moderation', 'comdt_moderation_marking.mks'
                            , 'comdt_moderation_marking.wt', 'comdt_moderation_marking.percentage', 'grading_system.grade_name'
                            , 'grading_system.id as grade_id')
                    ->get();

            if (!$prevMksWtDataArr->isEmpty()) {
                foreach ($prevMksWtDataArr as $prevMksWtData) {
                    $prevMksWtArr[$prevMksWtData->cm_id] = $prevMksWtData->toArray();
                }
            }

//        Start:: Average Marking
            $avgDsMksWtArr = [];
            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $cmInfo) {
                    $totalDs = $dsMksSum = $dsWtSum = $dsPercentSum = 0;

                    if (!empty($dsDataList)) {
                        foreach ($dsDataList as $dsId => $dsInfo) {
                            $totalDs += (!empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 1 : 0);
                            $dsMksSum += (!empty($dsMksWtArr[$dsId][$cmId]['mks']) ? $dsMksWtArr[$dsId][$cmId]['mks'] : 0);

                            $dsWtSum += (!empty($dsMksWtArr[$dsId][$cmId]['wt']) ? $dsMksWtArr[$dsId][$cmId]['wt'] : 0);

                            $dsPercentSum += (!empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : 0);
                        }
                    }
                    if (!empty($totalDs)) {
                        $avgDsMksWtArr['mks'][$cmId] = $dsMksSum / $totalDs;
                        $avgDsMksWtArr['wt'][$cmId] = $dsWtSum / $totalDs;
                        $avgDsMksWtArr['percentage'][$cmId] = !empty($assingedMksWtInfo->mks_limit) ? ($avgDsMksWtArr['mks'][$cmId] / $assingedMksWtInfo->mks_limit) * 100 : 0;
                    }

                    $totalPercentage = !empty($avgDsMksWtArr['percentage'][$cmId]) ? Helper::numberFormatDigit2($avgDsMksWtArr['percentage'][$cmId]) : 0;
                    if (!empty($totalPercentage)) {
                        foreach ($gradeArr as $letter => $gradeRange) {
                            if ($totalPercentage == 100) {
                                $avgDsMksWtArr['grade'][$cmId] = "A+";
                                $avgDsMksWtArr['grade_id'][$cmId] = $gradeRange['id'];
                            }
                            if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                $avgDsMksWtArr['grade'][$cmId] = $letter;
                                $avgDsMksWtArr['grade_id'][$cmId] = $gradeRange['id'];
                            }
                        }
                    }

                    $cmArr[$cmId]['final_mks'] = !empty($prevMksWtArr[$cmId]['mks']) ? $prevMksWtArr[$cmId]['mks'] : (!empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : (!empty($avgDsMksWtArr['mks'][$cmId]) ? $avgDsMksWtArr['mks'][$cmId] : 0));
                    $cmArr[$cmId]['final_wt'] = !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : (!empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : (!empty($avgDsMksWtArr['wt'][$cmId]) ? $avgDsMksWtArr['wt'][$cmId] : 0));
                    $cmArr[$cmId]['final_percentage'] = !empty($prevMksWtArr[$cmId]['percentage']) ? $prevMksWtArr[$cmId]['percentage'] : (!empty($ciMksWtArr[$cmId]['percentage']) ? $ciMksWtArr[$cmId]['percentage'] : (!empty($avgDsMksWtArr['percentage'][$cmId]) ? $avgDsMksWtArr['percentage'][$cmId] : 0));
                    $cmArr[$cmId]['final_grade_name'] = !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : (!empty($ciMksWtArr[$cmId]['grade_name']) ? $ciMksWtArr[$cmId]['grade_name'] : (!empty($avgDsMksWtArr['grade'][$cmId]) ? $avgDsMksWtArr['grade'][$cmId] : 0));
                }
            }
            $cmArr = Common::getPosition($cmArr, 'final_percentage', 'position');
            if (empty($request->sort) || $request->sort == 'position') {
                if (!empty($cmArr)) {
                    usort($cmArr, function ($item1, $item2) {
                        if (!isset($item1['final_percentage'])) {
                            $item1['final_percentage'] = '';
                        }

                        if (!isset($item2['final_percentage'])) {
                            $item2['final_percentage'] = '';
                        }
                        return $item2['final_percentage'] <=> $item1['final_percentage'];
                    });
                }
            }

//        End:: Average Marking
//            echo '<pre>';            print_r($prevMksWtArr); exit;
        }

        if ($request->view == 'print') {
            return view('reportCrnt.eventResult.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'dsDataList'
                                    , 'numOfDs', 'cmArr', 'comdtMksInfo', 'dsMksWtArr', 'gradeInfo', 'sortByList'
                                    , 'avgDsMksWtArr', 'ciMksWtArr', 'assingedMksWtInfo', 'prevMksWtArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.eventResult.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'dsDataList'
                                    , 'numOfDs', 'cmArr', 'comdtMksInfo', 'dsMksWtArr', 'gradeInfo', 'sortByList'
                                    , 'avgDsMksWtArr', 'ciMksWtArr', 'assingedMksWtInfo', 'prevMksWtArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.eventResult.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'dsDataList'
                                    , 'numOfDs', 'cmArr', 'comdtMksInfo', 'dsMksWtArr', 'gradeInfo', 'sortByList'
                                    , 'avgDsMksWtArr', 'ciMksWtArr', 'assingedMksWtInfo', 'prevMksWtArr')), $fileName . '.xlsx');
        }

        return view('reportCrnt.eventResult.index', compact('activeTrainingYearList', 'courseList', 'termList'
                        , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                        , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'dsDataList'
                        , 'numOfDs', 'cmArr', 'comdtMksInfo', 'dsMksWtArr', 'gradeInfo', 'sortByList'
                        , 'avgDsMksWtArr', 'ciMksWtArr', 'assingedMksWtInfo', 'prevMksWtArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.eventResult.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();

        $html = view('reportCrnt.eventResult.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $html = view('reportCrnt.eventResult.getEvent', compact('eventList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getSubEventReportCrnt(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;
        $html = '';
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        if (sizeof($subEventList) > 1) {
            $html = view('reportCrnt.eventResult.getSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubEventReportCrnt(Request $request) {
        $html = '';
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();

        if (sizeof($subSubEventList) > 1) {
            $html = view('reportCrnt.eventResult.getSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventReportCrnt(Request $request) {
        $html = '';
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();


        if (sizeof($subSubSubEventList) > 1) {
            $html = view('reportCrnt.eventResult.getSubSubSubEvent', compact('subSubSubEventList'))->render();
        }
        return response()->json(['html' => $html]);
    }

    public function filter(Request $request) {

//        echo '<pre>';        print_r($request->all()); exit;

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'term_id.not_in' => __('label.THE_TERM_FIELD_IS_REQUIRED'),
            'event_id.not_in' => __('label.THE_EVENT_FIELD_IS_REQUIRED'),
        ];
        if (!empty($request->has_sub_event)) {
            $rules['sub_event_id'] = 'required|not_in:0';
            $messages['sub_event_id.not_in'] = __('label.THE_SUB_EVENT_FIELD_IS_REQUIRED');
        }
        if (!empty($request->has_sub_sub_event)) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_EVENT_FIELD_IS_REQUIRED');
        }
        if (!empty($request->has_sub_sub_sub_event)) {
            $rules['sub_sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_SUB_EVENT_FIELD_IS_REQUIRED');
        }

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id
                . '&event_id=' . $request->event_id . '&sub_event_id=' . $request->sub_event_id . '&sub_sub_event_id=' . $request->sub_sub_event_id
                . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('eventResultReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('eventResultReportCrnt?generate=true&' . $url);
    }

}
