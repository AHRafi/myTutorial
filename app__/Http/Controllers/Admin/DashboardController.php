<?php

namespace App\Http\Controllers\Admin;

use DB;
use URL;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Course;
use App\Wing;
use App\ArmsService;
use App\CommissioningCourse;
use App\CmBasicProfile;
use App\TrainingYear;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\EventAssessmentMarking;
use App\EventAssessmentMarkingLock;
use App\CiModerationMarkingLock;
use App\CriteriaWiseWt;
use App\DsObsnMarkingLimit;
use App\GradingSystem;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\NoticeBoard;
use App\Content;
use App\ContentCategory;
use Helper;
use Common;

class DashboardController extends Controller {

    public function __construct() {
        //$this->middleware('auth');
    }

    public function index(Request $request) {
        $today = date('y-m-d');
        $noticeList = NoticeBoard::where('end_date', '>=', $today)->pluck('headline', 'id')->toArray();

        //********************* Start :: term progress ******************//
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        $dsApptList = User::where('group_id', 4)->where('status', '1')
                        ->pluck('official_name', 'id')->toArray();

        $course = [];

        $termToCourseArr = $courseArr = $eventMksWtArr = [];
        if (!empty($activeTrainingYearInfo)) {
            $course = CmBasicProfile::join('course', 'course.id', 'cm_basic_profile.course_id')
                    ->select('course.name', 'course.id', DB::raw('COUNT(cm_basic_profile.id) as total_cm'))
                    ->groupBy('course.name', 'course.id')
                    ->where('course.training_year_id', $activeTrainingYearInfo->id)
                    ->where('course.status', '1')
                    ->first();

            $termInfo = TermToCourse::join('course', 'course.id', '=', 'term_to_course.course_id')
                    ->leftJoin('term', 'term.id', '=', 'term_to_course.term_id')
                    ->where('course.training_year_id', $activeTrainingYearInfo->id)
                    ->where('course.status', '1')
                    ->select('term.name as term', 'term_to_course.initial_date', 'term_to_course.termination_date'
                            , 'term_to_course.number_of_week', 'term_to_course.status', 'term_to_course.active'
                            , 'term_to_course.course_id', 'term_to_course.term_id', 'course.name as course'
                            , 'course.initial_date as course_initial_date', 'course.termination_date as course_termination_date')
                    ->get();

            if (!$termInfo->isEmpty()) {
                foreach ($termInfo as $info) {
                    $termToCourseArr[$info->course_id]['course'] = $info->course;
                    $termToCourseArr[$info->course_id]['course_initial_date'] = $info->course_initial_date;
                    $termToCourseArr[$info->course_id]['course_termination_date'] = $info->course_termination_date;
                    $termToCourseArr[$info->course_id][$info->term_id]['term'] = $info->term;
                    $termToCourseArr[$info->course_id][$info->term_id]['initial_date'] = $info->initial_date;
                    $termToCourseArr[$info->course_id][$info->term_id]['termination_date'] = $info->termination_date;
                    $termToCourseArr[$info->course_id][$info->term_id]['status'] = $info->status;
                    $termToCourseArr[$info->course_id][$info->term_id]['active'] = $info->active;
                    $courseArr[$info->course_id] = $info->course_id;
                }
            }
        }

        $courseTotalCm = !empty($course->total_cm) ? $course->total_cm : 0;


        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->whereIn('term_to_event.course_id', $courseArr)
                ->where('event.status', '1')
                ->select('event.id as event_id', 'event_mks_wt.wt', 'event.has_sub_event'
                        , 'term_to_event.course_id', 'term_to_event.term_id')
                ->orderBy('event.event_code', 'asc')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if (empty($ev->has_sub_event)) {
                    $eventMksWtArr['mks_wt'][$ev->course_id][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                    $eventMksWtArr['total_wt'][$ev->course_id][$ev->term_id] = !empty($eventMksWtArr['total_wt'][$ev->course_id][$ev->term_id]) ? $eventMksWtArr['total_wt'][$ev->course_id][$ev->term_id] : 0;
                    $eventMksWtArr['total_wt'][$ev->course_id][$ev->term_id] += !empty($ev->wt) ? $ev->wt : 0;
                }
            }
        }

        //sub event info
        $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->leftJoin('sub_event_mks_wt', function($join) {
                    $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->whereIn('term_to_sub_event.course_id', $courseArr)
                ->where('sub_event.status', '1')
                ->select('sub_event.id as sub_event_id', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'term_to_sub_event.course_id', 'term_to_sub_event.term_id')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                if (empty($subEv->has_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subEv->course_id][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                    $eventMksWtArr['total_wt'][$subEv->course_id][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->course_id][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->course_id][$subEv->term_id] : 0;
                    $eventMksWtArr['total_wt'][$subEv->course_id][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                }
            }
        }
        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->leftJoin('sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->whereIn('term_to_sub_sub_event.course_id', $courseArr)
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id', 'term_to_sub_sub_event.course_id'
                        , 'term_to_sub_sub_event.term_id')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                if (empty($subSubEv->has_sub_sub_sub_event)) {
                    $eventMksWtArr['mks_wt'][$subSubEv->course_id][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                    $eventMksWtArr['total_wt'][$subSubEv->course_id][$subSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubEv->course_id][$subSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubEv->course_id][$subSubEv->term_id] : 0;
                    $eventMksWtArr['total_wt'][$subSubEv->course_id][$subSubEv->term_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                }
            }
        }

        //sub sub sub event info
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
                ->whereIn('term_to_sub_sub_sub_event.course_id', $courseArr)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'term_to_sub_sub_sub_event.course_id', 'term_to_sub_sub_sub_event.term_id')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->course_id][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                $eventMksWtArr['total_wt'][$subSubSubEv->course_id][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubSubEv->course_id][$subSubSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubSubEv->course_id][$subSubSubEv->term_id] : 0;
                $eventMksWtArr['total_wt'][$subSubSubEv->course_id][$subSubSubEv->term_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
            }
        }

        $eventAssessmentInfo = EventAssessmentMarking::select('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw('COUNT(DISTINCT updated_by) as total'))
                ->groupBy('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id')
                ->whereIn('course_id', $courseArr)
                ->get();
        $eventStatusArr = [];
        if (!$eventAssessmentInfo->isEmpty()) {
            foreach ($eventAssessmentInfo as $info) {
                $eventStatusArr[$info->course_id][$info->term_id][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id]['event_marked'] = $info->total;
            }
        }
        $eventAssessmentLockInfo = EventAssessmentMarkingLock::select('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw('COUNT(id) as total'))
                ->groupBy('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id')
                ->whereIn('course_id', $courseArr)
                ->get();

        if (!$eventAssessmentLockInfo->isEmpty()) {
            foreach ($eventAssessmentLockInfo as $info) {
                $eventStatusArr[$info->course_id][$info->term_id][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id]['event_locked'] = $info->total;
            }
        }


        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $courseId => $courseEvInfo) {
                foreach ($courseEvInfo as $termId => $termEvInfo) {
                    foreach ($termEvInfo as $eventId => $evInfo) {
                        foreach ($evInfo as $subEventId => $subEvInfo) {
                            foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                    $eventMksWtArr['event_to_be_locked'][$courseId][$termId] = !empty($eventMksWtArr['event_to_be_locked'][$courseId][$termId]) ? $eventMksWtArr['event_to_be_locked'][$courseId][$termId] : 0;
                                    $eventMksWtArr['event_to_be_locked'][$courseId][$termId] += 1;
                                    $eventMksWtArr['event_to_be_moderated'][$courseId][$termId] = !empty($eventMksWtArr['event_to_be_moderated'][$courseId][$termId]) ? $eventMksWtArr['event_to_be_moderated'][$courseId][$termId] : 0;
                                    $eventMksWtArr['event_to_be_moderated'][$courseId][$termId] += 1;
                                    $eventDsMarked = !empty($eventStatusArr[$courseId][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['event_marked']) ? $eventStatusArr[$courseId][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['event_marked'] : 0;
                                    $eventDsLocked = !empty($eventStatusArr[$courseId][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['event_locked']) ? $eventStatusArr[$courseId][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['event_locked'] : 0;
                                    $eventMksWtArr['event_completed'][$courseId][$termId] = !empty($eventMksWtArr['event_completed'][$courseId][$termId]) ? $eventMksWtArr['event_completed'][$courseId][$termId] : 0;
                                    $eventMksWtArr['event_completed'][$courseId][$termId] += (!empty($eventDsMarked) && !empty($eventDsLocked) && ($eventDsMarked == $eventDsLocked) ? 1 : 0);
                                }
                            }
                        }
                    }
                }
            }
        }



        $ciModLockInfo = CiModerationMarkingLock::select('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw('COUNT(id) as total'))
                ->groupBy('course_id', 'term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id')
                ->whereIn('course_id', $courseArr)
                ->get();

        if (!$ciModLockInfo->isEmpty()) {
            foreach ($ciModLockInfo as $info) {
                $eventMksWtArr['event_moderated'][$info->course_id][$info->term_id] = !empty($eventMksWtArr['event_moderated'][$info->course_id][$info->term_id]) ? $eventMksWtArr['event_moderated'][$info->course_id][$info->term_id] : 0;
                $eventMksWtArr['event_moderated'][$info->course_id][$info->term_id] += $info->total;
            }
        }

        //ds obsn marking info
        $dsObsnMksWtInfo = DsObsnMarking::whereIn('course_id', $courseArr)
                ->select('course_id', 'term_id', DB::raw('COUNT(DISTINCT updated_by) as total'))
                ->groupBy('course_id', 'term_id')
                ->get();
        $dsObsnMksWtArr = [];
        if (!$dsObsnMksWtInfo->isEmpty()) {
            foreach ($dsObsnMksWtInfo as $info) {
                $eventMksWtArr['ds_to_be_obsd'][$info->course_id][$info->term_id] = !empty($eventMksWtArr['ds_to_be_obsd'][$info->course_id][$info->term_id]) ? $eventMksWtArr['ds_to_be_obsd'][$info->course_id][$info->term_id] : 0;
                $eventMksWtArr['ds_to_be_obsd'][$info->course_id][$info->term_id] += $info->total;
            }
        }

        //ds obsn marking info
        $dsObsnMksWtLockInfo = DsObsnMarkingLock::whereIn('course_id', $courseArr)
                ->select('course_id', 'term_id', DB::raw('COUNT(locked_by) as total'))
                ->groupBy('course_id', 'term_id')
                ->get();
        $dsObsnMksWtLockArr = [];
        if (!$dsObsnMksWtLockInfo->isEmpty()) {
            foreach ($dsObsnMksWtLockInfo as $info) {
                $eventMksWtArr['ds_obsd'][$info->course_id][$info->term_id] = !empty($eventMksWtArr['ds_obsd'][$info->course_id][$info->term_id]) ? $eventMksWtArr['ds_obsd'][$info->course_id][$info->term_id] : 0;
                $eventMksWtArr['ds_obsd'][$info->course_id][$info->term_id] += $info->total;
            }
        }

        if (!empty($termToCourseArr)) {
            foreach ($termToCourseArr as $courseId => $courseInfo) {
                foreach ($courseInfo as $termId => $termInfo) {
                    if (is_int($termId)) {
                        $eventProgress = $modProgress = $dsObsProgress = 0;

                        $totalEventLocked = !empty($eventMksWtArr['event_completed'][$courseId][$termId]) ? $eventMksWtArr['event_completed'][$courseId][$termId] : 0;
                        $totalEventModerated = !empty($eventMksWtArr['event_moderated'][$courseId][$termId]) ? $eventMksWtArr['event_moderated'][$courseId][$termId] : 0;
                        $totalDsObsd = !empty($eventMksWtArr['ds_obsd'][$courseId][$termId]) ? $eventMksWtArr['ds_obsd'][$courseId][$termId] : 0;
                        $totalWt = !empty($eventMksWtArr['total_wt'][$courseId][$termId]) ? $eventMksWtArr['total_wt'][$courseId][$termId] : 0;

                        if (!empty($eventMksWtArr['event_to_be_locked'][$courseId][$termId])) {
                            $eventProgress = ($totalEventLocked / $eventMksWtArr['event_to_be_locked'][$courseId][$termId]);
                        }
                        if (!empty($eventMksWtArr['event_to_be_moderated'][$courseId][$termId])) {
                            $modProgress = ($totalEventModerated / $eventMksWtArr['event_to_be_moderated'][$courseId][$termId]);
                        }
                        if (!empty($eventMksWtArr['ds_to_be_obsd'][$courseId][$termId])) {
                            $dsObsProgress = ($totalDsObsd / $eventMksWtArr['ds_to_be_obsd'][$courseId][$termId]);
                        }

                        $termToCourseArr[$courseId][$termId]['percent'] = Helper::numberFormat2Digit(($eventProgress + $modProgress + $dsObsProgress) * 100 / 3);
                    }
                }
            }
        }

//        echo '<pre>';
//        print_r($termToCourseArr);
//        exit;
        //********************* End :: term progress *******************//
        //********************* Start :: participation (last 5 courses) ******************//
        $courseId = !empty($course->id) ? $course->id : 0;
        $lastFiveCourseList = Course::join('training_year', 'training_year.id', 'course.training_year_id')
                        ->where('course.status', '!=', '0')
                        ->orderBy('training_year.start_date', 'asc')->orderBy('course.id', 'asc')->limit(5);
        $lastFiveCourseIdList = $lastFiveCourseList->pluck('course.id', 'course.id')->toArray();
        $lastFiveCourseList = $lastFiveCourseList->pluck('course.name', 'course.id')->toArray();

        $courseWiseCmNoList = CmBasicProfile::select('course_id', DB::raw('COUNT(id) as total_cm'))
                        ->groupBy('course_id')->pluck('total_cm', 'course_id')->toArray();

        $eventAssessmentCmList = EventAssessmentMarking::where('course_id', $courseId)
                        ->pluck('cm_id', 'cm_id')->toArray();

        //********************* End :: participation (last 5 courses) *******************//
        //********************* Start :: participation (wing wise) *******************//

        $wingList = Wing::where('status', '1')->orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $wingWiseCmNoList = CmBasicProfile::select('wing_id', DB::raw('COUNT(id) as total_cm'))
                        ->where('course_id', $courseId)
                        ->groupBy('wing_id')->pluck('total_cm', 'wing_id')->toArray();
        
        //********************* End :: participation (wing wise) *******************//
        //START:: Overrall Performance Data
        $getOverAllPerformance = $this->getOverAllPerformance($courseId, $wingWiseCmNoList);
        $overallMksArr = $getOverAllPerformance['overallMksArr'];
        $gradeList = $getOverAllPerformance['gradeList'];
        $maxCm = $getOverAllPerformance['maxCm'];
        $wingWiseMksArr = $getOverAllPerformance['wingWiseMksArr'];
        $wingWiseMksPer = $getOverAllPerformance['wingWiseMksPer'];
        //END:: Overrall Performance Data
        
        //START::Content Summary
        $contentArr = $this->getContentSummary($courseId);
        //END::Content Summary

        if (Auth::user()->group_id == '1') {
            return view('admin.superAdmin.dashboard')->with(compact('request', 'termToCourseArr', 'lastFiveCourseList', 'courseWiseCmNoList', 'course'
                                    , 'wingList', 'wingWiseCmNoList', 'overallMksArr', 'gradeList', 'maxCm', 'courseTotalCm', 'eventAssessmentCmList'
                                    , 'noticeList', 'wingWiseMksPer', 'wingWiseMksArr', 'contentArr'));
        } elseif (Auth::user()->group_id == '2') {
            return view('admin.comdt.dashboard')->with(compact('request', 'termToCourseArr', 'lastFiveCourseList', 'courseWiseCmNoList', 'course'
                                    , 'wingList', 'wingWiseCmNoList', 'overallMksArr', 'gradeList', 'maxCm', 'courseTotalCm', 'eventAssessmentCmList'
                                    , 'noticeList', 'wingWiseMksPer', 'wingWiseMksArr', 'contentArr'));
        } elseif (Auth::user()->group_id == '3') {
            return view('admin.ci.dashboard')->with(compact('request', 'termToCourseArr', 'lastFiveCourseList', 'courseWiseCmNoList', 'course'
                                    , 'wingList', 'wingWiseCmNoList', 'dsApptList', 'overallMksArr', 'gradeList', 'maxCm', 'courseTotalCm'
                                    , 'noticeList', 'eventAssessmentCmList', 'wingWiseMksPer', 'wingWiseMksArr', 'contentArr'));
        } elseif (Auth::user()->group_id == '4') {
            return view('admin.ds.dashboard')->with(compact('request', 'termToCourseArr', 'lastFiveCourseList', 'courseWiseCmNoList', 'course'
                                    , 'wingList', 'wingWiseCmNoList', 'dsApptList', 'overallMksArr', 'gradeList', 'maxCm', 'courseTotalCm'
                                    , 'noticeList', 'eventAssessmentCmList', 'wingWiseMksPer', 'wingWiseMksArr', 'contentArr'));
        }
    }

    public function getOverAllPerformance($courseId, $wingWiseCmNoList) {
        $cmArr = CmBasicProfile::where('cm_basic_profile.course_id', $courseId)
                        ->pluck('cm_basic_profile.id', 'cm_basic_profile.id')->toArray();

        $wingIdList = CmBasicProfile::join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.course_id', $courseId)
                ->where('wing.status', '1')
                ->orderBy('wing.order', 'asc');

        $wingWiseCmArr = $wingIdList->pluck('cm_basic_profile.wing_id', 'cm_basic_profile.id')->toArray();


        $assignedObsnInfo = $gradeInfo = $comdtObsnLockInfo = $ciObsnLockInfo = $maxCm = 0;
        $eventMksWtArr = $eventWiseMksArr = $overallMksArr = $wingWiseMksArr = $wingWiseMksPer = $gradeList = [];

        // Get Assigned CI obsn wt
        $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')
                        ->where('course_id', $courseId)->first();
        $assignedDsObsnInfo = DsObsnMarkingLimit::select('term_id', 'mks_limit', 'obsn')
                        ->where('course_id', $courseId)->get();

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
                ->where('term_to_event.course_id', $courseId)
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
                ->where('term_to_sub_event.course_id', $courseId)
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
                ->where('term_to_sub_sub_event.course_id', $courseId)
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
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                })
                ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                    $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $courseId)
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit'
                        , 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event_to_sub_event.avg_marking')
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
                ->where('event_assessment_marking.course_id', $courseId)
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
                ->where('ci_moderation_marking.course_id', $courseId)
                ->select('ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                        , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.wt')
                ->get();

        if (!$ciModWiseMksInfo->isEmpty()) {
            foreach ($ciModWiseMksInfo as $ciMksInfo) {
                if (!empty($ciMwInfo->mks) && empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id])) {
                    $cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] = !empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id]) ? $cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] : 0;
                    $cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] += 1;
                }
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
                ->where('comdt_moderation_marking.course_id', $courseId)
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
                ->where('ds_obsn_marking.course_id', $courseId)
                ->select('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id', DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt')
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
            foreach ($cmArr as $cmId => $cmId) {
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

                                            if ($totalCount != 0 && $unitMksLimit != 0 && $unitWtLimit != 0) {
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
                ->where('ci_obsn_marking.course_id', $courseId)
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
                ->where('comdt_obsn_marking.course_id', $courseId)
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
                if (!empty($gradeArr)) {
                    foreach ($gradeArr as $letter => $gradeRange) {
                        $overallMksArr[$gradeRange['id']] = !empty($overallMksArr[$gradeRange['id']]) ? $overallMksArr[$gradeRange['id']] : 0;
                        $wingWiseMksArr[$gradeRange['id']][$wingWiseCmArr[$cmId]] = !empty($wingWiseMksArr[$gradeRange['id']][$wingWiseCmArr[$cmId]]) ? $wingWiseMksArr[$gradeRange['id']][$wingWiseCmArr[$cmId]] : 0;

                        if ($totalPercentage == 100) {
                            $overallMksArr[$gradeRange['id']] += 1;
                            $wingWiseMksArr[$gradeRange['id']][$wingWiseCmArr[$cmId]] += 1;
                        } elseif ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                            $overallMksArr[$gradeRange['id']] += 1;
                            $wingWiseMksArr[$gradeRange['id']][$wingWiseCmArr[$cmId]] += 1;
                        }
                    }
                }
            }
            $maxCm = max($overallMksArr);
        }

        if (!empty($wingWiseMksArr)) {
            foreach ($wingWiseMksArr as $gradeId => $gradeInfo) {
                foreach ($gradeInfo as $wingId => $number) {
                    $number = !empty($number) ? $number : 0;
                    $numberOfCm = !empty($wingWiseCmNoList[$wingId]) ? $wingWiseCmNoList[$wingId] : 0;
                    $wingWiseMksPer[$gradeId][$wingId] = !empty($numberOfCm) ? (($number * 100) / $numberOfCm) : 0;
                }
            }
        }
        
//        echo '<pre>';
//        print_r($wingWiseCmArr);
//        print_r($wingWiseMksArr);
//        print_r($wingWiseMksPer);
//        exit;

        return [
            'overallMksArr' => $overallMksArr,
            'maxCm' => $maxCm,
            'gradeList' => $gradeList,
            'wingWiseMksArr' => $wingWiseMksArr,
            'wingWiseMksPer' => $wingWiseMksPer,
        ];
    }
    
    public function getContentSummary($courseId) {
        return Common::getContentSummary($courseId);
    }

    public function requestCourseSatatusSummary(Request $request) {
        $loadView = 'admin.showCourseStatusSummary';
        return Common::requestCourseSatatusSummary($request, $loadView);
    }

    public function getDsMarkingSummary(Request $request) {
        $loadView = 'admin.showDsMarkingSummaryModal';
        return Common::getDsMarkingSummary($request, $loadView);
    }

}

