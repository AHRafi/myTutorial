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
use App\CiModerationMarkingLock;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\TrainingYear;
use App\TermToCourse;
use App\TermToEvent;
use App\Appointment;
use App\Course;
use DB;

class UnlockDsObsnMarkingController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.UNLOCK_DS_OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }
        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        if (empty($courseList)) {
            $void['header'] = __('label.UNLOCK_DS_OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        // check all terms are closed 
        $openTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();
        if (empty($openTermInfo)) {
            $void['header'] = __('label.UNLOCK_DS_OBSN_MARKING');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $courseList->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }


        $dsArr = DsObsnMarkingLock::join('users', 'users.id', 'ds_obsn_marking_lock.locked_by')
                        ->leftJoin('rank', 'rank.id', 'users.rank_id')->where('users.status', '1')
                        ->select(DB::raw("CONCAT(users.official_name) as ds_name"), 'users.id')
                        ->where('ds_obsn_marking_lock.course_id', $courseList->id)
                        ->where('ds_obsn_marking_lock.term_id', $openTermInfo->id)
                        ->where('ds_obsn_marking_lock.status', '2')
                        ->pluck('ds_name', 'users.id')->toArray();
        $dsList = ['0' => __('label.SELECT_DS_OPT')] + $dsArr;

        $targetArr = DsObsnMarkingLock::join('course', 'course.id', 'ds_obsn_marking_lock.course_id')
                ->join('term', 'term.id', 'ds_obsn_marking_lock.term_id')
                ->join('users', 'users.id', 'ds_obsn_marking_lock.locked_by')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->where('ds_obsn_marking_lock.course_id', $courseList->id)
                ->where('ds_obsn_marking_lock.term_id', $openTermInfo->id)
                ->where('ds_obsn_marking_lock.status', '2')
                ->select(DB::raw("CONCAT(users.official_name) as ds_name")
                , 'ds_obsn_marking_lock.id', 'ds_obsn_marking_lock.unlock_message', 'term.name as term_name'
                , 'course.name as course_name');

        //begin filtering
        $searchDs = $request->fil_ds_id;
        if (!empty($searchDs)) {
            $targetArr = $targetArr->where('ds_obsn_marking_lock.locked_by', '=', $searchDs);
        }
        //end filtering

        $targetArr = $targetArr->get();

        return view('unlockDsObsnMarking.index', compact('targetArr', 'dsList'));
    }

    public function unlock(Request $request) {
        $id = $request->id;
        $target = DsObsnMarkingLock::find($id);

        if ($target->delete()) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.DS_OBSN_MARKING_COULD_NOT_BE_UNLOCKED')), 401);
        }
    }

    public function deny(Request $request) {
        $id = $request->id;
        $updateEventLock = DsObsnMarkingLock::where('id', $id)->where('status', '2');

        if ($updateEventLock->update(array('status' => '1', 'unlock_message' => null))) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.DS_OBSN_MARKING_COULD_NOT_BE_DENIED')), 401);
        }
    }

    public function filter(Request $request) {
        $url = 'fil_ds_id=' . $request->fil_ds_id;
        return Redirect::to('unlockDsObsnMarking?' . $url);
    }

}
