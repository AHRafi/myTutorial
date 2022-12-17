<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\Term;
use App\ArmsService;
use App\Wing;
use App\Rank;
use App\Religion;
use Response;
use PDF;
use Auth;
use File;
use DB;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class BasicInfoWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();
        $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : 0;
        $courseList = Course::where('status', '<>', '0')
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('for_course_member', '1')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();

        $bloodGroupList = ['0' => __('label.SELECT_BLOOD_GROUP')] + Common::getBloodGroup();
        $genderList = ['0' => __('label.SELECT_GENDER')] + Common::getGenderList();
        $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();


        $nameArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', 'cm_basic_profile.arms_service_id')
                ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                ->where('cm_basic_profile.status', '1')
                ->select('official_name')
                ->orderBy('course.id', 'desc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->get();


        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '9' => __('label.FULL_NAME_BANGLA'),
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.EMAIL'),
            '4' => __('label.MOBILE'),
            '5' => __('label.BlOOD_GROUP'),
            '6' => __('label.DATE_OF_BIRTH'),
            '7' => __('label.RELIGION'),
            '8' => __('label.GENDER')
        ];

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
//            'rank' => __('label.RANK'),
//            'personal_no' => __('label.PERSONAL_NO'),
            'dob_asc' => __('label.DATE_OF_BIRTH') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dob_desc' => __('label.DATE_OF_BIRTH') . ' (' . __('label.DESCENDING_ORDER') . ')',
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->where('cm_basic_profile.status', '1');

            if (!empty($explodeCourses)) {
                $cmArr = $cmArr->where('cm_basic_profile.course_id', $explodeCourses);
            }


            $name = $request->name;
            if (!empty($request->name)) {
                $cmArr->where(function($query) use ($name) {
                    $query->where('cm_basic_profile.full_name', 'LIKE', '%' . $name . '%')
                            ->orWhere('cm_basic_profile.official_name', 'LIKE', '%' . $name . '%');
                });
            }

            if (!empty($request->rank_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.rank_id', $request->rank_id);
            }

            if (!empty($request->wing_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.wing_id', $request->wing_id);
            }

            if (!empty($request->arms_service_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.arms_service_id', $request->arms_service_id);
            }
            if (!empty($request->blood_group)) {
                $cmArr = $cmArr->where('cm_basic_profile.blood_group', $request->blood_group);
            }
            if (!empty($request->religion)) {
                $cmArr = $cmArr->where('cm_basic_profile.religion_id', $request->religion);
            }
            if (!empty($request->gender)) {
                $cmArr = $cmArr->where('cm_basic_profile.gender', $request->gender);
            }


            $birthDateFrom = !empty($request->birth_date_from) ? date("Y-m-d", strtotime($request->birth_date_from)) : '';
            $birthDateTo = !empty($request->birth_date_to) ? date("Y-m-d", strtotime($request->birth_date_to)) : '';
            if (!empty($birthDateFrom) && !empty($birthDateTo)) {
                $cmArr = $cmArr->whereBetween('cm_basic_profile.date_of_birth', [$birthDateFrom, $birthDateTo]);
            } else {

                if (!empty($birthDateFrom)) {
                    $cmArr = $cmArr->where('cm_basic_profile.date_of_birth', '>=', $birthDateFrom);
                }
                if (!empty($birthDateTo)) {
                    $cmArr = $cmArr->where('cm_basic_profile.date_of_birth', '<=', $birthDateTo);
                }
            }


            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                            , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'rank.code as rank', 'cm_basic_profile.email', 'cm_basic_profile.blood_group', 'cm_basic_profile.id', 'course.name as course_name'
                            , 'cm_basic_profile.number', 'cm_basic_profile.bn_name', 'cm_basic_profile.date_of_birth', 'arms_service.code as arms_service_name', 'cm_basic_profile.religion_id', 'cm_basic_profile.gender')
                    ->orderBy('course.id', 'desc');
            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'dob_asc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_birth', 'asc');
                } elseif ($request->sort == 'dob_desc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_birth', 'desc');
                } elseif ($request->sort == 'rank') {
                    $cmArr = $cmArr->orderBy('rank.order', 'asc');
                } elseif ($request->sort == 'personal_no') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.personal_no', 'asc');
                }
            } else {
                $cmArr = $cmArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }

            $cmArr = $cmArr->get();

            if (!$cmArr->isEmpty()) {
                foreach ($cmArr as $cmInfo) {
                    $targetArr[$cmInfo->id] = $cmInfo->toArray();
                }
            }

            $fileName = 'Basic_Info_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {

            return view('cmAnalytics.basicInfo.print.index')->with(compact('request', 'courseList', 'bloodGroupList', 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.basicInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.basicInfo.print.index', compact('request', 'courseList', 'bloodGroupList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'religionList', 'genderList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.basicInfo.index', compact('request', 'courseList', 'nameArr', 'printOptionList', 'columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'bloodGroupList', 'religionList', 'genderList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;

        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&blood_group=' . $request->blood_group . '&birth_date_from=' . $request->birth_date_from . '&birth_date_to=' . $request->birth_date_to . '&religion=' . $request->religion . '&gender=' . $request->gender
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }

        return redirect('basicInfoWiseCmAnalytics?generate=true&' . $url);
    }

}
