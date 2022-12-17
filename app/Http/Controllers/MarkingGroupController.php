<?php

namespace App\Http\Controllers;

use App\Course;
use App\TermToCourse;
use App\TrainingYear;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\Event;
use App\EventToEventGroup;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\MarkingGroup;
use App\CmMarkingGroup;
use App\DsMarkingGroup;
use App\CmGroup;
use App\CmGroupToCourse;
use App\DsGroup;
use App\DsGroupToCourse;
use App\SynToCourse;
use App\CmBasicProfile;
use App\SynToSubSyn;
use App\CmToSyn;
use App\EventAssessmentMarking;
use App\User;
use App\CmGroupMemberTemplate;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class MarkingGroupController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.ASSIGN_MARKING_GROUP');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $activeCourse->id)
                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
        $eventGroupList = EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                ->where('event_to_event_group.course_id', $activeCourse->id)
                ->where('event_group.status', '1')
                ->orderBy('event_group.order', 'asc')
                ->pluck('event_group.name', 'event_group.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')];

        return view('markingGroup.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'eventGroupList'
                                , 'termList', 'eventList'));
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
        $eventGroupList = EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                ->where('event_to_event_group.course_id', $request->course_id)
                ->where('event_group.status', '1')
                ->orderBy('event_group.order', 'asc')
                ->pluck('event_group.name', 'event_group.id')
                ->toArray();

        $html = view('markingGroup.showTerm', compact('termList', 'eventGroupList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();


        $html = view('markingGroup.showEvent', compact('eventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubEventCmDs(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_event.course_id');
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $has = Event::where('id', $request->event_id)->select('has_ds_assesment', 'has_sub_event')->first();

        if ((!empty($has->has_ds_assesment))) {
            $requiredEvent['sub'] = 0;
            $requiredEvent['sub_sub'] = 0;
            $requiredEvent['sub_sub_sub'] = 0;

            $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                            ->where('event_to_event_group.course_id', $request->course_id)
                            ->where('event_to_event_group.event_id', $request->event_id)
                            ->where('event_group.status', '1')
                            ->orderBy('event_group.order', 'asc')
                            ->pluck('event_group.name', 'event_group.id')
                            ->toArray();

            $html = view('markingGroup.showCmDs', compact('eventGroupList', 'has', 'requiredEvent'))->render();
        } else {
            $html = view('markingGroup.showSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubEventCmDs(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();
        $has = EventToSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->select('has_ds_assesment', 'has_sub_sub_event')->first();
//        echo '<pre>';        print_r($hasDsAssesment);        exit;

        if ((!empty($has->has_ds_assesment))) {
            $requiredEvent['sub'] = 1;
            $requiredEvent['sub_sub'] = 0;
            $requiredEvent['sub_sub_sub'] = 0;
            $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                            ->where('event_to_event_group.course_id', $request->course_id)
                            ->where('event_to_event_group.event_id', $request->event_id)
                            ->where('event_group.status', '1')
                            ->orderBy('event_group.order', 'asc')
                            ->pluck('event_group.name', 'event_group.id')
                            ->toArray();

//            echo '<pre>';            print_r($eventGroupList);exit;

            $html = view('markingGroup.showCmDs', compact('eventGroupList', 'has', 'requiredEvent'))->render();
        } else {
            $html = view('markingGroup.showSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventCmDs(Request $request) {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                            $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        $has = EventToSubSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->select('has_ds_assesment', 'has_sub_sub_sub_event')->first();

        if ((!empty($has->has_ds_assesment))) {
            $requiredEvent['sub'] = 1;
            $requiredEvent['sub_sub'] = 1;
            $requiredEvent['sub_sub_sub'] = 0;

            $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                            ->where('event_to_event_group.course_id', $request->course_id)
                            ->where('event_to_event_group.event_id', $request->event_id)
                            ->where('event_group.status', '1')
                            ->orderBy('event_group.order', 'asc')
                            ->pluck('event_group.name', 'event_group.id')
                            ->toArray();

            $html = view('markingGroup.showCmDs', compact('eventGroupList', 'has', 'requiredEvent'))->render();
        } else {
            $html = view('markingGroup.showSubSubSubEvent', compact('subSubSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getCmDs(Request $request) {
        $requiredEvent['sub'] = 1;
        $requiredEvent['sub_sub'] = 1;
        $requiredEvent['sub_sub_sub'] = 1;

        $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                        ->where('event_to_event_group.course_id', $request->course_id)
                        ->where('event_to_event_group.event_id', $request->event_id)
                        ->where('event_group.status', '1')
                        ->orderBy('event_group.order', 'asc')
                        ->pluck('event_group.name', 'event_group.id')
                        ->toArray();

        $html = view('markingGroup.showCmDs', compact('eventGroupList', 'requiredEvent'))->render();


        return response()->json(['html' => $html]);
    }

    public function getCmDsSelection(Request $request) {
//      // Start :: CM Selection
        //for get syn wise cm
        $synList = ['0' => __('label.SELECT_SYN_OPT')] + CmGroupMemberTemplate::join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->where('cm_group_member_template.course_id', $request->course_id)
                        ->where('cm_group.type', 1)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();


        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')];

        //for get Group Template cm
        $cmGroupList = ['0' => __('label.SELECT_CM_GROUP_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)
                        ->where('cm_group.status', '1')
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')
                        ->toArray();

        //for get Individual Searched cm
        $nameArr = CmBasicProfile::select('personal_no')->get();


        $submitFrom = 'Individual';
        $selectionClass = 'individual-search';



        //selected cm of this group
        $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('cm_marking_group.cm_id')
                ->toArray();

        //selected cm of other groups
        $prevOtherGroupCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'cm_marking_group.cm_id')->toArray();

        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('cm_basic_profile.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();


        $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->whereIn('cm_basic_profile.id', $prevCmArr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        // End :: CM Selection
        // Start :: DS Selection
        //for get Group Template DS
        $dsGroupList = ['0' => __('label.SELECT_DS_GROUP_OPT')] + DsGroupToCourse::join('ds_group', 'ds_group.id', 'ds_group_to_course.ds_group_id')
                        ->where('ds_group_to_course.course_id', $request->course_id)
                        ->where('ds_group.status', '1')
                        ->orderBy('ds_group.order', 'asc')
                        ->pluck('ds_group.name', 'ds_group.id')
                        ->toArray();

        //for get Individual Searched DS

        $submitFromDs = 'DsIndividual';
        $selectionClassDs = 'ds-individual-search';

        //selected ds of this group
        $prevDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevDsArr = $prevDsArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('ds_marking_group.ds_id')
                ->toArray();

        //selected ds of other groups
        $prevOtherGroupDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'ds_marking_group.ds_id')->toArray();

        $targetArrDs = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                        , 'users.id', 'users.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        $dsArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->whereIn('users.id', $prevDsArr)
                ->where('users.group_id', 4)
                ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                        , 'users.id', 'users.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        // End :: DS Selection
        // Start :: Group Cloning
        $cloneEventInfo = Event::where('id', $request->event_id)
                ->select('has_ds_assesment', 'has_group_cloning')
                ->first();

        $cloneSubEventList = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->where('term_to_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_event.event_id', $request->event_id)
                ->where('term_to_sub_event.sub_event_id', '!=', $request->sub_event_id)
                ->orderBy('sub_event.event_code', 'asc')
                ->pluck('sub_event.event_code', 'sub_event.id')
                ->toArray();
        $cloneSubEventIds = [];

        $cloneSubEventPreData = MarkingGroup::where('marking_group.has_clone', '1')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id)
                ->where('marking_group.event_group_id', $request->event_group_id);

        if (!empty($request->sub_event_id)) {
            $cloneSubEventPreData = $cloneSubEventPreData->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cloneSubEventPreData = $cloneSubEventPreData->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cloneSubEventPreData = $cloneSubEventPreData->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $cloneSubEventPreData = $cloneSubEventPreData->first();

        if (!empty($cloneSubEventPreData)) {
            $cloneSubEventIds = explode(',', $cloneSubEventPreData->clone_sub_event_id);
        }
//        echo '<pre>';        print_r($cloneSubEventPreDataArr);exit;
        // End :: Group Cloning
        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingDataArr = $eventAssessmentMarkingDataArr->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingDataArr = $eventAssessmentMarkingDataArr->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingDataArr = $eventAssessmentMarkingDataArr->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $eventAssessmentMarkingDataArr = $eventAssessmentMarkingDataArr->whereNotNull('mks')->get();

        $html = view('markingGroup.showCmDsSelection', compact('synList', 'subSynList', 'cmGroupList', 'nameArr'
                        , 'targetArr', 'submitFrom', 'selectionClass', 'request', 'dsGroupList'
                        , 'targetArrDs', 'submitFromDs', 'selectionClassDs', 'prevCmArr', 'prevDsArr'
                        , 'cmArr', 'dsArr', 'prevOtherGroupCmArr', 'prevOtherGroupDsArr', 'cloneSubEventList'
                        , 'cloneEventInfo', 'cloneSubEventIds', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getGroupTemplateWiseSearchCm(Request $request) {
        //selected cm of this group
        $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('cm_marking_group.cm_id')
                ->toArray();

        //selected cm of other groups
        $prevOtherGroupCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'cm_marking_group.cm_id')->toArray();


        $targetArr = CmBasicProfile::join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', '=', 'cm_basic_profile.id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_group_member_template.course_id', $request->course_id)
                ->where('cm_group_member_template.term_id', $request->term_id)
                ->where('cm_group_member_template.cm_group_id', $request->cm_group_id_2)
                ->where('cm_basic_profile.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();
        $submitFrom = 'GroupWise';
        $selectionClass = 'group-wise';

        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end


        $html = view('markingGroup.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                        , 'selectionClass', 'prevOtherGroupCmArr', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubSyn(Request $request) {

        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')] + CmGroupMemberTemplate::join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->where('cm_group_member_template.course_id', $request->course_id)
                        ->where('cm_group.type', 2)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $submitFrom = 'SynWise';
        $selectionClass = 'syn-wise';

        if (sizeof($subSynList) == 1) {
            //selected cm of this group
            $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();

            //selected cm of other groups
            $prevOtherGroupCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                            ->pluck('event_group.name', 'cm_marking_group.cm_id')->toArray();


            $targetArr = CmBasicProfile::join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->where('cm_group_member_template.course_id', $request->course_id)
                    ->where('cm_group_member_template.term_id', $request->term_id)
                    ->where('cm_group_member_template.cm_group_id', $request->syn_id)
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_group_member_template.type', 1)
                    ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                            , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('cm_basic_profile.official_name', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();
            $html = view('markingGroup.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                            , 'selectionClass', 'prevOtherGroupCmArr'))->render();
        } else {
            $html = view('markingGroup.showSubSyn', compact('subSynList'))->render();
        }
        return response()->json(['html' => $html]);
    }

    public function getSynWiseSearchCm(Request $request) {
        $submitFrom = 'SynWise';
        $selectionClass = 'syn-wise';

        //selected cm of this group
        $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('cm_marking_group.cm_id')
                ->toArray();

        //selected cm of other groups
        $prevOtherGroupCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'cm_marking_group.cm_id')->toArray();

		$targetArr = CmBasicProfile::join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->where('cm_group_member_template.course_id', $request->course_id)
                    ->where('cm_group_member_template.term_id', $request->term_id)
                    ->where('cm_group_member_template.cm_group_id', $request->sub_syn_id)
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_group_member_template.type', 2)
                    ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                            , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('cm_basic_profile.official_name', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

        $html = view('markingGroup.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                        , 'selectionClass', 'prevOtherGroupCmArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getFilterIndividualCm(Request $request) {
        $submitFrom = 'Individual';
        $selectionClass = 'individual-search';

        //selected cm of this group
        $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('cm_marking_group.cm_id')
                ->toArray();

//        echo '<pre>';
//        print_r($prevCmArr);
//        exit;
        //selected cm of other groups
        $prevOtherGroupCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupCmArr = $prevOtherGroupCmArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'cm_marking_group.cm_id')->toArray();


        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('cm_basic_profile.personal_no', 'LIKE', '%' . $request->individual_search . '%')
                ->where('cm_basic_profile.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();
        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end


        $html = view('markingGroup.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                        , 'selectionClass', 'prevOtherGroupCmArr', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function setCm(Request $request) {
        $cmArr = $request->selected_cm_id;

        if (empty($cmArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_CM')), 401);
        }

        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->whereIn('cm_basic_profile.id', $cmArr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();
        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end

        $html = view('markingGroup.showSetCmList', compact('targetArr', 'request', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getGroupTemplateWiseSearchDs(Request $request) {
        $submitFromDs = 'DsGroupWise';
        $selectionClassDs = 'ds-group-wise';

        //selected ds of this group
        $prevDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevDsArr = $prevDsArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('ds_marking_group.ds_id')
                ->toArray();

        //selected ds of other groups
        $prevOtherGroupDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'ds_marking_group.ds_id')->toArray();

        $targetArr = User::join('ds_group_member_template', 'ds_group_member_template.user_id', '=', 'users.id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->where('ds_group_member_template.course_id', $request->course_id)
                ->where('ds_group_member_template.term_id', $request->term_id)
                ->where('ds_group_member_template.ds_group_id', $request->ds_group_id_2)
                ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                        , 'users.id', 'users.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end


        $html = view('markingGroup.showSearchDs', compact('targetArr', 'request', 'submitFromDs', 'prevDsArr'
                        , 'selectionClassDs', 'prevOtherGroupDsArr', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getFilterIndividualDs(Request $request) {

        $submitFromDs = 'DsIndividual';
        $selectionClassDs = 'ds-individual-search';

        //selected ds of this group
        $prevDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevDsArr = $prevDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevDsArr = $prevDsArr->where('marking_group.event_group_id', $request->event_group_id)
                ->pluck('ds_marking_group.ds_id')
                ->toArray();

        //selected ds of other groups
        $prevOtherGroupDsArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevOtherGroupDsArr = $prevOtherGroupDsArr->where('marking_group.event_group_id', '<>', $request->event_group_id)
                        ->pluck('event_group.name', 'ds_marking_group.ds_id')->toArray();

        $targetArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->where('users.personal_no', 'LIKE', '%' . $request->individual_search_ds . '%')
                ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                        , 'users.id', 'users.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end


        $html = view('markingGroup.showSearchDs', compact('targetArr', 'request', 'submitFromDs', 'prevDsArr'
                        , 'selectionClassDs', 'prevOtherGroupDsArr', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function setDs(Request $request) {
        $dsArr = $request->selected_ds_id;

        if (empty($dsArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_DS')), 401);
        }
        $targetArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->whereIn('users.id', $dsArr)
                ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                        , 'users.id', 'users.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        //Dependency check Disable data
        $eventAssessmentMarkingDataArr = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->whereNotNull('mks')
                ->get();
//        echo '<pre>';        print_r($eventAssessmentMarkingDataArr);exit;
        //end

        $html = view('markingGroup.showSetDsList', compact('targetArr', 'request', 'eventAssessmentMarkingDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function saveMarkingGroup(Request $request) {

        $cmArr = $request->selected_cm;
        $dsArr = $request->selected_ds;
        $requiredEventArr = $request->required_event;
        $hasGroupCloning = !empty($request->has_group_cloning) ? $request->has_group_cloning : '0';
        $cloneSubEventIdArr = !empty($request->clone_sub_event_id) ? $request->clone_sub_event_id : [];

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
            'event_group_id' => 'required|not_in:0',
        ];

        if (!empty($requiredEventArr['sub'])) {
            $rules['sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $errorArr = [];
        if (empty($cmArr)) {
            $errorArr[] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_CM');
        }
        if (empty($dsArr)) {
            $errorArr[] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_DS');
        }
        if (!empty($hasGroupCloning)) {
            if (empty($cloneSubEventIdArr)) {
                $errorArr[] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_SUB_EVENT_TO_CLONE');
            }
        }
        if (!empty($errorArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $errorArr), 400);
        }

        //ds appt id
        $dsApptList = User::whereIn('id', $dsArr)->pluck('appointment_id', 'id')->toArray();

        //event grouping
        $termToEventInfo = TermToEvent::join('event', 'event.id', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('term_to_event.event_id', $request->event_id)
                        ->where('event.has_sub_event', '0')
                        ->select('term_to_event.event_id')->first();

        $markingEventGroupArr = [];
        if (!empty($termToEventInfo)) {
            $markingEventGroupArr[$termToEventInfo->event_id][0][0][0] = 0;
        }

        //sub event grouping
        $termToSubEventInfo = TermToSubEvent::join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubEventInfo = $termToSubEventInfo->where('term_to_sub_event.sub_event_id', $request->sub_event_id);
        }
        $termToSubEventInfo = $termToSubEventInfo->where('event_to_sub_event.has_sub_sub_event', '0')
                        ->select('term_to_sub_event.event_id', 'term_to_sub_event.sub_event_id')->get();

        if (!empty($termToSubEventInfo)) {
            foreach ($termToSubEventInfo as $subEvInfo) {
                $markingEventGroupArr[$subEvInfo->event_id][$subEvInfo->sub_event_id][0][0] = 0;
            }
        }

        //sub sub event grouping
        $termToSubSubEventInfo = TermToSubSubEvent::join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubSubEventInfo = $termToSubSubEventInfo->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $termToSubSubEventInfo = $termToSubSubEventInfo->where('term_to_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
        }
        $termToSubSubEventInfo = $termToSubSubEventInfo->where('event_to_sub_sub_event.has_sub_sub_sub_event', '0')
                        ->select('term_to_sub_sub_event.event_id', 'term_to_sub_sub_event.sub_event_id'
                                , 'term_to_sub_sub_event.sub_sub_event_id')->get();

        if (!empty($termToSubSubEventInfo)) {
            foreach ($termToSubSubEventInfo as $subSubEvInfo) {
                $markingEventGroupArr[$subSubEvInfo->event_id][$subSubEvInfo->sub_event_id][$subSubEvInfo->sub_sub_event_id][0] = 0;
            }
        }

        //sub sub sub event grouping
        $termToSubSubSubEventInfo = TermToSubSubSubEvent::join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_sub_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->select('term_to_sub_sub_sub_event.event_id', 'term_to_sub_sub_sub_event.sub_event_id'
                        , 'term_to_sub_sub_sub_event.sub_sub_event_id', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')->get();

        if (!empty($termToSubSubSubEventInfo)) {
            foreach ($termToSubSubSubEventInfo as $subSubSubEvInfo) {
                $markingEventGroupArr[$subSubSubEvInfo->event_id][$subSubSubEvInfo->sub_event_id][$subSubSubEvInfo->sub_sub_event_id][$subSubSubEvInfo->sub_sub_sub_event_id] = $subSubSubEvInfo->sub_sub_sub_event_id;
            }
        }

        $cloneableEventArr = $this->getCloneableEvents($request->course_id, $request->event_id, $cloneSubEventIdArr);

//        echo '<pre>';
//        print_r($cloneableEventArr);
//        exit;

        DB::beginTransaction();
        try {
            if (!empty($markingEventGroupArr)) {
                foreach ($markingEventGroupArr as $eventId => $evDetail) {
                    foreach ($evDetail as $subEventId => $subEvDetail) {
                        foreach ($subEvDetail as $subSubEventId => $subSubEvDetail) {
                            foreach ($subSubEvDetail as $subSubSubEventId => $subSubSubEvDetail) {
                                if ((sizeof($evDetail) > 1 && $subEventId != 0) || (sizeof($evDetail) == 1)) {
                                    if ((sizeof($subEvDetail) > 1 && $subSubEventId != 0) || (sizeof($subEvDetail) == 1)) {
                                        if ((sizeof($subSubEvDetail) > 1 && $subSubSubEventId != 0) || (sizeof($subSubEvDetail) == 1)) {
                                            $markingGroup = new MarkingGroup;
                                            $markingGroup->course_id = $request->course_id;
                                            $markingGroup->term_id = $request->term_id;
                                            $markingGroup->event_id = $eventId;
                                            $markingGroup->sub_event_id = $subEventId;
                                            $markingGroup->sub_sub_event_id = $subSubEventId;
                                            $markingGroup->sub_sub_sub_event_id = $subSubSubEventId;
                                            $markingGroup->event_group_id = $request->event_group_id;
                                            $markingGroup->has_clone = !empty($hasGroupCloning) ? $hasGroupCloning : '0';
                                            $markingGroup->clone_sub_event_id = (!empty($hasGroupCloning) && !empty($cloneSubEventIdArr)) ? implode(',', $cloneSubEventIdArr) : null;
                                            $markingGroup->updated_at = date('Y-m-d H:i:s');
                                            $markingGroup->updated_by = Auth::user()->id;


                                            $prevMarkingGroupList = MarkingGroup::where('course_id', $request->course_id)
                                                            ->where('term_id', $request->term_id)
                                                            ->where('event_id', $eventId)
                                                            ->where('sub_event_id', $subEventId)
                                                            ->where('sub_sub_event_id', $subSubEventId)
                                                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                            ->where('event_group_id', $request->event_group_id)
                                                            ->pluck('id')->toArray();

                                            MarkingGroup::where('course_id', $request->course_id)
                                                    ->where('term_id', $request->term_id)
                                                    ->where('event_id', $eventId)
                                                    ->where('sub_event_id', $subEventId)
                                                    ->where('sub_sub_event_id', $subSubEventId)
                                                    ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                    ->where('event_group_id', $request->event_group_id)
                                                    ->delete();

                                            CmMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                            DsMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();

                                            if ($markingGroup->save()) {
                                                $cmMarkingGroupArr = [];
                                                $dsMarkingGroupArr = [];
                                                $cmI = $dsI = 0;
                                                if (!empty($cmArr)) {
                                                    foreach ($cmArr as $cmId => $cmId) {
                                                        $cmMarkingGroupArr[$cmI]['marking_group_id'] = $markingGroup->id;
                                                        $cmMarkingGroupArr[$cmI]['cm_id'] = $cmId;
                                                        $cmMarkingGroupArr[$cmI]['active'] = '1';
                                                        $cmMarkingGroupArr[$cmI]['updated_at'] = date('Y-m-d H:i:s');
                                                        $cmMarkingGroupArr[$cmI]['updated_by'] = Auth::user()->id;
                                                        $cmI++;
                                                    }
                                                }
                                                if (!empty($dsArr)) {
                                                    foreach ($dsArr as $dsId => $dsId) {
                                                        $dsMarkingGroupArr[$dsI]['marking_group_id'] = $markingGroup->id;
                                                        $dsMarkingGroupArr[$dsI]['ds_id'] = $dsId;
                                                        $dsMarkingGroupArr[$dsI]['ds_appt_id'] = !empty($dsApptList[$dsId]) ? $dsApptList[$dsId] : 0;
                                                        $dsMarkingGroupArr[$dsI]['updated_at'] = date('Y-m-d H:i:s');
                                                        $dsMarkingGroupArr[$dsI]['updated_by'] = Auth::user()->id;
                                                        $dsI++;
                                                    }
                                                }
                                                CmMarkingGroup::insert($cmMarkingGroupArr);
                                                DsMarkingGroup::insert($dsMarkingGroupArr);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($cloneableEventArr)) {
                foreach ($cloneableEventArr as $eventId => $evInfo) {
                    if (!empty($evInfo)) {
                        foreach ($evInfo as $subEventId => $subEvInfo) {
                            if (!empty($subEvInfo)) {
                                foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                                    if (!empty($subSubEvInfo)) {
                                        foreach ($subSubEvInfo as $subSubSubEventId => $termId) {
                                            $markingGroup = new MarkingGroup;
                                            $markingGroup->course_id = $request->course_id;
                                            $markingGroup->term_id = $termId;
                                            $markingGroup->event_id = $eventId;
                                            $markingGroup->sub_event_id = $subEventId;
                                            $markingGroup->sub_sub_event_id = $subSubEventId;
                                            $markingGroup->sub_sub_sub_event_id = $subSubSubEventId;
                                            $markingGroup->event_group_id = $request->event_group_id;
                                            $markingGroup->updated_at = date('Y-m-d H:i:s');
                                            $markingGroup->updated_by = Auth::user()->id;


                                            $prevMarkingGroupList = MarkingGroup::where('course_id', $request->course_id)
                                                            ->where('term_id', $termId)
                                                            ->where('event_id', $eventId)
                                                            ->where('sub_event_id', $subEventId)
                                                            ->where('sub_sub_event_id', $subSubEventId)
                                                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                            ->where('event_group_id', $request->event_group_id)
                                                            ->pluck('id')->toArray();

                                            MarkingGroup::where('course_id', $request->course_id)
                                                    ->where('term_id', $termId)
                                                    ->where('event_id', $eventId)
                                                    ->where('sub_event_id', $subEventId)
                                                    ->where('sub_sub_event_id', $subSubEventId)
                                                    ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                    ->where('event_group_id', $request->event_group_id)
                                                    ->delete();

                                            CmMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                            DsMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();

                                            if ($markingGroup->save()) {
                                                $cmMarkingGroupArr = [];
                                                $dsMarkingGroupArr = [];
                                                $cmI = $dsI = 0;
                                                if (!empty($cmArr)) {
                                                    foreach ($cmArr as $cmId => $cmId) {
                                                        $cmMarkingGroupArr[$cmI]['marking_group_id'] = $markingGroup->id;
                                                        $cmMarkingGroupArr[$cmI]['cm_id'] = $cmId;
                                                        $cmMarkingGroupArr[$cmI]['active'] = '1';
                                                        $cmMarkingGroupArr[$cmI]['updated_at'] = date('Y-m-d H:i:s');
                                                        $cmMarkingGroupArr[$cmI]['updated_by'] = Auth::user()->id;
                                                        $cmI++;
                                                    }
                                                }
                                                if (!empty($dsArr)) {
                                                    foreach ($dsArr as $dsId => $dsId) {
                                                        $dsMarkingGroupArr[$dsI]['marking_group_id'] = $markingGroup->id;
                                                        $dsMarkingGroupArr[$dsI]['ds_id'] = $dsId;
                                                        $dsMarkingGroupArr[$dsI]['ds_appt_id'] = !empty($dsApptList[$dsId]) ? $dsApptList[$dsId] : 0;
                                                        $dsMarkingGroupArr[$dsI]['updated_at'] = date('Y-m-d H:i:s');
                                                        $dsMarkingGroupArr[$dsI]['updated_by'] = Auth::user()->id;
                                                        $dsI++;
                                                    }
                                                }
                                                CmMarkingGroup::insert($cmMarkingGroupArr);
                                                DsMarkingGroup::insert($dsMarkingGroupArr);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return Response::json(['success' => true, 'message' => __('label.MARKING_GROUP_HAS_BEEN_ASSIGNED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'heading' => 'Error', 'message' => __('label.FAILED_TO_ASSIGN_MARKING_GROUP')), 401);
        }
    }

    public function getCloneableEvents($courseId, $eventId, $cloneSubEventList = []) {
        //sub event grouping
        $termToSubEventInfo = TermToSubEvent::join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $courseId)
                ->where('term_to_sub_event.event_id', $eventId)
                ->whereIn('term_to_sub_event.sub_event_id', $cloneSubEventList)
                ->where('event_to_sub_event.has_sub_sub_event', '0')
                ->select('term_to_sub_event.term_id', 'term_to_sub_event.event_id', 'term_to_sub_event.sub_event_id')
                ->get();
        $cloneableEventArr = [];
        if (!empty($termToSubEventInfo)) {
            foreach ($termToSubEventInfo as $subEvInfo) {
                $cloneableEventArr[$subEvInfo->event_id][$subEvInfo->sub_event_id][0][0] = $subEvInfo->term_id;
            }
        }

        //sub sub event grouping
        $termToSubSubEventInfo = TermToSubSubEvent::join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $courseId)
                ->where('term_to_sub_sub_event.event_id', $eventId)
                ->whereIn('term_to_sub_sub_event.sub_event_id', $cloneSubEventList)
                ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '0')
                ->select('term_to_sub_sub_event.term_id', 'term_to_sub_sub_event.event_id', 'term_to_sub_sub_event.sub_event_id'
                        , 'term_to_sub_sub_event.sub_sub_event_id')
                ->get();

        if (!empty($termToSubSubEventInfo)) {
            foreach ($termToSubSubEventInfo as $subSubEvInfo) {
                $cloneableEventArr[$subSubEvInfo->event_id][$subSubEvInfo->sub_event_id][$subSubEvInfo->sub_sub_event_id][0] = $subSubEvInfo->term_id;
            }
        }

        //sub sub sub event grouping
        $termToSubSubSubEventInfo = TermToSubSubSubEvent::join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $courseId)
                ->where('term_to_sub_sub_sub_event.event_id', $eventId)
                ->whereIn('term_to_sub_sub_sub_event.sub_event_id', $cloneSubEventList)
                ->select('term_to_sub_sub_sub_event.term_id', 'term_to_sub_sub_sub_event.event_id', 'term_to_sub_sub_sub_event.sub_event_id'
                        , 'term_to_sub_sub_sub_event.sub_sub_event_id', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->get();

        if (!empty($termToSubSubSubEventInfo)) {
            foreach ($termToSubSubSubEventInfo as $subSubSubEvInfo) {
                $cloneableEventArr[$subSubSubEvInfo->event_id][$subSubSubEvInfo->sub_event_id][$subSubSubEvInfo->sub_sub_event_id][$subSubSubEvInfo->sub_sub_sub_event_id] = $subSubSubEvInfo->term_id;
            }
        }


        //close events data
        $closeEventData = EventAssessmentMarking::where('course_id', $courseId)
                ->where('event_id', $eventId)
                ->whereNotNull('mks')
                ->select('event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id'
                        , DB::raw("AVG(mks) as avg_mks"))
                ->groupBy('event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id')
                ->get();


        if (!empty($closeEventData)) {
            foreach ($closeEventData as $cEvInfo) {
                $eventId = $cEvInfo->event_id;
                $subEventId = $cEvInfo->sub_event_id;
                $subSubEventId = $cEvInfo->sub_sub_event_id;
                $subSubSubEventId = $cEvInfo->sub_sub_sub_event_id;

                if (!empty($cloneableEventArr[$eventId][$subEventId][$subSubEventId])) {
                    if (array_key_exists($subSubSubEventId, $cloneableEventArr[$eventId][$subEventId][$subSubEventId])) {
                        unset($cloneableEventArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]);
                    }
//              
                    if (sizeof($cloneableEventArr[$eventId][$subEventId][$subSubEventId]) == 0) {
                        unset($cloneableEventArr[$eventId][$subEventId][$subSubEventId]);
                    }
                    if (sizeof($cloneableEventArr[$eventId][$subEventId]) == 0) {
                        unset($cloneableEventArr[$eventId][$subEventId]);
                    }
                    if (sizeof($cloneableEventArr[$eventId]) == 0) {
                        unset($cloneableEventArr[$eventId]);
                    }
                }
            }
        }

        return $cloneableEventArr;
    }

    public function removeMarkingGroup(Request $request) {
        $cmArr = $request->selected_cm;
        $dsArr = $request->selected_ds;
        $requiredEventArr = $request->required_event;
        $hasGroupCloning = !empty($request->has_group_cloning) ? $request->has_group_cloning : '0';
        $cloneSubEventIdArr = !empty($request->clone_sub_event_id) ? $request->clone_sub_event_id : [];

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
            'event_group_id' => 'required|not_in:0',
        ];

        if (!empty($requiredEventArr['sub'])) {
            $rules['sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }
        if (!empty($requiredEventArr['sub_sub_sub'])) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $errorArr = [];
        if ((empty($cmArr)) || (empty($dsArr))) {
            $errorArr[] = __('label.NO_MARKING_GROUP_ARE_ASSIGNED');
        }
        if (!empty($hasGroupCloning)) {
            if (empty($cloneSubEventIdArr)) {
                $errorArr[] = __('label.NO_SUB_EVENT_CHOOSE_TO_CLONE');
            }
        }
        if (!empty($errorArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $errorArr), 400);
        }
//        echo '<pre>';        print_r($request->all()); exit;
        $cmArr = $request->selected_cm;
        $dsArr = $request->selected_ds;
        $requiredEventArr = $request->required_event;
        $hasGroupCloning = !empty($request->has_group_cloning) ? $request->has_group_cloning : '0';
        $cloneSubEventIdArr = !empty($request->clone_sub_event_id) ? $request->clone_sub_event_id : [];

        //ds appt id
        $dsApptList = User::whereIn('id', $dsArr)->pluck('appointment_id', 'id')->toArray();

        //event grouping
        $termToEventInfo = TermToEvent::join('event', 'event.id', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('term_to_event.event_id', $request->event_id)
                        ->where('event.has_sub_event', '0')
                        ->select('term_to_event.event_id')->first();

        $markingEventGroupArr = [];
        if (!empty($termToEventInfo)) {
            $markingEventGroupArr[$termToEventInfo->event_id][0][0][0] = 0;
        }

        //sub event grouping
        $termToSubEventInfo = TermToSubEvent::join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubEventInfo = $termToSubEventInfo->where('term_to_sub_event.sub_event_id', $request->sub_event_id);
        }
        $termToSubEventInfo = $termToSubEventInfo->where('event_to_sub_event.has_sub_sub_event', '0')
                        ->select('term_to_sub_event.event_id', 'term_to_sub_event.sub_event_id')->get();

        if (!empty($termToSubEventInfo)) {
            foreach ($termToSubEventInfo as $subEvInfo) {
                $markingEventGroupArr[$subEvInfo->event_id][$subEvInfo->sub_event_id][0][0] = 0;
            }
        }

        //sub sub event grouping
        $termToSubSubEventInfo = TermToSubSubEvent::join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubSubEventInfo = $termToSubSubEventInfo->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $termToSubSubEventInfo = $termToSubSubEventInfo->where('term_to_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
        }
        $termToSubSubEventInfo = $termToSubSubEventInfo->where('event_to_sub_sub_event.has_sub_sub_sub_event', '0')
                        ->select('term_to_sub_sub_event.event_id', 'term_to_sub_sub_event.sub_event_id'
                                , 'term_to_sub_sub_event.sub_sub_event_id')->get();

        if (!empty($termToSubSubEventInfo)) {
            foreach ($termToSubSubEventInfo as $subSubEvInfo) {
                $markingEventGroupArr[$subSubEvInfo->event_id][$subSubEvInfo->sub_event_id][$subSubEvInfo->sub_sub_event_id][0] = 0;
            }
        }

        //sub sub sub event grouping
        $termToSubSubSubEventInfo = TermToSubSubSubEvent::join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_sub_sub_event.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $termToSubSubSubEventInfo = $termToSubSubSubEventInfo->select('term_to_sub_sub_sub_event.event_id', 'term_to_sub_sub_sub_event.sub_event_id'
                        , 'term_to_sub_sub_sub_event.sub_sub_event_id', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')->get();

        if (!empty($termToSubSubSubEventInfo)) {
            foreach ($termToSubSubSubEventInfo as $subSubSubEvInfo) {
                $markingEventGroupArr[$subSubSubEvInfo->event_id][$subSubSubEvInfo->sub_event_id][$subSubSubEvInfo->sub_sub_event_id][$subSubSubEvInfo->sub_sub_sub_event_id] = $subSubSubEvInfo->sub_sub_sub_event_id;
            }
        }

        $cloneableEventArr = $this->getCloneableEvents($request->course_id, $request->event_id, $cloneSubEventIdArr);

//        echo '<pre>';
//        print_r($cloneableEventArr);
//        exit;

        DB::beginTransaction();
        try {
            if (!empty($markingEventGroupArr)) {
                foreach ($markingEventGroupArr as $eventId => $evDetail) {
                    foreach ($evDetail as $subEventId => $subEvDetail) {
                        foreach ($subEvDetail as $subSubEventId => $subSubEvDetail) {
                            foreach ($subSubEvDetail as $subSubSubEventId => $subSubSubEvDetail) {
                                if ((sizeof($evDetail) > 1 && $subEventId != 0) || (sizeof($evDetail) == 1)) {
                                    if ((sizeof($subEvDetail) > 1 && $subSubEventId != 0) || (sizeof($subEvDetail) == 1)) {
                                        if ((sizeof($subSubEvDetail) > 1 && $subSubSubEventId != 0) || (sizeof($subSubEvDetail) == 1)) {

                                            $prevMarkingGroupList = MarkingGroup::where('course_id', $request->course_id)
                                                            ->where('term_id', $request->term_id)
                                                            ->where('event_id', $eventId)
                                                            ->where('sub_event_id', $subEventId)
                                                            ->where('sub_sub_event_id', $subSubEventId)
                                                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                            ->where('event_group_id', $request->event_group_id)
                                                            ->pluck('id')->toArray();

                                            MarkingGroup::where('course_id', $request->course_id)
                                                    ->where('term_id', $request->term_id)
                                                    ->where('event_id', $eventId)
                                                    ->where('sub_event_id', $subEventId)
                                                    ->where('sub_sub_event_id', $subSubEventId)
                                                    ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                    ->where('event_group_id', $request->event_group_id)
                                                    ->delete();

                                            CmMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                            DsMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($cloneableEventArr)) {
                foreach ($cloneableEventArr as $eventId => $evInfo) {
                    if (!empty($evInfo)) {
                        foreach ($evInfo as $subEventId => $subEvInfo) {
                            if (!empty($subEvInfo)) {
                                foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                                    if (!empty($subSubEvInfo)) {
                                        foreach ($subSubEvInfo as $subSubSubEventId => $termId) {


                                            $prevMarkingGroupList = MarkingGroup::where('course_id', $request->course_id)
                                                            ->where('term_id', $termId)
                                                            ->where('event_id', $eventId)
                                                            ->where('sub_event_id', $subEventId)
                                                            ->where('sub_sub_event_id', $subSubEventId)
                                                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                            ->where('event_group_id', $request->event_group_id)
                                                            ->pluck('id')->toArray();

                                            MarkingGroup::where('course_id', $request->course_id)
                                                    ->where('term_id', $termId)
                                                    ->where('event_id', $eventId)
                                                    ->where('sub_event_id', $subEventId)
                                                    ->where('sub_sub_event_id', $subSubEventId)
                                                    ->where('sub_sub_sub_event_id', $subSubSubEventId)
                                                    ->where('event_group_id', $request->event_group_id)
                                                    ->delete();

                                            CmMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                            DsMarkingGroup::whereIn('marking_group_id', $prevMarkingGroupList)->delete();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return Response::json(['success' => true, 'message' => __('label.MARKING_GROUP_HAS_BEEN_REMOVED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'heading' => 'Error', 'message' => __('label.FAILED_TO_REMOVE_MARKING_GROUP')), 401);
        }
    }

}
