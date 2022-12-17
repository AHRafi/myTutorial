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
use App\EventAssessmentMarking;
use App\Term;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class TermToEventController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_TERM_TO_EVENT');
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

        return view('termToEvent.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'));
    }

    public function getEvent(Request $request) {

        //get event data 
        $targetArr = Event::select('event.id', 'event.event_code', 'event.has_sub_event')
                ->where('status', '1')
                ->where('course_id', $request->course_id)
                ->orderBy('event.event_code', 'asc')
                ->get();

        $prevTermToEventList = TermToEvent::where('course_id', $request->course_id)
                        ->pluck('term_id', 'event_id')->toArray();

        $prevCourseWiseTermToEventList = TermToEvent::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->pluck('term_id', 'event_id')->toArray();


        $prevDataArr = TermToEvent::leftJoin('term', 'term.id', '=', 'term_to_event.term_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')->get();


        $chackPrevDataArr = TermToEvent::where('course_id', $request->course_id)
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

        $chackPrevDataList = [];
        if (!empty($chackPrevDataArr)) {
            foreach ($chackPrevDataArr as $item) {
                $chackPrevDataList[$item->event_id] = $item->term_id;
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

        $hasChild = TermToSubEvent::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->pluck('sub_event_id', 'event_id')->toArray();
        
        $hasSubEvent = TermToEvent::where('course_id', $request->course_id)
                        ->pluck('term_id', 'event_id')->toArray();
        //dependency
        $eventAssessmentMarkingData = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->whereNotNull('mks')
                ->pluck('event_id', 'event_id')
                ->toArray();
//        echo '<pre>'; print_r($eventAssessmentMarkingData); exit;   

        $html = view('termToEvent.getEvent', compact('targetArr', 'termList', 'prevDataArr', 'prevDataList'
                        , 'chackPrevDataList', 'prevCourseWiseTermToEventList', 'hasChild', 'hasSubEvent'
                        , 'markingCheck', 'termToParticular', 'prevTermToEventList', 'request', 'eventAssessmentMarkingData'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveTermToEvent(Request $request) {
        $eventArr = $request->event_id;
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

        $data = [];
        $i = 0;
        if (!empty($eventArr)) {
            foreach ($eventArr as $eventId => $eventInfo) {
                if (!empty($eventId)) {
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['term_id'] = $request->term_id;
                    $data[$i]['event_id'] = $eventId;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                }
                $i++;
            }
        }

        TermToEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->delete();

        if (TermToEvent::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.TERM_TO_EVENT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function deleteTermToEvent(Request $request) {
        // Delete previous record for this course_id
        $termToEvent = TermToEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id);

        TermToSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->delete();
        TermToSubSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->delete();
        TermToSubSubSubEvent::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->delete();

        if ($termToEvent->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => 'Event could not be deleted'), 401);
        }
    }

    public function getAssignedEvent(Request $request) {

        $courseName = Course::select('name')
                ->where('id', $request->course_id)
                ->first();
        $termName = Term::select('name')
                ->where('id', $request->term_id)
                ->first();

        $assignedEventArr = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->select('event.id', 'event.event_code', 'event.has_sub_event')
                ->where('term_to_event.course_id', $request->course_id)
                ->where('term_to_event.term_id', $request->term_id)
                ->orderBy('event.event_code', 'asc')
                ->get();
//        dd($assignedEventArr);

        $view = view('termToEvent.showAssignedEvent', compact('assignedEventArr', 'termName', 'courseName'))->render();
        return response()->json(['html' => $view]);
    }

}
