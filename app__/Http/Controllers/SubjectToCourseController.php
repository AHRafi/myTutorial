<?php

namespace App\Http\Controllers;

use Validator;
use App\SubjectToCourse;
use App\TrainingYear;
use App\Course;
use App\Subject;
use Response;
use Auth;
use Illuminate\Http\Request;

class SubjectToCourseController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_SUBJECT_TO_COURSE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_BATCH_OPT')] + CenterToBatch::join('recruit_course', 'recruit_course.id', '=', 'center_to_course.course_id')
//                        ->where('recruit_course.training_year_id', $activeTrainingYear->id)
//                        ->where('center_to_course.center_id', Auth::user()->center_id)
//                        ->orderBy('recruit_course.id','desc')
//                        ->pluck('recruit_course.name', 'recruit_course.id')->toArray();
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->where('center_to_course.center_id', Auth::user()->center_id)
                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        return view('subjectToCourse.index')->with(compact('activeTrainingYear', 'courseList'));
    }

    public function getSubject(Request $request) {

        $targetArr = Subject::select('id', 'name')
                        ->where('status', '1')
//                        ->where('center_id', Auth::user()->center_id)
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = SubjectToCourse::select('subject_id', 'id')
                        ->where('course_id', $request->course_id)
//                        ->where('center_id', Auth::user()->center_id)
                        ->get()->toArray();
        
        $courseData = Course::select('wing_id')->where('id', $request->course_id)->first();

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['subject_id']] = $previousData['subject_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $disableSubject = [];
//        $disableSubject = PlCmdrToSubject::join('subject', 'subject.id', '=', 'pl_cmdr_to_subject.subject_id')
//                        ->where('pl_cmdr_to_subject.course_id', $request->course_id)
//                        ->where('pl_cmdr_to_subject.center_id', Auth::user()->center_id)
//                        ->pluck('subject.name', 'subject.id')->toArray();
        //end
        $html = view('subjectToCourse.showSubject', compact('targetArr', 'courseData', 'previousDataList', 'disableSubject'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveSubject(Request $request) {

        $subjectArr = $request->subject_id;

        $data = [];
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($subjectArr)) {
                foreach ($subjectArr as $key => $subjectId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['wing_id'] = $request->wing_id;
                    $data[$key]['subject_id'] = $subjectId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            SubjectToCourse::where('course_id', $request->course_id)
                    ->where('wing_id', $request->wing_id)
                    ->delete();
        }

        if (SubjectToCourse::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_SUBJECT')), 401);
        }
    }

}
