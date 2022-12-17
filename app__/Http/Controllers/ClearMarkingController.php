<?php

namespace App\Http\Controllers;

use Validator;
use App\Course;
use App\TrainingYear;
use App\User;
use App\TermToCourse;
use App\EventAssessmentMarking;
use App\EventAssessmentMarkingLock;
use App\CiModerationMarking;
use App\CiModerationMarkingLock;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\DsMarkingGroup;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use Response;
use Auth;
use Common;
use DB;
use Illuminate\Http\Request;

class ClearMarkingController extends Controller {

    public function index(Request $request) {
        $dsDeligationList = Common::getDsDeligationList();
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CLEAR_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.CLEAR_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        // check all terms are closed 
        $activeTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();
        if (empty($activeTermInfo)) {
            $void['header'] = __('label.CLEAR_MARKING');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $courseList->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }

        $canClrEventAssessment = 0;

        $dsObsnInfo = DsObsnMarking::where('course_id', $courseList->id)->where('term_id', $activeTermInfo->id)
                        ->whereNotNull('obsn_mks')->first();
        $ciModInfo = CiModerationMarking::where('course_id', $courseList->id)->where('term_id', $activeTermInfo->id)
                        ->whereNotNull('ci_moderation')->first();

        $canClrEventAssessment = !empty($dsObsnInfo) || !empty($ciModInfo) ? 1 : 0;

        $criteriaList = ['0' => __('label.SELECT_ASSESMENT_CRITERIA')];

        if (empty($dsObsnInfo) || empty($ciModInfo)) {
            $criteriaList = $criteriaList + ['1' => __('label.EVENT_ASSESSMENT')];
        }

        $clearBtnDisabled = 'disabled';
        if (!empty($ciModInfo)) {
            $criteriaList = $criteriaList + ['2' => __('label.CI_MODERATION')];
        }
        if (!empty($dsObsnInfo)) {
            $criteriaList = $criteriaList + ['3' => __('label.DS_OBSN')];
        }
//        echo '<pre>';
//        print_r($prevDataArr);
//        exit;

        return view('clearMarking.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request', 'activeTermInfo'
                                , 'canClrEventAssessment', 'criteriaList', 'clearBtnDisabled'));
    }

    public function getDsEvent(Request $request) {
        $dsList = ['0' => __('label.ALL_DS')] + DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $eventList = ['0' => __('label.ALL_EVENT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $html = view('clearMarking.getDs', compact('dsList'))->render();
        $showEventView = view('clearMarking.getEvent', compact('eventList'))->render();
        return Response::json(['showEventView' => $showEventView, 'html' => $html]);
    }

    public function getDs(Request $request) {
		$dsList = ['0' => __('label.ALL_DS')] + DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $html = view('clearMarking.getDs', compact('dsList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.ALL_EVENT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $showEventView = view('clearMarking.getEvent', compact('eventList'))->render();
        return Response::json(['showEventView' => $showEventView]);
    }

    public function getSubEvent(Request $request) {
        $html = '';
        $hasSubEvent = Event::where('id', $request->event_id)->where('has_sub_event', '1')->first();
        $subEventList = ['0' => __('label.ALL_SUB_EVENT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('sub_event.status', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')
                        ->toArray();

        if (!empty($hasSubEvent)) {
            $html = view('clearMarking.getSubEvent', compact('subEventList'))->render();
        }
        return Response::json(['html' => $html]);
    }

    public function getSubSubEvent(Request $request) {
        $html = '';
        $hasSubSubEvent = EventToSubEvent::where('event_id', $request->event_id)->where('sub_event_id', $request->sub_event_id)
                        ->where('has_sub_sub_event', '1')->first();
        $subSubEventList = ['0' => __('label.ALL_SUB_SUB_EVENT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event.status', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();

        if (!empty($hasSubSubEvent)) {
            $html = view('clearMarking.getSubSubEvent', compact('subSubEventList'))->render();
        }
        return Response::json(['html' => $html]);
    }

    public function getSubSubSubEvent(Request $request) {
        $html = '';
        $hasSubSubSubEvent = EventToSubSubEvent::where('event_id', $request->event_id)->where('sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event_id', $request->sub_sub_event_id)->where('has_sub_sub_sub_event', '1')->first();
        $subSubSubEventList = ['0' => __('label.ALL_SUB_SUB_SUB_EVENT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('sub_sub_sub_event.status', '1')
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        if (!empty($hasSubSubSubEvent)) {
            $html = view('clearMarking.getSubSubSubEvent', compact('subSubSubEventList'))->render();
        }
        return Response::json(['html' => $html]);
    }

    public function doClear(Request $request) {

//        echo '<pre>';        print_r($request->criteria_id); exit;

        $criteria = $request->criteria_id;
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'criteria_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }



        DB::beginTransaction();

        try {

            if ($criteria == '1') {

                $evAsses = EventAssessmentMarking::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->event_id)) {
                    $evAsses = $evAsses->where('event_id', $request->event_id);
                }
                if (!empty($request->sub_event_id)) {
                    $evAsses = $evAsses->where('sub_event_id', $request->sub_event_id);
                }
                if (!empty($request->sub_sub_event_id)) {
                    $evAsses = $evAsses->where('sub_sub_event_id', $request->sub_sub_event_id);
                }
                if (!empty($request->sub_sub_sub_event_id)) {
                    $evAsses = $evAsses->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
                }
                if (!empty($request->ds_id)) {
                    $evAsses = $evAsses->where('updated_by', $request->ds_id);
                }
                $evAsses = $evAsses->delete();


                $evAssesLock = EventAssessmentMarkingLock::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->event_id)) {
                    $evAssesLock = $evAssesLock->where('event_id', $request->event_id);
                }
                if (!empty($request->sub_event_id)) {
                    $evAssesLock = $evAssesLock->where('sub_event_id', $request->sub_event_id);
                }
                if (!empty($request->sub_sub_event_id)) {
                    $evAssesLock = $evAssesLock->where('sub_sub_event_id', $request->sub_sub_event_id);
                }
                if (!empty($request->sub_sub_sub_event_id)) {
                    $evAssesLock = $evAssesLock->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
                }
                if (!empty($request->ds_id)) {
                    $evAssesLock = $evAssesLock->where('locked_by', $request->ds_id);
                }
                $evAssesLock = $evAssesLock->delete();
            }

            if ($criteria == '2') {

                $ciMod = CiModerationMarking::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->event_id)) {
                    $ciMod = $ciMod->where('event_id', $request->event_id);
                }
                if (!empty($request->sub_event_id)) {
                    $ciMod = $ciMod->where('sub_event_id', $request->sub_event_id);
                }
                if (!empty($request->sub_sub_event_id)) {
                    $ciMod = $ciMod->where('sub_sub_event_id', $request->sub_sub_event_id);
                }
                if (!empty($request->sub_sub_sub_event_id)) {
                    $ciMod = $ciMod->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
                }
                $ciMod = $ciMod->delete();



                $ciModLock = CiModerationMarkingLock::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->event_id)) {
                    $ciModLock = $ciModLock->where('event_id', $request->event_id);
                }
                if (!empty($request->sub_event_id)) {
                    $ciModLock = $ciModLock->where('sub_event_id', $request->sub_event_id);
                }
                if (!empty($request->sub_sub_event_id)) {
                    $ciModLock = $ciModLock->where('sub_sub_event_id', $request->sub_sub_event_id);
                }
                if (!empty($request->sub_sub_sub_event_id)) {
                    $ciModLock = $ciModLock->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
                }
                $ciModLock = $ciModLock->delete();
            }

            if ($criteria == '3') {


                $dsObsn = DsObsnMarking::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->ds_id)) {
                    $dsObsn = $dsObsn->where('updated_by', $request->ds_id);
                }
                $dsObsn = $dsObsn->delete();




                $dsObsnLock = DsObsnMarkingLock::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id);
                if (!empty($request->ds_id)) {
                    $dsObsnLock = $dsObsnLock->where('locked_by', $request->ds_id);
                }
                $dsObsnLock = $dsObsnLock->delete();
            }


            $successMsg = __('label.MARKING_HAS_BEEN_CLEARED_SUCCESSFULLY');
            $errorMsg = __('label.MARKING_COULD_NOT_BE_CLEARED');

            DB::commit();
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => $errorMsg], 401);
        }
    }

    public function requestCourseSatatusSummary(Request $request) {
        $loadView = 'clearMarking.showCourseStatusSummary';
        return Common::requestCourseSatatusSummary($request, $loadView);
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'clearMarking.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

}
