<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\TrainingYear;
use App\Course;
use App\UserPassport;
use App\UserRelativeInDefence;
use App\Term;
use App\ArmsService;
use App\Appointment;
use App\Wing;
use App\MilCourse;
use App\Rank;
use App\User;
use App\UserBasicProfile;
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

class MilQualWiseDsAnalyticsController extends Controller {

    public function index(Request $request) {

        $qpArr = $request->all();

        $milForeignCourseList = MilCourse::where('category_id', '2')
                        ->pluck('id', 'id')->toArray();


        $milCourseList = MilCourse::where('status', '1')->where('category_id', '<>', '3')
                        ->orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();

        $knGradeList = Common::getKnGradeList();
        $instGradeList = Common::getInstGradeList();

        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('responsibility', '3')->where('status', '1')->pluck('code', 'id')->toArray();

        $printOptionList = ['1' => __('label.WITH_PHOTO'), '2' => __('label.WITHOUT_PHOTO')];
        $columnArr = [
            '1' => __('label.PHOTO'),
            '2' => __('label.APPT_AFWC'),
            '3' => __('label.INSTITUTE_N_COUNTRY'),
            '4' => __('label.MIL_COURSE'),
            '5' => __('label.RESULT'),
            '6' => __('label.FROM'),
            '7' => __('label.TO')
        ];

        $nameArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select('users.full_name')
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->get();

        $sortByList = [
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
        ];
        $synList = $targetArr = $subSynList = [];
        $dsArr = $dsIdArr = $dsDetailArr = $targetArr = [];

        if ($request->generate == 'true') {

            $explodeMilCourses = !empty($request->mil_course_id) ? explode(",", $request->mil_course_id) : [];
            $dsMilQualInfo = UserRelativeInDefence::join('user_basic_profile', 'user_basic_profile.id', 'user_relative_in_defence.user_basic_profile_id');

            $dsMilQualInfo = $dsMilQualInfo->select('user_relative_in_defence.user_basic_profile_id as ds_id', 'user_relative_in_defence.user_relative_info as mil_qual_info')
                    ->get();


            $milCourses = [];
            if (!empty($dsMilQualInfo)) {
                foreach ($dsMilQualInfo as $milQualInfo) {
                    $dsId = $milQualInfo->ds_id;
                    $milQualInfoArr = !empty($milQualInfo->mil_qual_info) ? json_decode($milQualInfo->mil_qual_info, true) : [];

                    if (!empty($milQualInfoArr)) {
                        foreach ($milQualInfoArr as $rsKey => $rsInfo) {
                            $result = '';
                            if (!empty($request->mil_course_id)) {
                                if ($rsInfo['course'] != 5) {
                                    if (!empty($request->kn_result)) {
                                        if (!empty($request->inst_result)) {
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && $rsInfo['result'] == ($request->kn_result . $request->inst_result)) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        } else {
                                            $resArr = Common::getResList($request->kn_result, 1);
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && !empty($resArr[$rsInfo['result']])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        }
                                    } else {
                                        if (!empty($request->inst_result)) {
                                            $resArr = Common::getResList($request->inst_result, 2);
                                            if (in_array($rsInfo['course'], $explodeMilCourses) && !empty($resArr[$rsInfo['result']])) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        } else {
                                            if (in_array($rsInfo['course'], $explodeMilCourses)) {
                                                $dsIdArr[$dsId] = $dsId;
                                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                            }
                                        }
                                    }
                                }
                            } else {
                                $result = $rsInfo['course'] != 5 ? $rsInfo['result'] : $rsInfo['other_result'];
                                if (!empty($request->kn_result)) {
                                    if (!empty($request->inst_result)) {
                                        if ($result == ($request->kn_result . $request->inst_result)) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    } else {
                                        $resArr = Common::getResList($request->kn_result, 1);
                                        if (!empty($resArr[$result])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    }
                                } else {
                                    if (!empty($request->inst_result)) {
                                        $resArr = Common::getResList($request->inst_result, 2);
                                        if (!empty($resArr[$result])) {
                                            $dsIdArr[$dsId] = $dsId;
                                            $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                                        }
                                    }
                                }
                            }

                            if (empty($request->mil_course_id) && empty($request->kn_result) && empty($request->inst_result)) {
                                $dsIdArr[$dsId] = $dsId;
                                $dsDetailArr[$dsId][$rsKey] = $this->pushBankInfo($rsInfo, $milCourseList);
                            }
                        }
                    }
                }
            }
//            

            $dsArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                    ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                    ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id')
                    ->whereIn('user_basic_profile.id', $dsIdArr);

            $name = $request->name;
            if (!empty($request->name)) {
                $dsArr->where(function($query) use ($name) {
                    $query->where('users.full_name', 'LIKE', '%' . $name . '%')
                            ->orWhere('users.official_name', 'LIKE', '%' . $name . '%');
                });
            }

            if (!empty($request->rank_id)) {
                $dsArr = $dsArr->where('users.rank_id', $request->rank_id);
            }
            if (!empty($request->wing_id)) {
                $dsArr = $dsArr->where('users.wing_id', $request->wing_id);
            }
            if (!empty($request->arms_service_id)) {
                $dsArr = $dsArr->where('user_basic_profile.arms_service_id', $request->arms_service_id);
            }
            if (!empty($request->appt_id)) {
                $dsArr = $dsArr->where('appointment.id', $request->appt_id);
            }

            $dsArr = $dsArr->select('users.personal_no', 'users.full_name', 'users.official_name', 'appointment.code as appointment_name'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name")
                            , 'users.photo', 'rank.code as rank', 'arms_service.code as arms_service_name', 'user_basic_profile.id')
                    ->where('users.status', '1');

            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $dsArr = $dsArr->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $dsArr = $dsArr->orderBy('wing.order', 'asc')
                            ->orderBy('users.official_name', 'asc');
                }
            } else {
                $dsArr = $dsArr->orderBy('appointment.order', 'asc')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc');
            }
            $dsArr = $dsArr->get();

//            echo '<pre>';
//            echo count($dsArr->toArray());
//            print_r($dsArr->toArray());
//            exit;

            if (!$dsArr->isEmpty()) {
                foreach ($dsArr as $dsInfo) {
                    $targetArr[$dsInfo->id] = $dsInfo->toArray();
                    if (!empty($dsDetailArr[$dsInfo->id])) {
                        $targetArr[$dsInfo->id]['rec_svc'] = $dsDetailArr[$dsInfo->id];
                        $targetArr[$dsInfo->id]['rec_svc_span'] = sizeof($dsDetailArr[$dsInfo->id]);
                    }
                }
            }


            $fileName = 'Mil_Qual_Wise_DS_Analytics';
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('dsAnalytics.milQualInfo.print.index')->with(compact('request', 'targetArr', 'appointmentList', 'qpArr'
                                    , 'sortByList', 'printOptionList', 'knGradeList', 'instGradeList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.milQualInfo.print.index', compact('request', 'activeTrainingYearList', 'appointmentList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'knGradeList', 'instGradeList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.milQualInfo.print.index', compact('request', 'appointmentList'
                                    , 'targetArr', 'qpArr', 'sortByList', 'knGradeList', 'instGradeList'), 3), $fileName . '.xlsx');
        } else {

            return view('dsAnalytics.milQualInfo.index', compact('request', 'milCourseList', 'nameArr', 'appointmentList','columnArr'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList', 'armsServiceList', 'wingList', 'rankList'
                            , 'printOptionList', 'knGradeList', 'instGradeList'));
        }
    }

    public function filter(Request $request) {
        $rules = $messages = [];
        $implodeMilCourses = !empty($request->mil_course_id) ? implode(",", $request->mil_course_id) : '';
        $url = 'name=' . urlencode($request->name) . '&wing_id=' . $request->wing_id
                . '&rank_id=' . $request->rank_id . '&arms_service_id=' . $request->arms_service_id
                . '&mil_course_id=' . $implodeMilCourses . '&kn_result=' . urlencode($request->kn_result)
                . '&inst_result=' . urlencode($request->inst_result)
                . '&foreign_course=' . $request->foreign_course
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('milQualWiseDsAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('milQualWiseDsAnalytics?generate=true&' . $url);
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

        $dsDetailArr['institute_name'] = $rsInfo['institute_name'] ?? '';
        $dsDetailArr['course'] = $course;
        $dsDetailArr['from'] = $rsInfo['from'] ?? '';
        $dsDetailArr['to'] = $rsInfo['to'] ?? '';
        $dsDetailArr['result'] = $result;

        return $dsDetailArr;
    }

    

}
