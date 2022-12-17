<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\DsGroupMemberTemplate;
use App\DsGroup;
use App\DsGroupToCourse;
use App\TermToCourse;
use App\User;
use App\Rank;
use App\Term;
use App\Wing;
use Session;
use Redirect;
use Helper;
use Response;
use Auth;
use DB;
use Illuminate\Http\Request;

class DsGroupMemberTemplateController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.DS_GROUP_MEMBER_TEMPLATE');
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


        $dsGroupList = ['0' => __('label.SELECT_DS_GROUP_OPT')];

        $numberOfDs = '';
        $dsGroupMemberArr = $previousDsGroupMemberList = $prevCourseWiseDsGroupMemberList = [];
        $prevDataArr = $chackPrevDataArr = $dsGroupDataList = $prevDataList = $chackPrevDataList = [];
        $totalPrevDataArr = $totalPrevDataList = $disableDs = [];

        if (!empty($request->course_id) && !empty($request->term_id) && !empty($request->ds_group_id)) {

            $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                            ->where('term_to_course.course_id', $request->course_id)
                            ->orderBy('term.order', 'asc')
                            ->pluck('term.name', 'term.id')
                            ->toArray();

            $dsGroupList = ['0' => __('label.SELECT_DS_GROUP_OPT')] + DsGroupToCourse::Join('ds_group', 'ds_group.id', '=', 'ds_group_to_course.ds_group_id')
                            ->where('ds_group.status', '1')
                            ->where('ds_group_to_course.course_id', $request->course_id)
                            ->orderBy('ds_group.id', 'asc')
                            ->pluck('ds_group.name', 'ds_group.id')
                            ->toArray();

            $dsGroupMemberArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
					->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->select('users.id', 'users.photo', 'users.personal_no', 'wing.code as wing_name'
                            , 'rank.code as rank_code', 'users.official_name')
                    ->whereIn('users.group_id', [4])
					->where('users.status', '1')
                    ->orderBy('wing.order', 'asc')
					->orderBy('appointment.order', 'asc')
					->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc')
                    ->get();
//        echo '<pre>';        print_r($dsGroupMemberArr->toArray());exit;

            $previousDsGroupMemberList = DsGroupMemberTemplate::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->pluck('ds_group_id', 'user_id')->toArray();

            $prevCourseWiseDsGroupMemberList = DsGroupMemberTemplate::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('ds_group_id', $request->ds_group_id)
                            ->pluck('ds_group_id', 'user_id')->toArray();

            $prevDataArr = DsGroupMemberTemplate::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->get();

            $chackPrevDataArr = DsGroupMemberTemplate::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('ds_group_id', $request->ds_group_id)
                    ->get();

            $dsGroupDataList = DsGroup::pluck('name', 'id')->toArray();

            if (!empty($prevDataArr)) {
                foreach ($prevDataArr as $item) {
                    $prevDataList[$item->user_id][] = $item->ds_group_id;
                }
            }

            if (!empty($chackPrevDataArr)) {
                foreach ($chackPrevDataArr as $item) {
                    $chackPrevDataList[$item->ds_basic_profile_id] = $item->ds_group_id;
                }
            }

            $totalPrevDataArr = DsGroupMemberTemplate::get();

            if (!empty($totalPrevDataArr)) {
                foreach ($totalPrevDataArr as $item) {
                    $totalPrevDataList[$item->user_id][] = $item->ds_group_id;
                }
            }

            // number of assigned ds selected ds group

            $numberOfDs = DsGroupMemberTemplate::join('users', 'users.id', '=', 'ds_group_member_template.user_id')
                    ->where('ds_group_member_template.ds_group_id', $request->ds_group_id)
                    ->where('ds_group_member_template.course_id', $request->course_id)
                    ->where('ds_group_member_template.term_id', $request->term_id)
                    ->count();


            //checked
            //Dependency check Disable data
            $disableDs = [];
        }

        return view('dsGroupMemberTemplate.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'termList'
                                , 'dsGroupList', 'dsGroupMemberArr', 'prevDataList', 'chackPrevDataArr'
                                , 'previousDsGroupMemberList', 'prevDataArr', 'dsGroupDataList', 'disableDs', 'totalPrevDataList'
                                , 'numberOfDs', 'request', 'prevCourseWiseDsGroupMemberList', 'chackPrevDataList'));
    }

    public function getDsGroup(Request $request) {

        $dsGroupList = ['0' => __('label.SELECT_DS_GROUP_OPT')] + DsGroupToCourse::Join('ds_group', 'ds_group.id', '=', 'ds_group_to_course.ds_group_id')
                        ->where('ds_group.status', '1')
                        ->where('ds_group_to_course.course_id', $request->course_id)
                        ->orderBy('ds_group.id', 'asc')
                        ->pluck('ds_group.name', 'ds_group.id')
                        ->toArray();

        $html = view('dsGroupMemberTemplate.showDsGroup', compact('dsGroupList'))->render();

        return response()->json(['html' => $html]);
    }

    public function dsGroupMember(Request $request) {
        $dsGroupMemberArr = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select('users.id', 'users.photo', 'users.personal_no', 'wing.code as wing_name'
                        , 'rank.code as rank_code', 'users.official_name')
                ->whereIn('users.group_id', [4])
				->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();
//        echo '<pre>';        print_r($dsGroupMemberArr->toArray());exit;

        $previousDsGroupMemberList = DsGroupMemberTemplate::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->pluck('ds_group_id', 'user_id')->toArray();

        $prevCourseWiseDsGroupMemberList = DsGroupMemberTemplate::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('ds_group_id', $request->ds_group_id)
                        ->pluck('ds_group_id', 'user_id')->toArray();

        $prevDataArr = DsGroupMemberTemplate::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->get();

        $chackPrevDataArr = DsGroupMemberTemplate::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('ds_group_id', $request->ds_group_id)
                ->get();

        $dsGroupDataList = DsGroup::pluck('name', 'id')->toArray();

        $prevDataList = [];
        if (!empty($prevDataArr)) {
            foreach ($prevDataArr as $item) {
                $prevDataList[$item->user_id][] = $item->ds_group_id;
            }
        }

        $chackPrevDataList = [];
        if (!empty($chackPrevDataArr)) {
            foreach ($chackPrevDataArr as $item) {
                $chackPrevDataList[$item->ds_basic_profile_id] = $item->ds_group_id;
            }
        }

        $totalPrevDataArr = DsGroupMemberTemplate::get();
        $totalPrevDataList = [];
        if (!empty($totalPrevDataArr)) {
            foreach ($totalPrevDataArr as $item) {
                $totalPrevDataList[$item->user_id][] = $item->ds_group_id;
            }
        }

        // number of assigned ds selected ds group
        $numberOfDs = '';
        $numberOfDs = DsGroupMemberTemplate::join('users', 'users.id', '=', 'ds_group_member_template.user_id')
                ->where('ds_group_member_template.ds_group_id', $request->ds_group_id)
                ->where('ds_group_member_template.course_id', $request->course_id)
                ->where('ds_group_member_template.term_id', $request->term_id)
                ->count();

        $numberOfRecruit = '';
        //checked
        //Dependency check Disable data
        $disableDs = [];
        //end

        $html = view('dsGroupMemberTemplate.dsGroupMember', compact('dsGroupMemberArr', 'prevDataList', 'chackPrevDataArr'
                        , 'previousDsGroupMemberList', 'prevDataArr', 'dsGroupDataList', 'disableDs', 'totalPrevDataList'
                        , 'numberOfDs', 'request', 'prevCourseWiseDsGroupMemberList', 'chackPrevDataList'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveDsGroupMember(Request $request) {
        $dsArr = $request->ds_id;


        if (empty($dsArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_DS_GROUP_TO_ATLEAST_ONE_DS')), 401);
        }
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'ds_group_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($request->ds_group_id)) {
            if (!empty($dsArr)) {
                foreach ($dsArr as $key => $dsId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['term_id'] = $request->term_id;
                    $data[$key]['ds_group_id'] = $request->ds_group_id;
                    $data[$key]['user_id'] = $dsId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }
            DsGroupMemberTemplate::where('ds_group_id', $request->ds_group_id)
                    ->where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->delete();
        }

        if (DsGroupMemberTemplate::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_DS')), 401);
        }
    }

    public function getAssignedDs(Request $request) {

        $dsGroupName = DsGroup::select('name')
                ->where('id', $request->ds_group_id)
                ->first();

        $courseName = Course::select('name')
                ->where('id', $request->course_id)
                ->first();
        
        $termName = Term::select('name')
                ->where('id', $request->term_id)
                ->first();

        $assignedDsArr = User::join('ds_group_member_template', 'ds_group_member_template.user_id', '=', 'users.id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select('users.id', 'users.photo', 'users.personal_no', 'wing.code as wing_name'
                        , 'rank.code as rank_code', 'users.official_name')
                ->where('ds_group_member_template.ds_group_id', $request->ds_group_id)
                ->where('ds_group_member_template.course_id', $request->course_id)
                ->where('ds_group_member_template.term_id', $request->term_id)
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

//            echo '<pre>';            print_r($dsGroupName->name);exit;


        $view = view('dsGroupMemberTemplate.showAssignedDs', compact('assignedDsArr', 'dsGroupName', 'courseName', 'termName'))->render();
        return response()->json(['html' => $view]);
    }

}
