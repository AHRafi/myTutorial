<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\Term;
use App\ArmsService;
use App\Wing;
use App\CommissioningCourse;
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

class ComCourseWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();
        $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];

//        print_r($qpArr);
//        exit;
        $commissionTypeList = array('0' => __('label.SELECT_COMMISSIONING_TYPE')) + Common::getCommissionType();
        $comCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE')) + CommissioningCourse::where('status', '1')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();

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
        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.COMMISSIONING_COURSE'),
            '4' => __('label.COMMISSIONING_DATE'),
            '5' => __('label.COMMISSIONING_TYPE')
        ];

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

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'seniority' => __('label.SENIORITY'),
//            'rank' => __('label.RANK'),
//            'personal_no' => __('label.PERSONAL_NO'),
            'com_asc' => __('label.COMMISSIONING_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'com_desc' => __('label.COMMISSIONING_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
        ];
//        $sortByList = ['position' => __('label.POSITION'), 'svc' => __('label.WING'), 'syn' => __('label.SYN'), 'alphabatically' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'personal_no' => __('label.PERSONAL_NO')];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->join('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
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
            if (!empty($request->com_course_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.commissioning_course_id', $request->com_course_id);
            }
            if (!empty($request->com_type_id)) {
                $cmArr = $cmArr->where('cm_basic_profile.commission_type', $request->com_type_id);
            }


            $commissionDateFrom = !empty($request->commissioning_date_from) ? date("Y-m-d", strtotime($request->commissioning_date_from)) : '';
            $commissionDateTo = !empty($request->commissioning_date_to) ? date("Y-m-d", strtotime($request->commissioning_date_to)) : '';
            if (!empty($commissionDateFrom) && !empty($commissionDateTo)) {
                $cmArr = $cmArr->whereBetween('cm_basic_profile.commisioning_date', [$commissionDateFrom, $commissionDateTo]);
            } else {

                if (!empty($commissionDateFrom)) {
                    $cmArr = $cmArr->where('cm_basic_profile.commisioning_date', '>=', $commissionDateFrom);
                }
                if (!empty($commissionDateTo)) {
                    $cmArr = $cmArr->where('cm_basic_profile.commisioning_date', '<=', $commissionDateTo);
                }
            }


            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                            , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'rank.code as rank', 'cm_basic_profile.email', 'cm_basic_profile.commissioning_course_id', 'cm_basic_profile.id', 'course.name as course_name'
                            , 'cm_basic_profile.commisioning_date', 'cm_basic_profile.commission_type', 'arms_service.code as arms_service_name', 'cm_basic_profile.religion_id', 'cm_basic_profile.gender')
                    ->orderBy('course.id', 'desc');
            
//            $cmArr = $cmArr->select('cm_basic_profile.*')->get();
//            
//            echo "<pre>";
//            print_r($cmArr);
//            exit;
            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'seniority') {
                    $cmArr = $cmArr->orderBy('rank.svc_order', 'asc')
                            ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'rank') {
                    $cmArr = $cmArr->orderBy('rank.order', 'asc');
                } elseif ($request->sort == 'personal_no') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmArr = $cmArr->orderBy('wing.order', 'asc')
                            ->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'com_asc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.commisioning_date', 'asc');
                } elseif ($request->sort == 'com_desc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.commisioning_date', 'desc');
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

            $fileName = 'Commissioning_Course_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('cmAnalytics.comCourseInfo.print.index')->with(compact('request', 'courseList', 'comCourseList', 'commissionTypeList', 'targetArr', 'qpArr', 'sortByList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.comCourseInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList', 'comCourseList', 'commissionTypeList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.comCourseInfo.print.index', compact('request', 'courseList', 'comCourseList', 'commissionTypeList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.comCourseInfo.index', compact('request', 'courseList', 'nameArr', 'comCourseList', 'commissionTypeList', 'printOptionList'
                            ,'columnArr', 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id . '&com_course_id=' . $request->com_course_id . '&commissioning_date_from=' . $request->commissioning_date_from . '&commissioning_date_to=' . $request->commissioning_date_to . '&com_type_id=' . $request->com_type_id
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('basicInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('comCourseWiseCmAnalytics?generate=true&' . $url);
    }

}
