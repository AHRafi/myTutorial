<?php

namespace App\Http\Controllers;

use App\CmBasicProfile;
use App\GsEvalByCm;
use App\TrainingYear;
use App\Course;
use Redirect;

use Illuminate\Http\Request;

class UnlockCmFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.CM_EVAL_OF_GS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.CM_EVAL_OF_GS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $cmList = ['0' => __('label.SELECT_CM_OPT')] + CmBasicProfile::join('gs_eval_by_cm', 'gs_eval_by_cm.locked_by', 'cm_basic_profile.id')
            ->where('cm_basic_profile.status', '1')
            ->where('gs_eval_by_cm.status', '2')
            ->pluck('cm_basic_profile.official_name', 'cm_basic_profile.id')
            ->toArray();


        $lessonCmList = GsEvalByCm::join('lesson', 'lesson.id', 'gs_eval_by_cm.lesson_id')
            ->join('cm_basic_profile', 'cm_basic_profile.id', 'gs_eval_by_cm.locked_by')
            ->where('gs_eval_by_cm.course_id', $activeCourse->id)
            ->where('gs_eval_by_cm.status', '2')
            ->select(
                'lesson.title as title',
                'cm_basic_profile.official_name as official_name',
                'gs_eval_by_cm.unlock_message as unlock_message',
                'gs_eval_by_cm.id as id'
            );

            //begin filtering
        $searchCm = $request->cm_id;
        if (!empty($searchCm)) {
            $lessonCmList = $lessonCmList->where('gs_eval_by_cm.locked_by', '=', $searchCm);
        }
        //end filtering

        $lessonCmList = $lessonCmList->get();


    return view('unlockCmFeedback.index')->with(compact('activeTrainingYear', 'activeCourse', 'cmList','lessonCmList'));
    }

    public function filter(Request $request) {
        $url = 'cm_id=' . $request->cm_id;
        return Redirect::to('unlockCmFeedback?' . $url);
    }

    public function unlockRequest(Request $request)
    {

        $target = GsEvalByCm::find($request->id);
        if (!empty($target)) {
            $target->status = '0';
            $target->save();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }
    public function denyRequest(Request $request)
    {

        $target = GsEvalByCm::find($request->id);
        if (!empty($target)) {
            $target->status = '1';
            $target->save();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }




}
