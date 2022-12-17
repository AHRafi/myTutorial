<?php

namespace App\Http\Controllers;

use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\TrainingYear;
use App\Event;
use App\SubEvent;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventAssessmentMarking;
use App\Term;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class TermToSubSubEventController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_TERM_TO_SUB_SUB_EVENT');
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
                        ->orderBy('term.order', 'asc')
                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
        $eventList = ['0' => __('label.SELECT_EVENT_OPT')];
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')];

        return view('termToSubSubEvent.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'
                                , 'eventList', 'subEventList'));
    }

//    public function getTerm(Request $request) {
//
//        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
//                        ->where('term_to_course.course_id', $request->course_id)
//                        ->orderBy('term.order', 'asc')
//                        ->where('term.status', '1')->pluck('term.name', 'term.id')->toArray();
//
//        $html = view('termToSubSubEvent.showTerm', compact('termList'))->render();
//
//        return response()->json(['html' => $html]);
//    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->join('term_to_sub_event', 'term_to_sub_event.event_id', '=', 'term_to_event.event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.event_id', '=', 'term_to_event.event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.event_id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.has_sub_event', '1')
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $html = view('termToSubSubEvent.showEvent', compact('eventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubEvent(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $html = view('termToSubSubEvent.showSubEvent', compact('subEventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubSubEvent(Request $request) {

        //get event data 
        $targetArr = EventToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'event_to_sub_sub_event.sub_sub_event_id')
                ->where('event_to_sub_sub_event.event_id', $request->event_id)
                ->where('event_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                ->select('sub_sub_event.id', 'sub_sub_event.event_code', 'event_to_sub_sub_event.has_sub_sub_sub_event')
                ->orderBy('sub_sub_event.event_code', 'asc')->get();
//        echo '<pre>';        print_r($targetArr->toArray()); exit;

        $prevTermToSubSubEventList = TermToSubSubEvent::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->pluck('sub_event_id', 'sub_sub_event_id')->toArray();

        $prevDataArr = TermToSubSubEvent::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->get();

        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->sub_sub_event_id][] = $item->term_id;
            }
        }

        $hasChild = TermToSubSubSubEvent::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->pluck('sub_sub_sub_event_id', 'sub_sub_event_id')->toArray();
//        echo '<pre>'; print_r($hasChild); exit;   


        $hasSubSubSubEvent = TermToSubSubEvent::where('course_id', $request->course_id)
                        ->where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->pluck('term_id', 'sub_sub_event_id')->toArray();

        $termList = Term::pluck('name', 'id')->toArray();
        
        //dependency
        $eventAssessmentMarkingData = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->whereNotNull('mks')
                ->pluck('sub_sub_event_id', 'sub_sub_event_id')
                ->toArray();


        $html = view('termToSubSubEvent.getSubSubEvent', compact('targetArr', 'prevDataArr', 'termList', 'prevDataList'
                        , 'prevTermToSubSubEventList', 'request', 'hasChild', 'hasSubSubSubEvent', 'eventAssessmentMarkingData'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveTermToSubSubEvent(Request $request) {
        $subSubEventArr = $request->sub_sub_event_id;
        if (empty($subSubEventArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_TERM_TO_ATLEAST_ONE_SUB_SUB_EVENT')), 401);
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
            'sub_event_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        $i = 0;
        if (!empty($subSubEventArr)) {
            foreach ($subSubEventArr as $subSubEventId => $subSubEventInfo) {
                if (!empty($subSubEventId)) {
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['term_id'] = $request->term_id;
                    $data[$i]['event_id'] = $request->event_id;
                    $data[$i]['sub_event_id'] = $request->sub_event_id;
                    $data[$i]['sub_sub_event_id'] = $subSubEventId;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                }
                $i++;
            }
        }

        TermToSubSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->delete();

        if (TermToSubSubEvent::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.TERM_TO_SUB_SUB_EVENT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }
    
    public function deleteTermToSubSubEvent(Request $request) {
        // Delete previous record for this course_id
        $termToSubSubEvent = TermToSubSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id);
        
        TermToSubSubSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->delete();
        
        if ($termToSubSubEvent->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => 'Sub Sub Event could not be deleted'), 401);
        }
    }

    public function getAssignedSubSubEvent(Request $request) {

        $courseName = Course::select('name')
                ->where('id', $request->course_id)
                ->first();
        $termName = Term::select('name')
                ->where('id', $request->term_id)
                ->first();

        $eventName = Event::select('event_code')
                ->where('id', $request->event_id)
                ->first();

        $subEventName = SubEvent::select('event_code')
                ->where('id', $request->sub_event_id)
                ->first();

        $assignedSubSubEventArr = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->select('sub_sub_event.id', 'sub_sub_event.event_code', 'event_to_sub_sub_event.has_sub_sub_sub_event')
                ->where('term_to_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_event.term_id', $request->term_id)
                ->where('term_to_sub_sub_event.event_id', $request->event_id)
                ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->get();
//        echo '<pre>';        print_r($assignedSubEventArr->toArray());  exit;

        $view = view('termToSubSubEvent.showAssignedSubSubEvent', compact('assignedSubSubEventArr', 'termName', 'courseName', 'eventName', 'subEventName'))->render();
        return response()->json(['html' => $view]);
    }

}
