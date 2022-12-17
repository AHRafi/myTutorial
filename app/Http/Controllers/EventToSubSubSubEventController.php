<?php

namespace App\Http\Controllers;

use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\TermToSubSubSubEvent;
use App\MarkingGroup;
use App\Event;
use App\SubEvent;
use App\SubSubEvent;
use App\SubSubSubEvent;
use App\TrainingYear;
use App\Course;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class EventToSubSubSubEventController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_EVENT_TO_SUB_SUB_SUB_EVENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.RELATE_EVENT_TO_SUB_SUB_SUB_EVENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::join('event_to_sub_event', 'event_to_sub_event.event_id', '=', 'event.id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.event_id', '=', 'event.id')
                        ->where('event.status', '1')
                        ->where('event.course_id', $activeCourse->id)
                        ->where('event.has_sub_event', '1')
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();


        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')];

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')];

        return view('eventToSubSubSubEvent.index')->with(compact('activeTrainingYear', 'activeCourse', 'eventList', 'subEventList', 'subSubEventList'));
    }

    public function getSubEvent(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + EventToSubEvent::join('sub_event', 'sub_event.id', '=', 'event_to_sub_event.sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.course_id', '=', 'event_to_sub_event.course_id');
                            $join->on('event_to_sub_sub_event.event_id', '=', 'event_to_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'event_to_sub_event.sub_event_id');
                        })
                        ->where('event_to_sub_event.course_id', $request->course_id)
                        ->where('event_to_sub_event.event_id', $request->event_id)
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $html = view('eventToSubSubSubEvent.showSubEvent', compact('subEventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubSubEvent(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + EventToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'event_to_sub_sub_event.sub_sub_event_id')
                        ->where('event_to_sub_sub_event.course_id', $request->course_id)
                        ->where('event_to_sub_sub_event.event_id', $request->event_id)
                        ->where('event_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();

        $html = view('eventToSubSubSubEvent.showSubSubEvent', compact('subSubEventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEvent(Request $request) {

        //get event data 
        $targetArr = SubSubSubEvent::select('sub_sub_sub_event.id', 'sub_sub_sub_event.event_code')
                ->where('sub_sub_sub_event.status', '1')
                ->where('sub_sub_sub_event.hidden', '0')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();

        $prevEventToSubSubSubEventList = EventToSubSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->pluck('sub_sub_event_id', 'sub_sub_sub_event_id')
                ->toArray();

        $prevDataArr = EventToSubSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->get();

        $eventList = Event::where('status', '1')
                        ->where('has_sub_event', '1')
                        ->orderBy('order', 'asc')
                        ->pluck('event_code', 'id')->toArray();


        $checkHasDsAssesment = [];
        $i = 0;
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {

                if ($item->has_ds_assesment == 1) {
                    $checkHasDsAssesment[$i] = $item->sub_sub_sub_event_id;
                    $i++;
                }
            }
        }
        $prevDsAssesment = Event::where('id', $request->event_id)
                ->select('has_ds_assesment')
                ->first();
        $prevDsAssesment1 = EventToSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('course_id', $request->course_id)
                ->select('has_ds_assesment')
                ->first();
        $prevDsAssesment2 = EventToSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->select('has_ds_assesment')
                ->first();

        //Dependency check Disable data
        $termToSubSubSubEventDataArr = TermToSubSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->pluck('sub_sub_sub_event_id', 'sub_sub_sub_event_id')
                ->toArray();

        $markingGroupDataArr = MarkingGroup::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->where('sub_sub_sub_event_id', '!=', 0)
                ->pluck('sub_sub_sub_event_id', 'sub_sub_sub_event_id')
                ->toArray();
//        echo '<pre>';        print_r($termToSubSubSubEventDataArr);exit;
        //end

        $html = view('eventToSubSubSubEvent.getSubSubSubEvent', compact('targetArr', 'prevDataArr', 'eventList'
                        , 'prevEventToSubSubSubEventList', 'checkHasDsAssesment'
                        , 'request', 'prevDsAssesment', 'prevDsAssesment1', 'prevDsAssesment2'
                        , 'termToSubSubSubEventDataArr', 'markingGroupDataArr'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveEventToSubSubSubEvent(Request $request) {

//        echo '<pre>';        print_r($request->sub_sub_sub_event_id);exit;

        $subSubSubEventArr = $request->sub_sub_sub_event_id;
        $hasDsAssesmentArr = $request->has_ds_assesment;

        if (empty($subSubSubEventArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_EVENT_TO_ATLEAST_ONE_SUB_SUB_SUB_EVENT')), 401);
        }
        $rules = [
            'sub_sub_sub_event_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($subSubSubEventArr)) {
            foreach ($subSubSubEventArr as $key => $subSubSubEventId) {
                $data[$key]['course_id'] = $request->course_id;
                $data[$key]['event_id'] = $request->event_id;
                $data[$key]['sub_event_id'] = $request->sub_event_id;
                $data[$key]['sub_sub_event_id'] = $request->sub_sub_event_id;
                $data[$key]['sub_sub_sub_event_id'] = $subSubSubEventId;
                $data[$key]['has_ds_assesment'] = !empty($hasDsAssesmentArr[$subSubSubEventId]) ? $hasDsAssesmentArr[$subSubSubEventId] : '0';
                $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                $data[$key]['updated_by'] = Auth::user()->id;
            }
        }
//        echo '<pre>';        print_r($data);exit;
        EventToSubSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->delete();

        if (EventToSubSubSubEvent::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.EVENT_TO_SUB_SUB_SUB_EVENT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function getAssignedSubSubSubEvent(Request $request) {
        $course = Course::where('id', $request->course_id)->select('name', 'id')->first();

        $eventName = Event::select('event_code')
                ->where('id', $request->event_id)
                ->first();

        $subEventName = SubEvent::select('event_code')
                ->where('id', $request->sub_event_id)
                ->first();


        $subSubEventName = SubSubEvent::select('event_code')
                ->where('id', $request->sub_sub_event_id)
                ->first();


        $assignedSubSubSubEventArr = EventToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->select('sub_sub_sub_event.id', 'sub_sub_sub_event.event_code'
                        , 'event_to_sub_sub_sub_event.has_ds_assesment')
                ->where('event_to_sub_sub_sub_event.course_id', $request->course_id)
                ->where('event_to_sub_sub_sub_event.event_id', $request->event_id)
                ->where('event_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                ->where('event_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();

        $prevDsAssesment = EventToSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('course_id', $request->course_id)
                ->select('has_ds_assesment')
                ->first();

        $prevDsAssesment2 = EventToSubSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->where('course_id', $request->course_id)
                ->select('has_ds_assesment')
                ->first();

        $view = view('eventToSubSubSubEvent.showAssignedSubSubSubEvent', compact('assignedSubSubSubEventArr', 'eventName'
                        , 'subEventName', 'prevDsAssesment', 'subSubEventName', 'prevDsAssesment2', 'course'))->render();
        return response()->json(['html' => $view]);
    }

}
