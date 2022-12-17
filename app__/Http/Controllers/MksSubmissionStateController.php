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
use Response;
use Auth;
use Common;
use DB;
use Illuminate\Http\Request;

class MksSubmissionStateController extends Controller {

    public function index(Request $request) {
        $dsDeligationList = Common::getDsDeligationList();
        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.MKS_SUBMISSION_STATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.MKS_SUBMISSION_STATE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        // check all terms are closed 
        $activeTermInfo = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                ->select('term.id', 'term.name')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '1')
                ->where('term_to_course.active', '1')
                ->first();
        if (empty($activeTermInfo)) {
            $void['header'] = __('label.MKS_SUBMISSION_STATE');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $courseList->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }

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

        $dsObservationMarkingInfo = DsObsnMarking::where('course_id', $courseList->id)
                        ->where('term_id', $activeTermInfo->id)
                        ->select('term_id', 'updated_by')->get();
        $dsObservationMarkingLockInfo = DsObsnMarkingLock::where('course_id', $courseList->id)
                        ->where('term_id', $activeTermInfo->id)
                        ->select('term_id', 'locked_by')->get();

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

        $assessmentActDeactInfo = AssessmentActDeact::where('course_id', $courseList->id)
                ->where('term_id', $activeTermInfo->id)->where('status', '1')
                ->select('criteria', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id', 'status')
                ->get();
        $assessmentActDeactArr = [];
        if (!$assessmentActDeactInfo->isEmpty()) {
            foreach ($assessmentActDeactInfo as $info) {
                $assessmentActDeactArr[$info->criteria][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id] = $info->status;
            }
        }

        //************Start :: DS own mks submission state************//
        $dsOwnMarkingGpInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $courseList->id)
                ->where('marking_group.term_id', $activeTermInfo->id)
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->select('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id', 'marking_group.sub_sub_sub_event_id')
                ->get();

        $dsOwnMksSubmissionArr = [];
        if (!$dsOwnMarkingGpInfo->isEmpty()) {
            foreach ($dsOwnMarkingGpInfo as $info) {
                $dsOwnMksSubmissionArr[$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id]['to_be_put'] = 1;
            }
        }

        $dsOwnMksSubmissionInfo = EventAssessmentMarking::where('event_assessment_marking.course_id', $courseList->id)
                ->where('event_assessment_marking.term_id', $activeTermInfo->id)
                ->where('event_assessment_marking.updated_by', Auth::user()->id)
                ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                        , DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
                ->groupBy('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id')
                ->get();
        if (!$dsOwnMksSubmissionInfo->isEmpty()) {
            foreach ($dsOwnMksSubmissionInfo as $info) {
                $dsOwnMksSubmissionArr[$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id]['drafted'] = 1;
            }
        }

        $dsOwnMksSubmissionLockInfo = EventAssessmentMarkingLock::where('event_assessment_marking_lock.course_id', $courseList->id)
                ->where('event_assessment_marking_lock.term_id', $activeTermInfo->id)
                ->where('event_assessment_marking_lock.locked_by', Auth::user()->id)
                ->select('event_assessment_marking_lock.event_id', 'event_assessment_marking_lock.sub_event_id', 'event_assessment_marking_lock.sub_sub_event_id'
                        , 'event_assessment_marking_lock.sub_sub_sub_event_id')
                ->get();

        if (!$dsOwnMksSubmissionLockInfo->isEmpty()) {
            foreach ($dsOwnMksSubmissionLockInfo as $info) {
                $dsOwnMksSubmissionArr[$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id]['submitted'] = 1;
            }
        }


        //************End :: DS own mks submission state************//


        return view('mksSubmissionState.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request'
                                , 'activeTermInfo', 'courseName', 'request', 'assessmentActDeactArr'
                                , 'eventMksWtArr', 'rowSpanArr', 'termName', 'ciObsnMarking', 'dsOwnMksSubmissionArr'
                                , 'ciObsnMarkingLock', 'comdtObsnMarking', 'comdtObsnMarkingLock', 'dsDataList'
                                , 'dsObservationMarkingArr', 'dsObservationMarkingLockArr', 'courseTermArr'));
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'mksSubmissionState.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

}
