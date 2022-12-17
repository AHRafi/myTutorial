<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\CiComdtModerationMarkingLimit;
use App\EventAssessmentMarking;
use App\GradingSystem;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsMarkingGroup;
use App\CmMarkingGroup;
use App\CmToSyn;
use App\User;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ApptToCmReportCrntController extends Controller {

    private $controller = 'ApptToCmReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.EVENT_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.EVENT_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();
        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $subEventList = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();
        $hasSubEvent = !empty($subEventList) ? 1 : 0;
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;

        $subSubEventList = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();
        $hasSubSubEvent = !empty($subSubEventList) ? 1 : 0;
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;

        $subSubSubEventList = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();
        $hasSubSubSubEvent = !empty($subSubSubEventList) ? 1 : 0;
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;

        $sortByList = ['personal_no' => __('label.PERSONAL_NO'), 'position' => __('label.POSITION')];


        $targetArr = [];
        if ($request->generate == 'true') {

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $subEventName = $request->sub_event_id != '0' && !empty($subEventList[$request->sub_event_id]) ? '_' . $subEventList[$request->sub_event_id] : '';
            $subSubEventName = $request->sub_sub_event_id != '0' && !empty($subSubEventList[$request->sub_sub_event_id]) ? '_' . $subSubEventList[$request->sub_sub_event_id] : '';
            $subSubSubEventName = $request->sub_sub_sub_event_id != '0' && !empty($subSubSubEventList[$request->sub_sub_sub_event_id]) ? '_' . $subSubSubEventList[$request->sub_sub_sub_event_id] : '';
            $fileName = 'Appt_To_CM' . $tyName . $courseName . $termName . $eventName . $subEventName . $subSubEventName . $subSubSubEventName;
            $fileName = Common::getFileFormatedName($fileName);

            $targetArr = CmBasicProfile::join('appt_to_cm', 'appt_to_cm.cm_id', '=', 'cm_basic_profile.id')
                    ->leftJoin('cm_appointment', 'cm_appointment.id', '=', 'appt_to_cm.appt_id')
                    ->leftJoin('cm_to_syn', 'cm_to_syn.cm_id', '=', 'appt_to_cm.cm_id')
                    ->leftJoin('syndicate', 'syndicate.id', '=', 'cm_to_syn.syn_id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->where('appt_to_cm.course_id', $request->course_id)
                    ->where('appt_to_cm.term_id', $request->term_id)
                    ->where('appt_to_cm.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $targetArr = $targetArr->where('appt_to_cm.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $targetArr = $targetArr->where('appt_to_cm.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $targetArr = $targetArr->where('appt_to_cm.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $targetArr = $targetArr->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'wing.code as wing_name', 'rank.code as rank_code', 'cm_basic_profile.full_name'
                            , 'cm_appointment.code as appt', 'syndicate.name as syn')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

//            echo '<pre>';
//            print_r($targetArr->toArray());
//            exit;
        }

        if ($request->view == 'print') {
            return view('reportCrnt.apptToCm.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'targetArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.apptToCm.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'targetArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.apptToCm.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'targetArr')), $fileName . '.xlsx');
        }

        return view('reportCrnt.apptToCm.index', compact('activeTrainingYearList', 'courseList', 'termList'
                        , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                        , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'targetArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.apptToCm.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();

        $html = view('reportCrnt.apptToCm.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $html = view('reportCrnt.apptToCm.getEvent', compact('eventList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getSubEventReportCrnt(Request $request) {
//        echo '<pre>';        print_r($request->all()); exit;
        $html = '';
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        if (sizeof($subEventList) > 1) {
            $html = view('reportCrnt.apptToCm.getSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubEventReportCrnt(Request $request) {
        $html = '';
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();

        if (sizeof($subSubEventList) > 1) {
            $html = view('reportCrnt.apptToCm.getSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEventReportCrnt(Request $request) {
        $html = '';
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();


        if (sizeof($subSubSubEventList) > 1) {
            $html = view('reportCrnt.apptToCm.getSubSubSubEvent', compact('subSubSubEventList'))->render();
        }
        return response()->json(['html' => $html]);
    }

    public function filter(Request $request) {

//        echo '<pre>';        print_r($request->all()); exit;

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'term_id.not_in' => __('label.THE_TERM_FIELD_IS_REQUIRED'),
            'event_id.not_in' => __('label.THE_EVENT_FIELD_IS_REQUIRED'),
        ];
        if (!empty($request->has_sub_event)) {
            $rules['sub_event_id'] = 'required|not_in:0';
            $messages['sub_event_id.not_in'] = __('label.THE_SUB_EVENT_FIELD_IS_REQUIRED');
        }
        if (!empty($request->has_sub_sub_event)) {
            $rules['sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_EVENT_FIELD_IS_REQUIRED');
        }
        if (!empty($request->has_sub_sub_sub_event)) {
            $rules['sub_sub_sub_event_id'] = 'required|not_in:0';
            $messages['sub_sub_sub_event_id.not_in'] = __('label.THE_SUB_SUB_SUB_EVENT_FIELD_IS_REQUIRED');
        }

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id
                . '&event_id=' . $request->event_id . '&sub_event_id=' . $request->sub_event_id . '&sub_sub_event_id=' . $request->sub_sub_event_id
                . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('apptToCmReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('apptToCmReportCrnt?generate=true&' . $url);
    }

}
