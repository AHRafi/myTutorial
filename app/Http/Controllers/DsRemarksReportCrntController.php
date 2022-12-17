<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\SynToSubSyn;
use App\TermToCourse;
use App\CmBasicProfile;
use App\Event;
use App\DsRemarks;
use App\DsRemarksViewer;
use Response;
use PDF;
use DB;
use Common;
use Auth;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class DsRemarksReportCrntController extends Controller {

    private $controller = 'DsRemarksReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.ALL_TERMS')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();
        $eventList = Event::join('term_to_event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();
        $eventList = ['0' => __('label.ALL_EVENT')] + $eventList;

        $cmList = ['0' => __('label.ALL_CM')] + CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->where('cm_basic_profile.course_id', $courseList->id)
                        ->where('cm_basic_profile.status', '1')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();
        $dsRemarksArr = [];
        if ($request->generate == 'true') {
            $dsRemarksArr = DsRemarks::join('users', 'users.id', 'ds_remarks.remarked_by')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', 'ds_remarks.cm_id')
                    ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('term', 'term.id', 'ds_remarks.term_id')
                    ->leftJoin('event', 'event.id', 'ds_remarks.event_id')
                    ->select('ds_remarks.date', 'ds_remarks.remarks', 'users.official_name'
                            , DB::raw('CONCAT(rank.code, " ", cm_basic_profile.full_name) as cm')
                            , 'event.event_code as event', 'term.name as term')
                    ->where('ds_remarks.course_id', $request->course_id);

            if (!empty($request->term_id)) {
                $dsRemarksArr = $dsRemarksArr->where('ds_remarks.term_id', $request->term_id);
            }
            if (!empty($request->cm_id)) {
                $dsRemarksArr = $dsRemarksArr->where('ds_remarks.cm_id', $request->cm_id);
            }
            if (!empty($request->event_id)) {
                $dsRemarksArr = $dsRemarksArr->where('ds_remarks.event_id', $request->event_id);
            }
            $dsRemarksArr = $dsRemarksArr->orderBy('ds_remarks.date', 'desc')
                    ->get();

//            echo '<pre>';
//            print_r($dsRemarksArr->toArray());
//            exit;

            $dsRemarksViewer = DsRemarksViewer::where('ds_remarks_viewer.user_id', Auth::user()->id);
            if (!empty($request->cm_id)) {
                $dsRemarksViewer = $dsRemarksViewer->where('ds_remarks_viewer.cm_id', $request->cm_id);
            }
            if (!empty($request->event_id)) {
                $dsRemarksViewer = $dsRemarksViewer->where('ds_remarks_viewer.event_id', $request->event_id);
            }
            $dsRemarksViewer = $dsRemarksViewer->update(['ds_remarks_viewer.status' => '1']);


            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $cmName = $request->cm_id != '0' && !empty($cmList[$request->cm_id]) ? '_' . $cmList[$request->cm_id] : '';
            $fileName = 'Ds_Remarks_Report' . $tyName . $courseName . $termName . $eventName . $cmName;
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('reportCrnt.dsRemarks.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'cmList', 'dsRemarksArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.dsRemarks.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'cmList', 'dsRemarksArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        }

        return view('reportCrnt.dsRemarks.index', compact('activeTrainingYearList', 'courseList', 'eventList', 'termList'
                        , 'cmList', 'dsRemarksArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.dsRemarks.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.ALL_TERMS')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();

        $cmList = ['0' => __('label.ALL_CM')] + CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->where('cm_basic_profile.course_id', $request->course_id)
                        ->where('cm_basic_profile.status', '1')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();


        $html = view('reportCrnt.dsRemarks.getTerm', compact('termList'))->render();
        $html2 = view('reportCrnt.dsRemarks.getCm', compact('cmList'))->render();
        return Response::json(['html' => $html, 'html2' => $html2]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.ALL_EVENT')] + Event::join('term_to_event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();


        $html = view('reportCrnt.dsRemarks.getEvent', compact('eventList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
//            'event_id.not_in' => __('label.THE_EVENT_FIELD_IS_REQUIRED'),
//            'cm_id.not_in' => __('label.THE_CM_FIELD_IS_REQUIRED'),
        ];


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id
                . '&event_id=' . $request->event_id . '&cm_id=' . $request->cm_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('dsRemarksReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('dsRemarksReportCrnt?generate=true&' . $url);
    }

}
