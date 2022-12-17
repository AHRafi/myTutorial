<?php

namespace App\Http\Controllers;

use Validator;
use App\Course;
use App\TrainingYear;
use App\User;
use App\Term;
use App\TermToCourse;
use App\EventAssessmentMarking;
use App\EventAssessmentMarkingLock;
use App\CiModerationMarking;
use App\CiModerationMarkingLock;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\DsMarkingGroup;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\AssessmentActDeact;
use App\ComdtModerationMarkingLock;
use App\ComdtModerationMarking;
use App\CiObsnMarking;
use App\CiObsnMarkingLock;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\MarkingGroup;
use App\CmMarkingGroup;
use App\MaProcess;
use Response;
use Auth;
use Common;
use DB;
use Illuminate\Http\Request;

class AssessmentActDeactController extends Controller {

    public function index(Request $request) {
        $dsDeligationList = Common::getDsDeligationList();
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.ASSESSMENT_ACTIVATE_DEACTIVATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.ASSESSMENT_ACTIVATE_DEACTIVATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        // check all terms are closed
        $openTerms = TermToCourse::select('id')->where('status', '1')->where('course_id', $courseList->id)->count();

        // check active term 
        $activeTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();

        $activeTermInfId = !empty($activeTermInfo->id) ? $activeTermInfo->id : 0;

        $canClrEventAssessment = 0;

        $dsObsnInfo = DsObsnMarking::where('course_id', $courseList->id);
        if (!empty($activeTermInfo->id)) {
            $dsObsnInfo = $dsObsnInfo->where('term_id', $activeTermInfo->id);
        }
        $dsObsnInfo = $dsObsnInfo->whereNotNull('obsn_mks')->first();

        $ciModInfo = CiModerationMarking::where('course_id', $courseList->id);
        if (!empty($activeTermInfo->id)) {
            $ciModInfo = $ciModInfo->where('term_id', $activeTermInfo->id);
        }
        $ciModInfo = $ciModInfo->whereNotNull('ci_moderation')->first();

        $canClrEventAssessment = !empty($dsObsnInfo) || !empty($ciModInfo) ? 1 : 0;

        $criteriaList = [
            '0' => __('label.SELECT_ASSESMENT_CRITERIA'),
            '1' => __('label.EVENT_ASSESSMENT'),
//            '2' => __('label.CI_MODERATION'),
            '3' => __('label.DS_OBSN'),
        ];

        $clearBtnDisabled = 'disabled';
//        echo '<pre>';
//        print_r($prevDataArr);
//        exit;

        $courseName = Course::select('name')->where('id', $courseList->id)->first();

        $courseTermArr = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->pluck('term.name', 'term.id')->toArray();

        $termName = !empty($activeTermInfo->id) ? Term::select('name')->where('id', $activeTermInfo->id)->first() : [];
        $eventMksWtArr = [];
        //event info
        $eventInfo = MarkingGroup::join('event', 'event.id', '=', 'marking_group.event_id')
                ->join('term', 'term.id', 'marking_group.term_id')
                ->leftJoin('sub_event', 'sub_event.id', 'marking_group.sub_event_id')
                ->leftJoin('sub_sub_event', 'sub_sub_event.id', 'marking_group.sub_sub_event_id')
                ->leftJoin('sub_sub_sub_event', 'sub_sub_sub_event.id', 'marking_group.sub_sub_sub_event_id')
                ->where('marking_group.course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $eventInfo = $eventInfo->where('marking_group.term_id', $activeTermInfo->id);
        }

        $eventInfo = $eventInfo->select('event.event_code as event_name', 'event.id as event_id', 'marking_group.term_id'
                        , 'sub_event.event_code as sub_event_name', 'marking_group.sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_name', 'marking_group.sub_sub_event_id'
                        , 'sub_sub_sub_event.event_code as sub_sub_sub_event_name', 'marking_group.sub_sub_sub_event_id'
                        , 'term.name as term_name')
                ->orderBy('term.order', 'asc')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();

        $termEventArr = Common::getEventList($request, $courseList->id, $activeTermInfo->id, 0);

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                $eventMksWtArr['event'][$ev->term_id]['name'] = $ev->term_name ?? '';
//                $termEventArr['mks_wt'][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id]
                if (!empty($termEventArr['event'][$ev->event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id]['name'] = $ev->event_name ?? '';
                }
                if (!empty($termEventArr['event'][$ev->event_id][$ev->sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id]['name'] = $ev->sub_event_name ?? '';
                }
                if (!empty($termEventArr['event'][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id]['name'] = $ev->sub_sub_event_name ?? '';
                }
                if (!empty($termEventArr['event'][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id]['name'] = $ev->sub_sub_sub_event_name ?? '';
                }
            }
        }

        $totalMarkingDsInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->select('marking_group.term_id', 'marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id', DB::raw("COUNT(DISTINCT ds_marking_group.ds_id) as ds_id"))
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $totalMarkingDsInfo = $totalMarkingDsInfo->where('marking_group.term_id', $activeTermInfo->id);
        }

        $totalMarkingDsInfo = $totalMarkingDsInfo->groupBy('marking_group.term_id', 'marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id')
                ->get();


        $totalDsArr = $totalLockedDsArr = $rowSpanArr = [];


        $totalLockedDsInfo = EventAssessmentMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw("COUNT(locked_by) as locked_ds"))
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $totalLockedDsInfo = $totalLockedDsInfo->where('term_id', $activeTermInfo->id);
        }

        $totalLockedDsInfo = $totalLockedDsInfo->groupBy('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->get();

        if (!$totalLockedDsInfo->isEmpty()) {
            foreach ($totalLockedDsInfo as $lockInfo) {
                $eventMksWtArr['mks_wt'][$lockInfo->term_id][$lockInfo->event_id][$lockInfo->sub_event_id][$lockInfo->sub_sub_event_id][$lockInfo->sub_sub_sub_event_id]['forwarded'] = $lockInfo->locked_ds;
            }
        }
        if (!$totalMarkingDsInfo->isEmpty()) {
            foreach ($totalMarkingDsInfo as $dsInfo) {
                $forwarded = !empty($eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['forwarded']) ? $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['forwarded'] : 0;

                $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['total'] = $dsInfo->ds_id;
                $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['not_forwarded'] = $dsInfo->ds_id - $forwarded;
            }
        }



        // ci mod check
        $ciModInfo = CiModerationMarking::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $ciModInfo = $ciModInfo->where('term_id', $activeTermInfo->id);
        }

        $ciModInfo = $ciModInfo->get();
        $ciModLockInfo = CiModerationMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $ciModLockInfo = $ciModLockInfo->where('term_id', $activeTermInfo->id);
        }

        $ciModLockInfo = $ciModLockInfo->get();

        if (!$ciModInfo->isEmpty()) {
            foreach ($ciModInfo as $ciInfo) {
                $eventMksWtArr['mks_wt'][$ciInfo->term_id][$ciInfo->event_id][$ciInfo->sub_event_id][$ciInfo->sub_sub_event_id][$ciInfo->sub_sub_sub_event_id]['ci_mod'] = 1;
            }
        }
        if (!$ciModLockInfo->isEmpty()) {
            foreach ($ciModLockInfo as $ciLockInfo) {
                $eventMksWtArr['mks_wt'][$ciLockInfo->term_id][$ciLockInfo->event_id][$ciLockInfo->sub_event_id][$ciLockInfo->sub_sub_event_id][$ciLockInfo->sub_sub_sub_event_id]['ci_mod_lock'] = 1;
            }
        }

        //comdt mod check
        $comdtModInfo = ComdtModerationMarking::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $comdtModInfo = $comdtModInfo->where('term_id', $activeTermInfo->id);
        }

        $comdtModInfo = $comdtModInfo->get();
        $comdtModLockInfo = ComdtModerationMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $courseList->id);

        if (!empty($activeTermInfo->id)) {
            $comdtModLockInfo = $comdtModLockInfo->where('term_id', $activeTermInfo->id);
        }

        $comdtModLockInfo = $comdtModLockInfo->get();

        if (!$comdtModInfo->isEmpty()) {
            foreach ($comdtModInfo as $comdtInfo) {
                $eventMksWtArr['mks_wt'][$comdtInfo->term_id][$comdtInfo->event_id][$comdtInfo->sub_event_id][$comdtInfo->sub_sub_event_id][$comdtInfo->sub_sub_sub_event_id]['comdt_mod'] = 1;
            }
        }
        if (!$comdtModLockInfo->isEmpty()) {
            foreach ($comdtModLockInfo as $comdtLockInfo) {
                $eventMksWtArr['mks_wt'][$comdtLockInfo->term_id][$comdtLockInfo->event_id][$comdtLockInfo->sub_event_id][$comdtLockInfo->sub_sub_event_id][$comdtLockInfo->sub_sub_sub_event_id]['comdt_mod_lock'] = 1;
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

        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $termId => $evMksWtInfo) {
                foreach ($evMksWtInfo as $eventId => $evInfo) {
                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {

                                $rowSpanArr['event'][$termId][$eventId] = !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 0;
                                $rowSpanArr['event'][$termId][$eventId] += 1;

                                $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] = !empty($rowSpanArr['sub_event'][$termId][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] : 0;
                                $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] += 1;

                                $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] = !empty($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] : 0;
                                $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] += 1;

                                $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = !empty($rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : 0;
                                $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] += 1;
                            }
                        }
                    }
                }
            }
        }



//        echo '<pre>';
//        print_r($eventMksWtArr['mks_wt']);
//        exit;

        $ciObsnMarking = CiObsnMarking::select('id')->where('course_id', $courseList->id)->get();
        $ciObsnMarkingLock = CiObsnMarkingLock::select('id')->where('course_id', $courseList->id)->first();
        $comdtObsnMarking = ComdtObsnMarking::select('id')->where('course_id', $courseList->id)->get();
        $comdtObsnMarkingLock = ComdtObsnMarkingLock::select('id')->where('course_id', $courseList->id)->first();


        $dsDataInfo = User::join('user_group', 'user_group.id', 'users.group_id')
                ->join('appointment', 'appointment.id', 'users.appointment_id')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', 'users.wing_id')
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->select('users.official_name', 'users.id as ds_id', 'users.photo'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name) as ds_name")
                        , 'users.personal_no')
                ->get();
        $dsDataList = [];
        if (!$dsDataInfo->isEmpty()) {
            foreach ($dsDataInfo as $ds) {
                $dsDataList[$ds->ds_id] = $ds->toArray();
            }
        }

        $dsObservationMarkingInfo = DsObsnMarking::where('course_id', $courseList->id);
        if (!empty($activeTermInfo->id)) {
            $dsObservationMarkingInfo = $dsObservationMarkingInfo->where('term_id', $activeTermInfo->id);
        }
        $dsObservationMarkingInfo = $dsObservationMarkingInfo->select('term_id', 'updated_by')->get();

        $dsObservationMarkingLockInfo = DsObsnMarkingLock::where('course_id', $courseList->id);
        if (!empty($activeTermInfo->id)) {
            $dsObservationMarkingLockInfo = $dsObservationMarkingLockInfo->where('term_id', $activeTermInfo->id);
        }
        $dsObservationMarkingLockInfo = $dsObservationMarkingLockInfo->select('term_id', 'locked_by')->get();

        $dsObservationMarkingArr = $dsObservationMarkingLockArr = [];
        if (!$dsObservationMarkingInfo->isEmpty()) {
            foreach ($dsObservationMarkingInfo as $mInfo) {
                $dsObservationMarkingArr[$mInfo->updated_by] = $mInfo->updated_by;
            }
        }
        if (!$dsObservationMarkingLockInfo->isEmpty()) {
            foreach ($dsObservationMarkingLockInfo as $mLockInfo) {
                $dsObservationMarkingLockArr[$mLockInfo->locked_by] = $mLockInfo->locked_by;
            }
        }

//                echo '<pre>';
//        print_r($dsObservationMarkingInfo);
//        exit;

        $assessmentActDeactInfo = AssessmentActDeact::where('course_id', $courseList->id);
        if (!empty($openTerms)) {
            $assessmentActDeactInfo = $assessmentActDeactInfo->where('term_id', $activeTermInfo->id);
        }
        $assessmentActDeactInfo = $assessmentActDeactInfo->where('status', '1')
                ->select('criteria', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id', 'status')
                ->get();
        $assessmentActDeactArr = [];
        if (!$assessmentActDeactInfo->isEmpty()) {
            foreach ($assessmentActDeactInfo as $info) {
                $assessmentActDeactArr[$info->criteria][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id] = $info->status;
            }
        }


        if (!empty($activeTermInfo->id)) {
            $maProcessInfo = MaProcess::where('course_id', $courseList->id)->where('term_id', $activeTermInfo->id)
                            ->select('process')->first();
        }
        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';




        $maEventMksWtArr = [];
        if ($maProcess == '3') {
            $maEventMksWtArr = Common::getEventList($request, $courseList->id, $activeTermInfo->id, 1);
        } elseif (in_array($maProcess, ['1', '2'])) {
            $maEventMksWtArr['mks_wt'][0][0][0][0]['name'] = $maProcess == '1' ? __('label.SYN') : ($maProcess == '2' ? __('label.SUB_SYN') : '');
        }

        return view('assessmentActDeact.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request', 'activeTermInfo'
                                , 'canClrEventAssessment', 'criteriaList', 'clearBtnDisabled', 'courseName', 'request'
                                , 'eventMksWtArr', 'rowSpanArr', 'termName', 'ciObsnMarking', 'assessmentActDeactArr'
                                , 'ciObsnMarkingLock', 'comdtObsnMarking', 'comdtObsnMarkingLock', 'dsDataList'
                                , 'dsObservationMarkingArr', 'dsObservationMarkingLockArr', 'courseTermArr'
                                , 'openTerms', 'maProcess', 'maEventMksWtArr'));
    }

    

    public function setStat(Request $request) {
//        echo '<pre>';
//        print_r($request->all());
//        exit;
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $criteria = $request->criteria;
        $eventId = !empty($request->event_id) ? $request->event_id : 0;
        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;
        $status = !empty($request->status) ? $request->status : '0';
        $statusMsg1 = !empty($request->status) ? __('label.ACTIVATION') : __('label.DEACTIVATION');
        $statusMsg2 = !empty($request->status) ? __('label.ACTIVATE_') : __('label.DEACTIVATE_');



        DB::beginTransaction();

        try {
            $prevActDeactInfo = AssessmentActDeact::where('course_id', $courseId)
                            ->where('term_id', $termId)->where('criteria', $criteria)
                            ->where('event_id', $eventId)->where('sub_event_id', $subEventId)
                            ->where('sub_sub_event_id', $subSubEventId)->where('sub_sub_sub_event_id', $subSubSubEventId)
                            ->select('id')->first();

            $actDeact = !empty($prevActDeactInfo->id) ? AssessmentActDeact::find($prevActDeactInfo->id) : new AssessmentActDeact;

            $actDeact->course_id = $courseId;
            $actDeact->term_id = $termId;
            $actDeact->criteria = $criteria;
            $actDeact->event_id = $eventId;
            $actDeact->sub_event_id = $subEventId;
            $actDeact->sub_sub_event_id = $subSubEventId;
            $actDeact->sub_sub_sub_event_id = $subSubSubEventId;
            $actDeact->status = $status;
            $actDeact->updated_by = Auth::user()->id;
            $actDeact->updated_at = date("Y-m-d H:i:s");

            $actDeact->save();
            $successMsg = __('label.DE_ACTIVATION_WAS_SUCCESSFUL', ['stat' => $statusMsg1]);
            $errorMsg = __('label.FAILED_TO_DE_ACTIVATE', ['stat' => $statusMsg2]);

            DB::commit();
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => $errorMsg], 401);
        }
    }

    public function requestCourseSatatusSummary(Request $request) {
        $loadView = 'assessmentActDeact.showCourseStatusSummary';
        return Common::requestCourseSatatusSummary($request, $loadView);
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'assessmentActDeact.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

    public function getCmActivationState(Request $request) {
        $loadView = 'assessmentActDeact.showCmActivationStateModal';
        return Common::getCmActivationStateSummary($request, $loadView);
    }

    public function setCmMarkingGroupStat(Request $request) {

        $cmMarkingGroupId = $request->cm_marking_group_id;
        $activeStatus = $request->status;
        $statusMsg1 = !empty($request->status) ? __('label.ACTIVATED') : __('label.PAUSED');
        $statusMsg2 = !empty($request->status) ? __('label.ACTIVATE_') : __('label.PAUSE_');



        DB::beginTransaction();

        try {
            $markingGroupInfo = CmMarkingGroup::where('id', $cmMarkingGroupId)->select('id')->first();

            $markingGroupData = !empty($markingGroupInfo->id) ? CmMarkingGroup::find($markingGroupInfo->id) : new CmMarkingGroup;

            $markingGroupData->active = $activeStatus;
            $markingGroupData->updated_by = Auth::user()->id;
            $markingGroupData->updated_at = date("Y-m-d H:i:s");

            $markingGroupData->save();
            $successMsg = __('label.CM_IS_ACTIVATED_SUCCESSFULLY', ['stat' => $statusMsg1]);
            $errorMsg = __('label.FAILED_TO_ACTIVATE_CM', ['stat' => $statusMsg2]);

            DB::commit();
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => $errorMsg], 401);
        }
    }

    public function setCmForceSubmit(Request $request) {
        /* echo '<pre>';
          print_r($request->all());exit; */

        $cmIdList = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('ds_marking_group', function($join) use($request) {
                    $join->on('ds_marking_group.marking_group_id', 'cm_marking_group.marking_group_id')
                    ->where('ds_marking_group.ds_id', $request->ds_id);
                })
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $cmIdList = $cmIdList->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cmIdList = $cmIdList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cmIdList = $cmIdList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }


        $cmIdList = $cmIdList->pluck('cm_marking_group.cm_id', 'cm_marking_group.cm_id')->toArray();



        // assessment marking data
        $eventAssessmentMarkingInfo = EventAssessmentMarking::where('event_assessment_marking.course_id', $request->course_id)
                ->where('event_assessment_marking.term_id', $request->term_id)
                ->where('event_assessment_marking.event_id', $request->event_id)
                ->where('event_assessment_marking.updated_by', $request->ds_id)
                ->whereIn('event_assessment_marking.cm_id', $cmIdList);
        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->select('event_assessment_marking.cm_id', 'event_assessment_marking.mks'
                        , 'event_assessment_marking.wt', 'event_assessment_marking.percentage', 'event_assessment_marking.grade_id')
                ->get();

        $eventAssessmentMarkingArr = [];

        if (!$eventAssessmentMarkingInfo->isEmpty()) {
            foreach ($eventAssessmentMarkingInfo as $markingInfo) {
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['mks'] = $markingInfo->mks;
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['wt'] = $markingInfo->wt;
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['percentage'] = $markingInfo->percentage;
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['grade_id'] = $markingInfo->grade_id;
            }
        }


        $data = [];
        $i = 0;
        if (!empty($cmIdList)) {
            foreach ($cmIdList as $cmId => $cmId) {
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $request->term_id;
                $data[$i]['event_id'] = $request->event_id;
                $data[$i]['sub_event_id'] = $request->sub_event_id;
                $data[$i]['sub_sub_event_id'] = $request->sub_sub_event_id;
                $data[$i]['sub_sub_sub_event_id'] = $request->sub_sub_sub_event_id;
                $data[$i]['cm_id'] = $cmId ?? 0;
                $data[$i]['mks'] = $eventAssessmentMarkingArr[$cmId]['mks'] ?? null;
                $data[$i]['wt'] = $eventAssessmentMarkingArr[$cmId]['wt'] ?? null;
                $data[$i]['percentage'] = $eventAssessmentMarkingArr[$cmId]['percentage'] ?? null;
                $data[$i]['grade_id'] = $eventAssessmentMarkingArr[$cmId]['grade_id'] ?? 0;
                $data[$i]['remarks'] = __('label.FORCE_SUBMITTED_BY_DS_COORD');
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = $request->ds_id;
                $i++;
            }
        }


        DB::beginTransaction();

        $successMsg = __('label.EVENT_ASSESSMENT_HAS_BEEN_FORCE_SUBMITTED_SUCCESSFULLY');
        $errorMsg = __('label.EVENT_ASSESSMENT_COULD_NOT_BE_FORCE_SUBMITTED');

        try {
            EventAssessmentMarking::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('event_id', $request->event_id)
                    ->where('sub_event_id', $request->sub_event_id)
                    ->where('sub_sub_event_id', $request->sub_sub_event_id)
                    ->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id)
                    ->where('updated_by', $request->ds_id)
                    ->delete();

            if (EventAssessmentMarking::insert($data)) {

                $target = new EventAssessmentMarkingLock;

                $target->course_id = $request->course_id;
                $target->term_id = $request->term_id;
                $target->event_id = $request->event_id;
                $target->sub_event_id = $request->sub_event_id;
                $target->sub_sub_event_id = $request->sub_sub_event_id;
                $target->sub_sub_sub_event_id = $request->sub_sub_sub_event_id;
                $target->status = '1';
                $target->force_submitted = '1';
                $target->locked_at = date('Y-m-d H:i:s');
                $target->locked_by = $request->ds_id;
                $target->save();
            }

            DB::commit();
            return Response::json(['success' => true, 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'message' => $errorMsg], 401);
        }
    }

}
