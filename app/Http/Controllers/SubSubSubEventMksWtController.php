<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\SubSubEventMksWt;
use App\TermToSubSubSubEvent;
use App\SubSubSubEventMksWt;
use App\EventAssessmentMarking;
use App\EventToSubEvent;
use Auth;
use DB;
use Validator;
use Illuminate\Http\Request;
use Response;

class SubSubSubEventMksWtController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.SUB_SUB_SUB_EVENT_MKS_WT_DISTRIBUTION');
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

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_event.event_id');
                        })
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_event.event_id');
                        })
                        ->where('term_to_event.course_id', $activeCourse->id)
                        ->where('event.status', '1')
                        ->where('event.has_sub_event', '1')
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        return view('subSubSubEventMksWt.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'eventList'));
    }

//    public function getEvent(Request $request) {
//
//        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
//                        ->where('term_to_event.course_id', $request->course_id)
//                        ->where('event.status', '1')
//                        ->where('event.has_sub_event', '1')
//                        ->orderBy('event.event_code', 'asc')
//                        ->pluck('event.event_code', 'event.id')
//                        ->toArray();
//        $html = view('subSubSubEventMksWt.getEvent', compact('eventList'))->render();
//        return response()->json(['html' => $html]);
//    }

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
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('sub_event.status', '1')
                        ->where('event_to_sub_event.has_sub_sub_event', '1')
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')
                        ->toArray();
        $html = view('subSubSubEventMksWt.getSubEvent', compact('subEventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSubSubEvent(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event.status', '1')
                        ->where('event_to_sub_sub_event.has_sub_sub_sub_event', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();
        $html = view('subSubSubEventMksWt.getSubSubEvent', compact('subSubEventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventMksWt(Request $request) {

        $assignedMksWtArr = SubSubEventMksWt::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'wt')
                ->first();

        // get sub sub sub event
        $subSubSubEventArr = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                ->where('sub_sub_sub_event.status', '1')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                ->toArray();
        
        
        //find if sub event allow avg marking
        $avgMarkingArr = EventToSubEvent::where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('avg_marking', '1')->first();

        // get previous data
        $subSubSubEventMksWtDataArr = SubSubSubEventMksWt::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'sub_sub_sub_event_id', 'wt')
                ->get();
        $subSubSubEventMksWtArr = [];
        $total = 0;
        if (!$subSubSubEventMksWtDataArr->isEmpty()) {
            foreach ($subSubSubEventMksWtDataArr as $subSubSubEventData) {
                $subSubSubEventMksWtArr[$subSubSubEventData->sub_sub_sub_event_id] = $subSubSubEventData->toArray();
                $total += $subSubSubEventData->wt;
            }
            //if sub event allow avg marking
            $count = !empty($avgMarkingArr) ? sizeof($subSubSubEventMksWtDataArr) : 1;
            $total = $total/$count;
        } else {
            $subSubSubEventMksWtArr['mks_limit'] = $assignedMksWtArr->mks_limit ?? null;
            $subSubSubEventMksWtArr['highest_mks_limit'] = $assignedMksWtArr->highest_mks_limit ?? null;
            $subSubSubEventMksWtArr['lowest_mks_limit'] = $assignedMksWtArr->lowest_mks_limit ?? null;
        }
        
        //dependency
        $eventAssessmentMarkingData = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->whereNotNull('mks')
                ->pluck('sub_sub_sub_event_id', 'sub_sub_sub_event_id')
                ->toArray();


//        echo '<pre>';
//        print_r($subSubSubEventMksWtArr);
//        exit;


        $html = view('subSubSubEventMksWt.showSubSubSubEventMksWt', compact('subSubSubEventMksWtArr', 'assignedMksWtArr'
                        , 'subSubSubEventArr', 'total', 'eventAssessmentMarkingData', 'avgMarkingArr'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveSubSubSubEventMksWt(Request $request) {

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
        SubSubSubEventMksWt::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id)
                ->delete();

        $i = 0;
        if (!empty($request->event_mks_wt)) {
            foreach ($request->event_mks_wt as $subSubSubEventId => $mksWtInfo) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['event_id'] = $request->event_id;
                $data[$i]['sub_event_id'] = $request->sub_event_id;
                $data[$i]['sub_sub_event_id'] = $request->sub_sub_event_id;
                $data[$i]['sub_sub_sub_event_id'] = $subSubSubEventId;
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
        if (SubSubSubEventMksWt::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.WT_COULD_NOT_BE_DISTRIBUTED')), 401);
        }
    }
    
    public function deleteSubSubSubEventMksWt(Request $request) {
        // Delete previous record for this course_id
        $subSubSubEventMksWt = SubSubSubEventMksWt::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $request->sub_event_id)
                ->where('sub_sub_event_id', $request->sub_sub_event_id);
        
        if ($subSubSubEventMksWt->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => 'Mks & Wt could not be deleted'), 401);
        }
    }

}
