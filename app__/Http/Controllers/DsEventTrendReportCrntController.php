<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\Term;
use App\TermToEvent;
use App\DsMarkingGroup;
use App\CmMarkingGroup;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\EventAssessmentMarking;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\CmBasicProfile;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Illuminate\Http\Request;

class DsEventTrendReportCrntController extends Controller {

    private $controller = 'DsEventTrendReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        $termList = array('0' => __('label.ALL_TERMS')) + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();

        $eventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event', 'event.id', '=', 'marking_group.event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('marking_group.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + $eventList;

        $subEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_event', 'sub_event.id', '=', 'marking_group.sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $subEventList = $subEventList->where('marking_group.term_id', $request->term_id);
        }
        $subEventList = $subEventList->where('marking_group.event_id', $request->event_id)
                ->where('sub_event.status', '1')
                ->orderBy('sub_event.event_code', 'asc')
                ->pluck('sub_event.event_code', 'sub_event.id')
                ->toArray();
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;

        $subSubEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'marking_group.sub_sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $subSubEventList = $subSubEventList->where('marking_group.term_id', $request->term_id);
        }
        $subSubEventList = $subSubEventList->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event.status', '1')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                ->toArray();
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;

        $subSubSubEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'marking_group.sub_sub_sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $subSubSubEventList = $subSubSubEventList->where('marking_group.term_id', $request->term_id);
        }
        $subSubSubEventList = $subSubSubEventList->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.sub_event_id', $request->sub_event_id)
                ->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('sub_sub_sub_event.status', '1')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                ->toArray();
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;

        $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->where('marking_group.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
        }
        $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $cmArr = $cmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cmArr = $cmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cmArr = $cmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $cmArr = $cmArr->where('cm_basic_profile.status', '1')
                ->pluck('cm_name', 'cm_basic_profile.id')
                ->toArray();

        $eventMksWtArr = $eventWiseMksArr = $eventWiseDsMksArr = $dsPercentageArr = [];
        $cmIds = $selectedCms = [];
        if ($request->generate == 'true') {
//            $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
//                            ->where('term_to_event.course_id', $request->course_id)
//                            ->where('event.status', '1')
//                            ->orderBy('event.event_code', 'asc')
//                            ->pluck('event.event_code', 'event.id')
//                            ->toArray();

            $cmIds = !empty($request->cm_id) ? explode(",", $request->cm_id) : [];

            $selectedCms = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->where('marking_group.course_id', $courseList->id);
            if (!empty($request->term_id)) {
                $selectedCms = $selectedCms->where('marking_group.term_id', $request->term_id);
            }
            $selectedCms = $selectedCms->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $selectedCms = $selectedCms->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $selectedCms = $selectedCms->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $selectedCms = $selectedCms->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $selectedCms = $selectedCms->whereIn('cm_basic_profile.id', $cmIds)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_name', 'cm_basic_profile.id')
                    ->toArray();

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
            $assingedMksWtInfo = $assingedMksWtInfo->select('mks_limit')
                    ->first();

            //START:: Event Wise Mks
            $eventWiseMksArr = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                        $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                        $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                        $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                        $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                    })
                    ->where('event_assessment_marking.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventWiseMksArr = $eventWiseMksArr->where('event_assessment_marking.term_id', $request->term_id);
            }
            $eventWiseMksArr = $eventWiseMksArr->where('event_assessment_marking.event_id', $request->event_id)
                    ->where('event_assessment_marking.sub_event_id', $request->sub_event_id)
                    ->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id)
                    ->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                    ->whereIn('event_assessment_marking.cm_id', $cmIds)
                    ->where('event_assessment_marking.updated_by', '<>', Auth::user()->id)
                    ->whereNotNull('event_assessment_marking.mks')
                    ->select('event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
                    ->groupBy('event_assessment_marking.cm_id')
                    ->pluck('avg_mks', 'event_assessment_marking.cm_id')
                    ->toArray();
            //END:: Event Wise Mks
            //START:: Event Wise DS Mks
            $eventWiseDsMksArr = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                        $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                        $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                        $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                        $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                    })
                    ->where('event_assessment_marking.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventWiseDsMksArr = $eventWiseDsMksArr->where('event_assessment_marking.term_id', $request->term_id);
            }
            $eventWiseDsMksArr = $eventWiseDsMksArr->where('event_assessment_marking.updated_by', Auth::user()->id)
                    ->where('event_assessment_marking.event_id', $request->event_id)
                    ->where('event_assessment_marking.sub_event_id', $request->sub_event_id)
                    ->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id)
                    ->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                    ->whereIn('event_assessment_marking.cm_id', $cmIds)
                    ->whereNotNull('event_assessment_marking.mks')
                    ->pluck('event_assessment_marking.mks', 'event_assessment_marking.cm_id')
                    ->toArray();

//            echo '<pre>';
//            print_r($eventWiseMksArr);
//            print_r($eventWiseDsMksArr);
//            exit;
            //END:: Event Wise DS Mks
            //over all avg event mks%
            if (!empty($eventWiseMksArr)) {
                foreach ($eventWiseMksArr as $cmId => $mksInfo) {
                    $eventMks = !empty($mksInfo) ? $mksInfo : 0;
                    $eventMksLimit = !empty($assingedMksWtInfo->mks_limit) ? $assingedMksWtInfo->mks_limit : 0;
                    $eventPercentage = 0;
                    if (!empty($eventMksLimit)) {
                        $eventPercentage = ($eventMks / $eventMksLimit) * 100;
                    }

                    $dsPercentageArr[$cmId]['over_all'] = $eventPercentage;
                    $dsMksMinMaxArr[$cmId] = $eventPercentage;
                }
            }
            //DS event mks%
            if (!empty($eventWiseDsMksArr)) {
                foreach ($eventWiseDsMksArr as $cmId => $mksInfo) {
                    $eventMks = !empty($mksInfo) ? $mksInfo : 0;
                    $eventMksLimit = !empty($assingedMksWtInfo->mks_limit) ? $assingedMksWtInfo->mks_limit : 0;
                    $eventPercentage = 0;
                    if (!empty($eventMksLimit)) {
                        $eventPercentage = ($eventMks / $eventMksLimit) * 100;
                    }

                    $dsPercentageArr[$cmId]['ds'] = $eventPercentage;
                    $dsMksMinMaxArr[$cmId] = $eventPercentage;
                }
            }

            if (!empty($selectedCms)) {
                foreach ($selectedCms as $cmId => $cm) {
                    if (!empty($eventWiseMksArr) && !array_key_exists($cmId, $eventWiseMksArr)) {
                        $dsPercentageArr[$cmId]['over_all'] = 0;
                        $dsMksMinMaxArr[$cmId] = 0;
                    }
                    if (!empty($eventWiseDsMksArr) && !array_key_exists($cmId, $eventWiseDsMksArr)) {
                        $dsPercentageArr[$cmId]['ds'] = 0;
                        $dsMksMinMaxArr[$cmId] = 0;
                    }
                }
            }

            if (!empty($dsMksMinMaxArr)) {
                $dsPercentageArr['grand_max_min']['max'] = !empty($dsMksMinMaxArr) ? max($dsMksMinMaxArr) + (max($dsMksMinMaxArr) + 5 < 100 ? 5 : 0) : 0;
                $dsPercentageArr['grand_max_min']['min'] = !empty($dsMksMinMaxArr) ? min($dsMksMinMaxArr) - (min($dsMksMinMaxArr) - 5 > 0 ? 5 : 0) : 0;
            }
        }


        return view('reportCrnt.dsEventTrend.index', compact('activeTrainingYearList', 'courseList'
                        , 'cmArr', 'eventList', 'cmIds', 'dsPercentageArr', 'selectedCms', 'termList'
                        , 'subEventList', 'subSubEventList', 'subSubSubEventList', 'eventWiseDsMksArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();

        $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('cm_basic_profile.status', '1')
                ->pluck('cm_name', 'cm_basic_profile.id')
                ->toArray();

        $eventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event', 'event.id', '=', 'marking_group.event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('marking_group.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + $eventList;

        $html2 = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        $showEventView = view('reportCrnt.dsEventTrend.getCourseWiseEvent', compact('eventList'))->render();
        $html = view('reportCrnt.dsEventTrend.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html, 'html2' => $html2, 'showEventView' => $showEventView]);
    }

    public function getCourseWiseCmEvent(Request $request) {

        $eventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event', 'event.id', '=', 'marking_group.event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('marking_group.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + $eventList;

        $cmArr = [];
        if (sizeof($eventList) == 1) {
            $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
            }
            $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_name', 'cm_basic_profile.id')
                    ->toArray();
        }

        $html = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        $showEventView = view('reportCrnt.dsEventTrend.getCourseWiseEvent', compact('eventList'))->render();
        return Response::json(['html' => $html, 'showEventView' => $showEventView]);
    }

    public function getCourseWiseCmSubEvent(Request $request) {

        $subEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_event', 'sub_event.id', '=', 'marking_group.sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subEventList = $subEventList->where('marking_group.term_id', $request->term_id);
        }
        $subEventList = $subEventList->where('marking_group.event_id', $request->event_id)
                ->where('sub_event.status', '1')
                ->orderBy('sub_event.event_code', 'asc')
                ->pluck('sub_event.event_code', 'sub_event.id')
                ->toArray();
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;

        $cmArr = [];
        if (sizeof($subEventList) == 1) {
            $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
            }
            $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('marking_group.event_id', $request->event_id)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_name', 'cm_basic_profile.id')
                    ->toArray();
        }

        $html = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        $showSubEventView = view('reportCrnt.dsEventTrend.getCourseWiseSubEvent', compact('subEventList'))->render();
        return Response::json(['html' => $html, 'showSubEventView' => $showSubEventView]);
    }

    public function getCourseWiseCmSubSubEvent(Request $request) {

        $subSubEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'marking_group.sub_sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubEventList = $subSubEventList->where('marking_group.term_id', $request->term_id);
        }
        $subSubEventList = $subSubEventList->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event.status', '1')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                ->toArray();
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;
        $cmArr = [];
        if (sizeof($subSubEventList) == 1) {
            $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
            }
            $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('marking_group.event_id', $request->event_id)
                    ->where('marking_group.sub_event_id', $request->sub_event_id)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_name', 'cm_basic_profile.id')
                    ->toArray();
        }
        $html = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        $showSubSubEventView = view('reportCrnt.dsEventTrend.getCourseWiseSubSubEvent', compact('subSubEventList'))->render();
        return Response::json(['html' => $html, 'showSubSubEventView' => $showSubSubEventView]);
    }

    public function getCourseWiseCmSubSubSubEvent(Request $request) {

        $subSubSubEventList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'marking_group.sub_sub_sub_event_id')
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubSubEventList = $subSubSubEventList->where('marking_group.term_id', $request->term_id);
        }
        $subSubSubEventList = $subSubSubEventList->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.sub_event_id', $request->sub_event_id)
                ->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('sub_sub_sub_event.status', '1')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                ->toArray();
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;

        $cmArr = [];
        if (sizeof($subSubSubEventList) == 1) {
            $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
            }
            $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                    ->where('marking_group.event_id', $request->event_id)
                    ->where('marking_group.sub_event_id', $request->sub_event_id)
                    ->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id)
                    ->where('cm_basic_profile.status', '1')
                    ->pluck('cm_name', 'cm_basic_profile.id')
                    ->toArray();
        }
        $html = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        $showSubSubSubEventView = view('reportCrnt.dsEventTrend.getCourseWiseSubSubSubEvent', compact('subSubSubEventList'))->render();
        return Response::json(['html' => $html, 'showSubSubSubEventView' => $showSubSubSubEventView]);
    }

    public function getCourseWiseCm(Request $request) {
        $cmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                ->join('ds_marking_group', 'ds_marking_group.marking_group_id', '=', 'marking_group.id')
                ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $cmArr = $cmArr->where('marking_group.term_id', $request->term_id);
        }
        $cmArr = $cmArr->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.sub_event_id', $request->sub_event_id)
                ->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                ->where('cm_basic_profile.status', '1')
                ->pluck('cm_name', 'cm_basic_profile.id')
                ->toArray();
        $html = view('reportCrnt.dsEventTrend.getCourseWiseCm', compact('cmArr'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {
        $hasSubEvent = !empty($request->has['sub_event']) ? $request->has['sub_event'] : 0;
        $hasSubSubEvent = !empty($request->has['sub_sub_event']) ? $request->has['sub_sub_event'] : 0;
        $hasSubSubSubEvent = !empty($request->has['sub_sub_sub_event']) ? $request->has['sub_sub_sub_event'] : 0;
        $cmIds = !empty($request->cm_id) ? implode(",", $request->cm_id) : '';

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
            'range_start' => 'lt:range_end|gte:0',
            'range_end' => 'lte:100|gt:range_start',
        ];

        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'event_id.not_in' => __('label.THE_EVENT_IS_REQUIRED'),
        ];
        if (!empty($hasSubEvent)) {
            $rules['sub_event_id'] = 'required|not_in:0';
            $messages['sub_event_id.not_in'] = __('label.THE_SUB_EVENT_IS_REQUIRED');
        }
        if (!empty($hasSubSubEvent)) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_EVENT_IS_REQUIRED');
        }
        if (!empty($hasSubSubSubEvent)) {
            $rules['sub_sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_SUB_EVENT_IS_REQUIRED');
        }
        if (empty($cmIds)) {
            $rules['cm_id'] = 'required';
            $messages['cm_id.required'] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_CM');
        }

        $request->range_start = !empty($request->range_start) ? $request->range_start : 0;
        $request->range_end = !empty($request->range_end) ? $request->range_end : 100;
        

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&term_id=' . $request->term_id . '&event_id=' . $request->event_id . '&sub_event_id=' . $request->sub_event_id
                . '&sub_sub_event_id=' . $request->sub_sub_event_id . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id
                . '&cm_id=' . $cmIds
                . '&range_start=' . $request->range_start . '&range_end=' . $request->range_end;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('dsEventTrendReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('dsEventTrendReportCrnt?generate=true&' . $url);
    }

}
