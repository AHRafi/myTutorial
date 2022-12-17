<?php

namespace App\Http\Controllers;

use App\Course;
use App\Subject;
use App\SubjectToDs;
use App\TrainingYear;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectToDsController extends Controller {

    public function index(Request $request) {
        $qpArr = $request->all();
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id ?? 0)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        $subjectArr = ['0' => __('label.SELECT_SUBJECT_OPT')] + Subject::where('subject.gs_feedback', '1')->orderBy('order', 'asc')
                        ->where('subject.status', '1')->pluck('title', 'id')->toArray();
        return view('subjectToDs.index', compact('qpArr', 'activeCourse', 'activeTrainingYear', 'subjectArr'));
    }

    public function getDsList(Request $request) {

        $dsArr = User::leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select(
                        'users.id', 'users.photo', 'users.personal_no', 'wing.code as wing_name', 'rank.code as rank_code', 'users.full_name'
                        // 'subject_to_ds.subject_id'
                )
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();
        $subjectArr = SubjectToDs::Join('users', 'users.id', 'subject_to_ds.ds_id')
                        ->Join('subject', 'subject.id', 'subject_to_ds.subject_id')
                        ->where('subject.gs_feedback', '1')
                        ->select('subject.title', 'users.id', 'subject.id as subject_id')->get();
        $previousCheck = SubjectToDs::where('subject_id', $request->subject_id)->pluck('ds_id', 'ds_id')->toArray();
        $htm = view('subjectToDs.showDsList', compact('dsArr', 'previousCheck', 'request', 'subjectArr'))->render();
        return response()->json(['html' => $htm]);
    }

    public function store(Request $request) {
        // dd($request->all());
        if (empty($request->ds_id)) {
            return response()->json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHECK_AT_LEAST_ONE_DS')], 401);
        }
        $items = [];
        $subjectId = $request->subject_id ?? 0;
        $courseId = $request->course_id ?? 0;
        DB::beginTransaction();
        try {
            if (SubjectToDs::where('course_id', $courseId)->where('subject_id', $subjectId)->exists()) {
                $delete = SubjectToDs::where('course_id', $courseId)->where('subject_id', $subjectId)->delete();
                if (!$delete) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
                }
            }
            foreach ($request->ds_id as $ds) {
                $item = array(
                    'course_id' => $request->course_id,
                    'subject_id' => $request->subject_id,
                    'ds_id' => $ds,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                );
                array_push($items, $item);
            }
            $insertedItem = SubjectToDs::insert($items);

            if (!$insertedItem) {
                DB::rollBack();
                return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
            }
            $countSubjectToDs = SubjectToDs::where('course_id', $courseId)->where('subject_id', $subjectId)->count();
            DB::commit();
            return response()->json(['success' => true, 'countSubjectToDs' => $countSubjectToDs, 'heading' => __('label.SUCCESS'), 'message' => __('label.DATA_INSERTED_SUCCESSFULLY')], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COULD_NOT_SAVE_DATA')], 401);
            // return response()->json(['success' => false, 'heading' => __('label.ERROR'), 'message' => 'Line: ' . $th->getLine() . $th->getMessage() . __('label.COULD_NOT_SAVE_DATA')], 401);
        }
    }

    public function getAssignedDs(Request $request) {
        $assignedDsArr = User::leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->join('subject_to_ds', 'subject_to_ds.ds_id', 'users.id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select(
                        'users.id', 'users.photo', 'users.personal_no', 'wing.code as wing_name', 'rank.code as rank_code', 'users.full_name'
                )
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->where('subject_to_ds.subject_id', $request->subject_id)
                ->where('subject_to_ds.course_id', $request->course_id)
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();
        $activeYear = TrainingYear::where('status', '1')->first();
        $courseName = Course::where('training_year_id', $activeYear->id ?? 0)->where('status', '1')->first();

        $html = view('subjectToDs.getAssignedDs', compact('assignedDsArr', 'courseName', 'activeYear'))->render();
        return response()->json(['html' => $html], 200);
    }

}
