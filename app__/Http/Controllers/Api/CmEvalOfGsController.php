<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TrainingYear;
use App\Course;
use App\ActivateGsFeedbackForCm;
use App\GsEvalByCm;
use App\Lesson;
use App\Gs;
use App\Subject;
use App\Comment;
use App\Considerations;
use App\GsGrading;
use App\Objective;
use App\User;
use Validator;
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use Common;
use DB;
use Helper;
use Illuminate\Http\Request;

class CmEvalOfGsController extends Controller
{

    public function index(Request $request)
    {

        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $evalData['activeTrainingYear'] = TrainingYear::where('status', '1')->first();

        $activeCourse = Course::where('training_year_id',  $evalData['activeTrainingYear']['id'] ?? 0)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        $evalData['activeCourse'] = $activeCourse;
        $activeGsList = ActivateGsFeedbackForCm::join('gs', 'gs.id', 'activate_gs_feedback_for_cm.gs_id')
            ->where('gs.status', '1')
            ->pluck('gs.name', 'gs.id')
            ->toArray();

        $evalData['activeGsList'] =  ['0' => __('label.SELECT_GS_OPT')] + $activeGsList;


        $subjectList = ['0' => __('label.SELECT_SUBJECT_OPT')] + ActivateGsFeedbackForCm::join('subject', 'subject.id', 'activate_gs_feedback_for_cm.subject_id')
            ->where('course_id', $activeCourse->id ?? 0)
            ->where('gs_id', $qpArr['gs_id'] ?? 0)
            ->pluck('subject.title', 'subject.id')
            ->toArray();

        $evalData['subjectList'] = $subjectList;


        $lessonList = ['0' => __('label.SELECT_LESSON_OPT')] + ActivateGsFeedbackForCm::join('lesson', 'lesson.id', 'activate_gs_feedback_for_cm.lesson_id')
            ->where('course_id', $activeCourse->id ?? 0)
            ->where('gs_id', $qpArr['gs_id'] ?? 0)
            ->where('activate_gs_feedback_for_cm.subject_id', $qpArr['subject_id'] ?? 0)
            ->pluck('lesson.title', 'lesson.id')
            ->toArray();

        $evalData['lessonList'] = $lessonList;



        $proceed = $qpArr['proceed']  ?? "";



        if ($proceed == "true") {


            $courseId = $qpArr['course_id'];
            $dsId = $qpArr['id'];
            $gsId = $qpArr['gs_id'];
            $subjectId = $qpArr['subject_id'];
            $lessonId = $qpArr['lesson_id'];


            $gsEvalLockInfo = GsEvalByCm::where('course_id', $courseId)
                ->where('gs_id', $gsId)
                ->where('subject_id', $subjectId)
                ->where('lesson_id', $lessonId)
                ->where('locked_by',  $dsId)
                ->first();

            if (!empty($gsEvalLockInfo)) {
                $gsEvalLockStatus = $gsEvalLockInfo->status;
                $evalData['gsEvalLockStatus'] = $gsEvalLockStatus;
            }


            $prevGrading = GsEvalByCm::where('course_id', $courseId)
                ->where('gs_id', $gsId)
                ->where('subject_id', $subjectId)
                ->where('lesson_id', $lessonId)
                ->where('locked_by',  $dsId)
                ->first();


            if (!empty($prevGrading)) {
                $prevGrading = $prevGrading->grading;
                $evalData['prevGrading'] = $prevGrading;
            }

            $prevComment = GsEvalByCm::where('course_id', $courseId)
                ->where('gs_id', $gsId)
                ->where('subject_id', $subjectId)
                ->where('lesson_id', $lessonId)
                ->where('locked_by',  $dsId)
                ->first();



            if (!empty($prevComment)) {
                $prevCommentArr = json_decode($prevComment->comment, true);
                $evalData['prevCommentArr'] = $prevCommentArr;
            }





            $subjectInfo = Subject::where('id', $subjectId)->first();
            $evalData['subjectInfo'] = $subjectInfo;
            $lessonInfo = Lesson::where('id', $lessonId)->first();
            $evalData['lessonInfo'] = $lessonInfo;
            $courseInfo = Course::where('id', $courseId)->first();
            $evalData['courseInfo'] = $courseInfo;
            $gsInfo = Gs::where('id', $gsId)->first();
            $evalData['gsInfo'] = $gsInfo;

            $lesson = Lesson::where('id', $lessonId)
                ->select('related_consideration', 'related_grading', 'related_comment')
                ->first();



            $consideration = json_decode($lesson['related_consideration'], true);
            $grading = json_decode($lesson['related_grading'], true);
            $comment = json_decode($lesson['related_comment'], true);


            $gradingArr = GsGrading::whereIn('id', $grading)
                ->select('gs_grading.*')
                ->orderBy('order', 'desc')
                ->get();
            $evalData['gradingArr'] = $gradingArr;


            $gradingList = GsGrading::pluck('wt', 'wt')->toArray();

            $evalData['gradingList'] = $gradingList;

            $considerationArr = Considerations::whereIn('id', $consideration)
                ->select('considerations.*')
                ->get();

            $evalData['considerationArr'] = $considerationArr;

            $commentArr = Comment::whereIn('id', $comment)
                ->select('comment.*')
                ->get();
            $evalData['commentArr'] = $commentArr;


            $objectiveArr = Objective::where('lesson_id', $lessonId)
                ->select('objective.*')->get()->toArray();

            $evalData['objectiveArr'] = $objectiveArr;

            $date = date('d F Y');
            $evalData['date'] = $date;

            $evalData['courseId'] = $courseId;
            $evalData['lessonId'] = $lessonId;
            $evalData['gsId'] = $gsId;
            $evalData['subjectId'] = $subjectId;
            $evalData['proceed'] = $proceed;
        }

        return response()->json(['result' => $evalData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function getSubject(Request $request)
    {

        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $courseId = $qpArr['course_id'];
        $gsId = $qpArr['gs_id'];

        $evalData['subjectList'] = ['0' => __('label.SELECT_SUBJECT_OPT')] + ActivateGsFeedbackForCm::join('subject', 'subject.id', 'activate_gs_feedback_for_cm.subject_id')
            ->where('activate_gs_feedback_for_cm.course_id', $courseId)
            ->where('activate_gs_feedback_for_cm.gs_id', $gsId)
            ->where('activate_gs_feedback_for_cm.status', '1')
            ->pluck('subject.title', 'subject.id')
            ->toArray();
        return response()->json(['result' => $evalData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function getLesson(Request $request)
    {

        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $courseId = $qpArr['course_id'];
        $gsId = $qpArr['gs_id'];
        $subjectId = $qpArr['subject_id'];

        $evalData['lessonList'] = ['0' => __('label.SELECT_LESSON_OPT')] + ActivateGsFeedbackForCm::join('lesson', 'lesson.id', 'activate_gs_feedback_for_cm.lesson_id')
            ->where('activate_gs_feedback_for_cm.course_id', $courseId)
            ->where('activate_gs_feedback_for_cm.gs_id', $gsId)
            ->where('activate_gs_feedback_for_cm.subject_id', $subjectId)
            ->where('activate_gs_feedback_for_cm.status', '1')
            ->pluck('lesson.title', 'lesson.id')
            ->toArray();
        return response()->json(['result' => $evalData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function storeGrading(Request $request)
    {

        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $courseId = $qpArr['course_id'];
        $gsId = $qpArr['gs_id'];
        $subjectId = $qpArr['subject_id'];
        $lessonId = $qpArr['lesson_id'];
        $dsId = $qpArr['id'];
        $grading = $qpArr['grading'];
        $comment = $qpArr['comment'];
        $dataId = $qpArr['data_id'];

        $gsEvalInfo = GsEvalByCm::where('course_id', $courseId)
            ->where('gs_id', $gsId)
            ->where('subject_id', $subjectId)
            ->where('lesson_id', $lessonId)
            ->where('locked_by',  $dsId)
            ->first();


        $target = !empty($gsEvalInfo->id) ? GsEvalByCm::find($gsEvalInfo->id) : new GsEvalByCm;

        $target->course_id = $courseId;
        $target->gs_id = $gsId;
        $target->subject_id = $subjectId;
        $target->lesson_id = $lessonId;
        $target->date = date("Y-m-d");
        $target->grading = $grading;
        $target->comment = json_encode($comment);
        $target->status = $dataId;
        $target->locked_at = date('Y-m-d H:i:s');
        $target->locked_by = $dsId;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = $dsId;

        if ($target->save()) {
            return response()->json(['result' => [], 'message' => '', 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => '', 'status' => 401]);
        }
    }

    public function saveRequestForUnlock(Request $request){
        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $courseId = $qpArr['course_id'];
        $gsId = $qpArr['gs_id'];
        $subjectId = $qpArr['subject_id'];
        $lessonId = $qpArr['lesson_id'];
        $dsId = $qpArr['id'];
        $unlockMessage = $qpArr['unlock_message'];


        $gsEvalInfo = GsEvalByCm::where('course_id', $courseId)
            ->where('gs_id', $gsId)
            ->where('subject_id', $subjectId)
            ->where('lesson_id', $lessonId)
            ->where('locked_by',  $dsId)
            ->first();

        if (!empty($gsEvalInfo)) {
            $target = GsEvalByCm::where('id', $gsEvalInfo->id)
                ->update(['status' => '2', 'unlock_message' => $unlockMessage]);
            if ($target) {
                return response()->json(['result' => [], 'message' => '', 'status' => $authRes['status']]);
            } else {
                return response()->json(['result' => [], 'message' => '', 'status' => 401]);
            }
        }

    }

}
