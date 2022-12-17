<?php

namespace App\Http\Controllers;

use Validator;
use App\Course;
use App\TrainingYear;
use App\User;
use App\Term;
use App\TermToCourse;
use App\CmMarkingGroup;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\AssessmentActDeact;
use App\MarkingGroup;
use App\CmGroupMemberTemplate;
use App\MutualAssessmentMarking;
use App\MutualAssessmentMarkingLock;
use Response;
use App\MaProcess;
use Auth;
use Common;
use DB;
use Illuminate\Http\Request;

class MutualAssessmentSubmissionStateController extends Controller {

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

        $activeTermInfId = !empty($activeTermInfo->id) ? $activeTermInfo->id : 0;
        $courseName = Course::select('name')->where('id', $courseList->id)->first();
        $termName = !empty($activeTermInfo->id) ? Term::select('name')->where('id', $activeTermInfo->id)->first() : [];


        $assessmentActDeactInfo = AssessmentActDeact::where('course_id', $courseList->id)
                ->where('term_id', $activeTermInfo->id)->where('status', '1')->where('criteria', '5')
                ->select('criteria', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id', 'status')
                ->get();
        $assessmentActDeactArr = [];
        if (!$assessmentActDeactInfo->isEmpty()) {
            foreach ($assessmentActDeactInfo as $info) {
                $assessmentActDeactArr[$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id] = $info->status;
            }
        }

        $maProcessInfo = MaProcess::where('course_id', $courseList->id)->where('term_id', $activeTermInfo->id)
                        ->select('process')->first();

        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';

        $maEventMksWtArr = [];
        if ($maProcess == '3') {
            $maEventMksWtArr = $this->getEventList($request, $courseList->id, $activeTermInfo->id);
        } elseif (in_array($maProcess, ['1', '2'])) {
            $maEventMksWtArr['mks_wt'][0][0][0][0]['name'] = ($maProcess == '1' ? __('label.SYN') : ($maProcess == '2' ? __('label.SUB_SYN') : ''));
        }

        //Start :: CM Marking Data
        $totalMarkingCmInfo = [];
        if ($maProcess == '3') {
            $totalMarkingCmInfo = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('event', 'event.id', 'marking_group.event_id')
                    ->leftJoin('event_to_sub_event', function($join) {
                        $join->on('event_to_sub_event.course_id', '=', 'marking_group.course_id');
                        $join->on('event_to_sub_event.event_id', '=', 'marking_group.event_id');
                        $join->on('event_to_sub_event.sub_event_id', '=', 'marking_group.sub_event_id');
                    })
                    ->leftJoin('event_to_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_event.course_id', '=', 'marking_group.course_id');
                        $join->on('event_to_sub_sub_event.event_id', '=', 'marking_group.event_id');
                        $join->on('event_to_sub_sub_event.sub_event_id', '=', 'marking_group.sub_event_id');
                        $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'marking_group.sub_sub_event_id');
                    })
                    ->leftJoin('event_to_sub_sub_sub_event', function($join) {
                        $join->on('event_to_sub_sub_sub_event.course_id', '=', 'marking_group.course_id');
                        $join->on('event_to_sub_sub_sub_event.event_id', '=', 'marking_group.event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'marking_group.sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'marking_group.sub_sub_event_id');
                        $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'marking_group.sub_sub_sub_event_id');
                    })
                    ->select('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                            , 'marking_group.sub_sub_sub_event_id', DB::raw("COUNT(DISTINCT cm_marking_group.cm_id) as total_cm")
                            , 'event.has_ds_assesment as event_has_ds_assessment'
                            , 'event_to_sub_event.has_ds_assesment as sub_event_has_ds_assessment'
                            , 'event_to_sub_sub_event.has_ds_assesment as sub_sub_event_has_ds_assessment')
                    ->where('marking_group.course_id', $courseList->id)
                    ->where('marking_group.term_id', $activeTermInfo->id)
                    ->where('event.for_ma_grouping', '1')
                    ->groupBy('marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                            , 'marking_group.sub_sub_sub_event_id', 'event.has_ds_assesment', 'event_to_sub_event.has_ds_assesment'
                            , 'event_to_sub_sub_event.has_ds_assesment')
                    ->get();
        } elseif (in_array($maProcess, ['1', '2'])) {
            $totalMarkingCmInfo = CmGroupMemberTemplate::join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                    ->select(DB::raw("COUNT(DISTINCT cm_group_member_template.cm_basic_profile_id) as total_cm"))
                    ->where('cm_group_member_template.course_id', $courseList->id)
                    ->where('cm_group.type', $maProcess)
                    ->where('cm_group_member_template.term_id', $activeTermInfo->id)
                    ->get();
        }



        $totalLockedCmInfo = MutualAssessmentMarkingLock::where('course_id', $courseList->id)
                ->where('term_id', $activeTermInfo->id)
                ->select('event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw("COUNT(marking_cm_id) as total_cm"))
                ->groupBy('event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->get();

        if (!$totalLockedCmInfo->isEmpty()) {
            foreach ($totalLockedCmInfo as $lInfo) {
                $maEventMksWtArr['mks_wt'][$lInfo->event_id][$lInfo->sub_event_id][$lInfo->sub_sub_event_id][$lInfo->sub_sub_sub_event_id]['forwarded'] = $lInfo->total_cm;
            }
        }

        if (!$totalMarkingCmInfo->isEmpty()) {
            foreach ($totalMarkingCmInfo as $mInfo) {
                $eventId = $maProcess == '3' ? $mInfo->event_id : 0;
                $subEventId = $maProcess == '3' && ($mInfo->sub_event_has_ds_assessment == '1' || ($mInfo->event_has_ds_assessment == '0' && $mInfo->sub_event_has_ds_assessment == '0' && $mInfo->sub_sub_event_has_ds_assessment == '1') || ($mInfo->event_has_ds_assessment == '0' && $mInfo->sub_event_has_ds_assessment == '0' && $mInfo->sub_sub_event_has_ds_assessment == '0')) ? $mInfo->sub_event_id : 0;
                $subSubEventId = $maProcess == '3' && ($mInfo->sub_sub_event_has_ds_assessment == '1' || ($mInfo->event_has_ds_assessment == '0' && $mInfo->sub_event_has_ds_assessment == '0' && $mInfo->sub_sub_event_has_ds_assessment == '0')) ? $mInfo->sub_sub_event_id : 0;
                $subSubSubEventId = $maProcess == '3' && $mInfo->event_has_ds_assessment == '0' && $mInfo->sub_event_has_ds_assessment == '0' && $mInfo->sub_sub_event_has_ds_assessment == '0' ? $mInfo->sub_sub_sub_event_id : 0;
                
//                $dds[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['total'] = $mInfo->total_cm;
                
                $forwarded = !empty($maEventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['forwarded']) ? $maEventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['forwarded'] : 0;

                $maEventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['total'] = $mInfo->total_cm;
                $maEventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['not_forwarded'] = $mInfo->total_cm - $forwarded;
            }
        }

//        echo '<pre>';
//        print_r($maEventMksWtArr['mks_wt']);
//        exit;
        //End :: CM Marking Data




        return view('mutualAssessmentSubmissionState.index')->with(compact('activeTrainingYearInfo', 'courseList', 'request', 'activeTermInfo'
                                , 'courseName', 'termName', 'assessmentActDeactArr', 'maProcess', 'maEventMksWtArr'));
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

    public function getEventList(Request $request, $courseId, $termId) {
        $eventMksWtArr = [];
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseId)
                ->where('term_to_event.term_id', $termId)
                ->where('event.for_ma_grouping', '1')
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event.has_ds_assesment')
                ->orderBy('event.event_code', 'asc')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                $eventMksWtArr['event'][$ev->event_id]['name'] = $ev->event_code ?? '';

                if ($ev->has_ds_assesment == '1') {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['name'] = $ev->event_code ?? '';
                }
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
                ->where('term_to_sub_event.course_id', $courseId)
                ->where('term_to_sub_event.term_id', $termId)
                ->where('event.for_ma_grouping', '1')
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'event_to_sub_event.has_ds_assesment'
                        , 'event_to_sub_event.event_id', 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['event'][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                $eventMksWtArr['event'][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';

                if ($subEv->has_ds_assesment == '1' && $subEv->event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['name'] = $subEv->sub_event_code ?? '';
                }
            }
        }

        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                })
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $courseId)
                ->where('term_to_sub_sub_event.term_id', $termId)
                ->where('event.for_ma_grouping', '1')
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'event_to_sub_sub_event.has_ds_assesment'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                        , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment'
                        , 'event_to_sub_event.has_ds_assesment as sub_event_has_ds_assessment')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                $eventMksWtArr['event'][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';

                if ($subSubEv->has_ds_assesment == '1' && $subSubEv->event_has_ds_assessment == '0' && $subSubEv->sub_event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['name'] = $subSubEv->sub_sub_event_code ?? '';
                }
            }
        }

        //sub sub sub event info
        $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                })
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                })
                ->join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $courseId)
                ->where('term_to_sub_sub_sub_event.term_id', $termId)
                ->where('event.for_ma_grouping', '1')
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment'
                        , 'event_to_sub_event.has_ds_assesment as sub_event_has_ds_assessment'
                        , 'event_to_sub_sub_event.has_ds_assesment as sub_sub_event_has_ds_assessment')
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

                if ($subSubSubEv->event_has_ds_assessment == '0' && $subSubSubEv->sub_event_has_ds_assessment == '0' && $subSubSubEv->sub_sub_event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';
                }
            }
        }

        return $eventMksWtArr;
    }

    public function getCmMarkingSummary(Request $request) {
        $loadView = 'mutualAssessmentSubmissionState.showCmMarkingSummaryModal';
        return Common::getCmMarkingSummary($request, $loadView);
    }

}
