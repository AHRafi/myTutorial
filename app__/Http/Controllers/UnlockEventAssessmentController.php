<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use Redirect;
use Helper;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;
use App\EventAssessmentMarkingLock;
use App\CiModerationMarking;
use App\DsObsnMarking;
use App\CiModerationMarkingLock;
use App\TrainingYear;
use App\TermToCourse;
use App\TermToEvent;
use App\Appointment;
use App\Course;
use DB;

class UnlockEventAssessmentController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.UNLOCK_EVENT_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $termArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.status', '1');
        $termIdArr = $termArr->pluck('term.id', 'term.id')->toArray();
        $termArr = $termArr->pluck('term.name', 'term.id')->toArray();
        $termArr = ['0' => __('label.SELECT_TERM_OPT')] + $termArr;

        $courseArr = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();

        $dsArr = EventAssessmentMarkingLock::join('users', 'users.id', 'event_assessment_marking_lock.locked_by')
                        ->leftJoin('rank', 'rank.id', 'users.rank_id')->where('users.status', '1')
                        ->select(DB::raw("CONCAT(users.official_name) as ds_name"), 'users.id')
                        ->where('event_assessment_marking_lock.status', '2')
                        ->pluck('ds_name', 'users.id')->toArray();
        $dsList = ['0' => __('label.SELECT_DS_OPT')] + $dsArr;

        //ci mod event start
        $ciModMarkList = CiModerationMarking::join('course', 'course.id', 'ci_moderation_marking.course_id')
                ->join('event_assessment_marking_lock', function($join) {
                    $join->on('event_assessment_marking_lock.course_id', '=', 'ci_moderation_marking.course_id');
                    $join->on('event_assessment_marking_lock.term_id', '=', 'ci_moderation_marking.term_id');
                    $join->on('event_assessment_marking_lock.event_id', '=', 'ci_moderation_marking.event_id');
                    $join->on('event_assessment_marking_lock.sub_event_id', '=', 'ci_moderation_marking.sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_event_id', '=', 'ci_moderation_marking.sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', '=', 'ci_moderation_marking.sub_sub_sub_event_id');
                })
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->whereIn('ci_moderation_marking.term_id', $termIdArr)
                ->whereNotNull('ci_moderation_marking.ci_moderation')
                ->pluck('event_assessment_marking_lock.id', 'event_assessment_marking_lock.id')
                ->toArray();

        $dsObsnMarkList = DsObsnMarking::join('course', 'course.id', 'ds_obsn_marking.course_id')
                ->join('event_assessment_marking_lock', function($join) {
                    $join->on('event_assessment_marking_lock.course_id', '=', 'ds_obsn_marking.course_id');
                    $join->on('event_assessment_marking_lock.term_id', '=', 'ds_obsn_marking.term_id');
                })
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->whereIn('ds_obsn_marking.term_id', $termIdArr)
                ->whereNotNull('ds_obsn_marking.obsn_mks')
                ->pluck('event_assessment_marking_lock.id', 'event_assessment_marking_lock.id')
                ->toArray();

        $targetArr = EventAssessmentMarkingLock::join('course', 'course.id', 'event_assessment_marking_lock.course_id')
                ->join('term', 'term.id', 'event_assessment_marking_lock.term_id')
                ->join('event', 'event.id', 'event_assessment_marking_lock.event_id')
                ->leftJoin('sub_event', 'sub_event.id', 'event_assessment_marking_lock.sub_event_id')
                ->leftJoin('sub_sub_event', 'sub_sub_event.id', 'event_assessment_marking_lock.sub_sub_event_id')
                ->leftJoin('sub_sub_sub_event', 'sub_sub_sub_event.id', 'event_assessment_marking_lock.sub_sub_sub_event_id')
                ->join('users', 'users.id', 'event_assessment_marking_lock.locked_by')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->whereIn('event_assessment_marking_lock.term_id', $termIdArr)
                ->where('event_assessment_marking_lock.status', '2')
                ->whereNotIn('event_assessment_marking_lock.id', $ciModMarkList)
                ->whereNotIn('event_assessment_marking_lock.id', $dsObsnMarkList)
                ->select(DB::raw("CONCAT(users.official_name) as ds_name")
                , 'event_assessment_marking_lock.id', 'event_assessment_marking_lock.unlock_message', 'term.name as term_name', 'course.name as course_name'
                , 'event.event_code as event', 'sub_event.event_code as sub_event', 'sub_sub_event.event_code as sub_sub_event'
                , 'sub_sub_sub_event.event_code as sub_sub_sub_event');

        //begin filtering
        $searchDs = $request->fil_ds_id;
        if (!empty($searchDs)) {
            $targetArr = $targetArr->where('event_assessment_marking_lock.locked_by', '=', $searchDs);
        }
        //end filtering

        $targetArr = $targetArr->get();

        return view('unlockEventAssessment.index', compact('targetArr', 'dsList', 'termArr'
                        , 'courseArr'));
    }

    public function unlock(Request $request) {
        $id = $request->id;
        $target = EventAssessmentMarkingLock::find($id);

        if ($target->delete()) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.EVENT_ASSESSMENT_COULD_NOT_BE_UNLOCKED')), 401);
        }
    }

    public function deny(Request $request) {
        $id = $request->id;
        $updateEventLock = EventAssessmentMarkingLock::where('id', $id)->where('status', '2');

        if ($updateEventLock->update(array('status' => '1', 'unlock_message' => null))) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.EVENT_ASSESSMENT_COULD_NOT_BE_DENIED')), 401);
        }
    }

    public function filter(Request $request) {
        $url = 'fil_ds_id=' . $request->fil_ds_id;
        return Redirect::to('unlockEventAssessment?' . $url);
    }

}
