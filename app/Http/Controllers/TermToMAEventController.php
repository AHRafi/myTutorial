<?php

namespace App\Http\Controllers;

use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmMarkingGroup;
use App\MarkingGroup;
use App\TermToMAEvent;
use App\TrainingYear;
use App\MutualAssessmentEvent;
use App\MutualAssessmentMarking;
use App\User;
use App\Term;
use App\EventGroup;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\MaGroup;
use App\CmMaGroup;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class TermToMAEventController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_TERM_TO_MA_EVENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
////                        ->where('wing_to_course.wing_id', Auth::user()->wing_id)
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
                        ->orderBy('term.order', 'asc')
                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();

        return view('termToMAEvent.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'));
    }

//    public function getTerm(Request $request) {
//
//        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
//                        ->where('term_to_course.course_id', $request->course_id)
//                        ->orderBy('term.order', 'asc')
//                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
//
//        $html = view('termToMAEvent.showTerm', compact('termList'))->render();
//
//        return response()->json(['html' => $html]);
//    }

    public function getEvent(Request $request) {

        //get event data 
        $targetArr = MutualAssessmentEvent::select('id', 'name')
                ->where('status', '1')->orderBy('order', 'asc')
                ->get();

        $prevTermToMAEventList = TermToMAEvent::where('course_id', $request->course_id)
                        ->pluck('term_id', 'event_id')->toArray();


        $prevDataArr = TermToMAEvent::where('course_id', $request->course_id)->get();



        $chackPrevDataArr = TermToMAEvent::where('course_id', $request->course_id)
//                ->where('center_id', Auth::user()->center_id)
                ->where('term_id', $request->term_id)
                ->get();

        $termList = Term::pluck('name', 'id')->toArray();
        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->event_id][] = $item->term_id;
            }
        }

        $chackPrevDataList = $chackPrevDataGroupList = [];
        if (!empty($chackPrevDataArr)) {
            foreach ($chackPrevDataArr as $item) {
                $chackPrevDataList[$item->event_id] = $item->term_id;
                $chackPrevDataGroupList[$item->event_id] = $item->event_wise_grouping;
            }
        }
        $markingCheck = $termToParticular = [];
        //dependency check **** if assign marking where Term to Event relationship event disabled
//        $markingCheck = Marking::where('course_id', $request->course_id)
//                        ->where('center_id', Auth::user()->center_id)
//                        ->where('term_id', $request->term_id)
//                        ->pluck('weight', 'event_id')->toArray();
        //event assign term wise term to particular :: dependency check
//        $termToParticular = TermToParticular::where('course_id', $request->course_id)
//                        ->where('center_id', Auth::user()->center_id)
//                        ->where('term_id', $request->term_id)
//                        ->pluck('particular_id', 'event_id')->toArray();
        //ENDOF Dependency
        //Dependency check Disable data
        $mutualAssessmentMarkingDataArr = MutualAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->pluck('event_id', 'event_id')
                ->toArray();

//        echo '<pre>';
//        print_r($mutualAssessmentMarkingDataArr);
//        exit;
//        echo '<pre>';        print_r($mutualAssessmentMarkingDataArr);exit;
        //end

        $html = view('termToMAEvent.getEvent', compact('targetArr', 'termList', 'prevDataArr', 'prevDataList', 'chackPrevDataList'
                        , 'markingCheck', 'termToParticular', 'prevTermToMAEventList', 'request'
                        , 'mutualAssessmentMarkingDataArr', 'chackPrevDataGroupList'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveTermToMAEvent(Request $request) {
        $eventArr = $request->event_id;
        $groupingArr = $request->event_wise_grouping;
        if (empty($eventArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_TERM_TO_ATLEAST_ONE_EVENT')), 401);
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = $gpEventArr = [];
        $i = 0;
        if (!empty($eventArr)) {
            foreach ($eventArr as $eventId => $eventInfo) {
                if (!empty($eventId)) {
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['term_id'] = $request->term_id;
                    $data[$i]['event_id'] = $eventId;
                    $data[$i]['event_wise_grouping'] = !empty($groupingArr[$eventId]) ? $groupingArr[$eventId] : '0';
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $i++;
                    
                    if(empty($groupingArr[$eventId])){
                        $gpEventArr[] = $eventId;
                    } 
                }
            }
        }
        
        
        DB::beginTransaction();
        try {
            TermToMAEvent::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->delete();
            if (TermToMAEvent::insert($data)) {
                $prevMaGroupList = MaGroup::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                                ->whereIn('ma_event_id', $gpEventArr)->pluck('id')->toArray();


                MaGroup::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                        ->whereIn('ma_event_id', $gpEventArr)->delete();

                CmMaGroup::whereIn('ma_group_id', $prevMaGroupList)->delete();
            }

            DB::commit();
            return Response::json(['success' => true, 'message' => __('label.MARKING_GROUP_HAS_BEEN_ASSIGNED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'message' => __('label.TERM_TO_EVENT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function getAddGrouping(Request $request) {
        $course = Course::where('id', $request->course_id)->select('name')->first();
        $term = Term::where('id', $request->term_id)->select('name')->first();
        $maEvent = MutualAssessmentEvent::where('id', $request->event_id)->select('name')->first();

        $request->course = !empty($course->name) ? $course->name : '';
        $request->term = !empty($term->name) ? $term->name : '';
        $request->ma_event = !empty($maEvent->name) ? $maEvent->name : '';

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)->where('term_to_event.term_id', $request->term_id)
                        ->where('event.for_ma_grouping', '1')->where('event.status', '1')->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $html = view('termToMAEvent.showAddGrouping', compact('request', 'eventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getEventGroup(Request $request) {
        $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventGroup::where('for_ma_grouping', '1')
                        ->where('status', '1')->pluck('name', 'id')->toArray();

        $html = view('termToMAEvent.showEventGroup', compact('request', 'eventGroupList'))->render();
        return $html;
    }

    public function getSubEventOrGroup(Request $request) {
        $html = $html1 = '';
        $subEventList = [];

        $hasDsAssessment = Event::where('id', $request->event_id)->where('has_ds_assesment', '0')->first();

        if (!empty($hasDsAssessment)) {
            $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', 'term_to_sub_event.sub_event_id')
                            ->join('event_to_sub_event', function($join) {
                                $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                                $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                            })
                            ->where('term_to_sub_event.course_id', $request->course_id)
                            ->where('term_to_sub_event.term_id', $request->term_id)
                            ->where('term_to_sub_event.event_id', $request->event_id)
                            ->where('sub_event.status', '1')
                            ->orderBy('sub_event.event_code', 'asc')
                            ->pluck('sub_event.event_code', 'sub_event.id')->toArray();
        }

        if (sizeof($subEventList) > 1) {
            $html = view('termToMAEvent.showSubEvent', compact('request', 'subEventList'))->render();
        } else {
            $html1 = $this->getEventGroup($request);
        }
        return response()->json(['html' => $html, 'html1' => $html1]);
    }

    public function getSubSubEventOrGroup(Request $request) {
        $html = $html1 = '';
        $subSubEventList = [];

        $hasDsAssessment = EventToSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('has_ds_assesment', '0')->first();

        if (!empty($hasDsAssessment)) {
            $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', 'term_to_sub_sub_event.sub_sub_event_id')
                            ->join('event_to_sub_sub_event', function($join) {
                                $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                                $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                                $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                            })
                            ->where('term_to_sub_sub_event.course_id', $request->course_id)
                            ->where('term_to_sub_sub_event.term_id', $request->term_id)
                            ->where('term_to_sub_sub_event.event_id', $request->event_id)
                            ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                            ->where('sub_sub_event.status', '1')
                            ->orderBy('sub_sub_event.event_code', 'asc')
                            ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();
        }

        if (sizeof($subSubEventList) > 1) {
            $html = view('termToMAEvent.showSubSubEvent', compact('request', 'subSubEventList'))->render();
        } else {
            $html1 = $this->getEventGroup($request);
        }
        return response()->json(['html' => $html, 'html1' => $html1]);
    }

    public function getSubSubSubEventOrGroup(Request $request) {
        $html = $html1 = '';
        $subSubSubEventList = [];

        $hasDsAssessment = EventToSubSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('has_ds_assesment', '0')->first();

        if (!empty($hasDsAssessment)) {
            $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                            ->join('event_to_sub_sub_sub_event', function($join) {
                                $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                                $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                                $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                                $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                            })
                            ->where('term_to_sub_sub_event.course_id', $request->course_id)
                            ->where('term_to_sub_sub_event.term_id', $request->term_id)
                            ->where('term_to_sub_sub_event.event_id', $request->event_id)
                            ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                            ->where('term_to_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                            ->where('sub_sub_sub_event.status', '1')
                            ->orderBy('sub_sub_sub_event.event_code', 'asc')
                            ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();
        }

        if (sizeof($subSubSubEventList) > 1) {
            $html = view('termToMAEvent.showSubSubSubEvent', compact('request', 'subSubSubEventList'))->render();
        } else {
            $html1 = $this->getEventGroup($request);
        }
        return response()->json(['html' => $html, 'html1' => $html1]);
    }

    public function getGroup(Request $request) {
        $html1 = $this->getEventGroup($request);
        return response()->json(['html1' => $html1]);
    }

    public function getGroupingCm(Request $request) {
        $maGroup = MaGroup::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                        ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)->where('event_group_id', $request->event_group_id)
                        ->select('mk_groups')->first();
        $mkGroupArr = [];
        if (!empty($maGroup)) {
            $mkGroupArr = !empty($maGroup->mk_groups) ? explode(",", $maGroup->mk_groups) : [];
        }


        $cmMaGroupInfo = CmMaGroup::join('ma_group', 'ma_group.id', 'cm_ma_group.ma_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_ma_group.cm_id')
                ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('ma_group.course_id', $request->course_id)->where('ma_group.term_id', $request->term_id)
                ->where('ma_group.ma_event_id', $request->ma_event_id)->where('ma_group.event_id', $request->event_id)
                ->where('ma_group.sub_event_id', $request->sub_event_id)->where('ma_group.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('ma_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)->where('ma_group.event_group_id', $request->event_group_id)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();


        $maOtherGroupInfo = MaGroup::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                        ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)->where('event_group_id', '<>', $request->event_group_id)
                        ->select('mk_groups')->get();
        $mkOtherGroupArr = [];
        if (!$maOtherGroupInfo->isEmpty()) {
            foreach ($maOtherGroupInfo as $maOInf) {
                $mkOGroupArr = !empty($maOInf->mk_groups) ? explode(",", $maOInf->mk_groups) : [];
                if (!empty($mkOGroupArr)) {
                    foreach ($mkOGroupArr as $key => $gpId) {
                        $mkOtherGroupArr[] = $gpId;
                    }
                }
            }
        }


        $markingGroupArr = MarkingGroup::join('event_group', 'event_group.id', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $markingGroupArr = $markingGroupArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $markingGroupArr = $markingGroupArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $markingGroupArr = $markingGroupArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $markingGroupArr = $markingGroupArr->orderBy('event_group.name', 'asc')
                        ->pluck('event_group.name', 'event_group.id')->toArray();


        $enableSubmit = !$cmMaGroupInfo->isEmpty() ? 1 : 0;

        $html = view('termToMAEvent.showEventGroupingCm', compact('request', 'markingGroupArr'
                        , 'mkGroupArr', 'cmMaGroupInfo', 'mkOtherGroupArr'))->render();
        return response()->json(['html' => $html, 'enableSubmit' => $enableSubmit]);
    }

    public function setGroupingCm(Request $request) {
        if (empty($request->gp_arr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_GROUP')), 401);
        }

        $cmDataArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $cmDataArr = $cmDataArr->whereIn('marking_group.event_group_id', $request->gp_arr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();
        $enableSubmit = !$cmDataArr->isEmpty() ? 1 : 0;

        $html = view('termToMAEvent.showCmList', compact('request', 'cmDataArr'))->render();
        return response()->json(['html' => $html, 'enableSubmit' => $enableSubmit]);
    }

    public function setAddGrouping(Request $request) {
        if (empty($request->gp)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_GROUP')), 401);
        }
        if (empty($request->selected_cm)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.NO_CM_IS_SELECTED_FOR_THIS_MA_GROUP_YET')), 401);
        }

        $gpArr = [];
        if (!empty($request->gp)) {
            foreach ($request->gp as $gpId => $gpId) {
                $gpArr[] = $gpId;
            }
        }

        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;

        $maGroup = new MaGroup;
        $maGroup->course_id = $request->ma_course_id;
        $maGroup->term_id = $request->ma_term_id;
        $maGroup->ma_event_id = $request->ma_event_id;
        $maGroup->event_id = $request->event_id;
        $maGroup->sub_event_id = $subEventId;
        $maGroup->sub_sub_event_id = $subSubEventId;
        $maGroup->sub_sub_sub_event_id = $subSubSubEventId;
        $maGroup->event_group_id = $request->event_group_id;
        $maGroup->mk_groups = !empty($gpArr) ? implode(",", $gpArr) : '';
        $maGroup->updated_at = date('Y-m-d H:i:s');
        $maGroup->updated_by = Auth::user()->id;

        DB::beginTransaction();
        try {
            $prevMaGroupList = MaGroup::where('course_id', $request->ma_course_id)->where('term_id', $request->ma_term_id)
                            ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                            ->where('sub_event_id', $subEventId)->where('sub_sub_event_id', $subSubEventId)
                            ->where('sub_sub_sub_event_id', $subSubSubEventId)->where('event_group_id', $request->event_group_id)
                            ->pluck('id')->toArray();


            MaGroup::where('course_id', $request->ma_course_id)->where('term_id', $request->ma_term_id)
                    ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                    ->where('sub_event_id', $subEventId)->where('sub_sub_event_id', $subSubEventId)
                    ->where('sub_sub_sub_event_id', $subSubSubEventId)->where('event_group_id', $request->event_group_id)
                    ->delete();

            CmMaGroup::whereIn('ma_group_id', $prevMaGroupList)->delete();

            if ($maGroup->save()) {
                $cmMaGroup = [];
                $cmI = 0;
                if (!empty($request->selected_cm)) {
                    foreach ($request->selected_cm as $cmId => $cmId) {
                        $cmMaGroup[$cmI]['ma_group_id'] = $maGroup->id;
                        $cmMaGroup[$cmI]['cm_id'] = $cmId;
                        $cmMaGroup[$cmI]['updated_at'] = date('Y-m-d H:i:s');
                        $cmMaGroup[$cmI]['updated_by'] = Auth::user()->id;
                        $cmI++;
                    }
                }

                CmMaGroup::insert($cmMaGroup);
            }


            DB::commit();
            return Response::json(['success' => true, 'message' => __('label.MA_GROUP_HAS_BEEN_ASSIGNED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'heading' => 'Error', 'message' => __('label.FAILED_TO_ASSIGN_MARKING_GROUP')), 401);
        }
    }

    public function deleteGrouping(Request $request) {
        if (empty($request->gp)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_GROUP')), 401);
        }
        if (empty($request->selected_cm)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.NO_CM_IS_SELECTED_FOR_THIS_MA_GROUP_YET')), 401);
        }

        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;

        DB::beginTransaction();
        try {
            $prevMaGroupList = MaGroup::where('course_id', $request->ma_course_id)->where('term_id', $request->ma_term_id)
                            ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                            ->where('sub_event_id', $subEventId)->where('sub_sub_event_id', $subSubEventId)
                            ->where('sub_sub_sub_event_id', $subSubSubEventId)->where('event_group_id', $request->event_group_id)
                            ->pluck('id')->toArray();


            MaGroup::where('course_id', $request->ma_course_id)->where('term_id', $request->ma_term_id)
                    ->where('ma_event_id', $request->ma_event_id)->where('event_id', $request->event_id)
                    ->where('sub_event_id', $subEventId)->where('sub_sub_event_id', $subSubEventId)
                    ->where('sub_sub_sub_event_id', $subSubSubEventId)->where('event_group_id', $request->event_group_id)
                    ->delete();

            CmMaGroup::whereIn('ma_group_id', $prevMaGroupList)->delete();

            DB::commit();
            return Response::json(['success' => true, 'message' => __('label.GROUPING_HAS_BEEN_DELETED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'heading' => 'Error', 'message' => __('label.FAILED_TO_DELETE_GROUPING')), 401);
        }
    }

}
