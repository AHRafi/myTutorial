<?php

namespace App\Http\Controllers;

use App\GsToLesson;
use App\Gs;
use App\TrainingYear;
use App\Course;
use App\SubjectToLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class GsToLessonController extends Controller
{

    public function index(Request $request)
    {

        $activeTrainingYear = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.GS_TO_LESSON');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.GS_TO_LESSON');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $gsList = ['0' => __('label.SELECT_GS_OPT')] + Gs::where('gs.status', '1')
            ->orderBy('gs.id', 'asc')
            ->pluck('gs.name', 'gs.id')->toArray();
        // echo '<pre>'; print_r($gsList);exit;


        $targetArr = $assignedLesson = $disableDataArr = [];
        $count = 0;
        if (!empty($request->gs_id)) {
            //get lesson data
            $targetArr = SubjectToLesson::join('lesson', 'lesson.id', 'subject_to_lesson.lesson_id')
                ->join('subject', 'subject.id', 'subject_to_lesson.subject_id');

            if (Auth::user()->group_id == 4) {
                $dsId = Auth::user()->id;
                $targetArr = $targetArr->join('subject_to_ds', function ($join) use ($dsId) {
                    $join->on('subject_to_ds.subject_id', '=', 'subject_to_lesson.subject_id')
                        ->where('subject_to_ds.ds_id', $dsId);
                });
            }

            $targetArr = $targetArr->where('subject_to_lesson.course_id',  $activeCourse->id ?? 0)
                ->select('lesson.id as id', 'subject_to_lesson.subject_id as subject_id', 'lesson.title as lesson', 'subject.title as subject')
                ->where('lesson.status', '1')
                ->orderBy('subject.order', 'asc')
                ->orderBy('lesson.order', 'asc')
                ->get()->toArray();


            $assignedLessonArr = GsToLesson::where('gs_to_lesson.course_id',  $activeCourse->id ?? 0)
                ->where('gs_to_lesson.gs_id',  $request->gs_id)
                ->select(
                    'gs_to_lesson.lesson_id as lesson_id',
                    'gs_to_lesson.subject_id as subject_id',
                    'gs_to_lesson.gs_id as gs_id'
                )
                ->get();



            // if (!$assignedLessonArr->isEmpty()) {
            //     foreach ($assignedLessonArr as $value) {
            //         $assignedLesson[$value->subject_id][$value->lesson_id] = $value->lesson_id ?? 0;
            //         array_push($countArr, [
            //             $value->subject_id => $value->lesson_id,
            //         ]);
            //     }
            // }

            if (!$assignedLessonArr->isEmpty()) {
                foreach ($assignedLessonArr as $value) {
                    $assignedLesson[$value->subject_id][$value->lesson_id] = $value->lesson_id ?? 0;
                    $count+= 1;
                }
            }

            $otherGSLessonInfo = GsToLesson::join('gs', 'gs.id', 'gs_to_lesson.gs_id')
                ->where('gs_to_lesson.course_id',  $activeCourse->id ?? 0)
                ->where('gs_to_lesson.gs_id', '<>',  $request->gs_id)
                ->select(
                    'gs_to_lesson.lesson_id as lesson_id',
                    'gs_to_lesson.subject_id as subject_id',
                    'gs_to_lesson.gs_id as gs_id',
                    'gs.name as gs_name'
                )
                ->get();


            if (!$otherGSLessonInfo->isEmpty()) {
                foreach ($otherGSLessonInfo as $other) {
                    $disableDataArr[$other->subject_id][$other->lesson_id] = $other->gs_name ?? '';
                }
            }
        }


        return view('gsToLesson.index')->with(compact(
            'activeTrainingYear',
            'activeCourse',
            'gsList',
            'targetArr',
            'assignedLesson',
            'disableDataArr',
            'count',
        ));
    }

    public function getLesson(Request $request)
    {

        //get lesson data
        $targetArr = SubjectToLesson::join('lesson', 'lesson.id', 'subject_to_lesson.lesson_id')
            ->join('subject', 'subject.id', 'subject_to_lesson.subject_id');

        if (Auth::user()->group_id == 4) {
            $dsId = Auth::user()->id;
            $targetArr = $targetArr->join('subject_to_ds', function ($join) use ($dsId) {
                $join->on('subject_to_ds.subject_id', '=', 'subject_to_lesson.subject_id')
                    ->where('subject_to_ds.ds_id', $dsId);
            });
        }

        $targetArr = $targetArr->where('subject_to_lesson.course_id',  $request->course_id)
            ->select('lesson.id as id', 'subject_to_lesson.subject_id as subject_id', 'lesson.title as lesson', 'subject.title as subject')
            ->where('lesson.status', '1')
            ->orderBy('subject.order', 'asc')
            ->orderBy('lesson.order', 'asc')
            ->get();

        $assignedLessonArr = GsToLesson::where('gs_to_lesson.course_id',  $request->course_id)
            ->where('gs_to_lesson.course_id',  $request->course_id)
            ->where('gs_to_lesson.gs_id',  $request->gs_id)
            ->select(
                'gs_to_lesson.lesson_id as lesson_id',
                'gs_to_lesson.subject_id as subject_id',
                'gs_to_lesson.gs_id as gs_id'
            )
            ->get();

        $assignedLesson =[];
        $count = 0;
        if (!$assignedLessonArr->isEmpty()) {
            foreach ($assignedLessonArr as $value) {
                $assignedLesson[$value->subject_id][$value->lesson_id] = $value->lesson_id ?? 0;
                $count+= 1;
            }
        }

        // if (!$assignedLessonArr->isEmpty()) {
        //     foreach ($assignedLessonArr as $value) {
        //         $assignedLesson[$value->subject_id][$value->lesson_id] = $value->lesson_id ?? 0;
        //     }
        // }


        $otherGSLessonInfo = GsToLesson::join('gs', 'gs.id', 'gs_to_lesson.gs_id')
            ->where('gs_to_lesson.course_id',  $request->course_id)
            ->where('gs_to_lesson.gs_id', '<>',  $request->gs_id)
            ->select(
                'gs_to_lesson.lesson_id as lesson_id',
                'gs_to_lesson.subject_id as subject_id',
                'gs_to_lesson.gs_id as gs_id',
                'gs.name as gs_name'
            )
            ->get();

        $disableDataArr = [];
        if (!$otherGSLessonInfo->isEmpty()) {
            foreach ($otherGSLessonInfo as $other) {
                $disableDataArr[$other->subject_id][$other->lesson_id] = $other->gs_name ?? '';
            }
        }

        // echo '<pre>'; print_r($assignedLesson);exit;

        $html = view('gsToLesson.getLesson', compact(
            'targetArr',
            'assignedLesson',
            'disableDataArr',
            'request',
            'count'
        ))->render();
        return response()->json(['html' => $html]);
    }

    public function saveGsToLesson(Request $request)
    {

        $lessonArr = !empty($request->lesson) ? $request->lesson : '';
        $gsId = !empty($request->gs_id) ? $request->gs_id : 0;
        $courseId = !empty($request->course_id) ? $request->course_id : 0;

        if (empty($lessonArr)) {
            return Response::json(array('success' => false, 'heading' => _('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_GS_TO_ATLEAST_ONE_LESSON')), 401);
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'gs_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        $i = 0;
        if (!empty($lessonArr)) {
            foreach ($lessonArr as $subjectId => $subjectInfo) {
                foreach ($subjectInfo as $lessonId => $lessonId) {
                    $data[$i]['course_id'] = $courseId;
                    $data[$i]['gs_id'] = $gsId;
                    $data[$i]['subject_id'] = $subjectId;
                    $data[$i]['lesson_id'] = $lessonId;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;

                    $i++;
                }
            }
        }

        DB::beginTransaction();

        try {
            GsToLesson::where('course_id', $courseId)->where('gs_id', $gsId)->delete();
            GsToLesson::insert($data);

            DB::commit();
            return Response::json(array('success' => true, 'message' => __('label.LESSON_HAS_BEEN_RELATE_TO_GS_SUCCESSFULLY')), 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'heading' => _('label.ERROR'), 'message' => _('label.FAILED_TO_RELATE_LESSON_TO_GS')], 401);
        }
    }



    public function getAssignedLesson(Request $request)
    {
        $course = Course::where('id', $request->course_id)->select('name', 'id')->first();

        $gsName = Gs::select('name')
            ->where('id', $request->gs_id)
            ->first();

        $assignedLessonArr = GsToLesson::join('subject', 'subject.id', 'gs_to_lesson.subject_id')
            ->join('lesson', 'lesson.id', 'gs_to_lesson.lesson_id')
            ->where('gs_to_lesson.course_id',  $request->course_id)
            ->where('gs_to_lesson.gs_id',  $request->gs_id)
            ->select(
                'gs_to_lesson.lesson_id as lesson_id',
                'gs_to_lesson.subject_id as subject_id',
                'gs_to_lesson.gs_id as gs_id',
                'lesson.title as lesson',
                'subject.title as subject'
            )
            ->get();

        $view = view('gsToLesson.showAssignedLesson', compact('assignedLessonArr', 'gsName', 'course'))->render();
        return response()->json(['html' => $view]);
    }
}
