<?php

namespace App\Http\Controllers;

use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\TrainingYear;
use App\CmAppointment;
use App\EventToApptMatrix;
use App\ApptToCm;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class EventToApptMatrixController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_EVENT_TO_APPT_MATRIX');
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
        $eventList = ['0' => __('label.SELECT_EVENT_OPT')];
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')];
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')];
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')];

        return view('eventToApptMatrix.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'
                                , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'));
    }

//    public function getTerm(Request $request) {
//
//        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
//                        ->where('term_to_course.course_id', $request->course_id)
//                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
//
//        $html = view('eventToApptMatrix.showTerm', compact('termList'))->render();
//
//        return response()->json(['html' => $html]);
//    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $html = view('eventToApptMatrix.showEvent', compact('eventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubEventApptMatrix(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        //checked
        $previousDataArr = EventToApptMatrix::select('appt_exist', 'id', 'appt_id')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->first();

        $apptStringToArr = [];
        if (!empty($previousDataArr['appt_id'])) {
            $apptStringToArr = explode(',', $previousDataArr['appt_id']);
        }
//        echo '<pre>';        print_r($apptStringToArr); exit;
        //checked



        if (sizeof($subEventList) == 1) {
            $targetArr = CmAppointment::select('id', 'name')
                            ->where('status', '1')
                            ->orderBy('order', 'asc')->get();
            //Dependency check Disable data
            $apptToCmDataArr = ApptToCm::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('event_id', $request->event_id)
                    ->pluck('appt_id', 'appt_id')
                    ->toArray();
//        echo '<pre>';        print_r($apptToCmDataArr);exit;
            //end

            $html = view('eventToApptMatrix.showApptMatrix', compact('request', 'previousDataArr', 'targetArr'
                            , 'apptStringToArr', 'apptToCmDataArr'))->render();
        } else {
            $html = view('eventToApptMatrix.showSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubEventApptMatrix(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();

        //checked
        $previousDataArr = EventToApptMatrix::select('appt_exist', 'id', 'appt_id')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->first();

        $apptStringToArr = [];
        if (!empty($previousDataArr['appt_id'])) {
            $apptStringToArr = explode(',', $previousDataArr['appt_id']);
        }
        //checked

        if (sizeof($subSubEventList) == 1) {

            $targetArr = CmAppointment::select('id', 'name')
                            ->where('status', '1')
                            ->orderBy('order', 'asc')->get();

            //Dependency check Disable data
            $apptToCmDataArr = ApptToCm::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('event_id', $request->event_id)
                    ->where('sub_event_id', $request->sub_event_id)
                    ->pluck('appt_id', 'appt_id')
                    ->toArray();
//        echo '<pre>';        print_r($apptToCmDataArr);exit;
            //end

            $html = view('eventToApptMatrix.showApptMatrix', compact('request', 'previousDataArr', 'targetArr'
                            , 'apptStringToArr', 'apptToCmDataArr'))->render();
        } else {
            $html = view('eventToApptMatrix.showSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventApptMatrix(Request $request) {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();

        //checked
        $previousDataArr = EventToApptMatrix::select('appt_exist', 'id', 'appt_id')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->first();
//        echo '<pre>';        print_r($previousDataArr); exit;
        $apptStringToArr = [];
        if (!empty($previousDataArr['appt_id'])) {
            $apptStringToArr = explode(',', $previousDataArr['appt_id']);
        }

        //checked

        if (sizeof($subSubSubEventList) == 1) {
            $targetArr = CmAppointment::select('id', 'name')
                            ->where('status', '1')
                            ->orderBy('order', 'asc')->get();

            //Dependency check Disable data
            $apptToCmDataArr = ApptToCm::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('event_id', $request->event_id)
                    ->where('sub_event_id', $request->sub_event_id)
                    ->where('sub_sub_event_id', $request->sub_sub_event_id)
                    ->pluck('appt_id', 'appt_id')
                    ->toArray();
//        echo '<pre>';        print_r($apptToCmDataArr);exit;
            //end

            $html = view('eventToApptMatrix.showApptMatrix', compact('request', 'previousDataArr', 'targetArr'
                            , 'apptStringToArr', 'apptToCmDataArr'))->render();
        } else {
            $html = view('eventToApptMatrix.showSubSubSubEvent', compact('subSubSubEventList'))->render();
        }
        return response()->json(['html' => $html]);
    }

    public function getApptMatrix(Request $request) {
        //checked
        $previousDataArr = EventToApptMatrix::select('appt_exist', 'id', 'appt_id')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                ->first();

        $apptStringToArr = [];
        if (!empty($previousDataArr['appt_id'])) {
            $apptStringToArr = explode(',', $previousDataArr['appt_id']);
        }


//        echo '<pre>';        print_r($previousDataArr); exit;
        //checked

        $targetArr = CmAppointment::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();

        //Dependency check Disable data
        $apptToCmDataArr = ApptToCm::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                ->pluck('appt_id', 'appt_id')
                ->toArray();
//        echo '<pre>';        print_r($apptToCmDataArr);exit;
        //end

        $html = view('eventToApptMatrix.showApptMatrix', compact('request', 'previousDataArr', 'targetArr'
                        , 'apptStringToArr', 'apptToCmDataArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function getAppt(Request $request) {

        $targetArr = CmAppointment::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();

        $html = view('eventToApptMatrix.showAppt', compact('request', 'targetArr'))->render();

        return response()->json(['html' => $html]);
    }

    public function saveEventToApptMatrix(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;


        $apptArr = $request->appt_id;
        $errors = [];
        if (empty($apptArr)) {
            $errors[] = __('label.PLEASE_CHECK_AT_LEAST_ONE_APPT');
        }
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

        $apptArrayToString = implode(',', $apptArr);


        $target = new EventToApptMatrix;
        $target->course_id = $request->course_id;
        $target->term_id = $request->term_id;
        $target->event_id = $request->event_id;
        $target->sub_event_id = !empty($request->sub_event_id) ? $request->sub_event_id : '0';
        $target->sub_sub_event_id = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : '0';
        $target->sub_sub_sub_event_id = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : '0';
        $target->appt_exist = '1';
        $target->appt_id = $apptArrayToString;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        EventToApptMatrix::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', !empty($request->sub_event_id) ? $request->sub_event_id : '0')
                ->where('sub_sub_event_id', !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : '0')
                ->where('sub_sub_sub_event_id', !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : '0')
                ->delete();

        if ($target->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.APPT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

}
