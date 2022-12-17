<?php

namespace App\Http\Controllers;

use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use Common;
use DateTime;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Validator;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CelebrationCmAnalyticsController extends Controller {

    public function index(Request $request) {
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.CELEBRATION_REPORT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.CELEBRATION_REPORT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $celEventList = [
            '0' => __('label.SELECT_CLEBRATION_EVENT_OPT'),
            '1' => __('label.SELF_BIRTH_DAY'),
            '2' => __('label.SPOUSE_BIRTH_DAY'),
            '3' => __('label.MARRIAGE_DAY'),
        ];

        $monthList = Common::getMonthList();
        $dayList = ['00' => __('label.SELECT_DAY_OPT')];

        $targetArr = [];
        if ($request->generate == 'true') {
            $month = $request->month;
            if (!empty($month) && $month != '00') {
                $startDate = date("Y-" . $month . "-01");
                $endDate = date("Y-" . $month . "-" . date('t', strtotime($startDate)));

                $startDay = new DateTime($startDate);
                $endDay = new DateTime($endDate);
                $monthDayList = [];
                for ($j = $startDay; $j <= $endDay; $j->modify("+1 day")) {
                    $day = $j->format("d");
                    $dayList[$day] = $day;
                }

                //this month
                $dayFrom = $request->day_from;
                $dayTo = $request->day_to;

                $startDate = date("Y-" . $month . "-" . $dayFrom);
                $endDate = date("Y-" . $month . "-" . $dayTo);

                $startDay = new DateTime($startDate);
                $endDay = new DateTime($endDate);
                $monthDayList = [];
                for ($j = $startDay; $j <= $endDay; $j->modify("+1 day")) {
                    $monthDay = $j->format("d-m");
                    $monthDayList[$monthDay] = $monthDay;
                }

                //prev month
                $prevMonth = ($month - 1) != '-1' || ($month - 1) != '-01' ? $month - 1 : '12';
                $startPrevMonthDate = date("Y-" . $prevMonth . "-01");
                $endPrevMonthDate = date("Y-" . $prevMonth . "-" . date('t', strtotime($startPrevMonthDate)));

                $startPrevMonthDay = new DateTime($startPrevMonthDate);
                $endPrevMonthDay = new DateTime($endPrevMonthDate);
                $prevMonthDayList = [];
                for ($j = $startPrevMonthDay; $j <= $endPrevMonthDay; $j->modify("+1 day")) {
                    $monthDay = $j->format("d-m");
                    $prevMonthDayList[$monthDay] = $monthDay;
                }
                //comming month
                $comingMonth = ($month + 1) != '13' ? $month + 1 : '01';
                $startCommingMonthDate = date("Y-" . $comingMonth . "-01");
                $endCommingMonthDate = date("Y-" . $comingMonth . "-" . date('t', strtotime($startCommingMonthDate)));

                $startCommingMonthDay = new DateTime($startCommingMonthDate);
                $endCommingMonthDay = new DateTime($endCommingMonthDate);
                $commingMonthDayList = [];
                for ($j = $startCommingMonthDay; $j <= $endCommingMonthDay; $j->modify("+1 day")) {
                    $monthDay = $j->format("d-m");
                    $commingMonthDayList[$monthDay] = $monthDay;
                }
            }





            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_basic_profile.course_id', $courseList->id)
                    ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (',  cm_basic_profile.personal_no, ')') as cm_name")
                            , 'rank.code as rank', 'cm_basic_profile.full_name as full_name', 'cm_basic_profile.personal_no as personal_no'
                            , 'cm_basic_profile.photo', 'cm_basic_profile.date_of_birth', 'cm_basic_profile.spouse_dob', 'cm_basic_profile.id', 'cm_basic_profile.marital_status'
                            , 'cm_basic_profile.date_of_marriage')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            if (!$cmArr->isEmpty()) {
                foreach ($cmArr as $cm) {
                    $date = $request->cel_event == '1' ? $cm->date_of_birth : ($request->cel_event == '2' ? $cm->spouse_dob : ($request->cel_event == '3' ? $cm->date_of_marriage : ''));
                    $day = !empty($date) ? date('m-d', strtotime($date)) : '';
                    if (!empty($month) && $month != '00') {
                        if (in_array($day, $monthDayList)) {
                            $targetArr['this'][$day] = $cm->toArray();
                        }
                        if (in_array($day, $prevMonthDayList)) {
                            $targetArr['prev'][$day] = $cm->toArray();
                        }
                        if (in_array($day, $commingMonthDayList)) {
                            $targetArr['coming'][$day] = $cm->toArray();
                        }
                    } else {
                        $targetArr['all'][$day] = $cm->toArray();
                    }
                }
                if (!empty($targetArr['all'])) {
                    ksort($targetArr['all']);
                }
                if (!empty($targetArr['this'])) {
                    ksort($targetArr['this']);
                }
                if (!empty($targetArr['prev'])) {
                    ksort($targetArr['prev']);
                }
                if (!empty($targetArr['coming'])) {
                    ksort($targetArr['coming']);
                }
            }

//            ksort($dateArr);
//            echo '<pre>';
//            print_r($targetArr['all']);
//            exit;

            $fileName = $celEventList[$request->cel_event] . '_Wise_CM_Celebration_Report';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('cmAnalytics.celebration.print.index')->with(compact('activeTrainingYearList', 'courseList'
                                    , 'celEventList', 'monthList', 'dayList', 'targetArr', 'request'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('cmAnalytics.celebration.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'celEventList', 'monthList', 'dayList', 'targetArr', 'request'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('cmAnalytics.celebration.print.index', compact('activeTrainingYearList', 'courseList'
                                    , 'celEventList', 'monthList', 'dayList', 'targetArr', 'request'), 3), $fileName . '.xlsx');
        }

        return view('cmAnalytics.celebration.index', compact('activeTrainingYearList', 'courseList'
                        , 'celEventList', 'monthList', 'dayList', 'targetArr', 'request'));
    }

    public function filter(Request $request) {
        $rules = $dayList = $messages = [];
        $messages = [];
        $rules = [
            'cel_event' => 'required|not_in:0',
//            'month' => 'required|not_in:00',
        ];
        $messages = [
            'cel_event.not_in' => __('label.THE_CELEBRATION_EVENT_FIELD_IS_REQUIRED'),
            'month.not_in' => __('label.THE_MONTH_FIELD_IS_REQUIRED'),
        ];
        $month = $request->month;
        if (!empty($month) && $month != '00') {
            $startDate = date("Y-" . $month . "-01");
            $endDate = date("Y-" . $month . "-" . date('t', strtotime($startDate)));

            $startDay = new DateTime($startDate);
            $endDay = new DateTime($endDate);
            for ($j = $startDay; $j <= $endDay; $j->modify("+1 day")) {
                $day = $j->format("d");
                $dayList[$day] = $day;
            }
        }

        $request->day_from = !empty($request->day_from) && $request->day_from != '00' ? $request->day_from : (!empty($dayList) ? '01' : '');
        $request->day_to = !empty($request->day_to) && $request->day_to != '00' ? $request->day_to : (!empty($dayList) ? end($dayList) : '');

        $url = 'cel_event=' . $request->cel_event . '&month=' . $request->month
                . '&day_from=' . $request->day_from . '&day_to=' . $request->day_to;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('celebrationCmAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('celebrationCmAnalytics?generate=true&' . $url);
    }

    public function getmonthDayList(Request $request) {
        $month = $request->month;
        $startDate = date("Y-" . $month . "-01");
        $endDate = date("Y-" . $month . "-" . date('t', strtotime($startDate)));

        $startDay = new DateTime($startDate);
        $endDay = new DateTime($endDate);
        $dayList = ['00' => __('label.SELECT_DAY_OPT')];
        for ($j = $startDay; $j <= $endDay; $j->modify("+1 day")) {
            $day = $j->format("d");
            $dayList[$day] = $day;
        }

        $html = view('cmAnalytics.celebration.dayFromList', compact('dayList'))->render();
        $html2 = view('cmAnalytics.celebration.dayToList', compact('dayList'))->render();
        return response()->json(['html' => $html, 'html2' => $html2]);
    }

}
