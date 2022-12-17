<?php

namespace App\Http\Controllers;

use App\Course;
use App\Term;
use App\Event;
use App\SubEvent;
use App\SubSubEvent;
use App\SubSubSubEvent;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\TrainingYear;
use App\CmAppointment;
use App\EventToApptMatrix;
use App\CmBasicProfile;
use App\ApptToCm;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class ApptToCmController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_APPT_TO_CM');
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

        $activeTermInfo = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.course_id', $activeCourse->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->where('term.status', '1')
                ->select('term.id', 'term.name')
                ->first();
        $eventList = ['0' => __('label.SELECT_EVENT_OPT')];

        if (!empty($activeTermInfo)) {
            $eventList = $eventList + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                            ->where('term_to_event.course_id', $activeCourse->id)
                            ->where('term_to_event.term_id', $activeTermInfo['id'])
                            ->where('event.status', '1')
                            ->orderBy('event.event_code', 'asc')
                            ->pluck('event.event_code', 'event.id')->toArray();
        }

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')];
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')];
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')];

        return view('apptToCm.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'activeTermInfo'
                                , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'));
    }

//    public function getTermEvent(Request $request) {
//
//        $activeTermInfo = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
//                ->where('term_to_course.course_id', $request->course_id)
//                ->where('term_to_course.status', '1')
//                ->where('term_to_course.active', '1')
//                ->where('term.status', '1')
//                ->select('term.id', 'term.name')
//                ->first();
//        $eventList = ['0' => __('label.SELECT_EVENT_OPT')];
//
//        if (!empty($activeTermInfo)) {
//            $eventList = $eventList + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
//                            ->where('term_to_event.course_id', $request->course_id)
//                            ->where('term_to_event.term_id', $activeTermInfo['id'])
//                            ->pluck('event.event_code', 'event.id')->toArray();
//        }
//
//        $html = view('apptToCm.showTermEvent', compact('activeTermInfo', 'eventList'))->render();
//
//        return response()->json(['html' => $html]);
//    }

    public function getSubEventCmAppt(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('sub_event.status', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        if (sizeof($subEventList) == 1) {

            $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftJoin('syndicate', 'syndicate.id', '=', 'cm_to_syn.syn_id')
                    ->where('cm_to_syn.course_id', $request->course_id)
                    ->where('cm_to_syn.term_id', $request->term_id)
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name'
                            , 'syndicate.name as syn_name', 'cm_to_syn.syn_id')
                    ->orderBy('syndicate.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            $apptArr = EventToApptMatrix::select('appt_id')
                    ->where('event_to_appt_matrix.course_id', $request->course_id)
                    ->where('event_to_appt_matrix.term_id', $request->term_id)
                    ->where('event_to_appt_matrix.event_id', $request->event_id)
                    ->first();

            $apptStringToArr = $apptList = [];
            if (!empty($apptArr->appt_id)) {
                $apptStringToArr = explode(',', $apptArr->appt_id);
            }
            $apptInfo = CmAppointment::whereIn('id', $apptStringToArr)->select('name', 'id', 'is_unique')
                            ->orderBy('order', 'desc')->get();

            if (!empty($apptInfo)) {
                foreach ($apptInfo as $info) {
                    $apptList[$info->id]['name'] = $info->name;
                    $apptList[$info->id]['is_unique'] = $info->is_unique;
                }
            }

            //start: checked
            $previousDataList = ApptToCm::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('event_id', $request->event_id)
                            ->pluck('appt_id', 'cm_id')->toArray();

            $previousDataOptionDisabledList = $selectedApptArr = [];
            if (!$targetArr->isEmpty()) {
                foreach ($targetArr as $target) {
                    $selectedApptArr[$target->syn_id][$target->id] = !empty($previousDataList) && array_key_exists($target->id, $previousDataList) ? $previousDataList[$target->id] : 0;
                }
            }

            if (!empty($selectedApptArr)) {
                foreach ($selectedApptArr as $synId => $selectedApptInfo) {
                    foreach ($selectedApptInfo as $cmId => $selectedApptId) {
                        if (!empty($apptList)) {
                            foreach ($apptList as $apptId => $info) {
                                if ($apptId != $selectedApptId && $info['is_unique'] == '1') {
                                    if (!empty($selectedApptArr[$synId]) && in_array($apptId, $selectedApptArr[$synId])) {
                                        $previousDataOptionDisabledList[$synId][$cmId][$apptId] = $apptId;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //end: checked


            $html = view('apptToCm.showCmAppt', compact('request', 'targetArr', 'apptList', 'previousDataOptionDisabledList'
                            , 'previousDataList'))->render();
        } else {
            $html = view('apptToCm.showSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubEventCmAppt(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event.status', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();

        if (sizeof($subSubEventList) == 1) {

            $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftJoin('syndicate', 'syndicate.id', '=', 'cm_to_syn.syn_id')
                    ->where('cm_to_syn.course_id', $request->course_id)
                    ->where('cm_to_syn.term_id', $request->term_id)
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name'
                            , 'syndicate.name as syn_name', 'cm_to_syn.syn_id')
                    ->orderBy('syndicate.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            $apptArr = EventToApptMatrix::select('appt_id')
                    ->where('event_to_appt_matrix.course_id', $request->course_id)
                    ->where('event_to_appt_matrix.term_id', $request->term_id)
                    ->where('event_to_appt_matrix.event_id', $request->event_id)
                    ->where('event_to_appt_matrix.sub_event_id', $request->sub_event_id)
                    ->first();


            $apptStringToArr = $apptList = [];
            if (!empty($apptArr->appt_id)) {
                $apptStringToArr = explode(',', $apptArr->appt_id);
            }
            $apptInfo = CmAppointment::whereIn('id', $apptStringToArr)->select('name', 'id', 'is_unique')
                            ->orderBy('order', 'desc')->get();

            if (!empty($apptInfo)) {
                foreach ($apptInfo as $info) {
                    $apptList[$info->id]['name'] = $info->name;
                    $apptList[$info->id]['is_unique'] = $info->is_unique;
                }
            }

            //start: checked
            $previousDataList = ApptToCm::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('event_id', $request->event_id)
                            ->where('sub_event_id', $request->sub_event_id)
                            ->pluck('appt_id', 'cm_id')->toArray();

            $previousDataOptionDisabledList = $selectedApptArr = [];
            if (!$targetArr->isEmpty()) {
                foreach ($targetArr as $target) {
                    $selectedApptArr[$target->syn_id][$target->id] = !empty($previousDataList) && array_key_exists($target->id, $previousDataList) ? $previousDataList[$target->id] : 0;
                }
            }

            if (!empty($selectedApptArr)) {
                foreach ($selectedApptArr as $synId => $selectedApptInfo) {
                    foreach ($selectedApptInfo as $cmId => $selectedApptId) {
                        if (!empty($apptList)) {
                            foreach ($apptList as $apptId => $info) {
                                if ($apptId != $selectedApptId && $info['is_unique'] == '1') {
                                    if (!empty($selectedApptArr[$synId]) && in_array($apptId, $selectedApptArr[$synId])) {
                                        $previousDataOptionDisabledList[$synId][$cmId][$apptId] = $apptId;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //end: checked

            $html = view('apptToCm.showCmAppt', compact('request', 'targetArr', 'apptList', 'previousDataOptionDisabledList'
                            , 'previousDataList'))->render();
        } else {
            $html = view('apptToCm.showSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventCmAppt(Request $request) {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('sub_sub_sub_event.status', '1')
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();


        if (sizeof($subSubSubEventList) == 1) {

            $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->leftJoin('syndicate', 'syndicate.id', '=', 'cm_to_syn.syn_id')
                    ->where('cm_to_syn.course_id', $request->course_id)
                    ->where('cm_to_syn.term_id', $request->term_id)
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name'
                            , 'syndicate.name as syn_name', 'cm_to_syn.syn_id')
                    ->orderBy('syndicate.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            $apptArr = EventToApptMatrix::select('appt_id')
                    ->where('event_to_appt_matrix.course_id', $request->course_id)
                    ->where('event_to_appt_matrix.term_id', $request->term_id)
                    ->where('event_to_appt_matrix.event_id', $request->event_id)
                    ->where('event_to_appt_matrix.sub_event_id', $request->sub_event_id)
                    ->where('event_to_appt_matrix.sub_sub_event_id', $request->sub_sub_event_id)
                    ->first();

            $apptStringToArr = $apptList = [];
            if (!empty($apptArr->appt_id)) {
                $apptStringToArr = explode(',', $apptArr->appt_id);
            }
            $apptInfo = CmAppointment::whereIn('id', $apptStringToArr)->select('name', 'id', 'is_unique')
                            ->orderBy('order', 'desc')->get();

            if (!empty($apptInfo)) {
                foreach ($apptInfo as $info) {
                    $apptList[$info->id]['name'] = $info->name;
                    $apptList[$info->id]['is_unique'] = $info->is_unique;
                }
            }

            //start: checked
            $previousDataList = ApptToCm::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('event_id', $request->event_id)
                            ->where('sub_event_id', $request->sub_event_id)
                            ->where('sub_sub_event_id', $request->sub_sub_event_id)
                            ->pluck('appt_id', 'cm_id')->toArray();

            $previousDataOptionDisabledList = $selectedApptArr = [];
            if (!$targetArr->isEmpty()) {
                foreach ($targetArr as $target) {
                    $selectedApptArr[$target->syn_id][$target->id] = !empty($previousDataList) && array_key_exists($target->id, $previousDataList) ? $previousDataList[$target->id] : 0;
                }
            }

            if (!empty($selectedApptArr)) {
                foreach ($selectedApptArr as $synId => $selectedApptInfo) {
                    foreach ($selectedApptInfo as $cmId => $selectedApptId) {
                        if (!empty($apptList)) {
                            foreach ($apptList as $apptId => $info) {
                                if ($apptId != $selectedApptId && $info['is_unique'] == '1') {
                                    if (!empty($selectedApptArr[$synId]) && in_array($apptId, $selectedApptArr[$synId])) {
                                        $previousDataOptionDisabledList[$synId][$cmId][$apptId] = $apptId;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //end: checked

            $html = view('apptToCm.showCmAppt', compact('request', 'targetArr', 'apptList', 'previousDataOptionDisabledList'
                            , 'previousDataList'))->render();
        } else {
            $html = view('apptToCm.showSubSubSubEvent', compact('subSubSubEventList'))->render();
        }
        return response()->json(['html' => $html]);
    }

    public function getCmAppt(Request $request) {

        $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftJoin('syndicate', 'syndicate.id', '=', 'cm_to_syn.syn_id')
                ->where('cm_to_syn.course_id', $request->course_id)
                ->where('cm_to_syn.term_id', $request->term_id)
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name'
                        , 'syndicate.name as syn_name', 'cm_to_syn.syn_id')
                ->orderBy('syndicate.order', 'asc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $apptArr = EventToApptMatrix::select('appt_id')
                ->where('event_to_appt_matrix.course_id', $request->course_id)
                ->where('event_to_appt_matrix.term_id', $request->term_id)
                ->where('event_to_appt_matrix.event_id', $request->event_id)
                ->where('event_to_appt_matrix.sub_event_id', $request->sub_event_id)
                ->where('event_to_appt_matrix.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('event_to_appt_matrix.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                ->first();

        $apptStringToArr = $apptList = [];
        if (!empty($apptArr->appt_id)) {
            $apptStringToArr = explode(',', $apptArr->appt_id);
        }
        $apptInfo = CmAppointment::whereIn('id', $apptStringToArr)->select('name', 'id', 'is_unique')
                        ->orderBy('order', 'desc')->get();

        if (!empty($apptInfo)) {
            foreach ($apptInfo as $info) {
                $apptList[$info->id]['name'] = $info->name;
                $apptList[$info->id]['is_unique'] = $info->is_unique;
            }
        }

        //start: checked
        $previousDataList = ApptToCm::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                        ->pluck('appt_id', 'cm_id')->toArray();

        $previousDataOptionDisabledList = $selectedApptArr = [];
        if (!$targetArr->isEmpty()) {
            foreach ($targetArr as $target) {
                $selectedApptArr[$target->syn_id][$target->id] = !empty($previousDataList) && array_key_exists($target->id, $previousDataList) ? $previousDataList[$target->id] : 0;
            }
        }

        if (!empty($selectedApptArr)) {
            foreach ($selectedApptArr as $synId => $selectedApptInfo) {
                foreach ($selectedApptInfo as $cmId => $selectedApptId) {
                    if (!empty($apptList)) {
                        foreach ($apptList as $apptId => $info) {
                            if ($apptId != $selectedApptId && $info['is_unique'] == '1') {
                                if (!empty($selectedApptArr[$synId]) && in_array($apptId, $selectedApptArr[$synId])) {
                                    $previousDataOptionDisabledList[$synId][$cmId][$apptId] = $apptId;
                                }
                            }
                        }
                    }
                }
            }
        }
        //end: checked


        $html = view('apptToCm.showCmAppt', compact('request', 'targetArr', 'apptList', 'previousDataOptionDisabledList'
                        , 'previousDataList'))->render();

        return response()->json(['html' => $html]);
    }

    public function saveApptToCm(Request $request) {


//        $cmArr = $request->cm_id;
        $apptArr = $request->appt_id;
        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;
        $errors = [];
//        if (empty($cmArr)) {
//            $errors[] = __('label.PLEASE_CHECK_AT_LEAST_ONE_CM');
//        }

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 401);
        }
//        echo '<pre>';        print_r($request->all()); exit;


        $data = [];
        $i = 0;
        if (!empty($apptArr)) {
            foreach ($apptArr as $cmId => $apptId) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $request->term_id;
                $data[$i]['event_id'] = $request->event_id;
                $data[$i]['sub_event_id'] = $subEventId;
                $data[$i]['sub_sub_event_id'] = $subSubEventId;
                $data[$i]['sub_sub_sub_event_id'] = $subSubSubEventId;
                $data[$i]['cm_id'] = $cmId;
                $data[$i]['appt_id'] = $apptId;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }


        ApptToCm::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $subEventId)
                ->where('sub_sub_event_id', $subSubEventId)
                ->where('sub_sub_sub_event_id', $subSubSubEventId)
                ->delete();

        if (ApptToCm::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_ASSIGNED_APPT_TO_CM')), 401);
        }
    }

    public function getAssignedAppt(Request $request) {


        $apptArr = EventToApptMatrix::select('appt_id')
                ->where('event_to_appt_matrix.course_id', $request->course_id)
                ->where('event_to_appt_matrix.term_id', $request->term_id)
                ->where('event_to_appt_matrix.event_id', $request->event_id)
                ->where('event_to_appt_matrix.sub_event_id', $request->sub_event_id)
                ->where('event_to_appt_matrix.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('event_to_appt_matrix.sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                ->first();

        $apptStringToArr = $apptList = [];
        if (!empty($apptArr->appt_id)) {
            $apptStringToArr = explode(',', $apptArr->appt_id);
        }


        $apptInfo = CmAppointment::whereIn('id', $apptStringToArr)->select('name', 'id', 'is_unique')
                        ->orderBy('order', 'asc')->get();
        $courseName = Course::select('name')->where('id', $request->course_id)
                ->first();
        $termName = Term::select('name')->where('id', $request->term_id)
                ->first();
        $eventName = Event::select('event_code')->where('id', $request->event_id)
                ->first();
        $subEventName = SubEvent::select('event_code')->where('id', $request->sub_event_id)
                ->first();
        $subSubEventName = SubSubEvent::select('event_code')->where('id', $request->sub_sub_event_id)
                ->first();
        $subSubSubEventName = SubSubSubEvent::select('event_code')->where('id', $request->sub_sub_sub_event_id)
                ->first();
//        echo '<pre>';
//        print_r($subSubSubEventName);
//        exit;

        $view = view('apptToCm.showAssignedAppt', compact('apptInfo', 'courseName', 'termName', 'eventName'
                        , 'subEventName', 'subSubEventName', 'subSubSubEventName'))->render();
        return response()->json(['html' => $view]);
    }

}
