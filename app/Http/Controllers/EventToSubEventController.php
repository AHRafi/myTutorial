<?php

namespace App\Http\Controllers;

use App\EventToSubEvent;
use App\TermToSubEvent;
use App\MarkingGroup;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\Event;
use App\SubEvent;
use App\TrainingYear;
use App\Course;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class EventToSubEventController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_EVENT_TO_SUB_EVENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.RELATE_EVENT_TO_SUB_EVENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::where('status', '1')
                        ->where('has_sub_event', '1')
                        ->where('course_id', $activeCourse->id)
                        ->orderBy('event_code', 'asc')
                        ->pluck('event_code', 'id')->toArray();

        return view('eventToSubEvent.index')->with(compact('activeTrainingYear', 'activeCourse', 'eventList'));
    }

    public function getSubEvent(Request $request) {

        //get event data 
        $targetArr = SubEvent::select('sub_event.id', 'sub_event.event_code')
                ->where('status', '1')
                ->where('sub_event.hidden', '0')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();

        $prevEventToSubEventList = EventToSubEvent::where('course_id', $request->course_id)
                        ->where('event_id', $request->event_id)
                        ->pluck('event_id', 'sub_event_id')->toArray();

        $prevDataArr = EventToSubEvent::where('course_id', $request->course_id)
                        ->where('event_id', $request->event_id)->get();

        $eventList = Event::where('status', '1')
                        ->where('has_sub_event', '1')
                        ->orderBy('event_code', 'asc')
                        ->pluck('event_code', 'id')->toArray();


        $checkHasSubSubEvent = $checkHasDsAssesment = $checkAvgMarking = [];
        $i = 0;
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                if ($item->has_sub_sub_event == 1) {
                    $checkHasSubSubEvent[$i] = $item->sub_event_id;
                    $i++;
                }
                if ($item->has_ds_assesment == 1) {
                    $checkHasDsAssesment[$i] = $item->sub_event_id;
                    $i++;
                }
                if ($item->avg_marking == 1) {
                    $checkAvgMarking[$i] = $item->sub_event_id;
                    $i++;
                }
            }
        }

        $prevDsAssesment = Event::where('id', $request->event_id)
                ->select('has_ds_assesment')
                ->first();
//        echo '<pre>';        print_r($prevHasSubSubEventList);exit;
        //Dependency check Disable data
        $termToSubEventDataArr = TermToSubEvent::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->pluck('sub_event_id', 'sub_event_id')
                ->toArray();

        $markingGroupDataArr = MarkingGroup::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', '!=', 0)
                ->pluck('sub_event_id', 'sub_event_id')
                ->toArray();


//        echo '<pre>';        print_r($markingGroupDataArr);exit;
        //end

        $html = view('eventToSubEvent.getSubEvent', compact('targetArr', 'prevDataArr', 'eventList'
                        , 'prevEventToSubEventList', 'checkHasDsAssesment', 'request', 'checkHasSubSubEvent'
                        , 'prevDsAssesment', 'termToSubEventDataArr', 'markingGroupDataArr', 'checkAvgMarking'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveEventToSubEvent(Request $request) {
        $subEventArr = $request->sub_event_id;
        $subEventArr = $request->sub_event_id;
        $hasSubSubEventArr = $request->has_sub_sub_event;
        $hasDsAssesmentArr = $request->has_ds_assesment;
        $avgMarkingArr = $request->avg_marking;

        if (empty($subEventArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_EVENT_TO_ATLEAST_ONE_SUB_EVENT')), 401);
        }
        $rules = [
            'sub_event_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($subEventArr)) {
            foreach ($subEventArr as $key => $subEventId) {
                $data[$key]['course_id'] = $request->course_id;
                $data[$key]['event_id'] = $request->event_id;
                $data[$key]['sub_event_id'] = $subEventId;
                $data[$key]['has_sub_sub_event'] = !empty($hasSubSubEventArr[$subEventId]) ? $hasSubSubEventArr[$subEventId] : '0';
                $data[$key]['has_ds_assesment'] = !empty($hasDsAssesmentArr[$subEventId]) ? $hasDsAssesmentArr[$subEventId] : '0';
                $data[$key]['avg_marking'] = !empty($avgMarkingArr[$subEventId]) ? $avgMarkingArr[$subEventId] : '0';
                $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                $data[$key]['updated_by'] = Auth::user()->id;

                if (!empty($hasDsAssesmentArr[$subEventId])) {
                    EventToSubSubEvent::where('course_id', $request->course_id)
                            ->where('event_id', $request->event_id)
                            ->where('sub_event_id', $subEventId)
                            ->update(['has_ds_assesment' => '0']);

                    EventToSubSubSubEvent::where('course_id', $request->course_id)
                            ->where('event_id', $request->event_id)
                            ->where('sub_event_id', $subEventId)
                            ->update(['has_ds_assesment' => '0']);
                }
            }
        }
//        echo '<pre>';        print_r($data);exit;
        EventToSubEvent::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->delete();

        if (EventToSubEvent::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.EVENT_TO_SUB_EVENT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function getAssignedSubEvent(Request $request) {
        $course = Course::where('id', $request->course_id)->select('name', 'id')->first();

        $eventName = Event::select('event_code')
                ->where('id', $request->event_id)
                ->first();

        $assignedSubEventArr = EventToSubEvent::join('sub_event', 'sub_event.id', '=', 'event_to_sub_event.sub_event_id')
                ->select('sub_event.id', 'sub_event.event_code', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.has_ds_assesment', 'event_to_sub_event.avg_marking')
                ->where('event_to_sub_event.course_id', $request->course_id)
                ->where('event_to_sub_event.event_id', $request->event_id)
                ->orderBy('sub_event.event_code', 'asc')
                ->get();

        $view = view('eventToSubEvent.showAssignedSubEvent', compact('assignedSubEventArr', 'eventName', 'course'))->render();
        return response()->json(['html' => $view]);
    }

}
