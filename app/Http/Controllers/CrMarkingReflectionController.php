<?php

namespace App\Http\Controllers;

use App\Course;
use App\CrMarkingReflection;
use App\CrMarkingSlab;
use App\CrGeneration;
use App\CrTrait;
use App\TrainingYear;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\EventAssessmentMarking;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\Term;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class CrMarkingReflectionController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')];
        $closedTrainingYear = TrainingYear::where('status', '2')->orderBy('start_date', 'desc')
                        ->select('name', 'id')->first();

        $activeTrainingYear = TrainingYear::where('status', '1')->select('name', 'id')->first();

        if (!empty($activeTrainingYear)) {
            $trainingYearList[$activeTrainingYear->id] = $activeTrainingYear->name;
        }
        if (!empty($closedTrainingYear)) {
            $trainingYearList[$closedTrainingYear->id] = $closedTrainingYear->name;
        }

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $traitList = ['0' => __('label.SELECT_TRAIT_OPT')] + CrTrait::orderBy('order', 'asc')
//                        ->where('for_grading_sentence', '0')->where('for_recomnd_sentence', '0')
                        ->where('status', '1')->pluck('title', 'id')->toArray();

        return view('crSetup.markingReflection.index')->with(compact('trainingYearList', 'courseList', 'traitList'));
    }

    public function getCourse(Request $request) {

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('crSetup.markingReflection.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getReflection(Request $request) {

        $prevReflection = CrMarkingReflection::select('reflection_type', 'wt_reflection')
                ->where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->first();
        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->get();

        $prevWtReflArr = !empty($prevReflection->wt_reflection) ? json_decode($prevReflection->wt_reflection, true) : [];

//        echo '<pre>';
//        print_r($prevReflection->toArray());
//        print_r($prevWtReflArr);
//        exit;
        //wt reflection list
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id)
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event.has_sub_event')
                ->orderBy('event.event_code', 'asc')
                ->get();
        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                $eventMksWtArr['event'][$ev->event_id]['name'] = $ev->event_code ?? '';

                if ($ev->has_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['name'] = $ev->event_code ?? '';
                }
            }
        }

        //sub event info
        $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $request->course_id)
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'event.event_code')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['event'][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                $eventMksWtArr['event'][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';

                if ($subEv->has_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['name'] = $subEv->sub_event_code ?? '';
                }
            }
        }

        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $request->course_id)
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                        , 'sub_event.event_code as sub_event_code', 'event.event_code')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                $eventMksWtArr['event'][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';

                if ($subSubEv->has_sub_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['name'] = $subSubEv->sub_sub_event_code ?? '';
                }
            }
        }

        //sub sub sub event info
        $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                ->join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['event'][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';
            }
        }


        $eventMksWtArr2 = [];
        if (!empty($eventMksWtArr['event'])) {
            foreach ($eventMksWtArr['event'] as $eventId => $evInfo) {
                if (sizeof($evInfo) == 1) {
                    $subEventId = $subSubEventId = $subSubSubEventId = 0;
                    $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                    $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                }

                foreach ($evInfo as $subEventId => $subEvInfo) {
                    if (is_int($subEventId)) {
                        if (sizeof($subEvInfo) == 1) {
                            $subSubEventId = $subSubSubEventId = 0;
                            $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                            $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                        }
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            if (is_int($subSubEventId)) {
                                if (sizeof($subSubEvInfo) == 1) {
                                    $subSubSubEventId = 0;
                                    $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                    $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                }
                                foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                    if (is_int($subSubSubEventId)) {
                                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $eventMksWtArr['mks_wt'] = $eventMksWtArr2;
//        echo '<pre>';
//        print_r($eventMksWtArr['mks_wt']);
//        exit;


        $html = view('crSetup.markingReflection.getReflection', compact('request', 'prevReflection'
                        , 'eventMksWtArr', 'prevWtReflArr', 'prevCrGen'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveReflection(Request $request) {
        $messages = [];
        $rules = [
            'course_id' => 'required|not_in:0',
            'trait_id' => 'required|not_in:0',
            'reflection_type' => 'required|not_in:0',
        ];

        if ($request->reflection_type == '2') {
            $rules['wt_reflection'] = 'required';
            $messages['wt_reflection.required'] = __('label.PLEASE_CHOOSE_WT_BASED_CRITERIA_OR_EVENTS_FOR_MARKING_REFLECTION');
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }


        $wtRefl = ($request->reflection_type == '2' && !empty($request->wt_reflection)) ? json_encode($request->wt_reflection) : '';

        $prevReflectionData = CrMarkingReflection::where('course_id', $request->course_id)
                        ->where('trait_id', $request->trait_id)->select('id')->first();
        $reflection = !empty($prevReflectionData->id) ? CrMarkingReflection::find($prevReflectionData->id) : new CrMarkingReflection;
        $reflection->course_id = $request->course_id;
        $reflection->trait_id = $request->trait_id;
        $reflection->reflection_type = $request->reflection_type;
        $reflection->wt_reflection = $wtRefl;
        $reflection->updated_at = date('Y-m-d H:i:s');
        $reflection->updated_by = Auth::user()->id;

        if ($reflection->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.MARKING_REFLECTION_TO_TRAIT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

}
