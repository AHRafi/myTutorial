<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\Term;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\EventAssessmentMarking;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\CmBasicProfile;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Illuminate\Http\Request;

class WingWiseEventTrendReportController extends Controller {

    private $controller = 'WingWiseEventTrendReport';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::where('status', '2')
                        ->orderBy('start_date', 'desc')
                        ->pluck('name', 'id')->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $termList = array('0' => __('label.SELECT_TERM_OPT'));
        $wingList = CmBasicProfile::join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('wing.status', '1')
                ->orderBy('wing.order', 'asc');

        $cmArr = $wingList->pluck('cm_basic_profile.wing_id', 'cm_basic_profile.id')->toArray();
        $wingList = $wingList->pluck('wing.code', 'wing.id')->toArray();


        $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();



        $eventMksWtArr = $eventWiseMksArr = $wingWiseMksArr = $cmMksMinMaxArr = [];
        $wingIds = $eventIds = $selectedEvents = $selectedWings = [];
        if ($request->generate == 'true') {
            $termList = array('0' => __('label.ALL_TERMS')) + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                            ->where('term_to_course.course_id', $request->course_id)
                            ->orderBy('term.order', 'asc')
                            ->pluck('term.name', 'term_id')->toArray();
            $wingList = CmBasicProfile::join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->where('wing.status', '1')
                    ->orderBy('wing.order', 'asc')
                    ->pluck('wing.code', 'wing.id')
                    ->toArray();

            $wingIds = !empty($request->wing_id) ? explode(",", $request->wing_id) : [];
            $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
            }
            $eventList = $eventList->where('event.status', '1')
                    ->orderBy('event.event_code', 'asc')
                    ->pluck('event.event_code', 'event.id')
                    ->toArray();

            $eventIds = !empty($request->event_id) ? explode(",", $request->event_id) : [];

            $selectedEvents = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $selectedEvents = $selectedEvents->where('term_to_event.term_id', $request->term_id);
            }
            $selectedEvents = $selectedEvents->whereIn('term_to_event.event_id', $eventIds)
                    ->where('event.status', '1')
                    ->orderBy('event.event_code', 'asc')
                    ->pluck('event.event_code', 'event.id')
                    ->toArray();

            $selectedWings = CmBasicProfile::join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                            ->where('cm_basic_profile.course_id', $request->course_id)
                            ->whereIn('cm_basic_profile.wing_id', $wingIds)
                            ->where('wing.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->pluck('wing.code', 'wing.id')->toArray();


            //START:: Event Information
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventInfo = $eventInfo->where('term_to_event.term_id', $request->term_id);
            }
            $eventInfo = $eventInfo->whereIn('term_to_event.event_id', $eventIds)
                    ->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event.has_sub_event')
                    ->get();


            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    if (empty($ev->has_sub_event)) {
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
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
                    ->where('term_to_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subEventInfo = $subEventInfo->where('term_to_sub_event.term_id', $request->term_id);
            }
            $subEventInfo = $subEventInfo->whereIn('term_to_sub_event.event_id', $eventIds)
                    ->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit'
                            , 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->get();

            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                    if ($subEv->has_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                    } else {
                        if ($subEv->avg_marking == '1') {
                            $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
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
                    ->where('term_to_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.term_id', $request->term_id);
            }
            $subSubEventInfo = $subSubEventInfo->whereIn('term_to_sub_sub_event.event_id', $eventIds)
                    ->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit'
                            , 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    if ($subSubEv->has_sub_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    }

                    if ($subSubEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
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
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.term_id', $request->term_id);
            }
            $subSubSubEventInfo = $subSubSubEventInfo->whereIn('term_to_sub_sub_sub_event.event_id', $eventIds)
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
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
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
                    ->where('event_assessment_marking.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventWiseMksInfo = $eventWiseMksInfo->where('event_assessment_marking.term_id', $request->term_id);
            }
            $eventWiseMksInfo = $eventWiseMksInfo->whereIn('event_assessment_marking.event_id', $eventIds)
				->whereNotNull('event_assessment_marking.mks')
                    ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
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
                    $eventWiseMksArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id][$eventMksInfo->sub_sub_event_id][$eventMksInfo->sub_sub_sub_event_id][$eventMksInfo->cm_id]['avg_mks'] = $eventMksInfo->avg_mks;
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
                    ->where('ci_moderation_marking.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $ciModWiseMksInfo = $ciModWiseMksInfo->where('ci_moderation_marking.term_id', $request->term_id);
            }
            $ciModWiseMksInfo = $ciModWiseMksInfo->whereIn('ci_moderation_marking.event_id', $eventIds)
                    ->select('ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                            , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.mks')
                    ->get();

            if (!$ciModWiseMksInfo->isEmpty()) {
                foreach ($ciModWiseMksInfo as $ciMksInfo) {
                    $eventWiseMksArr[$ciMksInfo->event_id][$ciMksInfo->sub_event_id][$ciMksInfo->sub_sub_event_id][$ciMksInfo->sub_sub_sub_event_id][$ciMksInfo->cm_id]['ci_mks'] = $ciMksInfo->mks;
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
                    ->where('comdt_moderation_marking.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $comdtModWiseMksInfo = $comdtModWiseMksInfo->where('comdt_moderation_marking.term_id', $request->term_id);
            }
            $comdtModWiseMksInfo = $comdtModWiseMksInfo->whereIn('comdt_moderation_marking.event_id', $eventIds)
                    ->select('comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                            , 'comdt_moderation_marking.cm_id', 'comdt_moderation_marking.mks')
                    ->get();
            if (!$comdtModWiseMksInfo->isEmpty()) {
                foreach ($comdtModWiseMksInfo as $comdtMksInfo) {
                    $eventWiseMksArr[$comdtMksInfo->event_id][$comdtMksInfo->sub_event_id][$comdtMksInfo->sub_sub_event_id][$comdtMksInfo->sub_sub_sub_event_id][$comdtMksInfo->cm_id]['comdt_mks'] = $comdtMksInfo->mks;
                }
            }
            //END:: COMDT Moderation Wise Mks

            $cmWiseMksArr = [];
            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $wingId) {
                    if (!empty($eventMksWtArr['mks_wt'])) {
                        foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $comdtMks = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks'] : 0;
                                        $ciMks = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks'] : 0;
                                        $eventAvgMks = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks'] : 0;

                                        $TotalTermMks = !empty($comdtMks) ? $comdtMks : (!empty($ciMks) ? $ciMks : $eventAvgMks);
                                        $assignedMks = !empty($info['mks_limit']) ? $info['mks_limit'] : 0;

                                        //count average where avg marking is enabled
                                        $totalCount = 0;
                                        if (!empty($cmEventCountArr[$cmId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($cmId, $cmEventCountArr)) {
                                                    $totalCount = $cmEventCountArr[$cmId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;

                                                $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;

                                                if ($totalCount != 0 && $unitMksLimit != 0) {
                                                    $assignedMks = $subEventMksLimit / $totalCount;
                                                    $TotalTermMks = ($TotalTermMks * $subEventMksLimit) / ($totalCount * $unitMksLimit);
                                                }
                                            }
                                        }

                                        //total assigned wt event wise
                                        $eventMksWtArr['total_mks_limit'][$eventId][$cmId] = !empty($eventMksWtArr['total_mks_limit'][$eventId][$cmId]) ? $eventMksWtArr['total_mks_limit'][$eventId][$cmId] : 0;
                                        $eventMksWtArr['total_mks_limit'][$eventId][$cmId] += (!empty($TotalTermMks) ? $assignedMks : 0);

                                        //total achieved mks cm wise
                                        $cmWiseMksArr[$cmId][$eventId] = !empty($cmWiseMksArr[$cmId][$eventId]) ? $cmWiseMksArr[$cmId][$eventId] : 0;
                                        $cmWiseMksArr[$cmId][$eventId] += $TotalTermMks;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $cmWiseWingArr = [];
            if (!empty($cmArr)) {
                foreach ($cmArr as $cmId => $wingId) {
                    $cmWiseWingArr[$wingId][$cmId] = $cmId;
                }
            }


            if (!empty($cmWiseMksArr)) {
                foreach ($cmWiseMksArr as $cmId => $eventInfo) {
                    foreach ($eventInfo as $eventId => $mksInfo) {
                        $eventMks = !empty($mksInfo) ? $mksInfo : 0;
                        $eventMksLimit = !empty($eventMksWtArr['total_mks_limit'][$eventId][$cmId]) ? $eventMksWtArr['total_mks_limit'][$eventId][$cmId] : 0;
                        $eventPercentage = 0;
                        if (!empty($eventMksLimit)) {
                            $eventPercentage = ($eventMks / $eventMksLimit) * 100;
                        }

                        //cm wise event mks %
                        $cmMksMinMaxArr[$eventId][$cmArr[$cmId]][$cmId] = $eventPercentage;
                        $cmMksMinMaxArr['grand_max_min'][] = $eventPercentage;

                        $wingWiseMksArr[$cmArr[$cmId]][$eventId]['total_mks_percent'] = !empty($wingWiseMksArr[$cmArr[$cmId]][$eventId]['total_mks_percent']) ? $wingWiseMksArr[$cmArr[$cmId]][$eventId]['total_mks_percent'] : 0;
                        $wingWiseMksArr[$cmArr[$cmId]][$eventId]['total_mks_percent'] += $eventPercentage;
                    }
                }
            }



            if (!empty($wingWiseMksArr)) {
                foreach ($wingWiseMksArr as $wingId => $eventInfo) {
                    foreach ($eventInfo as $eventId => $mksInfo) {
                        $eventMks = !empty($mksInfo['total_mks_percent']) ? $mksInfo['total_mks_percent'] : 0;
                        $wingWiseMksArr[$wingId][$eventId]['mks_percent'] = ($eventMks) / (!empty($cmWiseWingArr[$wingId]) ? sizeof($cmWiseWingArr[$wingId]) : 1);

                        //wing wise max/min 
                        $wingWiseMksArr[$wingId][$eventId]['max'] = !empty($cmMksMinMaxArr[$eventId][$wingId]) ? max($cmMksMinMaxArr[$eventId][$wingId]) : 0;
                        $wingWiseMksArr[$wingId][$eventId]['min'] = !empty($cmMksMinMaxArr[$eventId][$wingId]) ? min($cmMksMinMaxArr[$eventId][$wingId]) : 0;
                        $wingWiseMksArr['grand_max_min']['max'] = !empty($cmMksMinMaxArr['grand_max_min']) ? max($cmMksMinMaxArr['grand_max_min']) + (max($cmMksMinMaxArr['grand_max_min']) + 5 < 100 ? 5 : 0) : 0;
                        $wingWiseMksArr['grand_max_min']['min'] = !empty($cmMksMinMaxArr['grand_max_min']) ? min($cmMksMinMaxArr['grand_max_min']) - (min($cmMksMinMaxArr['grand_max_min']) - 5 > 0 ? 5 : 0) : 0;
                    }
                }
            }
        }

        return view('report.wingWiseEventTrend.index', compact('activeTrainingYearList', 'courseList'
                        , 'wingList', 'eventList', 'wingIds', 'eventIds', 'termList'
                        , 'wingWiseMksArr', 'selectedWings', 'selectedEvents'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $termList = array('0' => __('label.SELECT_TERM_OPT'));
        
        $wingList = [];

        $eventList = [];

        $html2 = view('report.wingWiseEventTrend.getCourseWiseWing', compact('wingList'))->render();
        $showEventView = view('report.wingWiseEventTrend.getCourseWiseEvent', compact('eventList'))->render();
        $html = view('report.wingWiseEventTrend.getCourse', compact('courseList'))->render();
        $html1 = view('report.wingWiseEventTrend.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html, 'html1' => $html1, 'html2' => $html2, 'showEventView' => $showEventView]);
    }
    
    public function getTerm(Request $request) {

        $termList = ['0' => __('label.ALL_TERMS')] + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();
        $wingList = [];
        $eventList = [];
        $html1 = view('report.wingWiseEventTrend.getTerm', compact('termList'))->render();
        $html = view('report.wingWiseEventTrend.getCourseWiseWing', compact('wingList'))->render();
        $showEventView = view('report.wingWiseEventTrend.getCourseWiseEvent', compact('eventList'))->render();

        return response()->json(['html' => $html, 'html1' => $html1, 'showEventView' => $showEventView]);
    }
    
    public function getCourseWiseWingEvent(Request $request) {

        $wingList = CmBasicProfile::join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.course_id', $request->course_id)
                ->where('wing.status', '1')
                ->orderBy('wing.order', 'asc')
                ->pluck('wing.code', 'wing.id')
                ->toArray();

        $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $html = view('report.wingWiseEventTrend.getCourseWiseWing', compact('wingList'))->render();
        $showEventView = view('report.wingWiseEventTrend.getCourseWiseEvent', compact('eventList'))->render();
        return Response::json(['html' => $html, 'showEventView' => $showEventView]);
    }

    public function filter(Request $request) {

        $wingIds = !empty($request->wing_id) ? implode(",", $request->wing_id) : '';
        $eventIds = !empty($request->event_id) ? implode(",", $request->event_id) : '';

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'range_start' => 'lt:range_end|gte:0',
            'range_end' => 'lte:100|gt:range_start',
        ];

        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];


        if (empty($wingIds)) {
            $rules['wing_id'] = 'required';
            $messages['wing_id.required'] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_WING');
        }

        if (empty($eventIds)) {
            $rules['event_id'] = 'required';
            $messages['event_id.required'] = __('label.PLEASE_CHOOSE_ATLEAST_ONE_EVENT');
        }

        $request->range_start = !empty($request->range_start) ? $request->range_start : 0;
        $request->range_end = !empty($request->range_end) ? $request->range_end : 100;
        

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&term_id=' . $request->term_id. '&wing_id=' . $wingIds
                . '&event_id=' . $eventIds
                . '&range_start=' . $request->range_start . '&range_end=' . $request->range_end;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('wingWiseEventTrendReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('wingWiseEventTrendReport?generate=true&' . $url);
    }

}
