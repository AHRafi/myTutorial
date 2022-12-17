<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\Term;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\EventAssessmentMarking;
use App\GradingSystem;
use App\CmToSyn;
use App\CriteriaWiseWt;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\CiObsnMarkingLock;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
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

class CommissioningCourseWisePerformanceTrendReportCrntController extends Controller {

    private $controller = 'CommissioningCourseWisePerformanceTrendReportCrnt';

    public function index(Request $request) {

        //Get only Active Training Year List
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.COMMISSIONING_COURSE_WISE_PERFORMANCE_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.COMMISSIONING_COURSE_WISE_PERFORMANCE_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $commissioningCourseList = CmBasicProfile::join('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                ->where('cm_basic_profile.course_id', $courseList->id)
                ->where('commissioning_course.status', '1')
                ->orderBy('commissioning_course.commissioning_date', 'asc');

        $cmArr = $commissioningCourseList->pluck('cm_basic_profile.commissioning_course_id', 'cm_basic_profile.id')->toArray();
        $commissioningCourseList = $commissioningCourseList->pluck('commissioning_course.short_info', 'commissioning_course.id')->toArray();

        $assignedObsnInfo = $gradeInfo = $comdtObsnLockInfo = $ciObsnLockInfo = $maxCm = 0;
        $eventMksWtArr = $eventWiseMksArr = $commissioningCourseWiseMksArr = $gradeList = $commissioningCourseIds = $selectedCommissioningCourses = [];

        if ($request->generate == 'true') {
            $commissioningCourseIds = !empty($request->commissioning_course_id) ? explode(",", $request->commissioning_course_id) : [];

            $selectedCommissioningCourses = CmBasicProfile::join('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->whereIn('cm_basic_profile.commissioning_course_id', $commissioningCourseIds)
                    ->where('commissioning_course.status', '1')
                    ->orderBy('commissioning_course.commissioning_date', 'asc')
                    ->pluck('commissioning_course.short_info', 'commissioning_course.id')
                    ->toArray();


            // Get Assigned CI obsn wt
            $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')
                            ->where('course_id', $request->course_id)->first();
            $assignedDsObsnInfo = DsObsnMarkingLimit::select('term_id', 'mks_limit', 'obsn')
                            ->where('course_id', $request->course_id)->get();

            $assignedDsObsnArr = [];
            if (!$assignedDsObsnInfo->isEmpty()) {
                foreach ($assignedDsObsnInfo as $dsObsn) {
                    $assignedDsObsnArr[$dsObsn->term_id]['mks_limit'] = $dsObsn->mks_limit;
                    $assignedDsObsnArr[$dsObsn->term_id]['obsn'] = $dsObsn->obsn;
                }
            }

            // Get Grade System
            $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();
            $gradeArr = $gradeList = [];
            if (!$gradeInfo->isEmpty()) {
                foreach ($gradeInfo as $grade) {
                    $gradeList[$grade->id] = $grade->grade_name;
                    $gradeArr[$grade->grade_name]['id'] = $grade->id;
                    $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                    $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
                }
            }


            //START:: Event Information
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id)
                    ->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id'
                            , 'event_mks_wt.wt', 'event.has_sub_event')
                    ->get();


            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    if (empty($ev->has_sub_event)) {
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
                    }
                }
            }
            //END:: Event Information
            //START:: Sub Event information
            $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->join('sub_event_mks_wt', function($join) {
                        $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->where('term_to_sub_event.course_id', $request->course_id)
                    ->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.wt'
                            , 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->get();

            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                    if ($subEv->has_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                    } else {
                        if ($subEv->avg_marking == '1') {
                            $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                        }
                    }
                }
            }
            //END:: Sub Event information
            //START:: Sub Sub Event Information
            $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_event.course_id', $request->course_id)
                    ->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.wt'
                            , 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    if ($subSubEv->has_sub_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                    }

                    if ($subSubEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                    }
                }
            }
            //END:: Sub Sub Event Information
            //START:: Sub Sub Sub Event Information
            $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                    ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                    ->where('sub_sub_sub_event.status', '1')
                    ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit'
                            , 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                            , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                            , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                            , 'event.event_code'
                    )
                    ->get();


            if (!$subSubSubEventInfo->isEmpty()) {
                foreach ($subSubSubEventInfo as $subSubSubEv) {
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                }
            }
            //END:: Sub Sub Sub Event Information
            //START:: Event Wise Mks
            $eventWiseMksInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                        $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                        $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                        $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                        $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
						$join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                    })
                    ->where('event_assessment_marking.course_id', $request->course_id)
                    ->whereNotNull('event_assessment_marking.mks')
                    ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.wt) as avg_wt")
                            , DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
                    ->groupBy('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id')
                    ->get();
            $cmEventCountArr = [];
            if (!$eventWiseMksInfo->isEmpty()) {
                foreach ($eventWiseMksInfo as $eventMksInfo) {
                    if (!empty($eventMksInfo->avg_mks)) {
                        $cmEventCountArr[$eventMksInfo->cm_id][$eventMksInfo->event_id][$eventMksInfo->sub_event_id] = !empty($cmEventCountArr[$eventMksInfo->cm_id][$eventMksInfo->event_id][$eventMksInfo->sub_event_id]) ? $cmEventCountArr[$eventMksInfo->cm_id][$eventMksInfo->event_id][$eventMksInfo->sub_event_id] : 0;
                        $cmEventCountArr[$eventMksInfo->cm_id][$eventMksInfo->event_id][$eventMksInfo->sub_event_id] += 1;
                    }
                    $eventWiseMksArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id][$eventMksInfo->sub_sub_event_id][$eventMksInfo->sub_sub_sub_event_id][$eventMksInfo->cm_id]['avg_wt'] = $eventMksInfo->avg_wt;
                }
            }
            //END:: Event Wise Mks
            //START:: CI Moderation Wise Mks 
            $ciModWiseMksInfo = CiModerationMarking::join('ci_moderation_marking_lock', function($join) {
                        $join->on('ci_moderation_marking_lock.course_id', 'ci_moderation_marking.course_id');
                        $join->on('ci_moderation_marking_lock.term_id', 'ci_moderation_marking.term_id');
                        $join->on('ci_moderation_marking_lock.event_id', 'ci_moderation_marking.event_id');
                        $join->on('ci_moderation_marking_lock.sub_event_id', 'ci_moderation_marking.sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_event_id', 'ci_moderation_marking.sub_sub_event_id');
                        $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id');
						$join->on('ci_moderation_marking_lock.locked_by', 'ci_moderation_marking.updated_by');
                    })
                    ->where('ci_moderation_marking.course_id', $request->course_id)
                    ->select('ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                            , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.wt')
                    ->get();

            if (!$ciModWiseMksInfo->isEmpty()) {
                foreach ($ciModWiseMksInfo as $ciMksInfo) {
                    $eventWiseMksArr[$ciMksInfo->event_id][$ciMksInfo->sub_event_id][$ciMksInfo->sub_sub_event_id][$ciMksInfo->sub_sub_sub_event_id][$ciMksInfo->cm_id]['ci_wt'] = $ciMksInfo->wt;
                }
            }
            //END:: CI Moderation Wise Mks
            //START:: COMDT Moderation Wise Mks 
            $comdtModWiseMksInfo = ComdtModerationMarking::join('comdt_moderation_marking_lock', function($join) {
                        $join->on('comdt_moderation_marking_lock.course_id', 'comdt_moderation_marking.course_id');
                        $join->on('comdt_moderation_marking_lock.term_id', 'comdt_moderation_marking.term_id');
                        $join->on('comdt_moderation_marking_lock.event_id', 'comdt_moderation_marking.event_id');
                        $join->on('comdt_moderation_marking_lock.sub_event_id', 'comdt_moderation_marking.sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_event_id');
                        $join->on('comdt_moderation_marking_lock.sub_sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id');
						$join->on('comdt_moderation_marking_lock.locked_by', 'comdt_moderation_marking.updated_by');
                    })
                    ->where('comdt_moderation_marking.course_id', $request->course_id)
                    ->select('comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                            , 'comdt_moderation_marking.cm_id', 'comdt_moderation_marking.wt')
                    ->get();
            if (!$comdtModWiseMksInfo->isEmpty()) {
                foreach ($comdtModWiseMksInfo as $comdtMksInfo) {
                    $eventWiseMksArr[$comdtMksInfo->event_id][$comdtMksInfo->sub_event_id][$comdtMksInfo->sub_sub_event_id][$comdtMksInfo->sub_sub_sub_event_id][$comdtMksInfo->cm_id]['comdt_wt'] = $comdtMksInfo->wt;
                }
            }
            //END:: COMDT Moderation Wise Mks
            //ds obsn marking info
            $dsObsnMksWtInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                        $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                        $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                        $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                    })
                    ->where('ds_obsn_marking.course_id', $request->course_id)
                    ->select('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id'
                            , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt')
                            , DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks'))
                    ->groupBy('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id')
                    ->get();
            $dsObsnMksWtArr = [];
            if (!$dsObsnMksWtInfo->isEmpty()) {
                foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                    $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['wt'] = $dsObsnInfo->obsn_wt;
                    $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['mks'] = $dsObsnInfo->obsn_mks;
                }
            }

            $cmWiseMksArr = [];
            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $commissioningCourseId) {
                    if (!empty($eventMksWtArr['mks_wt'])) {
                        foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $comdtWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt'] : 0;
                                        $ciWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt'] : 0;
                                        $eventAvgWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;

                                        $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                                        $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;

                                        //count average where avg marking is enabled
                                        $totalCount = 0;
                                        if (!empty($cmEventCountArr[$cmId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($cmId, $cmEventCountArr)) {
                                                    $totalCount = $cmEventCountArr[$cmId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;
                                                $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;
                                                $subSubEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit'] : 0;
                                                $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

                                                $mksLimit = !empty($subSubSubEventId) ? $subSubEventMksLimit : $subEventMksLimit;
                                                $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                                $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;
                                                $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                                if ($totalCount != 0) {
                                                    $assignedWt = $subEventWtLimit / $totalCount;
                                                    $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                                }
                                            }
                                        }


                                        //cm wise total assigned wt in events
                                        $eventMksWtArr['total_wt'][$cmId] = !empty($eventMksWtArr['total_wt'][$cmId]) ? $eventMksWtArr['total_wt'][$cmId] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $eventMksWtArr['total_wt'][$cmId] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }
                                        //cm wise total achieved wt in events
                                        $cmWiseMksArr[$cmId] = !empty($cmWiseMksArr[$cmId]) ? $cmWiseMksArr[$cmId] : 0;
                                        $cmWiseMksArr[$cmId] += $TotalTermWt;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //ds obsn marking count
            if (!empty($dsObsnMksWtArr)) {
                foreach ($dsObsnMksWtArr as $termId => $termInfo) {
                    foreach ($termInfo as $cmId => $info) {

                        //cm wise total assigned wt in events
                        $eventMksWtArr['total_wt'][$cmId] = !empty($eventMksWtArr['total_wt'][$cmId]) ? $eventMksWtArr['total_wt'][$cmId] : 0;
                        $TotalTermWt = $eventMksWtArr['total_wt'][$cmId];
                        if (!empty($TotalTermWt)) {
                            $eventMksWtArr['total_wt'][$cmId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                        }
                        $dsObsnWt = 0;
                        if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                            $dsObsnWt = (($info['mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                        }

                        //cm wise total achieved wt in events
                        $cmWiseMksArr[$cmId] = !empty($cmWiseMksArr[$cmId]) ? $cmWiseMksArr[$cmId] : 0;
                        $cmWiseMksArr[$cmId] += $dsObsnWt ?? 0;
                    }
                }
            }

            //START:: CI Obsn Wise Mks 
            $ciObsnWiseMksInfo = CiObsnMarking::join('ci_obsn_marking_lock', 'ci_obsn_marking_lock.course_id', 'ci_obsn_marking.course_id')
                    ->where('ci_obsn_marking.course_id', $request->course_id)
                    ->select('ci_obsn_marking.cm_id', 'ci_obsn_marking.ci_obsn')
                    ->get();

            if (!$ciObsnWiseMksInfo->isEmpty()) {
                foreach ($ciObsnWiseMksInfo as $ciObsInfo) {
                    $cmId = $ciObsInfo->cm_id;
                    $TotalTermWt = $ciObsInfo->ci_obsn;
                    //cm wise total assigned wt in events
                    $eventMksWtArr['total_wt'][$cmId] = !empty($eventMksWtArr['total_wt'][$cmId]) ? $eventMksWtArr['total_wt'][$cmId] : 0;
                    if (!empty($TotalTermWt)) {
                        $eventMksWtArr['total_wt'][$cmId] += (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);
                    }
                    //cm wise total achieved wt in events
                    $cmWiseMksArr[$cmId] = !empty($cmWiseMksArr[$cmId]) ? $cmWiseMksArr[$cmId] : 0;
                    $cmWiseMksArr[$cmId] += $TotalTermWt;
                }
            }
            //END:: CI Obsn Wise Mks
            //START:: COMDT Obsn Wise Mks 
            $comdtObsnWiseMksInfo = ComdtObsnMarking::join('comdt_obsn_marking_lock', 'comdt_obsn_marking_lock.course_id', 'comdt_obsn_marking.course_id')
                    ->where('comdt_obsn_marking.course_id', $request->course_id)
                    ->select('comdt_obsn_marking.cm_id', 'comdt_obsn_marking.comdt_obsn')
                    ->get();

            if (!$comdtObsnWiseMksInfo->isEmpty()) {
                foreach ($comdtObsnWiseMksInfo as $comdtObsnInfo) {
                    $cmId = $comdtObsnInfo->cm_id;
                    $TotalTermWt = $comdtObsnInfo->comdt_obsn;
                    //cm wise total assigned wt in events
                    $eventMksWtArr['total_wt'][$cmId] = !empty($eventMksWtArr['total_wt'][$cmId]) ? $eventMksWtArr['total_wt'][$cmId] : 0;
                    if (!empty($TotalTermWt)) {
                        $eventMksWtArr['total_wt'][$cmId] += (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    }
                    //cm wise total achieved wt in events
                    $cmWiseMksArr[$cmId] = !empty($cmWiseMksArr[$cmId]) ? $cmWiseMksArr[$cmId] : 0;
                    $cmWiseMksArr[$cmId] += $TotalTermWt;
                }
            }
            //END:: COMDT Obsn Wise Mks


            if (!empty($cmWiseMksArr)) {
                foreach ($cmWiseMksArr as $cmId => $wtInfo) {
                    $totalWt = !empty($wtInfo) ? $wtInfo : 0;
                    $totalWtLimit = !empty($eventMksWtArr['total_wt'][$cmId]) ? $eventMksWtArr['total_wt'][$cmId] : 0;
                    $totalPercentage = 0;
                    if (!empty($totalWtLimit)) {
                        $totalPercentage = Helper::numberFormatDigit2(($totalWt / $totalWtLimit) * 100);
                    }


                    //START:: Get Commissioning Course Wise Commissioning Member
                    if (!empty($gradeArr)) {
                        foreach ($gradeArr as $letter => $gradeRange) {
                            $commissioningCourseWiseMksArr[$gradeRange['id']][$cmArr[$cmId]] = !empty($commissioningCourseWiseMksArr[$gradeRange['id']][$cmArr[$cmId]]) ? $commissioningCourseWiseMksArr[$gradeRange['id']][$cmArr[$cmId]] : 0;
                            if ($totalPercentage == 100) {
                                $commissioningCourseWiseMksArr[$gradeRange['id']][$cmArr[$cmId]] += 1;
                            } elseif ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                $commissioningCourseWiseMksArr[$gradeRange['id']][$cmArr[$cmId]] += 1;
                            }
                        }
                    }
                    //END:: Get Commissioning Course Wise Commissioning Member
                }
            }

            $maxCmArr = [];
            if (!empty($commissioningCourseWiseMksArr)) {
                foreach ($commissioningCourseWiseMksArr as $gradeId => $gradeInfo) {
                    $maxCmArr[$gradeId] = max($gradeInfo);
                }
                $maxCm = max($maxCmArr);
            }
        }

        return view('reportCrnt.commissioningCourseWisePerformanceTrend.index', compact('activeTrainingYearList'
                        , 'courseList', 'selectedCommissioningCourses', 'cmArr'
                        , 'assignedObsnInfo', 'gradeInfo', 'request', 'maxCm'
                        , 'commissioningCourseList', 'commissioningCourseIds', 'gradeList'
                        , 'commissioningCourseWiseMksArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.commissioningCourseWisePerformanceTrend.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getCourseWiseCommissioningCourse(Request $request) {
        $commissioningCourseList = CmBasicProfile::join('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('commissioning_course.status', '1')
                ->orderBy('commissioning_course.commissioning_date', 'asc')
                ->pluck('commissioning_course.short_info', 'commissioning_course.id')
                ->toArray();

        $showCommissioningCourseView = view('reportCrnt.commissioningCourseWisePerformanceTrend.getCourseWiseCommissioningCourse', compact('commissioningCourseList'))->render();
        return Response::json(['showCommissioningCourseView' => $showCommissioningCourseView]);
    }

    public function filter(Request $request) {

        $commissioningCourseIds = !empty($request->commissioning_course_id) ? implode(",", $request->commissioning_course_id) : '';

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];
        if (empty($commissioningCourseIds)) {
            $rules['commissioning_course_id'] = 'required';
            $messages['commissioning_course_id.required'] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_COMMISSIONING_COURSE');
        }

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&commissioning_course_id=' . $commissioningCourseIds;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('commissioningCourseWisePerformanceTrendReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('commissioningCourseWisePerformanceTrendReportCrnt?generate=true&' . $url);
    }

}
