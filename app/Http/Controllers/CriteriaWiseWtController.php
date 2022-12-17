<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\CriteriaWiseWt;
use App\EventMksWt;
use App\SubEventMksWt;
use App\SubSubEventMksWt;
use App\SubSubSubEventMksWt;
use App\EventAssessmentMarking;
use App\DsObsnMarking;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use Auth;
use DB;
use Validator;
use Illuminate\Http\Request;
use Response;

class CriteriaWiseWtController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CRITERIA_WISE_WT_DISTRIBUTION');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        if (empty($courseList)) {
            $void['header'] = __('label.CRITERIA_WISE_WT_DISTRIBUTION');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $courseId = $courseList->id;
        $totalCourseWt = Course::where('id', $courseList->id)
                        ->select('total_course_wt')->first();
        $criteriaWtArr = CriteriaWiseWt::where('course_id', $courseList->id)->first();
        
        //dependency
        $eventAssessmentMarkingData = EventAssessmentMarking::where('course_id', $courseList->id)
                ->whereNotNull('mks')->first();
        $dsObsnMarkingData = DsObsnMarking::where('course_id', $courseList->id)
                ->whereNotNull('obsn_mks')->first();
        $ciObsnMarkingData = CiObsnMarking::where('course_id', $courseList->id)
                ->whereNotNull('ci_obsn')->first();
        $comdtObsnMarkingData = ComdtObsnMarking::where('course_id', $courseList->id)
                ->whereNotNull('comdt_obsn')->first();
//        echo '<pre>';print_r($comdtObsnMarkingData); exit;

        return view('criteriaWiseWt.index')->with(compact('activeTrainingYearInfo', 'courseList'
                                , 'courseId', 'criteriaWtArr', 'totalCourseWt', 'eventAssessmentMarkingData'
                                , 'dsObsnMarkingData', 'ciObsnMarkingData', 'comdtObsnMarkingData'));
    }

    public function getCriteriaWt(Request $request) {
        $courseId = $request->course_id;
        $totalCourseWt = Course::where('id', $request->course_id)
                        ->select('total_course_wt')->first();
        $criteriaWtArr = CriteriaWiseWt::where('course_id', $request->course_id)->first();

        $html = view('criteriaWiseWt.showCriteriaWt', compact('courseId', 'criteriaWtArr', 'totalCourseWt'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveCriteriaWt(Request $request) {

        // Validation
        $message = $errors = [];
        $rules = [
            'total_event_wt' => 'required | numeric|min:1',
            'ds_obsn_wt' => 'required | numeric|min:1',
            'ci_obsn_wt' => 'required | numeric|min:1',
            'comdt_obsn_wt' => 'required | numeric|min:1',
        ];

        $totalCourseWt = $request->total_course_wt;
        $totalWt = $request->total;
        if ($totalCourseWt != $totalWt) {
            $errors[] = __('label.TOTAL_WT_MUST_BE_EQUAL_TO', ['total_course_wt' => $totalCourseWt]);
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()], 400);
        }
        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 400);
        }
        // End validation
        // Get previous data
        $target = CriteriaWiseWt::where('course_id', $request->course_id)->first();
        if (empty($target)) {
            $target = new CriteriaWiseWt;
        }

        $target->course_id = $request->course_id;
        $target->total_event_wt = $request->total_event_wt;
        $target->ds_obsn_wt = $request->ds_obsn_wt;
        $target->ci_obsn_wt = $request->ci_obsn_wt;
        $target->comdt_obsn_wt = $request->comdt_obsn_wt;
        $target->total_wt = $request->total;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        if ($target->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.WT_COULD_NOT_BE_DISTRIBUTED')), 401);
        }
    }
    
    public function deleteCriteriaWt(Request $request) {
        $criteriaWiseWt = CriteriaWiseWt::where('course_id', $request->course_id);
        
        EventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        SubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        SubSubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        SubSubSubEventMksWt::where('course_id', $request->course_id)
                ->delete();
        
        if ($criteriaWiseWt->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => 'Mks & Wt could not be deleted'), 401);
        }
    }

}
