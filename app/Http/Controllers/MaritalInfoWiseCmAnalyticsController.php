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
use Helper;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MaritalInfoWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
//         
        $qpArr = $request->all();
        $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];

//        print_r($qpArr);
//        exit;

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

        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $spouseProf = ['0' => __('label.SELECT_SPOUSE_PROFESSION')] + Common::getSpouseProfessionList();

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
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.DOB_SELF'),
            '4' => __('label.SPOUSE_BIRTH_DATE'),
            '5' => __('label.MARRIAGE_DATE'),
            '6' => __('label.SPOUSE_PROFESSION')
        ];

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'marriage_date_des' => __('label.MARRIAGE_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'marriage_date_asc' => __('label.MARRIAGE_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'spouse_dob_desc' => __('label.SPOUSE_BIRTH_DATE') . ' (' . __('label.DESCENDING_ORDER') . ')',
            'spouse_dob_asc' => __('label.SPOUSE_BIRTH_DATE') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dob_asc' => __('label.DOB_SELF') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dob_desc' => __('label.DOB_SELF') . ' (' . __('label.DESCENDING_ORDER') . ')',
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];
			
			

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
            if (!empty($request->marital_status)) {
                $cmArr = $cmArr->where('cm_basic_profile.marital_status', $request->marital_status);
            }
            if (!empty($request->spouse_profession)) {
                $cmArr = $cmArr->where('cm_basic_profile.spouse_occupation', $request->spouse_profession);
            }


            $marriageDateFrom = !empty($request->marriage_date_from) ? date("Y-m-d", strtotime($request->marriage_date_from)) : '';
            $marriageDateTo = !empty($request->marriage_date_to) ? date("Y-m-d", strtotime($request->marriage_date_to)) : '';
            if (!empty($marriageDateFrom) && !empty($marriageDateTo)) {
                $cmArr = $cmArr->whereBetween('cm_basic_profile.date_of_marriage', [$marriageDateFrom, $marriageDateTo]);
            } else {

                if (!empty($marriageDateFrom)) {
                    $cmArr = $cmArr->where('cm_basic_profile.date_of_marriage', '>=', $marriageDateFrom);
                }
                if (!empty($marriageDateTo)) {
                    $cmArr = $cmArr->where('cm_basic_profile.date_of_marriage', '<=', $marriageDateTo);
                }
            }

            $spouseBirthDateFrom = !empty($request->spouse_birth_date_from) ? date("Y-m-d", strtotime($request->spouse_birth_date_from)) : '';
            $spouseBirthDateTo = !empty($request->spouse_birth_date_to) ? date("Y-m-d", strtotime($request->spouse_birth_date_to)) : '';
            if (!empty($spouseBirthDateFrom) && !empty($spouseBirthDateTo)) {
                $cmArr = $cmArr->whereBetween('cm_basic_profile.spouse_dob', [$spouseBirthDateFrom, $spouseBirthDateTo]);
            } else {

                if (!empty($spouseBirthDateFrom)) {
                    $cmArr = $cmArr->where('cm_basic_profile.spouse_dob', '>=', $spouseBirthDateFrom);
                }
                if (!empty($spouseBirthDateTo)) {
                    $cmArr = $cmArr->where('cm_basic_profile.spouse_dob', '<=', $spouseBirthDateTo);
                }
            }


            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name"), 'cm_basic_profile.date_of_birth'
                            , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'rank.code as rank', 'cm_basic_profile.spouse_dob', 'cm_basic_profile.id', 'cm_basic_profile.blood_group', 'cm_basic_profile.marital_status', 'course.name as course_name'
                            , 'cm_basic_profile.date_of_marriage', 'cm_basic_profile.spouse_occupation', 'arms_service.code as arms_service_name', 'cm_basic_profile.religion_id', 'cm_basic_profile.gender')
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
                } elseif ($request->sort == 'marriage_date_des') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_marriage', 'desc');
                } elseif ($request->sort == 'spouse_dob_desc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.spouse_dob', 'desc');
                } elseif ($request->sort == 'spouse_dob_asc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.spouse_dob', 'asc');
                } elseif ($request->sort == 'marriage_date_asc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_marriage', 'asc');
                } elseif ($request->sort == 'dob_desc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_birth', 'desc');
                } elseif ($request->sort == 'dob_asc') {
                    $cmArr = $cmArr->orderBy('cm_basic_profile.date_of_birth', 'asc');
                }
            } else {
                $cmArr = $cmArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }

            $cmArr = $cmArr->get();
//    echo '<pre>'; print_r($cmArr->toArray()); exit;      
            if (!$cmArr->isEmpty()) {
                foreach ($cmArr as $cmInfo) {
                    $targetArr[$cmInfo->id] = $cmInfo->toArray();
                }
            }


            $fileName = 'Marital_Info_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('cmAnalytics.maritalInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList', 'spouseProf', 'maritalStatusList', 'printOptionList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.maritalInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList', 'printOptionList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.maritalInfo.print.index', compact('request', 'courseList', 'spouseProf'
                                    , 'targetArr', 'qpArr', 'sortByList', 'maritalStatusList', 'printOptionList'), 3), $fileName . '.xlsx');
        } else {
			
            return view('cmAnalytics.maritalInfo.index', compact('request', 'courseList', 'maritalStatusList', 'spouseProf', 'nameArr', 'columnArr'
                            , 'targetArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'printOptionList'));
        }
//        return view('cmAnalytics.maritalInfo.index', compact( 'courseList','maritalStatusList','spouseProf'
//                            , 'targetArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'bloodGroupList', 'religionList', 'genderList'));
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id 
		. '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id 
		. '&marital_status=' . $request->marital_status . '&marriage_date_from=' . $request->marriage_date_from 
		. '&marriage_date_to=' . $request->marriage_date_to . '&spouse_profession=' . $request->spouse_profession 
		. '&spouse_birth_date_from=' . $request->spouse_birth_date_from 
		. '&spouse_birth_date_to=' . $request->spouse_birth_date_to
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('maritalInfoWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('maritalInfoWiseCmAnalytics?generate=true&' . $url);
    }
}
