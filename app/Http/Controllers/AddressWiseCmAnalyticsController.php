<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmPresentAddress;
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

class AddressWiseCmAnalyticsController extends Controller
{
    public function index(Request $request) {
      
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
            '3' => __('label.ADDRESS')
        ];
        
        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
//            'rank' => __('label.RANK'),
//            'personal_no' => __('label.PERSONAL_NO'),
            'address_asc' => __('label.ADD_RES'),
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $cmArr = CmPresentAddress::leftJoin('cm_basic_profile', 'cm_basic_profile.id', 'cm_present_address.cm_basic_profile_id')
                                     ->leftJoin('rank','rank.id','cm_basic_profile.rank_id')
                                     ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                                     ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                                     ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id');

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
            $address= $request->address;
            if (!empty($request->address)) {
                $cmArr = $cmArr->where('cm_present_address.address_details', 'LIKE', '%' . $address . '%');
            }


            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                    , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                    , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                    , 'cm_basic_profile.photo', 'cm_basic_profile.id','cm_present_address.address_details','cm_basic_profile.arms_service_id','course.name as course_name')
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
                } elseif ($request->sort == 'address_asc') {
                    $cmArr = $cmArr->orderBy('cm_present_address.address_details', 'asc');
                } elseif ($request->sort == 'rank') {
                    $cmArr = $cmArr->orderBy('rank.order', 'asc');
                }elseif ($request->sort == 'personal_no') {
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
            
            $fileName = 'Residence_Address_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('cmAnalytics.addressInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList','armsServiceList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.addressInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.addressInfo.print.index', compact('request', 'courseList','armsServiceList'
                                    , 'targetArr', 'qpArr', 'sortByList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.addressInfo.index', compact('request', 'courseList','nameArr','printOptionList'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList','columnArr'
                    , 'wingList', 'rankList'));
        }  
        
        
    }
    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : 0;
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id .  '&address=' .$request->address
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('addressWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('addressWiseCmAnalytics?generate=true&' . $url);
    }
}
