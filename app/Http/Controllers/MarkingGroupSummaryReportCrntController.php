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
use App\MarkingGroup;
use App\CmMarkingGroup;
use App\DsMarkingGroup;
use App\SynToSubSyn;
use App\CmToSyn;
use App\MaMksExport;
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

class MarkingGroupSummaryReportCrntController extends Controller {

    private $controller = 'MarkingGroupSummaryReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.MARKING_GROUP_SUMMARY');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.MARKING_GROUP_SUMMARY');
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

        $cmArr = $markingGroupArr = $dsArr = $rowspanArr = $cmDsCountArr = [];

        if ($request->generate == 'true') {
            $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                            ->where('term_to_course.course_id', $request->course_id)
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


            $markingGroupDataArr = MarkingGroup::join('event_group', 'event_group.id', 'marking_group.event_group_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $markingGroupDataArr = $markingGroupDataArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $markingGroupDataArr = $markingGroupDataArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $markingGroupDataArr = $markingGroupDataArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $markingGroupDataArr = $markingGroupDataArr->orderBy('event_group.name', 'asc')
                    ->select('marking_group.id', 'event_group.name')->get();

            if (!$markingGroupDataArr->isEmpty()) {
                foreach ($markingGroupDataArr as $markingGroupData) {
                    $markingGroupArr[$markingGroupData->id] = $markingGroupData->name;
                }
            }

            $cmDataArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $cmDataArr = $cmDataArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $cmDataArr = $cmDataArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $cmDataArr = $cmDataArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $cmDataArr = $cmDataArr->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                            , 'marking_group.id as marking_group_id', 'cm_basic_profile.id as cm_id', 'cm_basic_profile.photo', 'rank.code as rank_name')
					->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            if (!$cmDataArr->isEmpty()) {
                foreach ($cmDataArr as $cmData) {
                    $cmArr[$cmData->marking_group_id][$cmData->cm_id] = $cmData->toArray();
                    $cmDsCountArr['cm'] = !empty($cmDsCountArr['cm']) ? $cmDsCountArr['cm'] : 0;
                    $cmDsCountArr['cm'] += 1;
                }
            }
//            echo '<pre>';        print_r($cmArr); exit;

            $dsDataArr = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                    ->join('users', 'users.id', 'ds_marking_group.ds_id')
                    ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
					->leftJoin('rank', 'rank.id', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', 'users.wing_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $dsDataArr = $dsDataArr->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $dsDataArr = $dsDataArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $dsDataArr = $dsDataArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            $dsDataArr = $dsDataArr->select(DB::raw("CONCAT(users.official_name, ' (', users.personal_no, ')') as ds_name")
                            , 'marking_group.id as marking_group_id', 'users.id as user_id', 'users.photo', 'rank.code as rank_name')
					->orderBy('wing.order', 'asc')
					->orderBy('appointment.order', 'asc')
					->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc')
                    ->get();
            $cmDsArr = [];
            if (!$dsDataArr->isEmpty()) {
                foreach ($dsDataArr as $dsData) {
                    $dsArr[$dsData->marking_group_id][$dsData->user_id] = $dsData->toArray();
                    $cmDsCountArr['ds'] = !empty($cmDsCountArr['ds']) ? $cmDsCountArr['ds'] : 0;
                    $cmDsCountArr['ds'] += (empty($cmDsArr[$dsData->user_id]) ? 1 : 0);
                    $cmDsArr[$dsData->user_id] = $dsData->user_id;
                }
            }

            if (!empty($markingGroupArr)) {
                foreach ($markingGroupArr as $gpId => $gpInfo) {
                    if (!empty($cmArr[$gpId]) && !empty($dsArr[$gpId])) {
                        $rowspanArr[$gpId] = (sizeof($dsArr[$gpId]) > sizeof($cmArr[$gpId])) ? sizeof($dsArr[$gpId]) : sizeof($cmArr[$gpId]);
                    }
                }
            }

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $subEventName = $request->sub_event_id != '0' && !empty($subEventList[$request->sub_event_id]) ? '_' . $subEventList[$request->sub_event_id] : '';
            $subSubEventName = $request->sub_sub_event_id != '0' && !empty($subSubEventList[$request->sub_sub_event_id]) ? '_' . $subSubEventList[$request->sub_sub_event_id] : '';
            $subSubSubEventName = $request->sub_sub_sub_event_id != '0' && !empty($subSubSubEventList[$request->sub_sub_sub_event_id]) ? '_' . $subSubSubEventList[$request->sub_sub_sub_event_id] : '';
            $fileName = 'Marking_Group_Summary_Report' . $tyName . $courseName . $termName . $eventName . $subEventName . $subSubEventName . $subSubSubEventName;
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('reportCrnt.markingGroupSummary.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'markingGroupArr'
                                    , 'cmArr', 'dsArr', 'rowspanArr', 'cmDsCountArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.markingGroupSummary.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'markingGroupArr'
                                    , 'cmArr', 'dsArr', 'rowspanArr', 'cmDsCountArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.markingGroupSummary.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                                    , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'markingGroupArr'
                                    , 'cmArr', 'dsArr', 'rowspanArr', 'cmDsCountArr')), $fileName . '.xlsx');
        }

        return view('reportCrnt.markingGroupSummary.index', compact('activeTrainingYearList', 'courseList', 'termList'
                        , 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'
                        , 'hasSubEvent', 'hasSubSubEvent', 'hasSubSubSubEvent', 'markingGroupArr'
                        , 'cmArr', 'dsArr', 'rowspanArr', 'cmDsCountArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.markingGroupSummary.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();

        $html = view('reportCrnt.markingGroupSummary.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getEvent(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $html = view('reportCrnt.markingGroupSummary.getEvent', compact('eventList'))->render();
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
            $html = view('reportCrnt.markingGroupSummary.getSubEvent', compact('subEventList'))->render();
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
            $html = view('reportCrnt.markingGroupSummary.getSubSubEvent', compact('subSubEventList'))->render();
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
            $html = view('reportCrnt.markingGroupSummary.getSubSubSubEvent', compact('subSubSubEventList'))->render();
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
                . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('markingGroupSummaryReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('markingGroupSummaryReportCrnt?generate=true&' . $url);
    }

}
