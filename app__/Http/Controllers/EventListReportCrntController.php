<?php

namespace App\Http\Controllers;

use App\Course;
use App\SynToCourse;
use App\DsGroupMemberTemplate;
use App\TermToCourse;
use App\TrainingYear;
use App\User;
use App\Term;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CriteriaWiseWt;
use App\DsObsnMarkingLimit;
use App\Event;
use Auth;
use DB;
use Helper;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Response;
use PDF;
use Excel;
use Common;
use App\Exports\ExcelExport;

class EventListReportCrntController extends Controller {

    private $controller = 'EventListReportCrnt';

    public function __construct() {
        
    }

    public function index(Request $request) {
        $qpArr = $request->all();
        $targetArr = [];

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.EVENT_MKS_WT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.EVENT_MKS_WT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = array('0' => __('label.ALL_TERMS')) + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();

        $sortByList = [
            'event' => __('label.EVENT'),
            'wt' => __('label.WT')
        ];

        $eventMksWtArr = $rowSpanArr = [];
        $dsObsnMksWtInfo = $dsObsnMksWtArr = $courseWtInfo = [];

        if ($request->generate == 'true') {
            //event info
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventInfo = $eventInfo->where('term_to_event.term_id', $request->term_id);
            }
            $eventInfo = $eventInfo->where('event.status', '1')
                    ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                            , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event')
                    ->orderBy('event.event_code', 'asc')
                    ->get();
            if (!$eventInfo->isEmpty()) {
                foreach ($eventInfo as $ev) {
                    $eventMksWtArr['event'][$ev->event_id]['name'] = $ev->event_code ?? '';

                    if ($ev->has_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['highest_mks_limit'] = !empty($ev->highest_mks_limit) ? $ev->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['lowest_mks_limit'] = !empty($ev->lowest_mks_limit) ? $ev->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                        $eventMksWtArr['event_total_term_wt'][$ev->event_id] = !empty($eventMksWtArr['event_total_term_wt'][$ev->event_id]) ? $eventMksWtArr['event_total_term_wt'][$ev->event_id] : 0;
                        $eventMksWtArr['event_total_term_wt'][$ev->event_id] += !empty($ev->wt) ? $ev->wt : 0;
                    }
                    $eventMksWtArr['event_total_wt'][$ev->event_id] = !empty($ev->wt) ? $ev->wt : 0;
                    $eventMksWtArr['event_total_mks'][$ev->event_id] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                }
            }

            //sub event info
            $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_event_mks_wt', function($join) {
                        $join->on('sub_event_mks_wt.course_id', '=', 'term_to_sub_event.course_id');
                        $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                        $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                    })
                    ->where('term_to_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subEventInfo = $subEventInfo->where('term_to_sub_event.term_id', $request->term_id);
            }
            $subEventInfo = $subEventInfo->where('sub_event.status', '1')
                    ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                            , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                            , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->get();


            if (!$subEventInfo->isEmpty()) {
                foreach ($subEventInfo as $subEv) {
                    $eventMksWtArr['event'][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                    $eventMksWtArr['event'][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';
                    if ($subEv->has_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['highest_mks_limit'] = !empty($subEv->highest_mks_limit) ? $subEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['lowest_mks_limit'] = !empty($subEv->lowest_mks_limit) ? $subEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                    }

                    $eventMksWtArr['event_total_term_wt'][$subEv->event_id] = !empty($eventMksWtArr['event_total_term_wt'][$subEv->event_id]) ? $eventMksWtArr['event_total_term_wt'][$subEv->event_id] : 0;
                    $eventMksWtArr['event_total_term_wt'][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                }
            }

            //sub sub event info
            $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_event_mks_wt.course_id', '=', 'term_to_sub_sub_event.course_id');
                        $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                        $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.term_id', $request->term_id);
            }
            $subSubEventInfo = $subSubEventInfo->where('sub_sub_event.status', '1')
                    ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                            , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                            , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_event.event_code', 'asc')
                    ->get();


            if (!$subSubEventInfo->isEmpty()) {
                foreach ($subSubEventInfo as $subSubEv) {
                    $eventMksWtArr['event'][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';
                    if ($subSubEv->has_sub_sub_sub_event == '0') {
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['highest_mks_limit'] = !empty($subSubEv->highest_mks_limit) ? $subSubEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['lowest_mks_limit'] = !empty($subSubEv->lowest_mks_limit) ? $subSubEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                    }
                }
            }

            //sub sub sub event info
            $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                    ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                    ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                    ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                    ->join('event_to_sub_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->join('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    })
                    ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                        $join->on('sub_sub_sub_event_mks_wt.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                        $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                        $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                    })
                    ->where('term_to_sub_sub_sub_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.term_id', $request->term_id);
            }
            $subSubSubEventInfo = $subSubSubEventInfo->where('sub_sub_sub_event.status', '1')
                    ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                            , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                            , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                            , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                            , 'event.event_code', 'event_to_sub_event.avg_marking')
                    ->orderBy('event.event_code', 'asc')
                    ->orderBy('sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_event.event_code', 'asc')
                    ->orderBy('sub_sub_sub_event.event_code', 'asc')
                    ->get();


            if (!$subSubSubEventInfo->isEmpty()) {
                foreach ($subSubSubEventInfo as $subSubSubEv) {
                    $eventMksWtArr['event'][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                    $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['highest_mks_limit'] = !empty($subSubSubEv->highest_mks_limit) ? $subSubSubEv->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['lowest_mks_limit'] = !empty($subSubSubEv->lowest_mks_limit) ? $subSubSubEv->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    if ($subEv->avg_marking == '0') {
//                        $eventMksWtArr['event_total_term_wt'][$subSubSubEv->event_id] = !empty($eventMksWtArr['event_total_term_wt'][$subSubSubEv->event_id]) ? $eventMksWtArr['event_total_term_wt'][$subSubSubEv->event_id] : 0;
//                        $eventMksWtArr['event_total_term_wt'][$subSubSubEv->event_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    }
                }
            }


            $eventMksWtArr2 = [];
            if (!empty($eventMksWtArr['event'])) {
                foreach ($eventMksWtArr['event'] as $eventId => $evInfo) {
                    if (sizeof($evInfo) == 1) {
                        $subEventId = $subSubEventId = $subSubSubEventId = 0;
                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                    }

                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        if (is_int($subEventId)) {
                            if (sizeof($subEvInfo) == 1) {
                                $subSubEventId = $subSubSubEventId = 0;
                                $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                            }
                            foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                                if (is_int($subSubEventId)) {
                                    if (sizeof($subSubEvInfo) == 1) {
                                        $subSubSubEventId = 0;
                                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                    }
                                    foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                        if (is_int($subSubSubEventId)) {
                                            $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                            $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $eventMksWtArr['mks_wt'] = $eventMksWtArr2;


            $eventMksWtTotalArr = [];
            if (!empty($eventMksWtArr['mks_wt'])) {

                foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo) {
                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                $rowSpanArr['event'][$eventId] = !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 0;
                                $rowSpanArr['event'][$eventId] += 1;



                                $rowSpanArr['sub_event'][$eventId][$subEventId] = !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 0;
                                $rowSpanArr['sub_event'][$eventId][$subEventId] += 1;

                                $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] = !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 0;
                                $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] += 1;

                                $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = !empty($rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : 0;
                                $rowSpanArr['sub_sub_sub_event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] += 1;
                            }
                        }
                    }
                }
            }


            if (!empty($eventMksWtArr['event_total_wt'])) {
                foreach ($eventMksWtArr['event_total_wt'] as $eventId => $info) {
                    $eventMksWtArr['mks_wt'][$eventId]['event_total_wt'] = $eventMksWtArr['event_total_wt'][$eventId];
                    $eventMksWtArr['mks_wt'][$eventId]['event_id'] = $eventId;

                    $eventTotalWt = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? $eventMksWtArr['event_total_wt'][$eventId] : 0;
                    $eventTotalTermWt = !empty($eventMksWtArr['event_total_term_wt'][$eventId]) ? $eventMksWtArr['event_total_term_wt'][$eventId] : 0;

                    $eventWt = !empty($request->term_id) ? $eventTotalTermWt : $eventTotalWt;
                    $eventMksWtArr['total_wt'] = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
                    $eventMksWtArr['total_wt'] += (!empty($eventWt) ? $eventWt : 0);
                    $eventMksWtArr['total_mks_limit'] = !empty($eventMksWtArr['total_mks_limit']) ? $eventMksWtArr['total_mks_limit'] : 0;
                    $eventMksWtArr['total_mks_limit'] += !empty($eventMksWtArr['event_total_mks'][$eventId]) ? $eventMksWtArr['event_total_mks'][$eventId] : 0;
                }
            }
            
            if (!empty($request->sort) && $request->sort == 'wt') {
                if (!empty($eventMksWtArr['mks_wt'])) {
                    usort($eventMksWtArr['mks_wt'], function ($item1, $item2) {
                        $positioning = 'event_total_wt';

                        if (!isset($item1[$positioning])) {
                            $item1[$positioning] = '';
                        }

                        if (!isset($item2[$positioning])) {
                            $item2[$positioning] = '';
                        }
                        return $item2[$positioning] <=> $item1[$positioning];
                    });
                }
            }

//            echo '<pre>';
//            print_r($eventMksWtArr['mks_wt']);
//            exit;

            $dsObsnMksWtInfo = DsObsnMarkingLimit::join('term', 'term.id', 'ds_obsn_marking_limit.term_id')
                    ->where('ds_obsn_marking_limit.course_id', $request->course_id)
                    ->select('ds_obsn_marking_limit.term_id', 'term.name as term', 'ds_obsn_marking_limit.mks_limit'
                            , 'ds_obsn_marking_limit.limit_percent', 'ds_obsn_marking_limit.obsn')
                    ->get();

            if (!$dsObsnMksWtInfo->isEmpty()) {
                foreach ($dsObsnMksWtInfo as $dsObsn) {
                    $dsObsnMksWtArr['total_mks'] = !empty($dsObsnMksWtArr['total_mks']) ? $dsObsnMksWtArr['total_mks'] : 0;
                    $dsObsnMksWtArr['total_mks'] += $dsObsn->mks_limit;
                    $dsObsnMksWtArr['total_wt'] = !empty($dsObsnMksWtArr['total_wt']) ? $dsObsnMksWtArr['total_wt'] : 0;
                    $dsObsnMksWtArr['total_wt'] += $dsObsn->obsn;
                }
            }

            $courseWtInfo = CriteriaWiseWt::where('course_id', $request->course_id)
                    ->select('total_event_wt', 'ds_obsn_wt', 'ci_obsn_wt', 'comdt_obsn_wt', 'total_wt')
                    ->first();


//            echo '<pre>';
//            print_r($eventInfo->toArray());
//            print_r($subEventInfo->toArray());
//            print_r($subSubEventInfo->toArray());
//            print_r($subSubSubEventInfo->toArray());
//            print_r($eventMksWtArr);
//            print_r($rowSpanArr);
//            exit;
            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearInfo[$request->training_year_id]) ? '_' . $activeTrainingYearInfo[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $fileName = 'Event_Mks_Wt_Report' . $tyName . $courseName . $termName;
            $fileName = Common::getFileFormatedName($fileName);
        }
        if ($request->view == 'print') {
            return view('reportCrnt.eventList.print.index')->with(compact('activeTrainingYearInfo', 'request', 'termList'
                                    , 'courseList', 'eventMksWtArr', 'qpArr', 'rowSpanArr', 'dsObsnMksWtInfo', 'dsObsnMksWtArr', 'courseWtInfo'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.eventList.print.index', compact('activeTrainingYearInfo', 'request', 'termList'
                                    , 'courseList', 'eventMksWtArr', 'qpArr', 'rowSpanArr', 'dsObsnMksWtInfo', 'dsObsnMksWtArr', 'courseWtInfo'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.eventList.print.index', compact('activeTrainingYearInfo', 'request', 'termList'
                                    , 'courseList', 'eventMksWtArr', 'qpArr', 'rowSpanArr', 'dsObsnMksWtInfo', 'dsObsnMksWtArr', 'courseWtInfo')), $fileName . '.xlsx');
        } else {
            return view('reportCrnt.eventList.index')->with(compact('activeTrainingYearInfo', 'request', 'termList'
                                    , 'courseList', 'eventMksWtArr', 'qpArr', 'rowSpanArr', 'dsObsnMksWtInfo', 'dsObsnMksWtArr'
                                    , 'courseWtInfo', 'sortByList'));
        }
    }

    //Start::Get Course
    public function getCourse(Request $request) {
//        if (Auth::user()->group_id == 4) {
//            $courseList = DsGroupMemberTemplate::join('course', 'course.id', '=', 'ds_group_member_template.course_id')
//                            ->where('ds_group_member_template.user_id', Auth::user()->id)
//                            ->where('course.status', '!=', '0')
//                            ->where('course.training_year_id', $request->training_year_id)
//                            ->orderBy('course.id', 'desc')
//                            ->pluck('course.name', 'course.id')->toArray();
//        } else {
        $courseList = Course::where('status', '!=', '0')
                        ->where('training_year_id', $request->training_year_id)
                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
//        }
        $courseList = array('0' => __('label.SELECT_COURSE_OPT')) + $courseList;
        $view = view('reportCrnt.eventList.getCourse', compact('courseList'))->render();
        return response()->json(['view' => $view]);
    }

    //Start::Get Term
    public function getTerm(Request $request) {

        $termList = ['0' => __('label.ALL_TERMS')] + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();
        $view = view('reportCrnt.eventList.getTerm', compact('termList'))->render();
        return response()->json(['view' => $view]);
    }

    public function eventFilter(Request $request) {
        $rules = [
            'course_id' => 'required|not_in:0',
//            'term_id' => 'required|not_in:0',
            'training_year_id' => 'required|not_in:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        $url = 'training_year_id=' . $request->training_year_id
                . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id . '&sort=' . $request->sort;
        if ($validator->fails()) {
            return redirect('eventListReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('eventListReportCrnt?generate=true&' . $url);
    }

}
