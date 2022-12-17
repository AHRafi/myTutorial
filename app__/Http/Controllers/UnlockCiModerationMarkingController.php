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
use App\CiModerationMarking;
use App\ComdtModerationMarkingLock;
use App\CiModerationMarkingLock;
use App\TrainingYear;
use App\TermToCourse;
use App\TermToEvent;
use App\Appointment;
use App\Course;
use DB;

class UnlockCiModerationMarkingController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.UNLOCK_CI_MODERATION_MARKING');
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

        


        $comdtModMarkList = ComdtModerationMarkingLock::join('course', 'course.id', 'comdt_moderation_marking_lock.course_id')
                ->join('comdt_moderation_marking', function($join) {
                    $join->on('comdt_moderation_marking.course_id', '=', 'comdt_moderation_marking_lock.course_id');
                    $join->on('comdt_moderation_marking.term_id', '=', 'comdt_moderation_marking_lock.term_id');
                    $join->on('comdt_moderation_marking.event_id', '=', 'comdt_moderation_marking_lock.event_id');
                    $join->on('comdt_moderation_marking.sub_event_id', '=', 'comdt_moderation_marking_lock.sub_event_id');
                    $join->on('comdt_moderation_marking.sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_event_id');
                    $join->on('comdt_moderation_marking.sub_sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_sub_event_id');
                })
                ->join('ci_moderation_marking_lock', function($join) {
                    $join->on('ci_moderation_marking_lock.course_id', '=', 'comdt_moderation_marking_lock.course_id');
                    $join->on('ci_moderation_marking_lock.term_id', '=', 'comdt_moderation_marking_lock.term_id');
                    $join->on('ci_moderation_marking_lock.event_id', '=', 'comdt_moderation_marking_lock.event_id');
                    $join->on('ci_moderation_marking_lock.sub_event_id', '=', 'comdt_moderation_marking_lock.sub_event_id');
                    $join->on('ci_moderation_marking_lock.sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_event_id');
                    $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_sub_event_id');
                })
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->whereIn('comdt_moderation_marking_lock.term_id', $termIdArr)
                ->whereNotNull('comdt_moderation_marking.comdt_moderation')
                ->pluck('ci_moderation_marking_lock.id', 'ci_moderation_marking_lock.id')
                ->toArray();
                
        $targetArr = CiModerationMarkingLock::join('course', 'course.id', 'ci_moderation_marking_lock.course_id')
                ->join('term', 'term.id', 'ci_moderation_marking_lock.term_id')
                ->join('event', 'event.id', 'ci_moderation_marking_lock.event_id')
                ->leftJoin('sub_event', 'sub_event.id', 'ci_moderation_marking_lock.sub_event_id')
                ->leftJoin('sub_sub_event', 'sub_sub_event.id', 'ci_moderation_marking_lock.sub_sub_event_id')
                ->leftJoin('sub_sub_sub_event', 'sub_sub_sub_event.id', 'ci_moderation_marking_lock.sub_sub_sub_event_id')
                ->join('users', 'users.id', 'ci_moderation_marking_lock.locked_by')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->whereIn('ci_moderation_marking_lock.term_id', $termIdArr)
                ->where('ci_moderation_marking_lock.status', '2')
                ->whereNotIn('ci_moderation_marking_lock.id', $comdtModMarkList)
                ->select(DB::raw("CONCAT(rank.code, ' ',users.full_name) as ci_name")
                , 'ci_moderation_marking_lock.id', 'ci_moderation_marking_lock.unlock_message', 'term.name as term_name', 'course.name as course_name'
                , 'event.event_code as event', 'sub_event.event_code as sub_event', 'sub_sub_event.event_code as sub_sub_event'
                , 'sub_sub_sub_event.event_code as sub_sub_sub_event');

        //begin filtering
        
        //end filtering

        $targetArr = $targetArr->get();

        return view('unlockCiModerationMarking.index', compact('targetArr', 'termArr'
                        , 'courseArr'));
    }

    public function unlock(Request $request) {
        $id = $request->id;
        $target = CiModerationMarkingLock::find($id);

        if ($target->delete()) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.CI_MODERATION_MARKING_COULD_NOT_BE_UNLOCKED')), 401);
        }
    }

    public function deny(Request $request) {
        $id = $request->id;
        $updateEventLock = CiModerationMarkingLock::where('id', $id)->where('status', '2');

        if ($updateEventLock->update(array('status' => '1', 'unlock_message' => null))) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.CI_MODERATION_MARKING_COULD_NOT_BE_DENIED')), 401);
        }
    }

    public function filter(Request $request) {
        $url = 'fil_course_id=' . $request->fil_course_id . '&fil_term_id=' . $request->fil_term_id;
        return Redirect::to('unlockCiModerationMarking?' . $url);
    }

}
