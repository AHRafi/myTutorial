<?php

namespace App\Http\Controllers;

use App\Course;
use App\CiComdtObsnMarkingLimit;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\TrainingYear;
use App\CriteriaWiseWt;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class CiComdtObsnMarkingLimitController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CI_COMDT_OBSN_MARKING_LIMIT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')
                        ->select('name', 'id')->first();

        if (empty($courseList)) {
            $void['header'] = __('label.CI_COMDT_OBSN_MARKING_LIMIT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $criteriaWiseWt = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')
                        ->where('course_id', $courseList->id)->first();



        $prevDataArr = CiComdtObsnMarkingLimit::select('ci_mks_limit', 'ci_limit_percent'
                        , 'comdt_mks_limit', 'comdt_limit_percent')
                ->where('course_id', $courseList->id)
                ->first();


        $ciObsnData = CiObsnMarking::where('course_id', $courseList->id)
                        ->whereNotNull('ci_obsn_mks')->first();
        $comdtObsnData = ComdtObsnMarking::where('course_id', $courseList->id)
                        ->whereNotNull('comdt_obsn_mks')->first();


        return view('ciComdtObsnMarkingLimit.index')->with(compact('activeTrainingYearInfo'
                                , 'courseList', 'request', 'prevDataArr', 'criteriaWiseWt'
                                , 'ciObsnData', 'comdtObsnData'));
    }

    public function saveMarkingLimit(Request $request) {
        $rules = [
            'course_id' => 'required|not_in:0',
            'ci_mks_limit' => 'required',
            'ci_limit_percent' => 'required',
            'comdt_mks_limit' => 'required',
            'comdt_limit_percent' => 'required',
        ];
        $messages = [];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        
        $prevDataArr = CiComdtObsnMarkingLimit::select('id')->where('course_id', $request->course_id)
                ->first();

        $newLimit = !empty($prevDataArr->id) ? CiComdtObsnMarkingLimit::find($prevDataArr->id) : new CiComdtObsnMarkingLimit;
        $newLimit->course_id = $request->course_id;
        $newLimit->ci_mks_limit = $request->ci_mks_limit;
        $newLimit->ci_limit_percent = $request->ci_limit_percent;
        $newLimit->comdt_mks_limit = $request->comdt_mks_limit;
        $newLimit->comdt_limit_percent = $request->comdt_limit_percent;
        $newLimit->updated_at = date("Y-m-d H:i:s");
        $newLimit->updated_by = Auth::user()->id;
        
        if ($newLimit->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.CI_COMDT_OBSN_MARKING_LIMIT_COULD_NOT_ASSIGNED')), 401);
        }
    }

}
