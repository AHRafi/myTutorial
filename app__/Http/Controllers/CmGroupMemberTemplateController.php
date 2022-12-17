<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmGroupMemberTemplate;
use App\CmGroup;
use App\CmGroupToCourse;
use App\CmBasicProfile;
use App\TermToCourse;
use App\Term;
use Response;
use Auth;
use DB;
use Illuminate\Http\Request;

class CmGroupMemberTemplateController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CM_GROUP_MEMBER_TEMPLATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $activeCourse->id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();

        $cmGroupList = ['0' => __('label.SELECT_CM_GROUP_OPT')];

        $numberOfCm = '';
        $previousCmGroupMemberList = $cmGroupMemberArr = [];
        $cmGroupDataList = $chackPrevDataArr = $prevDataArr = $prevCourseWiseCmGroupMemberList = [];
        $prevDataList = $chackPrevDataList = $totalPrevDataList = $disableCm = [];

        if (!empty($request->course_id) && !empty($request->term_id) && !empty($request->cm_group_id)) {

            $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                            ->where('term_to_course.course_id', $request->course_id)
                            ->orderBy('term.order', 'asc')
                            ->pluck('term.name', 'term.id')->toArray();

            $cmGroupList = ['0' => __('label.SELECT_CM_GROUP_OPT')] + CmGroupToCourse::Join('cm_group', 'cm_group.id', '=', 'cm_group_to_course.cm_group_id')
                            ->where('cm_group.status', '1')
                            ->where('cm_group_to_course.course_id', $request->course_id)
                            ->orderBy('cm_group.id', 'asc')
                            ->pluck('cm_group.name', 'cm_group.id')
                            ->toArray();

            $cmGroupMemberArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();


            $previousCmGroupMemberList = CmGroupMemberTemplate::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->pluck('cm_group_id', 'cm_basic_profile_id')->toArray();


            $prevCourseWiseCmGroupMemberList = CmGroupMemberTemplate::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('cm_group_id', $request->cm_group_id)
                            ->pluck('cm_group_id', 'cm_basic_profile_id')->toArray();

            $prevDataArr = CmGroupMemberTemplate::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)->get();

            $chackPrevDataArr = CmGroupMemberTemplate::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('cm_group_id', $request->cm_group_id)
                    ->get();

            $cmGroupDataList = CmGroup::pluck('name', 'id')->toArray();

            if (!empty($prevDataArr)) {
                foreach ($prevDataArr as $item) {
                    $prevDataList[$item->cm_basic_profile_id][] = $item->cm_group_id;
                }
            }


            if (!empty($chackPrevDataArr)) {
                foreach ($chackPrevDataArr as $item) {
                    $chackPrevDataList[$item->cm_basic_profile_id] = $item->cm_group_id;
                }
            }

            $totalPrevDataArr = CmGroupMemberTemplate::get();

            if (!empty($totalPrevDataArr)) {
                foreach ($totalPrevDataArr as $item) {
                    $totalPrevDataList[$item->cm_basic_profile_id][] = $item->cm_group_id;
                }
            }

            // number of assigned cm selected cm group

            $numberOfCm = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_group_member_template.cm_basic_profile_id')
                    ->where('cm_group_member_template.cm_group_id', $request->cm_group_id)
                    ->where('cm_group_member_template.course_id', $request->course_id)
                    ->where('cm_group_member_template.term_id', $request->term_id)
                    ->count();


            //checked
            //Dependency check Disable data
            $disableCm = [];
        }

        return view('cmGroupMemberTemplate.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'
                                , 'cmGroupList', 'cmGroupMemberArr', 'prevDataList', 'chackPrevDataList'
                                , 'previousCmGroupMemberList', 'prevDataArr', 'cmGroupDataList', 'disableCm', 'totalPrevDataList', 'chackPrevDataArr'
                                , 'numberOfCm', 'request', 'prevCourseWiseCmGroupMemberList'));
    }

    public function getCmGroup(Request $request) {

        $cmGroupList = ['0' => __('label.SELECT_CM_GROUP_OPT')] + CmGroupToCourse::Join('cm_group', 'cm_group.id', '=', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group.status', '1')
                        ->where('cm_group_to_course.course_id', $request->course_id)
                        ->orderBy('cm_group.id', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')
                        ->toArray();

        $html = view('cmGroupMemberTemplate.showCmGroup', compact('cmGroupList'))->render();

        return response()->json(['html' => $html]);
    }

    public function cmGroupMember(Request $request) {

        $cmGroupMemberArr = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();


        $previousCmGroupMemberList = CmGroupMemberTemplate::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->pluck('cm_group_id', 'cm_basic_profile_id')->toArray();


        $prevCourseWiseCmGroupMemberList = CmGroupMemberTemplate::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('cm_group_id', $request->cm_group_id)
                        ->pluck('cm_group_id', 'cm_basic_profile_id')->toArray();

        $prevDataArr = CmGroupMemberTemplate::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->get();

        $chackPrevDataArr = CmGroupMemberTemplate::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('cm_group_id', $request->cm_group_id)
                ->get();

        $cmGroupDataList = CmGroup::pluck('name', 'id')->toArray();

        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->cm_basic_profile_id][] = $item->cm_group_id;
            }
        }

        $chackPrevDataList = [];
        if (!empty($chackPrevDataArr)) {
            foreach ($chackPrevDataArr as $item) {
                $chackPrevDataList[$item->cm_basic_profile_id] = $item->cm_group_id;
            }
        }

        $totalPrevDataArr = CmGroupMemberTemplate::get();
        $totalPrevDataList = [];
        if (!empty($totalPrevDataArr)) {
            foreach ($totalPrevDataArr as $item) {
                $totalPrevDataList[$item->cm_basic_profile_id][] = $item->cm_group_id;
            }
        }

        // number of assigned cm selected cm group
        $numberOfCm = '';
        $numberOfCm = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_group_member_template.cm_basic_profile_id')
                ->where('cm_group_member_template.cm_group_id', $request->cm_group_id)
                ->where('cm_group_member_template.term_id', $request->term_id)
                ->where('cm_group_member_template.course_id', $request->course_id)
                ->count();

        $numberOfRecruit = '';
        //checked
        //Dependency check Disable data
        $disableCm = [];
        //end

        $html = view('cmGroupMemberTemplate.cmGroupMember', compact('cmGroupMemberArr', 'prevDataList', 'chackPrevDataList'
                        , 'previousCmGroupMemberList', 'prevDataArr', 'cmGroupDataList', 'disableCm', 'totalPrevDataList', 'chackPrevDataArr'
                        , 'numberOfCm', 'request', 'prevCourseWiseCmGroupMemberList'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveCmGroupMember(Request $request) {
        $cmArr = $request->cm_id;

        if (empty($cmArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_CM_GROUP_TO_ATLEAST_ONE_CM')), 401);
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'cm_group_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($cmArr)) {
            foreach ($cmArr as $key => $cmId) {
                $data[$key]['course_id'] = $request->course_id;
                $data[$key]['term_id'] = $request->term_id;
                $data[$key]['cm_group_id'] = $request->cm_group_id;
                $data[$key]['cm_basic_profile_id'] = $cmId;
                $data[$key]['updated_by'] = Auth::user()->id;
                $data[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        CmGroupMemberTemplate::where('cm_group_id', $request->cm_group_id)
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->delete();


        if (CmGroupMemberTemplate::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_CM')), 401);
        }
    }

    public function getAssignedCm(Request $request) {

        $cmGroupName = CmGroup::select('name')
                ->where('id', $request->cm_group_id)
                ->first();

        $courseName = Course::select('name')
                ->where('id', $request->course_id)
                ->first();
        
        $termName = Term::select('name')
                ->where('id', $request->term_id)
                ->first();

        $assignedCmArr = CmBasicProfile::join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', '=', 'cm_basic_profile.id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name')
                ->where('cm_group_member_template.cm_group_id', $request->cm_group_id)
                ->where('cm_group_member_template.course_id', $request->course_id)
                ->where('cm_group_member_template.term_id', $request->term_id)
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();

//            echo '<pre>';            print_r($cmGroupName->name);exit;


        $view = view('cmGroupMemberTemplate.showAssignedCm', compact('assignedCmArr', 'cmGroupName', 'courseName', 'termName'))->render();
        return response()->json(['html' => $view]);
    }

}
