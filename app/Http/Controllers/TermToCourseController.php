<?php

namespace App\Http\Controllers;

use Validator;
use App\TermToCourse;
use App\Course;
use App\TrainingYear;
use App\Term;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\ComdtModerationMarkingLock;
use App\CiModerationMarkingLock;
use App\CiObsnMarking;
use App\User;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\DsObsnMarkingLimit;
use App\DsMarkingGroup;
use App\CmBasicProfile;
use App\CmToSyn;
use Helper;
use Response;
use Auth;
use DB;
use Common;
use Illuminate\Http\Request;

class TermToCourseController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::select('id', 'name')->where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termArr = Term::where('status', '1')->orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $previousDataArr = TermToCourse::select('id', 'course_id', 'term_id', 'initial_date', 'termination_date'
                                , 'number_of_week', 'recess_initial_date', 'recess_termination_date'
                                , 'recess_number_of_week', 'status', 'active')
                        ->where('course_id', $activeCourse->id)
                        ->get()
                ->toArray();

        $prevData = $prevTermArr = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $item) {
                $prevData[$item['term_id']] = $item;
                
                if($item['status'] == '2'){
                    $prevTermArr['status'][$item['term_id']] = $item['term_id'];
                }
                if($item['active'] == '1'){
                    $prevTermArr['active'][$item['term_id']] = $item['term_id'];
                }
            }
        }
        
        //Get Data from Cm to Syndicate
        $cmToSynArr = CmToSyn::where('course_id', $activeCourse->id)
                ->pluck('term_id','term_id')->toArray();
        
        //Get Data from Term to Event
        $termToEventArr = TermToEvent::where('course_id', $activeCourse->id)
                ->pluck('term_id','term_id')->toArray();
        
        return view('termToCourse.index')->with(compact('activeTrainingYear', 'activeCourse', 'termArr'
                                , 'prevData','cmToSynArr','termToEventArr','prevTermArr'));
    }
    
    public function courseSchedule(Request $request) {

        $termArr = Term::where('status', '1')->orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $previousDataArr = TermToCourse::select('id', 'course_id', 'term_id', 'initial_date', 'termination_date'
                                , 'number_of_week', 'recess_initial_date', 'recess_termination_date'
                                , 'recess_number_of_week', 'status', 'active')
                        ->where('course_id', $request->course_id)
                        ->get()->toArray();

        $prevData = $prevTermArr = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $item) {
                $prevData[$item['term_id']] = $item;
                
                if($item['status'] == '2'){
                    $prevTermArr['status'][$item['term_id']] = $item['term_id'];
                }
                if($item['active'] == '1'){
                    $prevTermArr['active'][$item['term_id']] = $item['term_id'];
                }
            }
        }
        
        //Get Data from Cm to Syndicate
        $cmToSynArr = CmToSyn::where('course_id', $request->course_id)
                ->pluck('term_id','term_id')->toArray();
        
        //Get Data from Term to Event
        $termToEventArr = TermToEvent::where('course_id', $request->course_id)
                ->pluck('term_id','term_id')->toArray();


        $html = view('termToCourse.courseSchedule', compact('termArr', 'prevData','cmToSynArr','termToEventArr','prevTermArr'))->render();
        return response()->json(compact('html'));
    }

    public function saveCourse(Request $request) {
        $termArr = $request->term_id;
        $initialDateArr = $request->initial_date;
        $terminationDateArr = $request->termination_date;
        $numberOfWeekArr = $request->number_of_week;
        $recessInitialDateArr = $request->recess_initial_date;
        $recessTerminationDateArr = $request->recess_termination_date;
        $recessNumberOfWeekArr = $request->recess_number_of_week;
        $statusArr = $request->status;
        $activeArr = $request->active;
        $rules = $messages = [];
        $termNameArr = Term::pluck('name', 'id')->toArray();



        if (!empty($termArr)) {
            foreach ($termArr as $termId) {
                $rules['initial_date.' . $termId] = 'required|date';
                $rules['termination_date.' . $termId] = 'required|date|after:initial_date.' . $termId;
                $rules['number_of_week.' . $termId] = 'required|numeric';

                $messages['initial_date.' . $termId . '.required'] = 'Intial Date is Required for ' . $termNameArr[$termId];
                $messages['initial_date.' . $termId . '.date'] = 'Intial Date is Supported Only Date for ' . $termNameArr[$termId];
                $messages['termination_date.' . $termId . '.date'] = 'Termination  Date is Supported Only Date for ' . $termNameArr[$termId];
                $messages['termination_date.' . $termId . '.required'] = 'Termination Date  is Required  for ' . $termNameArr[$termId];
                $messages['termination_date.' . $termId . '.after'] = 'Termination Date  must be greater than Initial Date  for ' . $termNameArr[$termId];
                $messages['number_of_week.' . $termId . '.required'] = 'Number of Week is Required for ' . $termNameArr[$termId];
                $messages['number_of_week.' . $termId . '.numeric'] = 'Number of Week must be Date for ' . $termNameArr[$termId];
            }
        } else {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.NO_TERM_HAS_BEEN_ASSIGNED_WITH_THIS_COURSE')], 401);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        $i = 0;
        if (!empty($termArr)) {
            foreach ($termArr as $termId => $value) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $termId;
                $data[$i]['initial_date'] = Helper::dateFormatConvert($initialDateArr[$termId]);
                $data[$i]['termination_date'] = Helper::dateFormatConvert($terminationDateArr[$termId]);
                $data[$i]['number_of_week'] = $numberOfWeekArr[$termId];
                $data[$i]['recess_initial_date'] = !empty($recessInitialDateArr[$termId]) ? Helper::dateFormatConvert($recessInitialDateArr[$termId]) : null;
                $data[$i]['recess_termination_date'] = !empty($recessTerminationDateArr[$termId]) ? Helper::dateFormatConvert($recessTerminationDateArr[$termId]) : null;
                $data[$i]['recess_number_of_week'] = !empty($recessNumberOfWeekArr[$termId]) ? $recessNumberOfWeekArr[$termId] : null;
                $data[$i]['status'] = !empty($statusArr[$termId]) ? $statusArr[$termId] : '0';
                $data[$i]['active'] = !empty($activeArr[$termId]) ? $activeArr[$termId] : '0';
                $data[$i]['updated_by'] = Auth::user()->id;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $i++;
            }
        }

//        echo '<pre>';
//        print_r($data);
//        exit;

        TermToCourse::where('course_id', $request->course_id)->delete();

        if (TermToCourse::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.NO_TERM_HAS_BEEN_ASSIGNED_WITH_THIS_COURSE')), 401);
        }
    }

    public function activationOrClosing() {
        $activeTrainingYear = TrainingYear::select('id', 'name')->where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.TERM_SCHEDULING_ACTIVATION_CLOSING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }
        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::where('status', '1')->pluck('name', 'id')->toArray();

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        
        
        
        $activeInactiveTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->select('term_to_course.*', 'term.name as term_name')
                ->where('term_to_course.course_id', $activeCourse->id)
                ->where('term.status', '1')->orderBy('term.order', 'asc')
                ->get();
        $termIdArr = $eventMksWtArr = $totalEvent = $closeConditionArr = [];
        if (!$activeInactiveTerm->isEmpty()) {
            foreach ($activeInactiveTerm as $info) {
                $termIdArr[$info->term_id] = $info->term_id;
            }
        }

        //event info
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $activeCourse->id)
                ->whereIn('term_to_event.term_id', $termIdArr)
                ->where('event.status', '1')
                ->select('event.id as event_id', 'event_mks_wt.wt', 'event.has_sub_event', 'term_to_event.term_id')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if (empty($ev->has_sub_event)) {
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
                }
            }
        }

        //sub event info
        $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->leftJoin('sub_event_mks_wt', function($join) {
                    $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $activeCourse->id)
                ->whereIn('term_to_sub_event.term_id', $termIdArr)
                ->where('sub_event.status', '1')
                ->select('sub_event.id as sub_event_id', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'term_to_sub_event.term_id')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                if (empty($subEv->has_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                }
            }
        }

        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->leftJoin('sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $activeCourse->id)
                ->whereIn('term_to_sub_sub_event.term_id', $termIdArr)
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id', 'term_to_sub_sub_event.term_id')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                if (empty($subSubEv->has_sub_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                }
            }
        }

        //sub sub sub event info
        $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                ->join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $activeCourse->id)
                ->whereIn('term_to_sub_sub_sub_event.term_id', $termIdArr)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id', 'term_to_sub_sub_sub_event.term_id')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
            }
        }


        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $termId => $evInfo) {
                foreach ($evInfo as $eventId => $eInfo) {
                    foreach ($eInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            $totalEvent[$termId] = !empty($totalEvent[$termId]) ? $totalEvent[$termId] : 0;
                            $totalEvent[$termId] += count($subSubEvInfo);
                        }
                    }
                }
            }
        }
        
        $totalCm = CmBasicProfile::select('id')->where('course_id', $activeCourse->id)->count();
        
        $dsObsnToBeLockedInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('users', 'users.id', 'ds_marking_group.ds_id')
                ->where('users.status', '1')
                ->where('marking_group.course_id', $activeCourse->id)
                ->whereIn('marking_group.term_id', $termIdArr)
                ->select('marking_group.term_id', 'ds_marking_group.ds_id')
                ->get();
        $dsObsnToBeLocked = [];
        if(!$dsObsnToBeLockedInfo->isEmpty()){
            foreach($dsObsnToBeLockedInfo as $info){
                $dsObsnToBeLocked[$info->term_id][$info->ds_id] = $info->ds_id;
            }
        }
        
        

        $termWiseComdtModarationLock = ComdtModerationMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $activeCourse->id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();
        $termWiseCiModarationLock = CiModerationMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $activeCourse->id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();
        $termWiseDsObsnLock = DsObsnMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $activeCourse->id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();
        
        if (!empty($termIdArr)) {
            foreach ($termIdArr as $termId => $info) {
                $dsObsnToLock = !empty($dsObsnToBeLocked[$termId]) ? sizeof($dsObsnToBeLocked[$termId]) : 0;
                $dsObsnLocked = !empty($termWiseDsObsnLock[$termId]) ? $termWiseDsObsnLock[$termId] : 0;
                $dsObsnUnlocked = $dsObsnToLock - $dsObsnLocked;
                $hasClose = !empty($totalEvent[$termId]) ? $totalEvent[$termId] : 0;
                $canClose = $hasClose - (!empty($termWiseCiModarationLock[$termId]) ? $termWiseCiModarationLock[$termId] : 0);
                $closeConditionArr['has_close'][$termId] = ($hasClose != 0) ? 1 : 0;
                $closeConditionArr['can_close'][$termId] = ($canClose == 0 && $dsObsnUnlocked == 0) ? 1 : 0;
            }
        }

        $ciObsnInfo = CiObsnMarking::select('id')->where('course_id', $activeCourse->id)->get();
        
        

        return view('termToCourse.activationOrClosing', compact('activeTrainingYear', 'trainingYearList'
                , 'activeCourse', 'activeInactiveTerm', 'closeConditionArr', 'ciObsnInfo'));
    }

    public function getActiveOrClose(Request $request) {

        $activeInactiveTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->select('term_to_course.*', 'term.name as term_name')
                ->where('term_to_course.course_id', $request->course_id)
                ->where('term.status', '1')->orderBy('term.order', 'asc')
                ->get();
        $termIdArr = $eventMksWtArr = $totalEvent = $closeConditionArr = [];
        if (!$activeInactiveTerm->isEmpty()) {
            foreach ($activeInactiveTerm as $info) {
                $termIdArr[$info->term_id] = $info->term_id;
            }
        }

        //event info
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id)
                ->whereIn('term_to_event.term_id', $termIdArr)
                ->where('event.status', '1')
                ->select('event.id as event_id', 'event_mks_wt.wt', 'event.has_sub_event', 'term_to_event.term_id')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if (empty($ev->has_sub_event)) {
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
                }
            }
        }

        //sub event info
        $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->leftJoin('sub_event_mks_wt', function($join) {
                    $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $request->course_id)
                ->whereIn('term_to_sub_event.term_id', $termIdArr)
                ->where('sub_event.status', '1')
                ->select('sub_event.id as sub_event_id', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'term_to_sub_event.term_id')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                if (empty($subEv->has_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                }
            }
        }

        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->leftJoin('sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $request->course_id)
                ->whereIn('term_to_sub_sub_event.term_id', $termIdArr)
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id', 'term_to_sub_sub_event.term_id')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                if (empty($subSubEv->has_sub_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                }
            }
        }

        //sub sub sub event info
        $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                ->join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                ->whereIn('term_to_sub_sub_sub_event.term_id', $termIdArr)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id', 'term_to_sub_sub_sub_event.term_id')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
            }
        }


        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $termId => $evInfo) {
                foreach ($evInfo as $eventId => $eInfo) {
                    foreach ($eInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            $totalEvent[$termId] = !empty($totalEvent[$termId]) ? $totalEvent[$termId] : 0;
                            $totalEvent[$termId] += count($subSubEvInfo);
                        }
                    }
                }
            }
        }

        $totalCm = CmBasicProfile::select('id')->where('course_id', $request->course_id)->count();
        
        $dsObsnToBeLockedInfo = DsObsnMarking::where('course_id', $request->course_id)
                ->whereIn('term_id', $termIdArr)
                ->select('term_id', DB::raw('COUNT(id) as total'))
                ->groupBy('term_id')
                ->pluck('total', 'term_id')
                ->toArray();
        $dsObsnToBeLocked = [];
        if(!empty($dsObsnToBeLockedInfo)){
            foreach($dsObsnToBeLockedInfo as $termId => $total){
                $dsObsnToBeLocked[$termId] = $total/(!empty($totalCm) ? $totalCm : 1);
            }
        }
        
        

        $termWiseComdtModarationLock = ComdtModerationMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $request->course_id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();
        $termWiseCiModarationLock = CiModerationMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $request->course_id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();
        $termWiseDsObsnLock = DsObsnMarkingLock::select(DB::raw("count(id) as total_lock"), 'term_id')
                ->where('course_id', $request->course_id)
                ->whereIn('term_id', $termIdArr)
                ->groupBy('term_id')
                ->pluck('total_lock', 'term_id')
                ->toArray();

        if (!empty($termIdArr)) {
            foreach ($termIdArr as $termId => $info) {
                $dsObsnToLock = !empty($dsObsnToBeLocked[$termId]) ? $dsObsnToBeLocked[$termId] : 0;
                $dsObsnLocked = !empty($termWiseDsObsnLock[$termId]) ? $termWiseDsObsnLock[$termId] : 0;
                $dsObsnUnlocked = $dsObsnToLock - $dsObsnLocked;
                $hasClose = !empty($totalEvent[$termId]) ? $totalEvent[$termId] : 0;
                $canClose = $hasClose - (!empty($termWiseCiModarationLock[$termId]) ? $termWiseCiModarationLock[$termId] : 0);
                $closeConditionArr['has_close'][$termId] = ($hasClose != 0) ? 1 : 0;
                $closeConditionArr['can_close'][$termId] = ($canClose == 0 && $dsObsnUnlocked == 0) ? 1 : 0;
            }
        }

        $ciObsnInfo = CiObsnMarking::select('id')->where('course_id', $request->course_id)->get();

        $html = view('termToCourse.getActiveOrClose', compact('activeInactiveTerm', 'closeConditionArr', 'ciObsnInfo'))->render();
        return response()->json(compact('html'));
    }

    public function activeInactive(Request $request) {

        $termInfo = Term::select('term.name')
                        ->join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.term_id', $request->term_id)
                        ->where('term_to_course.id', $request->id)
                        ->where('term.status', '1')->first();

        if ($request->status == '1') {

            //Find out if there is already any active term under this RecruitCourse
            $alreadyActiveTerm = Term::select('term.name')
                    ->join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                    ->where('term_to_course.status', '1')
                    ->where('term_to_course.course_id', $request->course_id)
                    ->first();

            if (!empty($alreadyActiveTerm)) {
                //if any active term found, don't proceed further
//                return Response::json(array('success' => false, 'message' => $alreadyActiveTerm->name . ' ' . __('label.IS_ALREADY_ACTIVE')), 401);
            }

            $update = TermToCourse::where('term_id', $request->term_id)
                    ->where('course_id', $request->course_id)
                    ->where('id', $request->id)
                    ->update(['status' => $request->status, 'active' => '1']);

            $update1 = TermToCourse::where('course_id', $request->course_id)
                    ->where('id', '!=', $request->id)
                    ->update(['active' => '0']);

            if ($update) {
                return Response::json(['success' => true, 'message' => $termInfo->name . ' ' . __('label.HAS_BEEN_ACTIVATED')], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.TERM_COULD_NOT_BE_ACTIVATED')), 401);
            }
        } else {
            $update = TermToCourse::where('id', $request->id)
                    ->update(['status' => $request->status]);
            if ($update) {
                return Response::json(['success' => true, 'message' => $termInfo->name . ' ' . __('label.HAS_BEEN_CLOSED')], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.TERM_COULD_NOT_BE_CLOSED')), 401);
            }
        }
    }

    public function redioAcIn(Request $request) {

        $termInfo = Term::select('term.name')
                        ->join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.term_id', $request->term_id)
                        ->where('term_to_course.id', $request->id)
                        ->where('term.status', '1')->first();
        $checkTermActive = TermToCourse::where('term_id', $request->term_id)
                        ->where('course_id', $request->course_id)
                        ->where('id', $request->id)
                        ->where('active', '1')->first();

        if (!empty($checkTermActive)) {
            return Response::json(['success' => true, 'message' => $termInfo->name . ' ' . __('label.IS_ALREADY_ACTIVE')], 200);
        }

        $update = TermToCourse::where('term_id', $request->term_id)
                ->where('course_id', $request->course_id)
                ->where('id', $request->id)
                ->update(['active' => '1']);

        $update1 = TermToCourse::where('course_id', $request->course_id)
                ->where('id', '!=', $request->id)
                ->update(['active' => '0']);

        if ($update) {
            return Response::json(['success' => true, 'message' => $termInfo->name . ' ' . __('label.HAS_BEEN_ACTIVATED')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.TERM_COULD_NOT_BE_ACTIVATED')), 401);
        }
    }

    public function requestCourseSatatusSummary(Request $request) {
        $loadView = 'termToCourse.showCourseStatusSummary';
        return Common::requestCourseSatatusSummary($request, $loadView);
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'termToCourse.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

}
