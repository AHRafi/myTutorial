<?php

namespace App\Http\Controllers;

use Validator;
use App\Course;
use App\TrainingYear;
use App\User;
use App\ActivateGsFeedbackForDs;
use App\GsToLesson;
use App\SubjectToLesson;
use Response;
use Auth;
use Common;
use DB;
use Illuminate\Http\Request;
class ActivateGsFeedbackForDsController extends Controller
{

    public function index(Request $request)
    {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.GS_EVALUATION_ACTIVATE_DEACTIVATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.GS_EVALUATION_ACTIVATE_DEACTIVATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $targetArr = GsToLesson::join('lesson', 'lesson.id', 'gs_to_lesson.lesson_id')
            ->join('subject', 'subject.id', 'gs_to_lesson.subject_id')
            // ->join('subject_to_lesson', 'subject_to_lesson.subject_id', 'gs_to_lesson.subject_id')
            ->join('gs', 'gs.id', 'gs_to_lesson.gs_id')
            ->where('gs_to_lesson.course_id', $courseList->id)
            ->select(
                'gs_to_lesson.gs_id',
                'gs_to_lesson.lesson_id',
                'gs_to_lesson.subject_id',
                'lesson.title as lesson',
                'subject.title as subject',
                'gs.name as gs_name'
            )
            ->orderBy('subject.order', 'asc')
            ->orderBy('lesson.order', 'asc')
            ->get();

        $detailsArr = $statArr = [];
        $statValue = '';
        $statusArr = ActivateGsFeedbackForDs::select('lesson_id', 'subject_id', 'gs_id', 'status')->get();

        if (!$statusArr->isEmpty()) {
            foreach ($statusArr as $status) {
                $statArr[$status->subject_id][$status->lesson_id][$status->gs_id] = $status->status ?? 0;
            }
        }


        return view('activateGsFeedbackForDs.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request', 'targetArr', 'statArr'));
    }



    public function setStat(Request $request)
    {
        $courseId = $request->course_id;
        $lessonId = !empty($request->lesson_id) ? $request->lesson_id : 0;
        $subjectId = !empty($request->subject_id) ? $request->subject_id : 0;
        $gsId = !empty($request->gs_id) ? $request->gs_id : 0;
        $status = !empty($request->status) ? $request->status : '0';
        $statusMsg1 = !empty($request->status) ? __('label.ACTIVATION') : __('label.DEACTIVATION');
        $statusMsg2 = !empty($request->status) ? __('label.ACTIVATE_') : __('label.DEACTIVATE_');

        DB::beginTransaction();

        try {
            $prevActDeactInfo = ActivateGsFeedbackForDs::where('course_id', $courseId)
                ->where('lesson_id', $lessonId)
                ->where('subject_id', $subjectId)->where('gs_id', $gsId)
                ->select('id')->first();

            $actDeact = !empty($prevActDeactInfo->id) ? ActivateGsFeedbackForDs::find($prevActDeactInfo->id) : new ActivateGsFeedbackForDs;

            $actDeact->course_id = $courseId;
            $actDeact->lesson_id = $lessonId;
            $actDeact->subject_id = $subjectId;
            $actDeact->gs_id = $gsId;
            $actDeact->status = $status;
            $actDeact->activation_by = Auth::user()->id;
            $actDeact->activation_date = date("Y-m-d H:i:s");

            $actDeact->save();
            $successMsg = __('label.DE_ACTIVATION_WAS_SUCCESSFUL', ['stat' => $statusMsg1]);
            $errorMsg = __('label.FAILED_TO_DE_ACTIVATE', ['stat' => $statusMsg2]);

            DB::commit();
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => $errorMsg], 401);
        }
    }
}
