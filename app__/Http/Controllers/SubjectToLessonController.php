<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\GsToLesson;
use App\Course;
use App\Subject;
use App\Lesson;
use App\SubjectToLesson;
use DB;
use App\Gs;
use App\GsModule;
use App\CourseToModule;
use Response;
use Auth;
use Illuminate\Http\Request;

class SubjectToLessonController extends Controller {

    public function index(Request $request) {

        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        $subjectList = ['0' => __('label.SELECT_SUBJECT_OPT')] + Subject::where('subject.gs_feedback', '1')->orderBy('order', 'asc')
                        ->where('subject.status', '1')->pluck('title', 'id')->toArray();

        return view('subjectToLesson.index')->with(compact('activeTrainingYear', 'activeCourse', 'subjectList'));
    }

    public function getLessonList(Request $request) {


        $subjectId = $request->subject_Id;
        $courseId = $request->course_id;

        $prevlessonList = SubjectToLesson::where('course_id', $courseId)
                ->where('subject_id', $subjectId)
                ->pluck('lesson_id', 'lesson_id')
                ->toArray();
        
        $prevAllLessonInfo = SubjectToLesson::join('subject', 'subject.id', 'subject_to_lesson.subject_id')
                ->where('subject_to_lesson.course_id', $courseId)
                ->select('subject_to_lesson.subject_id', 'subject_to_lesson.lesson_id'
                        , 'subject.title as subject')
                ->get();
        $prevAllLessonList = [];
        if(!$prevAllLessonInfo->isEmpty()){
            foreach($prevAllLessonInfo as $info){
                $prevAllLessonList[$info->lesson_id]['subject_id'] = $info->subject_id;
                $prevAllLessonList[$info->lesson_id]['subject'] = $info->subject;
            }
        }
        
        

        $lessonList = Lesson::where('consider_gs_feedback', '1')
                ->where('status', '1')
                ->pluck('title', 'id')
                ->toArray();

        $html = view('subjectToLesson.getLessonList', compact('lessonList', 'subjectId', 'courseId'
                        , 'prevlessonList', 'prevAllLessonList'))->render();
        return Response::json(['html' => $html]);
    }

    public function saveLesson(Request $request) {

        $subjectId = $request->subject_id;
        $courseId = $request->course_id;
        $lessonId = $request->lesson_id;


        if (empty($lessonId)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_GS_TO_ATLEAST_ONE_LESSON')), 401);
        }
        $rules = [
            'lesson_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }


        $data = [];

        DB::beginTransaction();
        try {
            if (SubjectToLesson::where('course_id', $courseId)->where('subject_id', $subjectId)->exists()) {
                $delete = SubjectToLesson::where('course_id', $courseId)->where('subject_id', $subjectId)->delete();
                if (!$delete) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'heading' => ('label.ERROR'), 'message' => ('label.COULD_NOT_SAVE_DATA')], 401);
                }
            }

            if (!empty($request->course_id)) {
                if (!empty($lessonId)) {
                    foreach ($lessonId as $id => $id) {

                        $data[$id]['course_id'] = $request->course_id;
                        $data[$id]['subject_id'] = $request->subject_id;
                        $data[$id]['lesson_id'] = $id;
                        $data[$id]['updated_at'] = date('Y-m-d H:i:s');
                        $data[$id]['updated_by'] = Auth::user()->id;
                    }
                }
            }

            $insertedItem = SubjectToLesson::insert($data);
            if (!$insertedItem) {
                DB::rollBack();
                return response()->json(['success' => false, 'heading' => ('label.ERROR'), 'message' => ('label.COULD_NOT_SAVE_DATA')], 401);
            }

            $prevlessonNumber = SubjectToLesson::where('course_id', $courseId)
                            ->where('subject_id', $subjectId)->count();

            $prevAllLessonNumber = SubjectToLesson::where('course_id', $courseId)->count();

            DB::commit();
            return response()->json(['success' => true, 'message' => __('label.LESSON_RELATE_TO_SUBJECT_SUCCESSFULLY')
                , 'prevLessonNumber' => $prevlessonNumber, 'prevAllLessonNumber' => $prevAllLessonNumber], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'heading' => ('label.ERROR'), 'message' => ('label.COULD_NOT_SAVE_DATA')], 401);
        }
    }

    public function getAssignedLesson(Request $request) {


        $subjectId = $request->subject_id;
        $courseId = $request->course_id;

        $subject = Subject::where('id', $subjectId)->first();
        $course = Course::where('id', $courseId)->first();


        $prevlessonList = Lesson::join('subject_to_lesson', 'subject_to_lesson.lesson_id', 'lesson.id')
                        ->join('subject', 'subject.id', 'subject_to_lesson.subject_id')
                        ->where('course_id', $courseId)
                        ->where('subject_id', $subjectId)
                        ->select('lesson.title as lesson', 'subject.title as subject')->get();

        $html = view('subjectToLesson.showAssignedLesson', compact('subject', 'course', 'prevlessonList'))->render();
        return Response::json(['html' => $html]);
    }

}
