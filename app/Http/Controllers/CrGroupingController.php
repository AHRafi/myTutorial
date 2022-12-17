<?php

namespace App\Http\Controllers;

use App\Course;
use App\CrGrouping;
use App\CrGeneration;
use App\CrMarkingSlab;
use App\CrTrait;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\TrainingYear;
use App\Event;
use App\EventAssessmentMarking;
use App\User;
use App\CmBasicProfile;
use App\CmGroupToCourse;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class CrGroupingController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')];
        $closedTrainingYear = TrainingYear::where('status', '2')->orderBy('start_date', 'desc')
                        ->select('name', 'id')->first();

        $activeTrainingYear = TrainingYear::where('status', '1')->select('name', 'id')->first();

        if (!empty($activeTrainingYear)) {
            $trainingYearList[$activeTrainingYear->id] = $activeTrainingYear->name;
        }
        if (!empty($closedTrainingYear)) {
            $trainingYearList[$closedTrainingYear->id] = $closedTrainingYear->name;
        }

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $dsList = ['0' => __('label.SELECT_DS_OPT')] + User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                        ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                        ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                        ->where('users.group_id', 4)
                        ->where('users.status', '1')
                        ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                                , 'users.id')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('appointment.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc')
                        ->pluck('ds_name', 'users.id')
                        ->toArray();

        return view('crSetup.grouping.index')->with(compact('trainingYearList', 'courseList', 'dsList'));
    }

    public function getCourse(Request $request) {

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('crSetup.grouping.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getCmSelectionPanel(Request $request) {
        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->where('updated_by', $request->ds_id)
                ->get();

        //for get Group Template cm
        $cmGroupList = ['0' => __('label.SELECT_CM_GROUP_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)
                        ->where('cm_group.status', '1')
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')
                        ->toArray();


        //for get Individual Searched cm
        $nameArr = CmBasicProfile::select('personal_no')->get();


        $submitFrom = 'Individual';
        $selectionClass = 'individual-search';
        //selected cm of this group
        $prevCmArr = CrGrouping::where('cr_grouping.course_id', $request->course_id)
                ->where('cr_grouping.ds_id', $request->ds_id)
                ->pluck('cr_grouping.cm_id', 'cr_grouping.cm_id')
                ->toArray();

        //selected cm of other groups
        $prevOtherGroupCmArr = CrGrouping::join('users', 'users.id', 'cr_grouping.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                        ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                        ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                        ->where('cr_grouping.course_id', $request->course_id)
                        ->where('cr_grouping.ds_id', '<>', $request->ds_id)
                        ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                                , 'cr_grouping.cm_id')
                        ->pluck('ds_name', 'cr_grouping.cm_id')->toArray();


        //list of all CM
        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('cm_basic_profile.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->whereIn('cm_basic_profile.id', $prevCmArr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $html = view('crSetup.grouping.getCmSelectionPanel', compact('request', 'prevCmArr', 'prevOtherGroupCmArr'
                        , 'targetArr', 'cmGroupList', 'nameArr', 'submitFrom', 'selectionClass', 'cmArr'
                        , 'prevCrGen'))->render();
        return response()->json(['html' => $html]);
    }

    public function getFilterIndividualCm(Request $request) {
        $submitFrom = 'Individual';
        $selectionClass = 'individual-search';

        //selected cm of this group
        $prevCmArr = CrGrouping::where('cr_grouping.course_id', $request->course_id)
                ->where('cr_grouping.ds_id', $request->ds_id)
                ->pluck('cr_grouping.cm_id', 'cr_grouping.cm_id')
                ->toArray();

        //selected cm of other groups
        $prevOtherGroupCmArr = CrGrouping::join('users', 'users.id', 'cr_grouping.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                        ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                        ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                        ->where('cr_grouping.course_id', $request->course_id)
                        ->where('cr_grouping.ds_id', '<>', $request->ds_id)
                        ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                                , 'cr_grouping.cm_id')
                        ->pluck('ds_name', 'cr_grouping.cm_id')->toArray();

        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('cm_basic_profile.personal_no', 'LIKE', '%' . $request->individual_search . '%')
                ->where('cm_basic_profile.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->where('updated_by', $request->ds_id)
                ->get();


        $html = view('crSetup.grouping.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                        , 'selectionClass', 'prevOtherGroupCmArr', 'prevCrGen'))->render();

        return response()->json(['html' => $html]);
    }

    public function setCm(Request $request) {
        $cmArr = $request->selected_cm_id;

        if (empty($cmArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_CHOOSE_ATLEAST_ONE_CM')), 401);
        }

        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->whereIn('cm_basic_profile.id', $cmArr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->where('updated_by', $request->ds_id)
                ->get();
        $html = view('crSetup.grouping.showSetCmList', compact('targetArr', 'request', 'prevCrGen'))->render();

        return response()->json(['html' => $html]);
    }

    public function getCmGroupWiseSearchCm(Request $request) {
        //selected cm of this group
        $prevCmArr = CrGrouping::where('cr_grouping.course_id', $request->course_id)
                ->where('cr_grouping.ds_id', $request->ds_id)
                ->pluck('cr_grouping.cm_id', 'cr_grouping.cm_id')
                ->toArray();


        //selected cm of other groups
        $prevOtherGroupCmArr = CrGrouping::join('users', 'users.id', 'cr_grouping.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                        ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                        ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                        ->where('cr_grouping.course_id', $request->course_id)
                        ->where('cr_grouping.ds_id', '<>', $request->ds_id)
                        ->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                                , 'cr_grouping.cm_id')
                        ->pluck('ds_name', 'cr_grouping.cm_id')->toArray();

        //list of all CM
        $cmIdArr = CmBasicProfile::join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', '=', 'cm_basic_profile.id')
                ->where('cm_group_member_template.course_id', $request->course_id)
                ->where('cm_group_member_template.cm_group_id', $request->cm_group_id)
                ->where('cm_basic_profile.status', '1')
                ->pluck('cm_group_member_template.cm_basic_profile_id', 'cm_group_member_template.cm_basic_profile_id')
                ->toArray();
        $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->whereIn('cm_basic_profile.id', $cmIdArr)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_basic_profile.id', 'cm_basic_profile.photo', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('cm_basic_profile.official_name', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

        $submitFrom = 'GroupWise';
        $selectionClass = 'group-wise';

        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->where('updated_by', $request->ds_id)
                ->get();


        $html = view('crSetup.grouping.showSearchCm', compact('targetArr', 'request', 'submitFrom', 'prevCmArr'
                        , 'selectionClass', 'prevOtherGroupCmArr', 'prevCrGen'))->render();

        return response()->json(['html' => $html]);
    }

    public function saveGroup(Request $request) {

        $cmArr = $request->selected_cm;

        $rules = [
            'course_id' => 'required|not_in:0',
            'ds_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $errorArr = [];
        if (empty($cmArr)) {
            $errorArr[] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_CM');
        }
        if (!empty($errorArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $errorArr), 400);
        }

        $data = [];
        $i = 0;
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmId) {

                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['ds_id'] = $request->ds_id;
                $data[$i]['cm_id'] = $cmId;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        CrGrouping::where('course_id', $request->course_id)
                ->where('ds_id', $request->ds_id)
                ->delete();

        if (CrGrouping::insert($data)) {
            return Response::json(['success' => true, 'message' => __('label.COURSE_REPORT_GROUP_HAS_BEEN_ASSIGNED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FAILED_TO_ASSIGN_COURSE_REPORT_GROUP')), 401);
        }
    }
    
    public function removeGroup(Request $request) {
        $cmArr = $request->selected_cm;

        $rules = [
            'course_id' => 'required|not_in:0',
            'ds_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        $delete = CrGrouping::where('course_id', $request->course_id)->where('ds_id', $request->ds_id);
        if ($delete->delete()) {
            return Response::json(['success' => true, 'message' => __('label.COURSE_REPORT_GROUP_HAS_BEEN_REMOVEED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FAILED_TO_REMOVE_COURSE_REPORT_GROUP')), 401);
        }
    }

}
