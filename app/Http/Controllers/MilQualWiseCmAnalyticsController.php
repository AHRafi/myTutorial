<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CmOthers;
use App\CmRelativeInDefence;
use App\CmServiceRecord;
use App\Term;
use App\MilCourse;
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

class MilQualWiseCmAnalyticsController extends Controller {

    public function index(Request $request) {
        //get only active training year

        $qpArr = $request->all();

        $milForeignCourseList = MilCourse::where('category_id', '2')
                        ->pluck('id', 'id')->toArray();

        $courseList = Course::where('status', '<>', '0')
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();
        $milCourseList = MilCourse::where('status', '1')->where('category_id', '<>', '3')
                        ->orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('for_course_member', '1')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();

        $knGradeList = Common::getKnGradeList();
        $instGradeList = Common::getInstGradeList();

        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.AFWC_COURSE_NAME'),
            '3' => __('label.INSTITUTE_N_COUNTRY'),
            '4' => __('label.MIL_COURSE'),
            '5' => __('label.RESULT'),
            '6' => __('label.FROM'),
            '7' => __('label.TO')
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
//            'rank' => __('label.RANK'),
//            'personal_no' => __('label.PERSONAL_NO'),
        ];
        $synList = $targetArr = $subSynList = [];
        $cmArr = $cmIdArr = $cmDetailArr = $targetArr = [];
        if ($request->generate == 'true') {

            $explodeCourses = !empty($request->course_id) ? explode(",", $request->course_id) : [];
            $explodeMilCourses = !empty($request->mil_course_id) ? explode(",", $request->mil_course_id) : [];
            $cmMilQualInfo = CmRelativeInDefence::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_relative_in_defence.cm_basic_profile_id');
            if (!empty($explodeCourses)) {
                $cmMilQualInfo = $cmMilQualInfo->whereIn('cm_basic_profile.course_id', $explodeCourses);
            }
            $cmMilQualInfo = $cmMilQualInfo->select('cm_relative_in_defence.cm_basic_profile_id as cm_id', 'cm_relative_in_defence.cm_relative_info as mil_qual_info')
                    ->get();
            $milCourses = [];
            if (!empty($cmMilQualInfo)) {
                foreach ($cmMilQualInfo as $milQualInfo) {
                    $cmId = $milQualInfo->cm_id;
                    $milQualInfoArr = !empty($milQualInfo->mil_qual_info) ? json_decode($milQualInfo->mil_qual_info, true) : [];

                    if (!empty($milQualInfoArr)) {
                        foreach ($milQualInfoArr as $rsKey => $rsInfo) {
                            $result = '';
                            if (!empty($request->mil_course_id)) {
                                if ($rsInfo['course'] != 5) {
                                    if (!empty($request->kn_result)) {
                                        if (!empty($request->inst_result)) {
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && $rsInfo['result'] == ($request->kn_result . $request->inst_result)) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        } else {
                                            $resArr = Common::getResList($request->kn_result, 1);
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && !empty($resArr[$rsInfo['result']])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        }
                                    } else {
                                        if (!empty($request->inst_result)) {
                                            $resArr = Common::getResList($request->inst_result, 2);
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && !empty($resArr[$rsInfo['result']])) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        } else {
                                            if (in_array($rsInfo['course'], $explodeMilCourses)) {
                                                $cmIdArr[$cmId] = $cmId;
                                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        }
                                    }
                                }
                            } else {
                                $result = $rsInfo['course'] != 5 ? $rsInfo['result'] : $rsInfo['other_result'];
                                if (!empty($request->kn_result)) {
                                    if (!empty($request->inst_result)) {
                                        if ($result == ($request->kn_result . $request->inst_result)) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    } else {
                                        $resArr = Common::getResList($request->kn_result, 1);
                                        if (!empty($resArr[$result])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    }
                                } else {
                                    if (!empty($request->inst_result)) {
                                        $resArr = Common::getResList($request->inst_result, 2);
                                        if (!empty($resArr[$result])) {
                                            $cmIdArr[$cmId] = $cmId;
                                            $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    }
                                }
                            }

                            if (empty($request->mil_course_id) && empty($request->kn_result) && empty($request->inst_result)) {
                                $cmIdArr[$cmId] = $cmId;
                                $cmDetailArr[$cmId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                            }
                        }
                    }
                }
            }

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->where('cm_basic_profile.status', '1')
                    ->whereIn('cm_basic_profile.id', $cmIdArr);

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

            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                            , 'rank.code as rank',  'cm_basic_profile.full_name as full_name' , 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'rank.code as rank', 'cm_basic_profile.email', 'cm_basic_profile.blood_group', 'cm_basic_profile.id', 'course.name as course_name'
                            , 'cm_basic_profile.number', 'cm_basic_profile.date_of_birth', 'arms_service.code as arms_service_name', 'cm_basic_profile.religion_id', 'cm_basic_profile.gender')
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
                    if (!empty($cmDetailArr[$cmInfo->id])) {
                        $targetArr[$cmInfo->id]['rec_svc'] = $cmDetailArr[$cmInfo->id];
                        $targetArr[$cmInfo->id]['rec_svc_span'] = sizeof($cmDetailArr[$cmInfo->id]);
                    }
                }
            }

            $fileName = 'Mil_Qual_Wise_CM_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('cmAnalytics.milQualInfo.print.index')->with(compact('request', 'courseList', 'targetArr', 'qpArr', 'sortByList', 'knGradeList', 'instGradeList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.milQualInfo.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'knGradeList', 'instGradeList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.milQualInfo.print.index', compact('request', 'courseList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'knGradeList', 'instGradeList'), 3), $fileName . '.xlsx');
        } else {

            return view('cmAnalytics.milQualInfo.index', compact('request', 'courseList', 'milCourseList', 'nameArr', 'printOptionList' , 'columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList', 'knGradeList', 'instGradeList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];

        $implodeCourses = !empty($request->course_id) ? implode(",", $request->course_id) : '';
        $implodeMilCourses = !empty($request->mil_course_id) ? implode(",", $request->mil_course_id) : '';
        $url = 'course_id=' . $implodeCourses . '&name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id
                . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id
                . '&mil_course_id=' . $implodeMilCourses . '&kn_result=' . urlencode($request->kn_result)
                . '&inst_result=' . urlencode($request->inst_result)
                . '&foreign_course=' . $request->foreign_course
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('milQualWiseCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('milQualWiseCmAnalytics?generate=true&' . $url);
    }

    public function pushBankInfo($rsInfo, $milCourseList) {
        $course = $result = '';
        if (!empty($rsInfo['course'])) {
            if ($rsInfo['course'] != 5) {
                $course = !empty($milCourseList[$rsInfo['course']]) ? $milCourseList[$rsInfo['course']] : '';
                $result = $rsInfo['result'] ?? '';
            } else {
                $course = $rsInfo['course_name'] ?? '';
                $result = $rsInfo['other_result'] ?? '';
            }
        }

        $cmDetailArr['institute_name'] = $rsInfo['institute_name'] ?? '';
        $cmDetailArr['course'] = $course;
        $cmDetailArr['from'] = $rsInfo['from'] ?? '';
        $cmDetailArr['to'] = $rsInfo['to'] ?? '';
        $cmDetailArr['result'] = $result;

        return $cmDetailArr;
    }

    

}
