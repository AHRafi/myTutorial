<?php

namespace App\Http\Controllers;

use App\Course;
use App\GsModule;
use App\Module;
use App\ModuleToSubject;
use App\Subject;
use App\SubjectToDs;
use App\TrainingYear;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleToSubjectController extends Controller
{

    private $controller = 'ModuleToSubject';
    public function index(Request $request)
    {
        $qpArr = $request->all();
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id ?? 0)
            ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        $moduleArr = ['0' => __('label.SELECT_MODULE')] + GsModule::where('status', '1')->orderBy('order')->pluck('name', 'id')->toArray();
        return view('moduleToSubject.index', compact('qpArr', 'activeCourse', 'activeTrainingYear', 'moduleArr'));
    }
    public function getDsList(Request $request)
    {

        $subjectArr = Subject::where('status', '1')
            ->select(
                'subject.id',
                'subject.title',
                'subject.order'
            )
            ->where('subject.gs_feedback', '1')
            ->orderBy('subject.order', 'asc')
            ->get();
        $moduleListArr = GsModule::where('status', '1')->orderBy('order')->pluck('name', 'id')->toArray();
        $moduleArr = ModuleToSubject::Join('gs_module', 'gs_module.id', 'module_to_subject.module_id')
            ->Join('subject', 'subject.id', 'module_to_subject.subject_id')
            ->where('subject.gs_feedback', '1')
            ->select('gs_module.name', 'subject.id', 'gs_module.id as module_id')->get();

        $previousCheck = ModuleToSubject::where('course_id', $request->course_id)->where('module_id', $request->module_id)
            ->pluck('subject_id', 'subject_id')->toArray();
        // dd($previousCheck);
        $htm = view('moduleToSubject.showSubjectList', compact('subjectArr', 'previousCheck', 'request', 'moduleArr', 'moduleListArr'))->render();
        return response()->json(['html' => $htm]);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        if (empty($request->subject_id)) {
            return response()->json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHECK_AT_LEAST_ONE_SUBJECT')], 401);
        }
        $items = [];
        $moduleId = $request->module_id ?? 0;
        $courseId = $request->course_id ?? 0;
        DB::beginTransaction();
        try {
            if (ModuleToSubject::where('course_id', $courseId)->where('module_id', $moduleId)->exists()) {
                $delete = ModuleToSubject::where('course_id', $courseId)->where('module_id', $moduleId)->delete();
                if (!$delete) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
                }
            }
            foreach ($request->subject_id as $subject) {
                $item = array(
                    'course_id' => $courseId,
                    'module_id' => $moduleId,
                    'subject_id' => $subject,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                );
                array_push($items, $item);
            }
            $insertedItem = ModuleToSubject::insert($items);
            if (!$insertedItem) {
                DB::rollBack();
                return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.A_RELADED_SUCCESSFULLY_TO_B')], 401);
            }
            $countModuleToSubject = ModuleToSubject::where('course_id', $courseId)->where('module_id', $moduleId)->count();
            DB::commit();
            return response()->json(['success' => true, 'countModuleToSubject' => $countModuleToSubject, 'heading' => __('label.SUCCESS'), 'message' => __('label.A_RELATED_SUCCESSFULLY_TO_B', ['a' => __('label.MODULE'), 'b' => __('label.SUBJECT')])], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
            // return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => 'Line: ' . $th->getLine() . $th->getMessage() . __('label.COULD_NOT_SAVE_DATA')], 401);
        }
    }

    public function getAssignedSubject(Request $request)
    {
        $assignedSubjectArr = Subject::join('module_to_subject', 'module_to_subject.subject_id', 'subject.id')
            ->select(
                'subject.id',
                'subject.title'
            )
            ->where('module_to_subject.course_id', $request->course_id)
            ->where('module_to_subject.module_id', $request->module_id)
            ->orderBy('subject.order', 'asc')
            ->get();
        $activeYear = TrainingYear::where('status', '1')->first();
        $courseName = Course::where('training_year_id', $activeYear->id ?? 0)->where('status', '1')->first();

        $html = view('moduleToSubject.getAssignedSubject', compact('assignedSubjectArr', 'courseName', 'activeYear'))->render();
        return response()->json(['html' => $html], 200);
    }
    public function deleteModule(Request $request)
    {
        $moduleId = $request->module_id;
        if (!ModuleToSubject::where('module_id', $moduleId)->exists()) {
            return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.MODULE_HAS_NO_RELATED_DATA_TO_DELETE')], 401);
        }
        $delete = ModuleToSubject::where('module_id', $moduleId)->delete();
        if ($delete) {
            $subjectArr = Subject::where('status', '1')
                ->select(
                    'subject.id',
                    'subject.title',
                    'subject.order'
                )
                ->where('subject.gs_feedback', '1')
                ->orderBy('subject.order', 'asc')
                ->get();
            $moduleListArr = GsModule::where('status', '1')->orderBy('order')->pluck('name', 'id')->toArray();
            $moduleArr = ModuleToSubject::Join('gs_module', 'gs_module.id', 'module_to_subject.module_id')
                ->Join('subject', 'subject.id', 'module_to_subject.subject_id')
                ->where('subject.gs_feedback', '1')
                ->select('gs_module.name', 'subject.id', 'gs_module.id as module_id')->get();

            $previousCheck = ModuleToSubject::where('course_id', $request->course_id)->where('module_id', $moduleId)
                ->pluck('subject_id', 'subject_id')->toArray();
            // dd($previousCheck);
            $html = view('moduleToSubject.showSubjectList', compact('subjectArr', 'previousCheck', 'request', 'moduleArr', 'moduleListArr'))->render();
            return response()->json(['success' => true, 'html' => $html, 'heading' => __('label.SUCCESS'), 'message' => __('label.MODULE_DELETED_SUCCESSFULLY')], 200);
        }
        return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.MODULE_COULD_NOT_BE_DELETED')], 401);
    }

    public function cloneCourse(Request $request)
    {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $courseArr = ['0' => __('label.SELECT_COURSE')] + Course::where('id', '<>', $request->course_id)->orderBy('name')->pluck('name', 'id')->toArray();
        $activeCourse = Course::find($request->course_id);
        $html = view('moduleToSubject.cloneCourse', compact('activeTrainingYear', 'courseArr', 'activeCourse'))->render();
        return response()->json(['html' => $html], 200);
    }

    public function getCourseDetails(Request $request)
    {
        $targetArr = ModuleToSubject::Join('gs_module', 'gs_module.id', 'module_to_subject.module_id')
            ->Join('subject', 'subject.id', 'module_to_subject.subject_id')
            ->where('course_id', $request->previous_course_id)
            ->select('module_to_subject.module_id', 'module_to_subject.subject_id', 'subject.title', 'gs_module.name')
            ->get();
        $html = view('moduleToSubject.showCourseDetails', compact('targetArr'))->render();
        return response()->json(['html' => $html, 'count' => $targetArr->count()], 200);
    }
    public function clone(Request $request)
    {
        $items = [];
        $courseId = $request->modal_course_id ?? 0;
        $dataArr = json_decode($request->data, true) ?? [];
        if (empty($dataArr)) {
            return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
        }
        foreach ($dataArr as $key => $data) {
            if (ModuleToSubject::where('module_id', $data['module_id'])->where('course_id', $courseId)->where('subject_id', $data['subject_id'])->exists())
            {
                continue;
            }
            $item = array(
                'course_id' => $courseId,
                'module_id' => $data['module_id'],
                'subject_id' => $data['subject_id'],
                "updated_at" => Carbon::now(),
                "updated_by" => Auth::id(),
            );
            array_push($items, $item);
        }
        if (ModuleToSubject::insert($items)) {
            return response()->json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.COURSE_CLONED_SUCCESSFULLY')], 200);
        }
        return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_CLONE_COURSE')], 401);
        // dd($request->all());
        // dd(json_decode($request->data));
    }
}
