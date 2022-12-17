<?php

namespace App\Http\Controllers;

use Validator;
use App\EventGroupToCourse;
use App\TrainingYear;
use App\Course;
use App\EventGroup;
use App\MarkingGroup;
use Response;
use Auth;
use Illuminate\Http\Request;

class EventGroupToCourseController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_EVENT_GROUP_TO_COURSE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        
        $targetArr = EventGroup::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = EventGroupToCourse::select('event_group_id', 'id')
                        ->where('course_id', $activeCourse->id)
                        ->get()->toArray();
        

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['event_group_id']] = $previousData['event_group_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $markingGroupDataArr = MarkingGroup::where('course_id', $activeCourse->id)
                        ->pluck('event_group_id','event_group_id')
                        ->toArray();
        
        $disableEventGroup = [];
        //end
        
        return view('eventGroupToCourse.index')->with(compact('activeTrainingYear', 'activeCourse'
                , 'targetArr', 'previousDataList', 'disableEventGroup', 'markingGroupDataArr'));
    }

//    public function getEventGroup(Request $request) {
//
//        $targetArr = EventGroup::select('id', 'name')
//                        ->where('status', '1')
//                        ->orderBy('order', 'asc')->get();
//        //checked
//        $previousDataArr = EventGroupToCourse::select('event_group_id', 'id')
//                        ->where('course_id', $request->course_id)
//                        ->get()->toArray();
//        
//
//        $previousDataList = [];
//        if (!empty($previousDataArr)) {
//            foreach ($previousDataArr as $previousData) {
//                $previousDataList[$previousData['event_group_id']] = $previousData['event_group_id'];
//            }
//        }
//        //checked
//        //Dependency check Disable data
//        $disableEventGroup = [];
//        //end
//        $html = view('eventGroupToCourse.showEventGroup', compact('targetArr', 'previousDataList', 'disableEventGroup'))->render();
//        return response()->json(['html' => $html]);
//    }

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
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($eventGroupArr)) {
                foreach ($eventGroupArr as $key => $eventGroupId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['event_group_id'] = $eventGroupId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            EventGroupToCourse::where('course_id', $request->course_id)
                    ->delete();
        }

        if (EventGroupToCourse::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_EVENT_GROUP')), 401);
        }
    }

}
