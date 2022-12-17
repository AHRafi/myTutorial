<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\GsToLesson;
use App\Course;
use App\User;
use App\Lesson;
use App\Gs;
use App\GsModule;
use App\CourseToModule;
use App\GsEvalByDs;
use Response;
use Redirect;
use Auth;
use Illuminate\Http\Request;

class UnlockDsFeedbackController extends Controller
{
    public function index(Request $request)
    {

        $activeTrainingYear = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.DS_EVAL_OF_GS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.DS_EVAL_OF_GS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $dsList = ['0' => __('label.SELECT_DS_OPT')] + User::join('gs_eval_by_ds', 'gs_eval_by_ds.locked_by', 'users.id')
            ->where('users.status', '1')
            ->where('gs_eval_by_ds.status', '2')
            ->pluck('users.official_name', 'users.id')
            ->toArray();


        $lessonDsList = GsEvalByDs::join('lesson', 'lesson.id', 'gs_eval_by_ds.lesson_id')
            ->join('users', 'users.id', 'gs_eval_by_ds.locked_by')
            ->where('gs_eval_by_ds.course_id', $activeCourse->id)
            ->where('gs_eval_by_ds.status', '2')
            ->select(
                'lesson.title as title',
                'users.official_name as official_name',
                'gs_eval_by_ds.unlock_message as unlock_message',
                'gs_eval_by_ds.id as id'
            );

        //begin filtering
        $searchDs = $request->ds_id;
        if (!empty($searchDs)) {
            $lessonDsList = $lessonDsList->where('gs_eval_by_ds.locked_by', '=', $searchDs);
        }
        //end filtering

        $lessonDsList = $lessonDsList->get();


        return view('unlockDsFeedback.index')->with(compact('activeTrainingYear', 'activeCourse', 'dsList','lessonDsList'));
    }

    public function filter(Request $request) {
        $url = 'ds_id=' . $request->ds_id;
        return Redirect::to('unlockDsFeedback?' . $url);
    }


    public function unlockRequest(Request $request)
    {

        $target = GsEvalByDs::find($request->id);
        if (!empty($target)) {
            $target->status = '0';
            $target->save();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }
    public function denyRequest(Request $request)
    {

        $target = GsEvalByDs::find($request->id);
        if (!empty($target)) {
            $target->status = '1';
            $target->save();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }

}
