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
use App\ComdtObsnMarkingLock;
use App\TrainingYear;
use App\TermToCourse;
use App\TermToEvent;
use App\Appointment;
use App\Course;
use DB;

class UnlockComdtObsnMarkingController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.UNLOCK_COMDT_OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }



        $courseArr = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();

        $targetArr = ComdtObsnMarkingLock::join('course', 'course.id', 'comdt_obsn_marking_lock.course_id')
                ->join('users', 'users.id', 'comdt_obsn_marking_lock.requested_by')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->where('course.training_year_id', $activeTrainingYearInfo->id)
                ->where('comdt_obsn_marking_lock.status', '2')
                ->select(DB::raw("CONCAT(rank.code, ' ',users.full_name) as comdt_name")
                        , 'comdt_obsn_marking_lock.id', 'comdt_obsn_marking_lock.unlock_message'
                        , 'course.name as course_name')
                ->get();

        //begin filtering
        //end filtering
//        $targetArr = $targetArr->get();

        return view('unlockComdtObsnMarking.index', compact('targetArr', 'courseArr'));
    }

    public function unlock(Request $request) {
        $id = $request->id;
        $target = ComdtObsnMarkingLock::find($id);

        if ($target->delete()) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.COMDT_OBSN_MARKING_COULD_NOT_BE_UNLOCKED')), 401);
        }
    }

    public function deny(Request $request) {
        $id = $request->id;
        $updateEventLock = ComdtObsnMarkingLock::where('id', $id)->where('status', '2');

        if ($updateEventLock->update(array('status' => '1', 'unlock_message' => null))) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(array('success' => false, 'message' => __('label.COMDT_OBSN_MARKING_COULD_NOT_BE_DENIED')), 401);
        }
    }

    public function filter(Request $request) {
        $url = 'fil_ds_id=' . $request->fil_ds_id;
        return Redirect::to('unlockComdtObsnMarking?' . $url);
    }

}
