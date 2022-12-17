<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\User;
use App\CriteriaWiseWt;
use App\GradingSystem;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\CmToSyn;
use App\EventAssessmentMarking;
use App\EventAssessmentMarkingLock;
use App\CiModerationMarking;
use App\CiObsnMarking;
use App\CiModerationMarkingLock;
use App\DsObsnMarking;
use App\DsMarkingGroup;
use App\DsObsnMarkingLimit;
use App\DsObsnMarkingLock;
use App\AssessmentActDeact;
use Auth;
use DB;
use Common;
use Validator;
use Illuminate\Http\Request;
use Response;

class DsObsnMarkingController extends Controller {

    public function index(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;
        $dsDeligationList = Common::getDsDeligationList();
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.DS_OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.DS_OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        // check all terms are closed 
        $openTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();
        if (empty($openTermInfo)) {
            $void['header'] = __('label.DS_OBSN_MARKING');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $courseList->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }


        $sortByList = ['svc' => __('label.WING'), 'official_name' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'syn' => __('label.SYN')];

        // get assigned ds obsn wt
        $assignedObsnInfo = DsObsnMarkingLimit::select('obsn', 'mks_limit', 'limit_percent')->where('course_id', $courseList->id)->first();

        // get grade system
        $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

        $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                ->join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                ->where('cm_group.order', '<=', '2')
                ->where('cm_basic_profile.status', '1')
                ->where('cm_basic_profile.course_id', $courseList->id)
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                , 'cm_basic_profile.full_name', 'rank.code as rank_name');
        if (!empty($request->sort_by) && $request->sort_by == 'official_name') {
            $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
        } elseif (!empty($request->sort_by) && ($request->sort_by == 'svc_alpha')) {
            $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
        } elseif (!empty($request->sort_by) && ($request->sort_by == 'syn')) {
            $cmDataArr = $cmDataArr->orderBy('cm_group.order', 'asc')
                    ->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc');
        } else {
            $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc');
        }
        $cmDataArr = $cmDataArr->get();



        $cmArr = [];
        if (!$cmDataArr->isEmpty()) {
            foreach ($cmDataArr as $cmData) {
                $cmArr[$cmData->id] = $cmData->toArray();
            }
        }

        //check if ds obsn is locked
        $dsObsnLockInfo = DsObsnMarkingLock::select('status')->where('course_id', $courseList->id)
                ->where('term_id', $openTermInfo->id)
                ->where('locked_by', Auth::user()->id)
                ->first();

//        echo '<pre>';
//        print_r(Auth::user()->id);
//        print_r($dsObsnLockInfo);
//        exit;

        $eventMksWtArr = $eventMksWtArr = $achieveMksWtArr = [];
        //event info
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseList->id)
                ->where('term_to_event.term_id', $openTermInfo->id)
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit'
                        , 'event_mks_wt.wt', 'event.has_sub_event')
                ->orderBy('event.event_code', 'asc')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if ($ev->has_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($ev->wt) ? $ev->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
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
                ->where('term_to_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_event.term_id', $openTermInfo->id)
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                        , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                if ($subEv->has_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
                } else {
                    if ($subEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                        $eventMksWtArr['total_marking_event'] += 1;
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
                ->where('term_to_sub_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_sub_event.term_id', $openTermInfo->id)
                ->where('sub_sub_event.status', '1')
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
                if ($subSubEv->has_sub_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                    if ($subSubEv->avg_marking == '0') {
                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                        $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                        $eventMksWtArr['total_marking_event'] += 1;
                    }
                }

                if ($subSubEv->avg_marking == '1') {
                    $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
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
                ->where('term_to_sub_sub_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_sub_sub_event.term_id', $openTermInfo->id)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                        , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event_to_sub_event.avg_marking')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                if ($subSubSubEv->avg_marking == '0') {
                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
                }
            }
        }




        $eventMksWtArr['total_wt_after_ds'] = (!empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0) + (!empty($assignedObsnInfo->ds_obsn_wt) ? $assignedObsnInfo->ds_obsn_wt : 0);

        $totalEventDsCount = 0;
        $eventDsCountInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $courseList->id)->where('marking_group.term_id', $openTermInfo->id)
                ->select('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id', DB::raw("COUNT(DISTINCT ds_marking_group.ds_id) as total_ds"))
                ->groupBy('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id')
                ->get();

        $eventDsCountArr = [];
        if (!$eventDsCountInfo->isEmpty()) {
            foreach ($eventDsCountInfo as $info) {
                $totalEventDsCount += (!empty($info->total_ds) ? $info->total_ds : 0);
            }
        }
        
//        echo '<pre>';
//        print_r($eventDsCountInfo->toArray());
//        print_r($totalEventDsCount);
//        exit;

        $eventMksLock = EventAssessmentMarkingLock::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->count();


        $ciModMksLock = CiModerationMarkingLock::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->count();

        //event mks wt info

        $eventWiseMksWtInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                    $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                    $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                    $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                    $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                })
                ->where('event_assessment_marking.course_id', $courseList->id)
                ->where('event_assessment_marking.term_id', $openTermInfo->id)
                ->whereNotNull('event_assessment_marking.mks')
                ->select('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                        , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"), DB::raw("AVG(event_assessment_marking.wt) as avg_wt"))
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
                $eventWiseMksWtArr[$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_wt'] = $eventMwInfo->avg_wt;
            }
        }
        $cmEventMksArr = [];
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                if (!empty($eventMksWtArr['mks_wt'])) {
                    foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                        foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                    $eventAvgWt = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;

                                    $TotalTermWt = $eventAvgWt;

                                    $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] = $TotalTermWt;

                                    $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                    $totalCount = 0;
                                    //count average where avg marking is enabled
                                    if (!empty($cmEventCountArr[$cmId][$eventId][$subEventId])) {
                                        if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                            if (array_key_exists($cmId, $cmEventCountArr)) {
                                                $totalCount = $cmEventCountArr[$cmId][$eventId][$subEventId];
                                            }

                                            $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;
                                            $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

                                            $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                            $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                            if ($totalCount != 0) {
                                                $assignedWt = $subEventWtLimit / $totalCount;
                                                $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                            }
                                        }
                                    }


                                    //term wise total
                                    $cmEventMksArr[$cmId]['achieved_wt'] = !empty($cmEventMksArr[$cmId]['achieved_wt']) ? $cmEventMksArr[$cmId]['achieved_wt'] : 0;
                                    $cmEventMksArr[$cmId]['achieved_wt'] += $TotalTermWt;

                                    $cmEventMksArr[$cmId]['assigned_wt'] = !empty($cmEventMksArr[$cmId]['assigned_wt']) ? $cmEventMksArr[$cmId]['assigned_wt'] : 0;
                                    if (!empty($TotalTermWt)) {
                                        $cmEventMksArr[$cmId]['assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                    }

                                    $percentage = 0;
                                    if (!empty($cmEventMksArr[$cmId]['assigned_wt'])) {
                                        $percentage = ($cmEventMksArr[$cmId]['achieved_wt'] / $cmEventMksArr[$cmId]['assigned_wt']) * 100;
                                    }
                                    $cmEventMksArr[$cmId]['percent'] = $percentage;
                                }
                            }
                        }
                    }
                }
            }
        }

        $dsObsnMksInfo = DsObsnMarking::where('ds_obsn_marking.course_id', $courseList->id)
                ->where('ds_obsn_marking.term_id', $openTermInfo->id)
                ->where('ds_obsn_marking.updated_by', Auth::user()->id)
                ->select('ds_obsn_marking.obsn_mks', 'ds_obsn_marking.obsn_wt', 'ds_obsn_marking.cm_id')
                ->get();

        $prevMksWtArr = [];
        if (!$dsObsnMksInfo->isEmpty()) {
            foreach ($dsObsnMksInfo as $info) {
                $prevMksWtArr[$info->cm_id] = $info->toArray();
            }
        }

        // if has ci obsn marking
        $ciObsnMarkingInfo = CiObsnMarking::where('course_id', $courseList->id)
                ->whereNotNull('ci_obsn')
                ->get();

        $prevActDeactInfo = AssessmentActDeact::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->where('criteria', '3')
                        ->where('status', '1')->first();

        $autoSave = empty($dsObsnLockInfo) && !empty($prevActDeactInfo) && !empty($assignedObsnInfo) && !empty($cmArr) && !empty($totalEventDsCount) && !empty($eventMksLock) && ($totalEventDsCount == $eventMksLock) ? 1 : 0;

        return view('dsObsnMarking.index')->with(compact('activeTrainingYearInfo', 'courseList', 'openTermInfo'
                                , 'assignedObsnInfo', 'gradeInfo', 'cmArr', 'dsObsnLockInfo', 'eventMksWtArr'
                                , 'ciModMksLock', 'cmEventMksArr', 'prevMksWtArr', 'sortByList', 'autoSave'
                                , 'eventMksLock', 'totalEventDsCount', 'ciObsnMarkingInfo', 'dsObsnMksInfo'
                                , 'prevActDeactInfo'));
    }

    public function filter(Request $request) {
        $dsDeligationList = Common::getDsDeligationList();
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        // check all terms are closed 
        $openTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();
        if (empty($openTermInfo)) {
            $void['header'] = __('label.OBSN_MARKING');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $courseList->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }


        $sortByList = ['svc' => __('label.WING'), 'official_name' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'syn' => __('label.SYN')];

        // get assigned ds obsn wt
        $assignedObsnInfo = DsObsnMarkingLimit::select('obsn', 'mks_limit', 'limit_percent')->where('course_id', $courseList->id)->first();

        // get grade system
        $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

        $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                ->join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                ->where('cm_group.order', '<=', '2')
                ->where('cm_basic_profile.status', '1')
                ->where('cm_basic_profile.course_id', $courseList->id)
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                , 'cm_basic_profile.full_name', 'rank.code as rank_name');
        if (!empty($request->sort_by) && $request->sort_by == 'official_name') {
            $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
        } elseif (!empty($request->sort_by) && ($request->sort_by == 'svc_alpha')) {
            $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
        } elseif (!empty($request->sort_by) && ($request->sort_by == 'syn')) {
            $cmDataArr = $cmDataArr->orderBy('cm_group.order', 'asc')
                    ->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc');
        } else {
            $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc');
        }
        $cmDataArr = $cmDataArr->get();




        $cmArr = [];
        if (!$cmDataArr->isEmpty()) {
            foreach ($cmDataArr as $cmData) {
                $cmArr[$cmData->id] = $cmData->toArray();
            }
        }

        //check if ds obsn is locked
        $dsObsnLockInfo = DsObsnMarkingLock::select('status')->where('course_id', $courseList->id)
                ->where('term_id', $openTermInfo->id)
                ->where('locked_by', Auth::user()->id)
                ->first();

        $eventMksWtArr = $eventMksWtArr = $achieveMksWtArr = [];
        //event info
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseList->id)
                ->where('term_to_event.term_id', $openTermInfo->id)
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit'
                        , 'event_mks_wt.wt', 'event.has_sub_event')
                ->orderBy('event.event_code', 'asc')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if ($ev->has_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($ev->wt) ? $ev->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
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
                ->where('term_to_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_event.term_id', $openTermInfo->id)
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                        , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                if ($subEv->has_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
                } else {
                    if ($subEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                        $eventMksWtArr['total_marking_event'] += 1;
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
                ->where('term_to_sub_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_sub_event.term_id', $openTermInfo->id)
                ->where('sub_sub_event.status', '1')
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
                if ($subSubEv->has_sub_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                    if ($subSubEv->avg_marking == '0') {
                        $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                        $eventMksWtArr['total_wt'] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                        $eventMksWtArr['total_mks_limit'] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                        $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                        $eventMksWtArr['total_marking_event'] += 1;
                    }
                }

                if ($subSubEv->avg_marking == '1') {
                    $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
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
                ->where('term_to_sub_sub_sub_event.course_id', $courseList->id)
                ->where('term_to_sub_sub_sub_event.term_id', $openTermInfo->id)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                        , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event_to_sub_event.avg_marking')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                if ($subSubSubEv->avg_marking == '0') {
                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                    $eventMksWtArr['total_marking_event'] = !empty($eventMksWtArr['total_marking_event']) ? $eventMksWtArr['total_marking_event'] : 0;
                    $eventMksWtArr['total_marking_event'] += 1;
                }
            }
        }




        $eventMksWtArr['total_wt_after_ds'] = (!empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0) + (!empty($assignedObsnInfo->ds_obsn_wt) ? $assignedObsnInfo->ds_obsn_wt : 0);

        $totalEventDsCount = 0;
        $eventDsCountInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $courseList->id)->where('marking_group.term_id', $openTermInfo->id)
                ->select('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id', DB::raw("COUNT(DISTINCT ds_marking_group.ds_id) as total_ds"))
                ->groupBy('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id')
                ->get();

        $eventDsCountArr = [];
        if (!$eventDsCountInfo->isEmpty()) {
            foreach ($eventDsCountInfo as $info) {
                $totalEventDsCount += (!empty($info->total_ds) ? $info->total_ds : 0);
            }
        }

        $eventMksLock = EventAssessmentMarkingLock::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->count();


        $ciModMksLock = CiModerationMarkingLock::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->count();

        //event mks wt info

        $eventWiseMksWtInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                    $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                    $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                    $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                    $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                })
                ->where('event_assessment_marking.course_id', $courseList->id)
                ->where('event_assessment_marking.term_id', $openTermInfo->id)
                ->whereNotNull('event_assessment_marking.mks')
                ->select('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                        , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"), DB::raw("AVG(event_assessment_marking.wt) as avg_wt"))
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
                $eventWiseMksWtArr[$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_wt'] = $eventMwInfo->avg_wt;
            }
        }
        $cmEventMksArr = [];
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                if (!empty($eventMksWtArr['mks_wt'])) {
                    foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                        foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                    $eventAvgWt = !empty($eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksWtArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;

                                    $TotalTermWt = $eventAvgWt;

                                    $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] = $TotalTermWt;

                                    $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                    $totalCount = 0;
                                    //count average where avg marking is enabled
                                    if (!empty($cmEventCountArr[$cmId][$eventId][$subEventId])) {
                                        if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                            if (array_key_exists($cmId, $cmEventCountArr)) {
                                                $totalCount = $cmEventCountArr[$cmId][$eventId][$subEventId];
                                            }

                                            $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;
                                            $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

                                            $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                            $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                            if ($totalCount != 0) {
                                                $assignedWt = $subEventWtLimit / $totalCount;
                                                $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                            }
                                        }
                                    }


                                    //term wise total
                                    $cmEventMksArr[$cmId]['achieved_wt'] = !empty($cmEventMksArr[$cmId]['achieved_wt']) ? $cmEventMksArr[$cmId]['achieved_wt'] : 0;
                                    $cmEventMksArr[$cmId]['achieved_wt'] += $TotalTermWt;

                                    $cmEventMksArr[$cmId]['assigned_wt'] = !empty($cmEventMksArr[$cmId]['assigned_wt']) ? $cmEventMksArr[$cmId]['assigned_wt'] : 0;
                                    if (!empty($TotalTermWt)) {
                                        $cmEventMksArr[$cmId]['assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                    }

                                    $percentage = 0;
                                    if (!empty($cmEventMksArr[$cmId]['assigned_wt'])) {
                                        $percentage = ($cmEventMksArr[$cmId]['achieved_wt'] / $cmEventMksArr[$cmId]['assigned_wt']) * 100;
                                    }
                                    $cmEventMksArr[$cmId]['percent'] = $percentage;
                                }
                            }
                        }
                    }
                }
            }
        }


        $dsObsnMksInfo = DsObsnMarking::where('ds_obsn_marking.course_id', $courseList->id)
                ->where('ds_obsn_marking.term_id', $openTermInfo->id)
                ->where('ds_obsn_marking.updated_by', Auth::user()->id)
                ->select('ds_obsn_marking.obsn_mks', 'ds_obsn_marking.obsn_wt', 'ds_obsn_marking.cm_id')
                ->get();

        $prevMksWtArr = [];
        if (!$dsObsnMksInfo->isEmpty()) {
            foreach ($dsObsnMksInfo as $info) {
                $prevMksWtArr[$info->cm_id] = $info->toArray();
            }
        }

        $prevActDeactInfo = AssessmentActDeact::where('course_id', $courseList->id)
                        ->where('term_id', $openTermInfo->id)->where('criteria', '3')
                        ->where('status', '1')->first();


        $autoSave = empty($dsObsnLockInfo) && !empty($prevActDeactInfo) && !empty(!empty($assignedObsnInfo)) && !empty($cmArr) && !empty($totalEventDsCount) && !empty($eventMksLock) && ($totalEventDsCount == $eventMksLock) ? 1 : 0;

        $html = view('dsObsnMarking.showCmMarkingList', compact('activeTrainingYearInfo', 'courseList', 'openTermInfo'
                        , 'assignedObsnInfo', 'gradeInfo', 'cmArr', 'dsObsnLockInfo', 'eventMksWtArr'
                        , 'ciModMksLock', 'cmEventMksArr', 'prevMksWtArr', 'sortByList', 'autoSave'
                        , 'eventMksLock', 'totalEventDsCount', 'prevActDeactInfo'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveObsnMarking(Request $request) {
// Validation
        $rules = $message = $errors = [];
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
        $sum = 0;
        $cmName = $request->cm_name;
        if (!empty($request->mks_wt)) {
            foreach ($request->mks_wt as $key => $wtInfo) {
                if ($request->data_id == '2') {
                    $rules['mks_wt.' . $key . '.obsn_mks'] = 'required|lt:' . $wtInfo['high_range'] . '|gt:' . $wtInfo['low_range'];
                    $message['mks_wt.' . $key . '.obsn_mks' . '.required'] = __('label.MKS_FIELD_IS_REQUIRED_FOR', ['CM_name' => $cmName[$key]]);
                    $message['mks_wt.' . $key . '.obsn_mks' . '.lt'] = __('label.YOUR_GIVEN_MKS_CAN_NOT_GREATER_THAN_HIGHEST_MKS_FOR', ['cm_name' => $cmName[$key]]);
                    $message['mks_wt.' . $key . '.obsn_mks' . '.gt'] = __('label.YOUR_GIVEN_MKS_CAN_NOT_LESS_THAN_LOWEST_MKS_FOR', ['cm_name' => $cmName[$key]]);
                }


                $mks = !empty($wtInfo['obsn_mks']) ? $wtInfo['obsn_mks'] : 0;
                $sum += $mks;
            }
        }
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $prevActDeactInfo = AssessmentActDeact::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)->where('criteria', '3')
                        ->where('status', '1')->first();


        if (empty($prevActDeactInfo)) {
            $errors = __('label.ASSESSMENT_IS_DEACTIVATED');
        }
        if (!empty($request->auto_saving) && $request->auto_saving == 1 && $sum == 0) {
            $errors = __('label.PUT_MKS_FOR_ATLEAST_ONE_CM');
        }

        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 401);
        }
// End validation
//echo '<pre>';
//print_r($request->wt);
//exit;
        $data = [];
        $i = 0;
        if (!empty($request->mks_wt)) {
            foreach ($request->mks_wt as $cmId => $wtInfo) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $request->term_id;
                $data[$i]['cm_id'] = $cmId ?? 0;
                $data[$i]['obsn_mks'] = $wtInfo['obsn_mks'] ?? null;
                $data[$i]['obsn_wt'] = $wtInfo['obsn_wt'] ?? null;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }
// Save data

        DB::beginTransaction();

        try {
            DsObsnMarking::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('updated_by', Auth::user()->id)
                    ->delete();
            if (DsObsnMarking::insert($data)) {
                $successMsg = __('label.OBSN_MARKING_HAS_BEEN_ASSIGNED_SUCCESSFULLY');
                $errorMsg = __('label.OBSN_MARKING_CUOLD_NOT_BE_ASSIGNED');

                if ($request->data_id == '2') {
                    $target = new DsObsnMarkingLock;

                    $target->course_id = $request->course_id;
                    $target->term_id = $request->term_id;
                    $target->status = 1;
                    $target->locked_at = date('Y-m-d H:i:s');
                    $target->locked_by = Auth::user()->id;
                    $target->save();

                    $successMsg = __('label.OBSN_MARKING_HAS_BEEN_ASSIGNED_AND_LOCKED_SUCCESSFULLY');
                    $errorMsg = __('label.OBSN_MARKING_COULD_NOT_BE_ASSIGNED_AND_LOCKED');
                }
            }
            DB::commit();
            return Response::json(['success' => true, 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'message' => $errorMsg], 401);
        }
    }

    public function getRequestForUnlockModal(Request $request) {
        $view = view('dsObsnMarking.showRequestForUnlockModal')->render();
        return response()->json(['html' => $view]);
    }

    public function saveRequestForUnlock(Request $request) {

// validation
        $rules = [
            'unlock_message' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $dsObsnLockInfo = DsObsnMarkingLock::select('id')->where('course_id', $request->course_id)
                        ->where('locked_by', Auth::user()->id)->first();

        if (!empty($dsObsnLockInfo)) {
            $target = DsObsnMarkingLock::where('id', $dsObsnLockInfo->id)
                    ->update(['status' => '2', 'unlock_message' => $request->unlock_message]);
            if ($target) {
                return Response::json(['success' => true], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.REQUEST_FOR_UNLOCK_COULD_NOT_BE_SENT_TO_COMDT')), 401);
            }
        }
    }

    public function requestCourseSatatusSummary(Request $request) {
        $loadView = 'dsObsnMarking.showCourseStatusSummary';
        return Common::requestCourseSatatusSummary($request, $loadView);
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'dsObsnMarking.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

    public function clearMarking(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;

        $target = DsObsnMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('updated_by', Auth::user()->id)
                ->delete();
//        echo '<pre>';
//        print_r($target->toArray());
//        exit;

        if ($target) {
            return Response::json(['success' => true, 'message' => __('label.CLEAR_MARKING_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.CLEAR_MARKING_UNSUCCESSFUL')), 401);
        }
    }

}
