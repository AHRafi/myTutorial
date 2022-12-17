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
use App\DsMarkingGroup;
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
use Illuminate\Http\Request;

class DsMarkingTrendReportController extends Controller {

    private $controller = 'DsMarkingTrendReport';

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
        $termList = ['0' => __('label.SELECT_TERM_OPT')];
        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.event_id', $request->event_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();


        $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + $eventList;


        $subEventList = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                ->where('term_to_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subEventList = $subEventList->where('term_to_sub_event.term_id', $request->term_id);
        }
        $subEventList = $subEventList->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;


        $subSubEventList = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->where('term_to_sub_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubEventList = $subSubEventList->where('term_to_sub_sub_event.term_id', $request->term_id);
        }
        $subSubEventList = $subSubEventList->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;


        $subSubSubEventList = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubSubEventList = $subSubSubEventList->where('term_to_sub_sub_sub_event.term_id', $request->term_id);
        }
        $subSubSubEventList = $subSubSubEventList->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;



        $eventMksWtArr = $eventWiseMksArr = $wingWiseMksArr = $dsMksArr = [];
        $dsIds = $selectedDs = [];
        $totalDsAvgLine = 0;
        if ($request->generate == 'true') {

            $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                    ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
					->join('rank', 'rank.id', 'users.rank_id')
                    ->join('wing', 'wing.id', '=', 'users.wing_id')
					->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $dsList = $dsList->where('marking_group.term_id', $request->term_id);
            }
            $dsList = $dsList->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $dsList = $dsList->where('users.status', '1')->where('users.group_id', 4)
                            ->orderBy('wing.order', 'asc')
							->orderBy('appointment.order', 'asc')
							->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                            ->pluck('users.official_name', 'users.id')->toArray();

            $dsIds = !empty($request->ds_id) ? explode(",", $request->ds_id) : [];
            $selectedDs = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                    ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
					->join('rank', 'rank.id', 'users.rank_id')
                    ->join('wing', 'wing.id', '=', 'users.wing_id')
					->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                    ->where('marking_group.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $selectedDs = $selectedDs->where('marking_group.term_id', $request->term_id);
            }
            $selectedDs = $selectedDs->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $selectedDs = $selectedDs->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $selectedDs = $selectedDs->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $selectedDs = $selectedDs->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $selectedDs = $selectedDs->whereIn('ds_marking_group.ds_id', $dsIds)
                            ->where('users.status', '1')->where('users.group_id', 4)
                            ->orderBy('wing.order', 'asc')
							->orderBy('appointment.order', 'asc')
							->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                            ->pluck('users.official_name', 'users.id')->toArray();

            //START:: Event Information
            $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                    ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                    ->where('term_to_event.course_id', $request->course_id);
            if (!empty($request->term_id)) {
                $eventInfo = $eventInfo->where('term_to_event.term_id', $request->term_id);
            }
            $eventInfo = $eventInfo->where('term_to_event.event_id', $request->event_id)
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
            $subEventInfo = $subEventInfo->where('term_to_sub_event.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $subEventInfo = $subEventInfo->where('term_to_sub_event.sub_event_id', $request->sub_event_id);
            }
            $subEventInfo = $subEventInfo->where('sub_event.status', '1')
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
            $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
            }
            $subSubEventInfo = $subSubEventInfo->where('sub_sub_event.status', '1')
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
            $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $subSubSubEventInfo = $subSubSubEventInfo->where('sub_sub_sub_event.status', '1')
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
            $eventWiseMksInfo = $eventWiseMksInfo->where('event_assessment_marking.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $eventWiseMksInfo = $eventWiseMksInfo->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $eventWiseMksInfo = $eventWiseMksInfo->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $eventWiseMksInfo = $eventWiseMksInfo->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $eventWiseMksInfo = $eventWiseMksInfo->whereNotNull('event_assessment_marking.mks')
                    ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.updated_by', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
                    ->groupBy('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                            , 'event_assessment_marking.updated_by')
                    ->get();
            $dsEventCountArr = [];
            if (!$eventWiseMksInfo->isEmpty()) {
                foreach ($eventWiseMksInfo as $eventMksInfo) {
                    if (!empty($eventMwInfo->avg_mks)) {
                        $dsEventCountArr[$eventMwInfo->updated_by][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] = !empty($dsEventCountArr[$eventMwInfo->updated_by][$eventMwInfo->event_id][$eventMwInfo->sub_event_id]) ? $dsEventCountArr[$eventMwInfo->updated_by][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] : 0;
                        $dsEventCountArr[$eventMwInfo->updated_by][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] += 1;
                    }
                    $eventWiseMksArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id][$eventMksInfo->sub_sub_event_id][$eventMksInfo->sub_sub_sub_event_id][$eventMksInfo->updated_by]['avg_mks'] = $eventMksInfo->avg_mks;
                }
            }
            //END:: Event Wise Mks


            $dsWiseMksArr = [];
            if (!empty($selectedDs)) {
                foreach ($selectedDs as $dsId => $dsName) {
                    if (!empty($eventMksWtArr['mks_wt'])) {
                        foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $eventAvgMks = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$dsId]['avg_mks']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$dsId]['avg_mks'] : 0;
                                        $assignedMks = !empty($info['mks_limit']) ? $info['mks_limit'] : 0;


                                        //count average where avg marking is enabled
                                        $totalCount = 0;
                                        if (!empty($dsEventCountArr[$dsId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($dsId, $dsEventCountArr)) {
                                                    $totalCount = $dsEventCountArr[$dsId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;

                                                $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;

                                                if ($totalCount != 0 && $unitMksLimit != 0) {
                                                    $assignedMks = $subEventMksLimit / $totalCount;
                                                    $eventAvgMks = ($eventAvgMks * $subEventMksLimit) / ($totalCount * $unitMksLimit);
                                                }
                                            }
                                        }

                                        //total assigned wt event wise
                                        $eventMksWtArr['total_mks_limit'][$dsId] = !empty($eventMksWtArr['total_mks_limit'][$dsId]) ? $eventMksWtArr['total_mks_limit'][$dsId] : 0;
                                        $eventMksWtArr['total_mks_limit'][$dsId] += (!empty($eventAvgMks) ? $assignedMks : 0);

                                        //total achieved mks ds wise
                                        $dsWiseMksArr[$dsId] = !empty($dsWiseMksArr[$dsId]) ? $dsWiseMksArr[$dsId] : 0;
                                        $dsWiseMksArr[$dsId] += $eventAvgMks;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $dsMksMinMaxArr = [];
            $totalDsPercent = 0;
            if (!empty($dsWiseMksArr)) {
                foreach ($dsWiseMksArr as $dsId => $mksInfo) {
                    $eventMks = !empty($mksInfo) ? $mksInfo : 0;
                    $eventMksLimit = !empty($eventMksWtArr['total_mks_limit'][$dsId]) ? $eventMksWtArr['total_mks_limit'][$dsId] : 0;
                    $eventPercentage = 0;
                    if (!empty($eventMksLimit)) {
                        $eventPercentage = ($eventMks / $eventMksLimit) * 100;
                    }

                    //ds wise event mks %
                    $dsMksArr[$dsId]['total_mks_percent'] = $eventPercentage;

                    $dsMksMinMaxArr[] = $eventPercentage;
                    $totalDsPercent += $eventPercentage;
                    $totalDsAvgLine = !empty($selectedDs) ? $totalDsPercent / sizeof($selectedDs) : 0;
                }
            }

            if (!empty($dsMksMinMaxArr)) {
                $dsMksArr['grand_max_min']['max'] = !empty($dsMksMinMaxArr) ? max($dsMksMinMaxArr) + (max($dsMksMinMaxArr) + 5 < 100 ? 5 : 0) : 0;
                $dsMksArr['grand_max_min']['min'] = !empty($dsMksMinMaxArr) ? min($dsMksMinMaxArr) - (min($dsMksMinMaxArr) - 5 > 0 ? 5 : 0) : 0;
            }

//            echo '<pre>';
//            print_r($dsMksArr['grand_max_min']);
//            exit;
        }


        return view('report.dsMarkingTrend.index', compact('activeTrainingYearList', 'courseList'
                        , 'dsList', 'eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList', 'dsIds', 'wingWiseMksArr', 'selectedDs', 'termList'
                        , 'totalDsAvgLine', 'dsMksArr'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
		$dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();
        $html2 = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showEventView = view('report.dsMarkingTrend.getCourseWiseEvent', compact('eventList'))->render();
        $html = view('report.dsMarkingTrend.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html, 'html2' => $html2, 'showEventView' => $showEventView]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.ALL_TERMS')] + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();

		$dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();
        $html2 = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showEventView = view('report.dsMarkingTrend.getCourseWiseEvent', compact('eventList'))->render();

        $html1 = view('report.armsServiceWiseEventTrend.getTerm', compact('termList'))->render();

        return response()->json(['html1' => $html1, 'html2' => $html2, 'showEventView' => $showEventView]);
    }

    public function getCourseWiseDsEvent(Request $request) {

		$dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                        ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                        ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
						->join('rank', 'rank.id', 'users.rank_id')
                        ->join('wing', 'wing.id', '=', 'users.wing_id')
						->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + $eventList;

        $html = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showEventView = view('report.dsMarkingTrend.getCourseWiseEvent', compact('eventList'))->render();
        return Response::json(['showEventView' => $showEventView, 'html' => $html]);
    }

    public function getCourseWiseDsSubEvent(Request $request) {

        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', '=', 'users.wing_id')
				->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $dsList = $dsList->where('marking_group.term_id', $request->term_id);
        }
        $dsList = $dsList->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsList = $dsList->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();


        $subEventList = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event_to_sub_event', 'event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id')
                ->where('term_to_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subEventList = $subEventList->where('term_to_sub_event.term_id', $request->term_id);
        }
        $subEventList = $subEventList->where('term_to_sub_event.event_id', $request->event_id)
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + $subEventList;
//        echo '<pre>';        print_r($subEventList); exit;

        $html = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showSubEventView = view('report.dsMarkingTrend.getCourseWiseSubEvent', compact('subEventList'))->render();
        return Response::json(['showSubEventView' => $showSubEventView, 'html' => $html]);
    }

    public function getCourseWiseDsSubSubEvent(Request $request) {

        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', '=', 'users.wing_id')
				->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $dsList = $dsList->where('marking_group.term_id', $request->term_id);
        }
        $dsList = $dsList->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsList = $dsList->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();


        $subSubEventList = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('event_to_sub_sub_event', 'event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->where('term_to_sub_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubEventList = $subSubEventList->where('term_to_sub_sub_event.term_id', $request->term_id);
        }
        $subSubEventList = $subSubEventList->where('term_to_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')->toArray();
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + $subSubEventList;
//        echo '<pre>';        print_r($subEventList); exit;

        $html = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showSubSubEventView = view('report.dsMarkingTrend.getCourseWiseSubSubEvent', compact('subSubEventList'))->render();
        return Response::json(['showSubSubEventView' => $showSubSubEventView, 'html' => $html]);
    }

    public function getCourseWiseDsSubSubSubEvent(Request $request) {

        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', '=', 'users.wing_id')
				->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $dsList = $dsList->where('marking_group.term_id', $request->term_id);
        }
        $dsList = $dsList->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsList = $dsList->where('users.status', '1')->where('users.group_id', 4)
						->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();


        $subSubSubEventList = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('event_to_sub_sub_sub_event', 'event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->where('term_to_sub_sub_sub_event.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $subSubSubEventList = $subSubSubEventList->where('term_to_sub_sub_sub_event.term_id', $request->term_id);
        }
        $subSubSubEventList = $subSubSubEventList->where('term_to_sub_sub_sub_event.event_id', $request->event_id)
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->sub_event_id)
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->sub_sub_event_id)
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')->toArray();
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + $subSubSubEventList;

//        echo '<pre>';        print_r($subEventList); exit;

        $html = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
        $showSubSubSubEventView = view('report.dsMarkingTrend.getCourseWiseSubSubSubEvent', compact('subSubSubEventList'))->render();
        return Response::json(['showSubSubSubEventView' => $showSubSubSubEventView, 'html' => $html]);
    }

    public function getCourseWiseDs(Request $request) {

        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', '=', 'users.wing_id')
				->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $dsList = $dsList->where('marking_group.term_id', $request->term_id);
        }
        $dsList = $dsList->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsList = $dsList->where('users.status', '1')->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();




//        echo '<pre>';        print_r($subEventList); exit;

        $html = view('report.dsMarkingTrend.getCourseWiseDs', compact('dsList'))->render();
//        $showSubSubSubEventView = view('report.dsMarkingTrend.getCourseWiseSubSubSubEvent', compact('subSubSubEventList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {

        $dsList = DsMarkingGroup::join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', '=', 'users.wing_id')
				->join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id);
        if (!empty($request->term_id)) {
            $dsList = $dsList->where('marking_group.term_id', $request->term_id);
        }
        $dsList = $dsList->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsList = $dsList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsList = $dsList->where('users.group_id', 4)
                        ->orderBy('wing.order', 'asc')
						->orderBy('appointment.order', 'asc')
						->orderBy('rank.order', 'asc')->orderBy('users.personal_no', 'asc')
                        ->pluck('users.official_name', 'users.id')->toArray();

        $dsIds = !empty($request->ds_id) ? implode(",", $request->ds_id) : implode(",", $dsList);



        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
            'range_start' => 'lt:range_end|gte:0',
            'range_end' => 'lte:100|gt:range_start',
        ];

        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'event_id.not_in' => __('label.THE_EVENT_FIELD_IS_REQUIRED'),
        ];



        if (!empty($request->threshold)) {
            $rules['threshold'] = 'lte:71|gte:51';
        }

        $request->range_start = !empty($request->range_start) ? $request->range_start : 0;
        $request->range_end = !empty($request->range_end) ? $request->range_end : 100;
        

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&term_id=' . $request->term_id . '&event_id=' . $request->event_id 
                . '&range_start=' . $request->range_start . '&range_end=' . $request->range_end
                 . '&sub_event_id=' . $request->sub_event_id . '&sub_sub_event_id=' . $request->sub_sub_event_id 
                . '&sub_sub_sub_event_id=' . $request->sub_sub_sub_event_id. '&threshold=' . $request->threshold . '&ds_id=' . $dsIds;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('dsMarkingTrendReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('dsMarkingTrendReport?generate=true&' . $url);
    }

}
