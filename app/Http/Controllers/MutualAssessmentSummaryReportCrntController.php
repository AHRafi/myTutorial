<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Term;
use App\MutualAssessmentEvent;
use App\Course;
use App\TermToCourse;
use App\TermToMAEvent;
use App\SynToCourse;
use App\Syndicate;
use App\SubSyndicate;
use App\SynToSubSyn;
use App\CmToSyn;
use App\CmToSubSyn;
use App\CmBasicProfile;
use App\MutualAssessmentMarking;
use App\MutualAssessmentMarkingLock;
use App\MaMksExport;
use App\EventGroup;
use App\MaGroup;
use App\CmMaGroup;
use App\MaProcess;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\CmGroupMemberTemplate;
use App\CmGroupToCourse;
use App\Event;
use App\SubEvent;
use App\SubSubEvent;
use App\SubSubSubEvent;
use App\EventToEventGroup;
use App\CmMarkingGroup;
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

class MutualAssessmentSummaryReportCrntController extends Controller {

    private $controller = 'MutualAssessmentSummaryReportCrnt';

    public function index(Request $request) {
        //get only active training year
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.MUTUAL_ASSESSMENT_SUMMARY_REPORT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.MUTUAL_ASSESSMENT_SUMMARY_REPORT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();
        $maProcessInfo = MaProcess::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                        ->select('process')->first();

        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.for_ma_grouping', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $synList = ['0' => __('label.OVERALL')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)->where('cm_group.type', 1)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $subSynList = ['0' => __('label.OVERALL')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)->where('cm_group.type', 2)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();


        $factorList = MutualAssessmentEvent::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('event_to_sub_event.has_ds_assesment', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('event_to_sub_sub_event.has_ds_assesment', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        $eventGroupList = ['0' => __('label.OVERALL')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                        ->where('event_to_event_group.course_id', $request->course_id)
                        ->where('event_to_event_group.event_id', $request->event_id)
                        ->where('event_group.status', '1')
                        ->orderBy('event_group.order', 'asc')
                        ->pluck('event_group.name', 'event_group.id')
                        ->toArray();


        $hasSubSyn = !empty($subSynList) ? 0 : 0;

        $sortByList = [];
        if (!empty($factorList)) {
            foreach ($factorList as $factorId => $factor) {
                $sortByList['position_' . $factorId] = __('label.POSITION') . ' (' . $factor . ')';
            }
        }

        $sortByList = $sortByList + [
            'svc' => __('label.WING'), 'alphabatically' => __('label.ALPHABATICALLY')
            , 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY')
            , 'personal_no' => __('label.PERSONAL_NO')];

        $cmArr = $markingCmArr = $markingPositionArr = $totalPositionArr = $totalMarkingCm = [];

        if ($request->generate == 'true') {
            if (in_array($maProcess, ['1', '2'])) {
                if ($maProcess == '1') {
                    $cmGroupId = $request->syn_id;
                } else if ($maProcess == '2') {
                    $cmGroupId = $request->sub_syn_id;
                }

                $cmDataArr = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                        ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->where('cm_group_member_template.course_id', $request->course_id)
                        ->where('cm_group_member_template.term_id', $request->term_id);
                if (!empty($cmGroupId)) {
                    $cmDataArr = $cmDataArr->where('cm_group_member_template.cm_group_id', $cmGroupId);
                }
            } elseif (in_array($maProcess, ['3'])) {
                $prevCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('marking_group.event_id', $request->event_id);
                if (!empty($request->sub_event_id)) {
                    $prevCmArr = $prevCmArr->where('marking_group.sub_event_id', $request->sub_event_id);
                }
                if (!empty($request->sub_sub_event_id)) {
                    $prevCmArr = $prevCmArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
                }
                if (!empty($request->sub_sub_sub_event_id)) {
                    $prevCmArr = $prevCmArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
                }
                if (!empty($request->event_group_id)) {
                    $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id);
                }
                $prevCmArr = $prevCmArr->pluck('cm_marking_group.cm_id')
                        ->toArray();


                $cmDataArr = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->whereIn('cm_basic_profile.id', $prevCmArr);
            }

            $cmDataArr = $cmDataArr->where('cm_basic_profile.status', '1')
                    ->select('cm_basic_profile.id', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                    , 'rank.code as rank_name', 'cm_basic_profile.personal_no', 'cm_basic_profile.photo');
            if (!empty($request->sort)) {
                if ($request->sort == 'alphabatically') {
                    $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
                } else {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                }
            } else {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }

            $cmDataArr = $cmDataArr->get();
            // echo '<pre>'; print_r($cmDataArr); exit;

            if (!$cmDataArr->isEmpty()) {
                foreach ($cmDataArr as $cmData) {
                    $cmArr[$cmData->id] = $cmData->toArray();
                    $markingCmArr[$cmData->id] = $cmData->toArray();
                }
            }

            $markingDataArr = MutualAssessmentMarking::join('cm_basic_profile', 'cm_basic_profile.id', 'mutual_assessment_marking.marking_cm_id')
                    ->join('course', 'course.id', 'cm_basic_profile.course_id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('course.training_year_id', $request->training_year_id)
                    ->where('mutual_assessment_marking.course_id', $request->course_id)
                    ->where('mutual_assessment_marking.term_id', $request->term_id)
                    ->where('mutual_assessment_marking.event_id', $request->event_id)
                    ->where('mutual_assessment_marking.sub_event_id', $request->sub_event_id)
                    ->where('mutual_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id)
                    ->where('mutual_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            if (!empty($request->syn_id)) {
                $markingDataArr = $markingDataArr->where('mutual_assessment_marking.syndicate_id', $request->syn_id);
            }
            if (!empty($request->sub_syn_id)) {
                $markingDataArr = $markingDataArr->where('mutual_assessment_marking.sub_syndicate_id', $request->sub_syn_id);
            }
            if (!empty($request->event_group_id)) {
                $markingDataArr = $markingDataArr->where('mutual_assessment_marking.event_group_id', $request->event_group_id);
            }
            $markingDataArr = $markingDataArr->where('cm_basic_profile.status', '1')
                            ->select('mutual_assessment_marking.marking_cm_id', 'mutual_assessment_marking.cm_id'
                                    , 'mutual_assessment_marking.position', 'mutual_assessment_marking.factor_id')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')->get();

            if (!$markingDataArr->isEmpty()) {
                foreach ($markingDataArr as $markingData) {
                    $markingArr[$markingData->marking_cm_id][$markingData->cm_id][$markingData->factor_id] = $markingData->toArray();
                }
            }

            $cmMarkingArr = [];
            if (!empty($markingArr)) {
                foreach ($markingArr as $markingCmId => $marking) {
                    foreach ($marking as $cmId => $factor) {
                        foreach ($factor as $factorId => $info) {
                            if ($markingCmId != $cmArr[$cmId]) {
                                $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] = $info['position'];
                                $totalPositionArr[$cmId][$factorId]['total'] = !empty($totalPositionArr[$cmId][$factorId]['total']) ? $totalPositionArr[$cmId][$factorId]['total'] : 0;
                                $totalPositionArr[$cmId][$factorId]['total'] += !empty($markingPositionArr[$markingCmId][$cmId][$factorId]['pos']) ? $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] : 0;

                                $totalMarkingCm[$cmId][$factorId] = !empty($totalMarkingCm[$cmId][$factorId]) ? $totalMarkingCm[$cmId][$factorId] : 0;
                                $totalMarkingCm[$cmId][$factorId] += 1;
                                $cmMarkingArr[$factorId][$cmId]['avg'] = (!empty($totalPositionArr[$cmId][$factorId]['total']) ? $totalPositionArr[$cmId][$factorId]['total'] : 0) / (!empty($totalMarkingCm[$cmId][$factorId]) ? $totalMarkingCm[$cmId][$factorId] : 1);
                                $cmArr[$cmId]['avg_' . $factorId] = $cmMarkingArr[$factorId][$cmId]['avg'];
                            }
                        }
                    }
                }
            }
            if (!empty($cmMarkingArr)) {
                foreach ($cmMarkingArr as $factorId => $marking) {
                    $cmArr = Common::getPosition($cmArr, 'avg_' . $factorId, 'position_' . $factorId, 1);
                }
            }


            if (!empty($cmMarkingArr)) {
                foreach ($cmMarkingArr as $factorId => $marking) {
                    if (!empty($request->sort) && $request->sort == 'position_' . $factorId) {
                        if (!empty($cmArr)) {
                            usort($cmArr, function ($item1, $item2) use ($factorId) {
                                if (!isset($item1['avg_' . $factorId])) {
                                    $item1['avg_' . $factorId] = '';
                                }

                                if (!isset($item2['avg_' . $factorId])) {
                                    $item2['avg_' . $factorId] = '';
                                }
                                return $item1['avg_' . $factorId] <=> $item2['avg_' . $factorId];
                            });
                        }
                    }
                }
            }

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $synName = $request->syn_id != '0' && !empty($synList[$request->syn_id]) ? '_' . $synList[$request->syn_id] : '';
            $subSynName = $request->sub_syn_id != '0' && !empty($subSynList[$request->sub_syn_id]) ? '_' . $subSynList[$request->sub_syn_id] : '';
            $eventName = $request->event_id != '0' && !empty($eventList[$request->event_id]) ? '_' . $eventList[$request->event_id] : '';
            $subEventName = $request->sub_event_id != '0' && !empty($subEventList[$request->sub_event_id]) ? '_' . $subEventList[$request->sub_event_id] : '';
            $subSubEventName = $request->sub_sub_event_id != '0' && !empty($subSubEventList[$request->sub_sub_event_id]) ? '_' . $subSubEventList[$request->sub_sub_event_id] : '';
            $subSubSubEventName = $request->sub_sub_sub_event_id != '0' && !empty($subSubSubEventList[$request->sub_sub_sub_event_id]) ? '_' . $subSubSubEventList[$request->sub_sub_sub_event_id] : '';
            $eventGpName = $request->event_group_id != '0' && !empty($eventGroupList[$request->event_group_id]) ? '_' . $eventGroupList[$request->event_group_id] : '';
            $fileName = 'Mutual_Assessment_Summary_Report' . $tyName . $courseName . $termName . $synName . $subSynName . $eventName . $subEventName . $subSubEventName . $subSubSubEventName . $eventGpName;
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('reportCrnt.mutualAssessmentSummary.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'factorList', 'synList', 'subSynList', 'hasSubSyn', 'cmArr', 'markingPositionArr'
                                    , 'totalPositionArr', 'markingCmArr', 'sortByList', 'eventGroupList'
                                    , 'maProcess', 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.mutualAssessmentSummary.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'factorList', 'synList', 'subSynList', 'hasSubSyn', 'cmArr', 'markingPositionArr'
                                    , 'totalPositionArr', 'markingCmArr', 'sortByList', 'eventGroupList'
                                    , 'maProcess', 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.mutualAssessmentSummary.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'factorList', 'synList', 'subSynList', 'hasSubSyn', 'cmArr', 'markingPositionArr'
                                    , 'totalPositionArr', 'markingCmArr', 'sortByList', 'eventGroupList'
                                    , 'maProcess', 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList')), $fileName . '.xlsx');
        }

        return view('reportCrnt.mutualAssessmentSummary.index', compact('activeTrainingYearList', 'courseList', 'termList'
                        , 'factorList', 'synList', 'subSynList', 'hasSubSyn', 'cmArr', 'markingPositionArr'
                        , 'totalPositionArr', 'markingCmArr', 'sortByList', 'eventGroupList'
                        , 'maProcess', 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.mutualAssessmentSummary.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();


        $html = view('reportCrnt.mutualAssessmentSummary.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getSubEvent(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_event.event_id', $request->event_id)
                        ->where('event_to_sub_event.has_ds_assesment', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')];
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')];
        $html2 = view('reportCrnt.mutualAssessmentSummary.showSubSubEvent', compact('subSubEventList'))->render();
        $html3 = view('reportCrnt.mutualAssessmentSummary.showSubSubSubEvent', compact('subSubSubEventList'))->render();

        $html1 = Self::getEventGroups($request);
        $html = view('reportCrnt.mutualAssessmentSummary.showSubEvent', compact('subEventList'))->render();

        return response()->json(['html' => $html, 'html1' => $html1, 'html2' => $html2, 'html3' => $html3]);
    }

    public function getSubSubEvent(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('event_to_sub_sub_event.has_ds_assesment', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')];
        $html2 = view('reportCrnt.mutualAssessmentSummary.showSubSubSubEvent', compact('subSubSubEventList'))->render();


        $html1 = Self::getEventGroups($request);
        $html = view('reportCrnt.mutualAssessmentSummary.showSubSubEvent', compact('subSubEventList'))->render();

        return response()->json(['html' => $html, 'html1' => $html1, 'html2' => $html2]);
    }

    public function getSubSubSubEvent(Request $request) {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->where('term_to_sub_sub_sub_event.term_id', $request->term_id)
                        ->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        $html1 = Self::getEventGroups($request);
        $html = view('reportCrnt.mutualAssessmentSummary.showSubSubSubEvent', compact('subSubSubEventList'))->render();

        return response()->json(['html' => $html, 'html1' => $html1]);
    }

    public function getEventGroups(Request $request) {

        $eventGroupList = ['0' => __('label.OVERALL')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                        ->where('event_to_event_group.course_id', $request->course_id)
                        ->where('event_to_event_group.event_id', $request->event_id)
                        ->where('event_group.status', '1')
                        ->orderBy('event_group.order', 'asc')
                        ->pluck('event_group.name', 'event_group.id')
                        ->toArray();

        $html = view('reportCrnt.mutualAssessmentSummary.showEventGroup', compact('eventGroupList'))->render();

        return $html;
    }

    public function getEventGroup(Request $request) {

        $html = Self::getEventGroups($request);
        return response()->json(['html' => $html]);
    }

    public function getSynOrGp(Request $request) {

        $maProcessInfo = MaProcess::where('course_id', $request->course_id)->where('term_id', $request->term_id)
                        ->select('process')->first();

        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.for_ma_grouping', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $synList = ['0' => __('label.OVERALL')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)->where('cm_group.type', 1)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $subSynList = ['0' => __('label.OVERALL')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $request->course_id)->where('cm_group.type', 2)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')];
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')];
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')];
        $eventGroupList = ['0' => __('label.OVERALL')];

        $html1 = view('reportCrnt.mutualAssessmentSummary.showSubEvent', compact('subEventList'))->render();
        $html2 = view('reportCrnt.mutualAssessmentSummary.showSubSubEvent', compact('subSubEventList'))->render();
        $html3 = view('reportCrnt.mutualAssessmentSummary.showSubSubSubEvent', compact('subSubSubEventList'))->render();
        $html4 = view('reportCrnt.mutualAssessmentSummary.showEventGroup', compact('eventGroupList'))->render();

        $html = view('reportCrnt.mutualAssessmentSummary.getSyn', compact('synList', 'subSynList', 'eventList', 'maProcess'))->render();
        return response()->json(['maProcess' => $maProcess, 'html' => $html, 'html1' => $html1, 'html2' => $html2, 'html3' => $html3, 'html4' => $html4]);
    }

    public function getsubSyn(Request $request) {

        $html = '';
        $subSynList = ['0' => __('label.OVERALL')] + SynToSubSyn::join('sub_syndicate', 'sub_syndicate.id', 'syn_to_sub_syn.sub_syn_id')
                        ->where('syn_to_sub_syn.course_id', $request->course_id)
                        ->where('syn_to_sub_syn.syn_id', $request->syn_id)
                        ->where('sub_syndicate.status', '1')
                        ->orderBy('sub_syndicate.order', 'asc')
                        ->pluck('sub_syndicate.name', 'sub_syndicate.id')
                        ->toArray();
        if (sizeof($subSynList) > 1) {
            $html = view('reportCrnt.mutualAssessmentSummary.getSubSyn', compact('subSynList'))->render();
        }
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {
        $request->syn_id = !empty($request->syn_id) ? $request->syn_id : '0';
        $request->sub_syn_id = !empty($request->sub_syn_id) ? $request->sub_syn_id : '0';
        $request->event_group_id = !empty($request->event_group_id) ? $request->event_group_id : '0';
        $request->event_id = !empty($request->event_id) ? $request->event_id : '0';
        $request->sub_event_id = !empty($request->sub_event_id) ? $request->sub_event_id : '0';
        $request->sub_sub_event_id = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : '0';
        $request->sub_sub_sub_event_id = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : '0';

        $messages = [];
        $rules = array(
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
        );
        $messages = array(
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.COURSE_MUST_BE_SELECTED'),
            'term_id.not_in' => __('label.TERM_MUST_BE_SELECTED'),
        );

        $maProcess = $request->ma_process;

        if ($maProcess == '1') {
//            $rules['syn_id'] = 'required|not_in:0';
//            $messages['syn_id.not_in'] = __('label.SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '2') {
//            $rules['sub_syn_id'] = 'required|not_in:0';
//            $messages['sub_syn_id.not_in'] = __('label.SUB_SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '3') {
            $rules['event_id'] = 'required|not_in:0';
            $messages['event_id.not_in'] = __('label.EVENT_MUST_BE_SELECTED');
            if (!empty($request->has_sub_event)) {
                $rules['sub_event_id'] = 'required|not_in:0';
                $messages['sub_event_id.not_in'] = __('label.SUB_EVENT_MUST_BE_SELECTED');
            }
            if (!empty($request->has_sub_sub_event)) {
                $rules['sub_sub_event_id'] = 'required|not_in:0';
                $messages['sub_sub_event_id.not_in'] = __('label.SUB_SUB_EVENT_MUST_BE_SELECTED');
            }
            if (!empty($request->has_sub_sub_sub_event)) {
                $rules['sub_sub_sub_event_id'] = 'required|not_in:0';
                $messages['sub_sub_sub_event_id.not_in'] = __('label.SUB_SUB_SUB_EVENT_MUST_BE_SELECTED');
            }
//            $rules['event_group_id'] = 'required|not_in:0';
//            $messages['event_group_id.not_in'] = __('label.EVENT_GROUP_MUST_BE_SELECTED');
        }

        $factor1st = MutualAssessmentEvent::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('name', 'id')->first();
        $request->sort = !empty($request->sort) ? $request->sort : (!empty($factor1st->id) ? 'position_' . $factor1st->id : '');

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id
                . '&factor_id=' . $request->factor_id . '&syn_id=' . $request->syn_id . '&sub_syn_id=' . $request->sub_syn_id
                . '&event_id=' . $request->event_id . '&sub_event_id=' . $request->sub_event_id
                . '&sub_sub_event_id=' . $request->sub_sub_event_id . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id
                . '&event_group_id=' . $request->event_group_id . '&ma_process=' . $request->ma_process
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('mutualAssessmentSummaryReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('mutualAssessmentSummaryReportCrnt?generate=true&' . $url);
    }

}
