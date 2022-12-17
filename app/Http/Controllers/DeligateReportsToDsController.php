<?php

namespace App\Http\Controllers;

use Validator;
use App\Course;
use App\TrainingYear;
use App\User;
use App\DeligateReportsToDs;
use Response;
use Auth;
use Illuminate\Http\Request;

class DeligateReportsToDsController extends Controller {

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.DELIGATE_REPORTS_TO_DS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.DELIGATE_REPORTS_TO_DS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $dsList = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('rank', 'rank.id', '=', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                        ->where('users.group_id', 4)->where('users.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('appointment.order', 'asc')
                        ->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();


        $prevDataInfo = DeligateReportsToDs::where('course_id', $courseList->id)->select('report', 'ds_id')->first();
	
	
        $prevDataArr = [];

        if (!empty($prevDataInfo)) {
            $prevDataArr = !empty($prevDataInfo->report) ? explode(',', $prevDataInfo->report) : [];
        }


        return view('deligateReportsToDs.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request'
                                , 'prevDataArr', 'prevDataInfo', 'dsList'));
    }
	
    public function setDeligation(Request $request) {
        $reports = $request->report;
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
		$dsIds = !empty($request->ds_id) ? implode(',', $request->ds_id) : '';
		if(empty($dsIds)){
			$rules['ds_id'] = 'required';
		}
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        if (empty($reports)) {
            $errMsg = __('label.PLEASE_CHOOSE_ATLEAST_ONE_REPORT_TO_DELEGATE');
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $errMsg), 401);
        }

        $report = !empty($reports) ? implode(',', $reports) : '';

        $prevDeligation = DeligateReportsToDs::select('id')->where('course_id', $request->course_id)->first();
        $deligateReportsToDs = !empty($prevDeligation->id) ? DeligateReportsToDs::find($prevDeligation->id) : new DeligateReportsToDs;
        $deligateReportsToDs->course_id = $request->course_id;
        $deligateReportsToDs->ds_id = $dsIds;
        $deligateReportsToDs->report = $report;
        $deligateReportsToDs->updated_by = Auth::user()->id;
        $deligateReportsToDs->updated_at = date('Y-m-d H:i:s');




        if ($deligateReportsToDs->save()) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.REPORTS_DELIGATED_TO_DS_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.REPORTS_COULD_NOT_BE_DELIGATED_TO_DS')], 401);
        }
    }

    public function cancelDeligation(Request $request) {
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cancelDeligation = DeligateReportsToDs::where('course_id', $request->course_id)->delete();

        if ($cancelDeligation) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.REPORTS_DELIGATION_CANCELLED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.REPORTS_DELIGATION_COULD_NOT_BE_CANCELLED')], 401);
        }
    }

}
