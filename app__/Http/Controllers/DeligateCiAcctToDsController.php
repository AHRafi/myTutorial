<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\User;
use App\DeligateCiAcctToDs;
use Auth;
use DB;
use Common;
use Validator;
use Illuminate\Http\Request;
use Response;

class DeligateCiAcctToDsController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.DELIGATE_CI_ACCOUNT_TO_DS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.DELIGATE_CI_ACCOUNT_TO_DS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $dsList = ['0' => __('label.SELECT_DS_OPT')] + USER::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
						->join('rank', 'rank.id', '=', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->where('users.group_id', '4')->where('users.status', '1')
						->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $prevDeligationInfo = DeligateCiAcctToDs::join('users', 'users.id', 'deligate_ci_acct_to_ds.ds_id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->join('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('deligate_ci_acct_to_ds.course_id', $courseList->id)
                ->select('users.id as ds_id', 'users.personal_no', 'users.wing_id', 'users.photo'
                        , DB::raw('CONCAT(rank.code, " ", users.full_name) as ds_name')
                        , 'appointment.code as appt')
                ->first();

        return view('deligateCiAcctToDs.index')->with(compact('activeTrainingYearInfo', 'courseList'
                                , 'dsList', 'prevDeligationInfo'));
    }

    //get DS list
    public function getDsList(Request $request) {
        $dsList = ['0' => __('label.SELECT_DS_OPT')] + USER::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
						->join('rank', 'rank.id', '=', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->where('users.group_id', '4')->where('users.status', '1')
						->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $prevDeligationInfo = DeligateCiAcctToDs::join('users', 'users.id', 'deligate_ci_acct_to_ds.ds_id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->join('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('deligate_ci_acct_to_ds.course_id', $request->course_id)
                ->select('users.id as ds_id', 'users.personal_no', 'users.wing_id', 'users.photo'
                        , DB::raw('CONCAT(rank.code, " ", users.full_name) as ds_name')
                        , 'appointment.code as appt')
                ->first();

        $html = view('deligateCiAcctToDs.showDsList', compact('dsList', 'prevDeligationInfo'))->render();
        $html2 = view('deligateCiAcctToDs.showDsInfo', compact('prevDeligationInfo'))->render();

        return response()->json(['html' => $html, 'html2' => $html2]);
    }

    //get DS Info
    public function getDsInfo(Request $request) {

        $prevDeligationInfo = User::join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->join('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('users.id', $request->ds_id)
                ->select('users.id as ds_id', 'users.personal_no', 'users.wing_id', 'users.photo'
                        , DB::raw('CONCAT(rank.code, " ", users.full_name) as ds_name')
                        , 'appointment.code as appt')
                ->first();

        $html = view('deligateCiAcctToDs.showDsInfo', compact('prevDeligationInfo'))->render();

        return response()->json(['html' => $html]);
    }

    public function setDeligation(Request $request) {
        $rules = [
            'course_id' => 'required|not_in:0',
            'ds_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $prevDeligation = DeligateCiAcctToDs::select('id')->where('course_id', $request->course_id)
                        ->where('ds_id', $request->ds_id)->first();
        $deligateCiAcctToDs = !empty($prevDeligation->id) ? DeligateCiAcctToDs::find($prevDeligation->id) : new DeligateCiAcctToDs;
        $deligateCiAcctToDs->course_id = $request->course_id;
        $deligateCiAcctToDs->ds_id = $request->ds_id;
        $deligateCiAcctToDs->updated_by = Auth::user()->id;
        $deligateCiAcctToDs->updated_at = date('Y-m-d H:i:s');




        if ($deligateCiAcctToDs->save()) {
			$dsList = ['0' => __('label.SELECT_DS_OPT')] + USER::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
						->join('rank', 'rank.id', '=', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->where('users.group_id', '4')->where('users.status', '1')
						->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();
						
            $prevDeligationInfo = DeligateCiAcctToDs::join('users', 'users.id', 'deligate_ci_acct_to_ds.ds_id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                    ->join('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->where('deligate_ci_acct_to_ds.course_id', $request->course_id)
                    ->select('users.id as ds_id', 'users.personal_no', 'users.wing_id', 'users.photo'
                            , DB::raw('CONCAT(rank.code, " ", users.full_name) as ds_name')
                            , 'appointment.code as appt')
                    ->first();

            $html = view('deligateCiAcctToDs.showDsList', compact('dsList', 'prevDeligationInfo'))->render();
            $html2 = view('deligateCiAcctToDs.showDsInfo', compact('prevDeligationInfo'))->render();

            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.CI_ACCOUNT_DELIGATED_TO_DS_SUCCESSFULLY')
                        , 'html' => $html, 'html2' => $html2], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.CI_ACCOUNT_COULD_NOT_BE_DELIGATED_TO_DS')], 401);
        }
    }

    public function cancelDeligation(Request $request) {
        $rules = [
            'course_id' => 'required|not_in:0',
            'ds_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cancelDeligation = DeligateCiAcctToDs::where('course_id', $request->course_id)
                        ->where('ds_id', $request->ds_id)->delete();

        if ($cancelDeligation) {
			$dsList = ['0' => __('label.SELECT_DS_OPT')] + USER::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
						->join('rank', 'rank.id', '=', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->where('users.group_id', '4')->where('users.status', '1')
						->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

            $prevDeligationInfo = DeligateCiAcctToDs::join('users', 'users.id', 'deligate_ci_acct_to_ds.ds_id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                    ->join('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->where('deligate_ci_acct_to_ds.course_id', $request->course_id)
                    ->select('users.id as ds_id', 'users.personal_no', 'users.wing_id', 'users.photo'
                            , DB::raw('CONCAT(rank.code, " ", users.full_name) as ds_name')
                            , 'appointment.code as appt')
                    ->first();

            $html = view('deligateCiAcctToDs.showDsList', compact('dsList', 'prevDeligationInfo'))->render();
            $html2 = view('deligateCiAcctToDs.showDsInfo', compact('prevDeligationInfo'))->render();

            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.ACCOUNT_DELIGATION_CANCELLED_SUCCESSFULLY')
                        , 'html' => $html, 'html2' => $html2], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.ACCOUNT_DELIGATION_COULD_NOT_BE_CANCELLED')], 401);
        }
    }

}
