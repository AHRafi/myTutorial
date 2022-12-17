<?php

namespace App\Http\Controllers;

use Validator;
use Session;
use App\TrainingYear;
use App\GsToLesson;
use App\ActivateGsFeedbackForDs;
use App\Course;
use App\GsEvalByDs;
use App\Comment;
use App\Considerations;
use App\Lesson;
use App\Gs;
use App\Subject;
use App\GsModule;
use App\CourseToModule;
use App\GsGrading;
use App\Objective;
use Response;
use Auth;

use Illuminate\Http\Request;

class DsEvalOfGsController extends Controller
{
    public function index(Request $request)
    {

        // echo "<pre>";
        // print_r($request->all());
        // exit;


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

        $activeGsList = ActivateGsFeedbackForDs::join('gs', 'gs.id', 'activate_gs_feedback_for_ds.gs_id')
            ->where('gs.status', '1')
            ->where('activate_gs_feedback_for_ds.status', '1')
            ->pluck('gs.name', 'gs.id')
            ->toArray();
        $activeGsList =  ['0' => __('label.SELECT_GS_OPT')] + $activeGsList;



        $subjectList = ['0' => __('label.SELECT_SUBJECT_OPT')] + ActivateGsFeedbackForDs::join('subject', 'subject.id', 'activate_gs_feedback_for_ds.subject_id')
                            ->where('course_id', $activeCourse->id ?? 0)
                            ->where('gs_id', $request->gs_id ?? 0)
                            ->where('activate_gs_feedback_for_ds.status', '1')
                            ->pluck('subject.title', 'subject.id')
                            ->toArray();


        $lessonList = ['0' => __('label.SELECT_LESSON_OPT')] + ActivateGsFeedbackForDs::join('lesson', 'lesson.id', 'activate_gs_feedback_for_ds.lesson_id')
            ->where('course_id', $activeCourse->id ?? 0)
            ->where('gs_id', $request->gs_id ?? 0)
            ->where('activate_gs_feedback_for_ds.subject_id', $request->subject_id ?? 0)
            ->where('activate_gs_feedback_for_ds.status', '1')
            ->pluck('lesson.title', 'lesson.id')
            ->toArray();

        $subjectInfo = $lessonInfo = $date = $gsInfo = $subjectId = $lessonId = $courseId = $gsId = [];
        $prevGrading = '';
        $prevComment = '';
        $prevCommentArr = '';
        $gsEvalLockStatus = '';
        $objectiveArr = $courseInfo = $gradingArr = $considerationArr = $commentArr = $gradingList = [];

        if ($request->proceed == 'true') {

            $gsEvalLockInfo = GsEvalByDs::where('course_id', $request->course_id)
                ->where('gs_id', $request->gs_id)
                ->where('subject_id', $request->subject_id)
                ->where('lesson_id', $request->lesson_id)
                ->where('locked_by',  Auth::user()->id)
                ->first();
            if (!empty($gsEvalLockInfo)) {
                $gsEvalLockStatus = $gsEvalLockInfo->status;
            }


            $subjectId = $request->subject_id;
            $lessonId = $request->lesson_id;
            $courseId = $request->course_id;
            $gsId = $request->gs_id;

            $prevGrading = GsEvalByDs::where('course_id', $request->course_id)
                ->where('gs_id', $request->gs_id)
                ->where('subject_id', $request->subject_id)
                ->where('lesson_id', $request->lesson_id)
                ->where('locked_by',  Auth::user()->id)
                ->first();


            if (!empty($prevGrading)) {
                $prevGrading = $prevGrading->grading;
            }

            $prevComment = GsEvalByDs::where('course_id', $request->course_id)
                ->where('gs_id', $request->gs_id)
                ->where('subject_id', $request->subject_id)
                ->where('lesson_id', $request->lesson_id)
                ->where('locked_by',  Auth::user()->id)
                ->first();



            if (!empty($prevComment)) {
                $prevCommentArr = json_decode($prevComment->comment, true);
            }

            // echo "<pre>";
            // print_r($prevCommentArr[4]);
            // exit;




            $subjectInfo = Subject::where('id', $request->subject_id)->first();
            $lessonInfo = Lesson::where('id', $request->lesson_id)->first();
            $courseInfo = Course::where('id', $request->course_id)->first();
            $gsInfo = Gs::where('id', $request->gs_id)->first();

            $lesson = Lesson::where('id', $request->lesson_id)
                ->select('related_consideration', 'related_grading', 'related_comment')
                ->first();



            $consideration = json_decode($lesson['related_consideration'], true);
            $grading = json_decode($lesson['related_grading'], true);
            $comment = json_decode($lesson['related_comment'], true);


            $gradingArr = GsGrading::whereIn('id', $grading)
                ->select('gs_grading.*')
                ->orderBy('order', 'desc')
                ->get();

            $gradingList = GsGrading::pluck('wt', 'wt')->toArray();



            $considerationArr = Considerations::whereIn('id', $consideration)
                ->select('considerations.*')
                ->get();

            $commentArr = Comment::whereIn('id', $comment)
                ->select('comment.*')
                ->get();



            $objectiveArr = Objective::where('lesson_id', $request->lesson_id)
                ->select('objective.*')->get();



            $date = date('d F Y');
        }

        return view('dsEvalOfGs.index')->with(compact(
            'request',
            'activeTrainingYear',
            'activeCourse',
            'activeGsList',
            'subjectList',
            'lessonList',
            'subjectInfo',
            'lessonInfo',
            'courseInfo',
            'gradingArr',
            'considerationArr',
            'commentArr',
            'date',
            'gsInfo',
            'lessonId',
            'courseId',
            'gsId',
            'prevGrading',
            'prevCommentArr',
            'gsEvalLockStatus',
            'gradingList',
            'subjectId',
            'objectiveArr'

        ));
    }


    public function getSubject(Request $request)
    {


        $courseId = $request->course_id;
        $gsId = $request->gs_id;

        $subjectList = ['0' => __('label.SELECT_SUBJECT_OPT')] + ActivateGsFeedbackForDs::join('subject', 'subject.id', 'activate_gs_feedback_for_ds.subject_id')
            ->where('course_id', $courseId)
            ->where('gs_id', $gsId)
            ->where('activate_gs_feedback_for_ds.status', '1')
            ->pluck('subject.title', 'subject.id')
            ->toArray();

        $html = view('dsEvalOfGs.showSubjectList', compact('subjectList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getLesson(Request $request)
    {


        $courseId = $request->course_id;
        $gsId = $request->gs_id;
        $subjectId = $request->subject_id;



        $lessonList = ['0' => __('label.SELECT_LESSON_OPT')] + ActivateGsFeedbackForDs::join('lesson', 'lesson.id', 'activate_gs_feedback_for_ds.lesson_id')
            ->where('course_id', $courseId)
            ->where('gs_id', $gsId)
            ->where('activate_gs_feedback_for_ds.subject_id', $subjectId)
            ->where('activate_gs_feedback_for_ds.status', '1')
            ->pluck('lesson.title', 'lesson.id')
            ->toArray();

        $html = view('dsEvalOfGs.showLessonList', compact('lessonList'))->render();

        return response()->json(['html' => $html]);
    }

    public function filter(Request $request)
    {

        // echo "<pre>";
        // print_r($request->all());
        // exit;

        $rules = [
            'gs_id' => 'required | not_in:0',
            'subject_id' => 'required | not_in:0',
            'lesson_id' => 'required | not_in:0'
        ];

        $messages = [
            'gs_id.not_in' => __('label.PLEASE_SELECT_GS'),
            'subject_id.not_in' => __('label.PLEASE_SELECT_SUBJECT'),
            'lesson_id.not_in' => __('label.PLEASE_SELECT_LESSON'),
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect('dsEvalOfGs')
                            ->withErrors($validator);
        }

        $url = 'gs_id=' . $request->gs_id . '&subject_id=' . $request->subject_id . '&course_id=' . $request->course_id . '&lesson_id=' . $request->lesson_id;

        return redirect('dsEvalOfGs?proceed=true&' . $url);
    }


    public function storeGrading(Request $request)
    {

        $rules['grading'] = 'required | in: 1,2,3,4';


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $gsEvalInfo = GsEvalByDs::where('course_id', $request->course_id)
            ->where('gs_id', $request->gs_id)
            ->where('subject_id', $request->subject_id)
            ->where('lesson_id', $request->lesson_id)
            ->where('locked_by',  Auth::user()->id)
            ->first();


            $target = !empty($gsEvalInfo->id) ? GsEvalByDs::find($gsEvalInfo->id) : new GsEvalByDs;

            $target->course_id = $request->course_id;
            $target->gs_id = $request->gs_id;
            $target->subject_id = $request->subject_id;
            $target->lesson_id = $request->lesson_id;
            $target->date = date("Y-m-d");
            $target->grading = $request->grading;
            $target->comment = json_encode($request->comment);
            $target->status = $request->data_id;
            $target->locked_at = date('Y-m-d H:i:s');
            $target->locked_by = Auth::user()->id;
            $target->updated_at = date('Y-m-d H:i:s');
            $target->updated_by = Auth::user()->id;

            if ($target->save()) {
                return response()->json(['success' => true, 'heading' => ('label.SUCCESS')], 200);
            } else {
                return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
            }
    }

    public function getRequestForUnlockModal(Request $request)
    {
        $view = view('dsEvalOfGs.showRequestForUnlockModal')->render();
        return response()->json(['html' => $view]);
    }

    public function saveRequestForUnlock(Request $request)
    {


        $rules = [
            'unlock_message' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $gsEvalInfo = GsEvalByDs::where('course_id', $request->course_id)
            ->where('gs_id', $request->gs_id)
            ->where('subject_id', $request->subject_id)
            ->where('lesson_id', $request->lesson_id)
            ->where('locked_by',  Auth::user()->id)
            ->first();

        if (!empty($gsEvalInfo)) {
            $target = GsEvalByDs::where('id', $gsEvalInfo->id)
                ->update(['status' => '2', 'unlock_message' => $request->unlock_message]);
            if ($target) {
                return Response::json(['success' => true], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.REQUEST_FOR_UNLOCK_COULD_NOT_BE_SENT')), 401);
            }
        }
    }
}
