<?php

namespace App\Http\Controllers;

use Validator;
use App\EventToEventGroup;
use App\TrainingYear;
use App\Course;
use App\EventGroup;
use App\MarkingGroup;
use App\TermToEvent;
use Response;
use Auth;
use Illuminate\Http\Request;

class EventToEventGroupController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_EVENT_TO_EVENT_GROUP');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.RELATE_EVENT_TO_EVENT_GROUP');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $activeCourse->id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();


        return view('eventToEventGroup.index')->with(compact('activeTrainingYear', 'activeCourse', 'eventList'));
    }

    public function getEventGroup(Request $request) {
        
        $targetArr = EventGroup::select('id', 'name')
                        ->where('status', '1')
                        ->where('hidden', '0')
                        ->orderBy('name', 'asc')->get();
        //checked
        $previousDataArr = EventToEventGroup::select('event_group_id', 'id')
                        ->where('course_id', $request->course_id)
                        ->where('event_id', $request->event_id)
                        ->get()->toArray();


        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['event_group_id']] = $previousData['event_group_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $markingGroupDataArr = MarkingGroup::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->pluck('event_group_id', 'event_group_id')
                ->toArray();
        
//        echo '<pre>';        print_r($markingGroupDataArr); exit;

        $disableEventGroup = [];
        //end
        $html = view('eventToEventGroup.showEventGroup', compact('targetArr', 'previousDataList'
                        , 'disableEventGroup', 'markingGroupDataArr'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveEventGroup(Request $request) {
        
        $eventGroupArr = $request->event_group_id;

        if (empty($eventGroupArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_COURSE_TO_ATLEAST_ONE_EVENT_GROUP')), 401);
        }
        $rules = [
            'event_group_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        $i = 0;
        if (!empty($request->course_id)) {
            if (!empty($eventGroupArr)) {
                foreach ($eventGroupArr as $key => $eventGroupId) {
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['event_id'] = $request->event_id;
                    $data[$i]['event_group_id'] = $eventGroupId;
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $i++;
                }
            }

            EventToEventGroup::where('course_id', $request->course_id)
                    ->where('event_id', $request->event_id)
                    ->delete();
        }
        if (EventToEventGroup::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_EVENT_GROUP')), 401);
        }
    }

}
