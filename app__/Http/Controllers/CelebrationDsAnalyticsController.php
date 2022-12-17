<?php

namespace App\Http\Controllers;

use App\TrainingYear;
use App\Course;
use App\UserBasicProfile;
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

class CelebrationDsAnalyticsController extends Controller {

    public function index(Request $request) {

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


            $dsArr = UserBasicProfile::leftJoin('users', 'users.id', '=', 'user_basic_profile.user_id')
                    ->leftJoin('rank', 'rank.id', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', 'users.wing_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                    ->leftJoin('appointment', 'appointment.id', '=', 'users.appointment_id')
                    ->where('users.status', '1')
                    ->select(DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (',  users.personal_no, ')') as ds_name"), 'users.official_name'
                            , 'users.photo', 'users.personal_no as personal_no', 'rank.code as rank', 'users.full_name as full_name', 'user_basic_profile.date_of_birth', 'user_basic_profile.spouse_dob', 'users.id', 'user_basic_profile.marital_status'
                            , 'user_basic_profile.date_of_marriage', 'appointment.code as appointment_name')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc')
                    ->get();

            if (!$dsArr->isEmpty()) {
                foreach ($dsArr as $ds) {
                    $date = $request->cel_event == '1' ? $ds->date_of_birth : ($request->cel_event == '2' ? $ds->spouse_dob : ($request->cel_event == '3' ? $ds->date_of_marriage : ''));
                    $day = !empty($date) ? date('m-d', strtotime($date)) : '';
                    if (!empty($month) && $month != '00') {
                        if (in_array($day, $monthDayList)) {
                            $targetArr['this'][$day] = $ds->toArray();
                        }
                        if (in_array($day, $prevMonthDayList)) {
                            $targetArr['prev'][$day] = $ds->toArray();
                        }
                        if (in_array($day, $commingMonthDayList)) {
                            $targetArr['coming'][$day] = $ds->toArray();
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

            $fileName = $celEventList[$request->cel_event] . '_Wise_DS_Celebration_Report';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('dsAnalytics.celebration.print.index')->with(compact('celEventList', 'monthList', 'dayList', 'targetArr', 'request'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('dsAnalytics.celebration.print.index', compact('celEventList', 'monthList', 'dayList', 'targetArr', 'request'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('dsAnalytics.celebration.print.index', compact('celEventList', 'monthList', 'dayList', 'targetArr', 'request'), 3), $fileName . '.xlsx');
        }

        return view('dsAnalytics.celebration.index', compact('celEventList', 'monthList', 'dayList', 'targetArr', 'request'));
    }

    public function filter(Request $request) {
        $rules = $messages = [];
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
            return redirect('celebrationDsAnalytics?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('celebrationDsAnalytics?generate=true&' . $url);
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

        $html = view('dsAnalytics.celebration.dayFromList', compact('dayList'))->render();
        $html2 = view('dsAnalytics.celebration.dayToList', compact('dayList'))->render();
        return response()->json(['html' => $html, 'html2' => $html2]);
    }

}
