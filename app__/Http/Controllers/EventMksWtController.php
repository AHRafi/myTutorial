<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\CriteriaWiseWt;
use App\EventMksWt;
use App\SubEventMksWt;
use App\SubSubEventMksWt;
use App\SubSubSubEventMksWt;
use App\TermToEvent;
use App\EventAssessmentMarking;
use Auth;
use DB;
use Validator;
use Illuminate\Http\Request;
use Response;

class EventMksWtController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.EVENT_MKS_WT_DISTRIBUTION');
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
        
        $totalEventWt = CriteriaWiseWt::where('course_id', $activeCourse->id)->select('total_event_wt')->first();

        $assignedMksWtArr = Course::where('id', $activeCourse->id)
                ->where('status', '1')
                ->select('event_mks_limit', 'highest_mks_limit', 'lowest_mks_limit')
                ->first();

        // get event
        $eventArr = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $activeCourse->id)
                ->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();


        // get previous data
        $eventMksWtDataArr = EventMksWt::where('course_id', $activeCourse->id)
                ->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'event_id', 'wt')
                ->get();
        $eventMksWtArr = [];
        $total = 0;
        if (!$eventMksWtDataArr->isEmpty()) {
            foreach ($eventMksWtDataArr as $eventData) {
                $eventMksWtArr[$eventData->event_id] = $eventData->toArray();
                $total += $eventData->wt;
            }
        }else{
            $eventMksWtArr['mks_limit'] = $assignedMksWtArr->event_mks_limit ?? null;
            $eventMksWtArr['highest_mks_limit'] = $assignedMksWtArr->highest_mks_limit ?? null;
            $eventMksWtArr['lowest_mks_limit'] = $assignedMksWtArr->lowest_mks_limit ?? null;
        }
        
        //dependency
        $eventAssessmentMarkingData = EventAssessmentMarking::where('course_id', $activeCourse->id)
                ->whereNotNull('mks')
                ->pluck('event_id', 'event_id')
                ->toArray();
        
        return view('eventMksWt.index')->with(compact('activeTrainingYearInfo', 'activeCourse'
                , 'totalEventWt', 'eventMksWtArr', 'assignedMksWtArr', 'eventArr', 'total', 'eventAssessmentMarkingData'));
    }

//    public function getEventMksWt(Request $request) {
//
//        $totalEventWt = CriteriaWiseWt::where('course_id', $request->course_id)->select('total_event_wt')->first();
//
//        $assignedMksWtArr = Course::where('id', $request->course_id)
//                ->where('status', '1')
//                ->select('event_mks_limit', 'highest_mks_limit', 'lowest_mks_limit')
//                ->first();
//
//        // get event
//        $eventArr = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
//                ->where('term_to_event.course_id', $request->course_id)
//                ->where('event.status', '1')
//                ->orderBy('event.event_code', 'asc')
//                ->pluck('event.event_code', 'event.id')
//                ->toArray();
//
//
//        // get previous data
//        $eventMksWtDataArr = EventMksWt::where('course_id', $request->course_id)
//                ->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'event_id', 'wt')
//                ->get();
//        $eventMksWtArr = [];
//        $total = 0;
//        if (!$eventMksWtDataArr->isEmpty()) {
//            foreach ($eventMksWtDataArr as $eventData) {
//                $eventMksWtArr[$eventData->event_id] = $eventData->toArray();
//                $total += $eventData->wt;
//            }
//        }else{
//            $eventMksWtArr['mks_limit'] = $assignedMksWtArr->event_mks_limit ?? null;
//            $eventMksWtArr['highest_mks_limit'] = $assignedMksWtArr->highest_mks_limit ?? null;
//            $eventMksWtArr['lowest_mks_limit'] = $assignedMksWtArr->lowest_mks_limit ?? null;
//        }
//
//        $html = view('eventMksWt.showEventMksWt', compact('totalEventWt', 'eventMksWtArr', 'assignedMksWtArr'
//                        , 'eventArr', 'total'))->render();
//        return response()->json(['html' => $html]);
//    }

    public function saveEventMksWt(Request $request) {
        
        $totalEventWt = $request->total_event_wt;
        // Validation
        $rules = $message = $errors = [];
        if ($request->total_event_wt != $request->total_wt) {
            $errors[] = __('label.THE_TOTAL_WT_MUST_BE_EQUAL_TO', ['total_event_wt' => $totalEventWt]);
        }

        $row = 1;
        if (!empty($request->event_mks_wt)) {
            foreach ($request->event_mks_wt as $eventId => $eInfo) {
                $rules['event_mks_wt.' . $eventId . '.mks'] = 'required';
                $rules['event_mks_wt.' . $eventId . '.highest'] = 'required';
                $rules['event_mks_wt.' . $eventId . '.lowest'] = 'required';
                $rules['event_mks_wt.' . $eventId . '.wt'] = 'required';
                $message['event_mks_wt.' . $eventId . '.mks' . '.required'] = __('label.MKS_IS_REQUIRED_FOR_SER', ['row' => $row]);
                $message['event_mks_wt.' . $eventId . '.highest' . '.required'] = __('label.HIGHEST_MKS_IS_REQUIRED_FOR_SER', ['row' => $row]);
                $message['event_mks_wt.' . $eventId . '.lowest' . '.required'] = __('label.LOWEST_MKS_IS_REQUIRED_FOR_SER', ['row' => $row]);
                $message['event_mks_wt.' . $eventId . '.wt' . '.required'] = __('label.WT_IS_REQUIRED_FOR_SER', ['row' => $row]);
                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()], 400);
        }

        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 400);
        }
        // End validation
        // Delete previous record for this course_id
        $eventMksWtArr = EventMksWt::where('course_id', $request->course_id)->delete();

        $i = 0;
        if (!empty($request->event_mks_wt)) {
            foreach ($request->event_mks_wt as $eventId => $mksWtInfo) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['event_id'] = $eventId;
                $data[$i]['mks_limit'] = $mksWtInfo['mks'];
                $data[$i]['highest_mks_limit'] = $mksWtInfo['highest'];
                $data[$i]['lowest_mks_limit'] = $mksWtInfo['lowest'];
                $data[$i]['wt'] = !empty($mksWtInfo['wt']) ? $mksWtInfo['wt'] : 0.00;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }
//echo '<pre>';
//                print_r($request->course_id);
//        exit;
        if (EventMksWt::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.WT_COULD_NOT_BE_DISTRIBUTED')), 401);
        }
    }
    
    public function deleteEventMksWt(Request $request) {
        $eventMksWt = EventMksWt::where('course_id', $request->course_id);
        
        SubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        SubSubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        SubSubSubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        if ($eventMksWt->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => 'Mks & Wt could not be deleted'), 401);
        }
    }

}
