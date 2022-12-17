<?php

namespace App\Http\Controllers;

//use App\CenterToCourse;
use App\Syndicate;
use App\SubSyndicate;
use App\CmToSyn;
use App\CmToSubSyn;
use App\SynToCourse;
use App\SynToSubSyn;
use App\CmBasicProfile;
use App\Course;
use App\Term;
use App\TermToCourse;
use App\TrainingYear;
use Auth;
use Illuminate\Http\Request;
use Response;
use Validator;
use DB;

class CmToSubSynController extends Controller {

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CM_TO_SYN');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $termList = ['0' => __('label.SELECT_TERM_OPT')];

        $synList = ['0' => __('label.SELECT_SYN_OPT')];

        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')];

        $targetArr = $previousCmToSubSynList = $totalNumOfAssignedCm = $prevDataList = [];
        $prevDataArr = $checkPreviousDataArr = $subSynDataList = $checkPreviousDataList = [];

        if (!empty($request->course_id) && !empty($request->term_id) && !empty($request->syn_id) && !empty($request->sub_syn_id)) {
            $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                            ->where('term_to_course.course_id', $request->course_id)
                            ->orderBy('term.order', 'asc')
                            ->pluck('term.name', 'term.id')->toArray();
            $synList = ['0' => __('label.SELECT_SYN_OPT')] + SynToCourse::join('syndicate', 'syndicate.id', '=', 'syn_to_course.syn_id')
                            ->where('syn_to_course.course_id', $request->course_id)
                            ->orderBy('syndicate.order', 'asc')
                            ->pluck('syndicate.name', 'syndicate.id')->toArray();
            $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')] + SynToSubSyn::join('sub_syndicate', 'sub_syndicate.id', '=', 'syn_to_sub_syn.sub_syn_id')
                            ->where('syn_to_sub_syn.course_id', $request->course_id)
                            ->where('syn_to_sub_syn.syn_id', $request->syn_id)
                            ->orderBy('sub_syndicate.order', 'asc')
                            ->pluck('sub_syndicate.name', 'sub_syndicate.id')->toArray();
            $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->where('cm_to_syn.course_id', $request->course_id)
                    ->where('cm_to_syn.term_id', $request->term_id)
                    ->where('cm_to_syn.syn_id', $request->syn_id)
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                    ->get();

            $previousCmToSubSynList = CmToSubSyn::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('syn_id', $request->syn_id)
                            ->pluck('sub_syn_id', 'cm_id')->toArray();
//        echo '<pre>';        print_r($previousCmToSubSynList);exit;

            $prevDataArr = CmToSubSyn::where('course_id', $request->course_id)
                    ->where('syn_id', $request->syn_id)
                    ->where('sub_syn_id', $request->sub_syn_id)
                    ->get();


            // list of cm assigned in the selected syndicate
            $checkPreviousDataArr = CmToSubSyn::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('syn_id', $request->syn_id)
                    ->get();

            $subSynDataList = SubSyndicate::pluck('name', 'id')->toArray();

            $prevDataList = [];
            if (!empty($prevDataArr)) {
                foreach ($prevDataArr as $item) {
                    $prevDataList[$item->cm_id] = $item->sub_syn_id;
                }
            }

            $checkPreviousDataList = [];
            if (!empty($checkPreviousDataArr)) {
                foreach ($checkPreviousDataArr as $item) {
                    $checkPreviousDataList[$item->cm_id] = $item->sub_syn_id;
                }
            }

            $totalNumOfAssignedCm = CmToSubSyn::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('syn_id', $request->syn_id)
                    ->where('sub_syn_id', $request->sub_syn_id)
                    ->count();
        }

        return view('cmToSubSyn.index')->with(compact('request', 'activeTrainingYearInfo', 'courseList', 'termList'
                                , 'synList', 'subSynList', 'targetArr', 'prevDataArr', 'checkPreviousDataList', 'subSynDataList'
                                , 'prevDataList', 'previousCmToSubSynList', 'totalNumOfAssignedCm'));
    }

    public function getTerm(Request $request) {
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();
        $html = view('cmToSubSyn.showTerm', compact('termList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSyn(Request $request) {
        $synList = ['0' => __('label.SELECT_SYN_OPT')] + SynToCourse::join('syndicate', 'syndicate.id', '=', 'syn_to_course.syn_id')
                        ->where('syn_to_course.course_id', $request->course_id)
                        ->orderBy('syndicate.order', 'asc')
                        ->pluck('syndicate.name', 'syndicate.id')->toArray();
        $html = view('cmToSubSyn.showSyn', compact('synList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSubSyn(Request $request) {
        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')] + SynToSubSyn::join('sub_syndicate', 'sub_syndicate.id', '=', 'syn_to_sub_syn.sub_syn_id')
                        ->where('syn_to_sub_syn.course_id', $request->course_id)
                        ->where('syn_to_sub_syn.syn_id', $request->syn_id)
                        ->orderBy('sub_syndicate.order', 'asc')
                        ->pluck('sub_syndicate.name', 'sub_syndicate.id')->toArray();
        $html = view('cmToSubSyn.showSubSyn', compact('subSynList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getCm(Request $request) {
        $targetArr = CmBasicProfile::join('cm_to_syn', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->where('cm_to_syn.course_id', $request->course_id)
                ->where('cm_to_syn.term_id', $request->term_id)
                ->where('cm_to_syn.syn_id', $request->syn_id)
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                ->get();

        $previousCmToSubSynList = CmToSubSyn::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('syn_id', $request->syn_id)
                        ->pluck('sub_syn_id', 'cm_id')->toArray();
//        echo '<pre>';        print_r($previousCmToSubSynList);exit;

        $prevDataArr = CmToSubSyn::where('course_id', $request->course_id)
                ->where('syn_id', $request->syn_id)
                ->where('sub_syn_id', $request->sub_syn_id)
                ->get();


        // list of cm assigned in the selected syndicate
        $checkPreviousDataArr = CmToSubSyn::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('syn_id', $request->syn_id)
                ->get();

        $subSynDataList = SubSyndicate::pluck('name', 'id')->toArray();

        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->cm_id] = $item->sub_syn_id;
            }
        }

        $checkPreviousDataList = [];
        if (!empty($checkPreviousDataArr)) {
            foreach ($checkPreviousDataArr as $item) {
                $checkPreviousDataList[$item->cm_id] = $item->sub_syn_id;
            }
        }

        $totalNumOfAssignedCm = CmToSubSyn::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('syn_id', $request->syn_id)
                ->where('sub_syn_id', $request->sub_syn_id)
                ->count();

        $html = view('cmToSubSyn.showCm', compact('targetArr', 'prevDataArr', 'checkPreviousDataList', 'subSynDataList'
                        , 'prevDataList', 'previousCmToSubSynList', 'totalNumOfAssignedCm', 'request'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveCmToSubSyn(Request $request) {

        $cmArr = $request->cm_id;
        $errors = [];
        if (empty($cmArr)) {
            $errors[] = __('label.PLEASE_CHECK_AT_LEAST_ONE_CM');
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'syn_id' => 'required|not_in:0',
            'sub_syn_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 401);
        }
//        echo '<pre>';
//        print_r($cmArr);
//        exit;
        $data = [];
        $i = 0;
        if (!empty($cmArr)) {
            foreach ($cmArr as $key => $cmId) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $request->term_id;
                $data[$i]['syn_id'] = $request->syn_id;
                $data[$i]['sub_syn_id'] = $request->sub_syn_id;
                $data[$i]['cm_id'] = $cmId;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        CmToSubSyn::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('syn_id', $request->syn_id)
                ->where('sub_syn_id', $request->sub_syn_id)
                ->delete();
//        echo '<pre>';
//        print_r($data);
//        exit;
        if (CmToSubSyn::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.CM_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function assignedCmDetails(Request $request) {

        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        $courseInfo = Course::where('id', $request->course_id)->first();
        $termInfo = Term::where('id', $request->term_id)->first();
        $synInfo = Syndicate::where('id', $request->syn_id)->first();

        $targetArr = CmToSyn::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_to_syn.cm_basic_profile_id')
                ->join('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->select('cm_basic_profile.*', 'arms_service.code as arms_service')
                ->where('cm_to_syn.course_id', $request->course_id)
                ->where('cm_to_syn.term_id', $request->term_id)
                ->where('cm_to_syn.syn_id', $request->syn_id)
                ->get();

        $html = view('cmToSyn.showAssignedCm', compact('targetArr'
                        , 'activeTrainingYearInfo', 'courseInfo'
                        , 'termInfo', 'synInfo'))->render();
        return response()->json(['html' => $html]);
    }

    public function getAssignedCm(Request $request) {

        $courseName = Course::select('name')
                ->where('id', $request->course_id)
                ->first();
        $termName = Term::select('name')
                ->where('id', $request->term_id)
                ->first();
        $synName = Syndicate::select('name')
                ->where('id', $request->syn_id)
                ->first();

        $subSynName = SubSyndicate::select('name')
                ->where('id', $request->sub_syn_id)
                ->first();

        $assignedCmArr = CmBasicProfile::join('cm_to_sub_syn', 'cm_to_sub_syn.cm_id', '=', 'cm_basic_profile.id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                ->where('cm_to_sub_syn.course_id', $request->course_id)
                ->where('cm_to_sub_syn.term_id', $request->term_id)
                ->where('cm_to_sub_syn.syn_id', $request->syn_id)
                ->where('cm_to_sub_syn.sub_syn_id', $request->sub_syn_id)
                ->get();

//            echo '<pre>';            print_r($cmGroupName->name);exit;
        $prevDataArr = CmToSubSyn::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('syn_id', $request->syn_id)
                ->where('sub_syn_id', $request->sub_syn_id)
                ->get();

        $subSynList = SubSyndicate::pluck('name', 'id')->toArray();

        // list of cm assigned in the selected syndicate

        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->cm_id][] = $item->sub_syn_id;
            }
        }
        $view = view('cmToSubSyn.showAssignedCm', compact('assignedCmArr', 'courseName'
                        , 'subSynList', 'prevDataList', 'termName', 'synName', 'subSynName'))->render();
        return response()->json(['html' => $view]);
    }

}
