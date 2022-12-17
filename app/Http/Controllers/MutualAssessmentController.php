<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Term;
use App\MutualAssessmentEvent;
use App\Course;
use App\TermToCourse;
use App\TermToMAEvent;
use App\CmGroupToCourse;
use App\CmGroup;
use App\MaGroup;
use App\CmMaGroup;
use App\EventGroup;
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
use App\MaProcess;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\CmGroupMemberTemplate;
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
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MutualAssessmentController extends Controller {

    private $controller = 'MutualAssessment';

    public function markingSheet(Request $request) {
//get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.GENERATE_MARKING_SHEET');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }
//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.GENERATE_MARKING_SHEET');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $courseId = $activeCourse->id;
        $activeTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name as name', 'term.id as id')
                        ->where('term_to_course.course_id', $courseId)
                        ->where('term_to_course.status', '1')
                        ->where('term_to_course.active', '1')
                        ->orderBy('term.order', 'asc')->first();

        if (empty($activeTerm)) {
            $void['header'] = __('label.GENERATE_MARKING_SHEET');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $activeCourse->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }

        $maProcessInfo = MaProcess::where('course_id', $courseId)->where('term_id', $activeTerm->id)
                        ->select('process')->first();

        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';

        $eventsList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $courseId)
                        ->where('term_to_event.term_id', $activeTerm->id)
                        ->where('event.for_ma_grouping', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $synList = ['0' => __('label.SELECT_SYN_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $courseId)->where('cm_group.type', 1)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $courseId)->where('cm_group.type', 2)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();


        return view('mutualAssessment.generateMarkingSheet', compact('activeTrainingYearInfo', 'activeCourse'
                        , 'activeTerm', 'eventsList', 'maProcess', 'synList', 'subSynList'));
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
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $has = Event::where('id', $request->event_id)->where('has_ds_assesment', '1')->first();

        if ((!empty($has))) {
            $html = Self::getEventGroups($request);
        } else {
            $html = view('mutualAssessment.showSubEvent', compact('subEventList'))->render();
        }

        return response()->json(['html' => $html]);
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
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();
        $has = EventToSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('has_ds_assesment', '1')->first();
//        echo '<pre>';        print_r($hasDsAssesment);        exit;

        if ((!empty($has))) {
            $html = Self::getEventGroups($request);
        } else {
            $html = view('mutualAssessment.showSubSubEvent', compact('subSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
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

        $has = EventToSubSubEvent::where('event_id', $request->event_id)
                        ->where('sub_event_id', $request->sub_event_id)
                        ->where('sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('has_ds_assesment', '1')->first();

        if ((!empty($has))) {
            $html = Self::getEventGroups($request);
        } else {
            $html = view('mutualAssessment.showSubSubSubEvent', compact('subSubSubEventList'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function getEventGroups(Request $request) {

        $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + EventToEventGroup::join('event_group', 'event_group.id', 'event_to_event_group.event_group_id')
                        ->where('event_to_event_group.course_id', $request->course_id)
                        ->where('event_to_event_group.event_id', $request->event_id)
                        ->where('event_group.status', '1')
                        ->orderBy('event_group.order', 'asc')
                        ->pluck('event_group.name', 'event_group.id')
                        ->toArray();

        $html = view('mutualAssessment.showEventGroup', compact('eventGroupList'))->render();

        return $html;
    }

    public function getEventGroup(Request $request) {

        $html = Self::getEventGroups($request);
        return response()->json(['html' => $html]);
    }

    public function getFactor(Request $request) {
        $factorList = MutualAssessmentEvent::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();

        return $factorList;
    }

    public function getSyn(Request $request) {
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $maEventId = $request->maEvent_id;

        $maProcessInfo = TermToMAEvent::where('course_id', $courseId)->where('term_id', $termId)
                ->where('event_id', $maEventId)->where('event_wise_grouping', '1')
                ->first();
        $maProcess = !empty($maProcessInfo) ? 1 : 0;

        $eventGroupList = ['0' => __('label.SELECT_EVENT_GROUP_OPT')] + MaGroup::join('event_group', 'event_group.id', 'ma_group.event_group_id')
                        ->where('ma_group.course_id', $courseId)->where('ma_group.term_id', $termId)
                        ->where('ma_group.ma_event_id', $maEventId)->orderBy('event_group.name', 'asc')
                        ->pluck('event_group.name', 'event_group.id')->toArray();

        $syndicateList = ['0' => __('label.SELECT_SYNDICATE_OPT')];
        if (empty($maProcess)) {
            if (!empty($courseId)) {
                $syndicateList = $syndicateList + SynToCourse::join('syndicate', 'syndicate.id', '=', 'syn_to_course.syn_id')
                                ->where('syn_to_course.course_id', $courseId)
                                ->where('syndicate.status', '1')->orderBy('syndicate.order', 'asc')
                                ->pluck('syndicate.name', 'syndicate.id')->toArray();
            }
        }
        $html = view('mutualAssessment.showSyn', compact('syndicateList', 'eventGroupList', 'hasGrouping'))->render();

        return Response::json(['success' => true, 'html' => $html], 200);
    }

    public function getCmAndSubSyndicate(Request $request) {
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;
        $html = $html1 = '';

//        $courseName = Course::select('name')->where('id', $courseId)->first();
//        $term = Term::select('name')->where('id', $termId)->first();
//        $eventName = MutualAssessmentEvent:: select('name')->where('id', $eventId)->first();
//        $syndicate = Syndicate::select('name')->where('id', $synId)->first();
        $exportCmIdArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syn_id', $synId)
                        ->where('sub_syn_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->pluck('marking_cm_id', 'marking_cm_id')->toArray();

        $deliverStatusArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syn_id', $synId)
                        ->where('sub_syn_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->where('deliver_status', '1')
                        ->pluck('marking_cm_id', 'id')->toArray();
//print_r($deliverStatusArr); exit;
        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                            ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->where('cm_group_member_template.course_id', $courseId)
                            ->where('cm_group_member_template.term_id', $termId)
                            ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                            ->where('cm_basic_profile.status', '1')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();
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
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();


            $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->whereIn('cm_basic_profile.id', $prevCmArr)
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();
        }

//print_r($subSyndicateList);

        $html1 = view('mutualAssessment.showCmList', compact('cmList', 'courseId', 'termId', 'eventId', 'synId', 'subSynId'
                        , 'exportCmIdArr', 'deliverStatusArr'))->render();

        return Response::json(['success' => true, 'cmList' => $html1], 200);
    }

    public function getCmbySubSyn(Request $request) {
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmList = [];
//        $courseName = Course::select('name')->where('id', $courseId)->first();
//        $term = Term::select('name')->where('id', $termId)->first();
//        $eventName = MutualAssessmentEvent:: select('name')->where('id', $eventId)->first();
//        $syndicate = Syndicate::select('name')->where('id', $synId)->first();

        $exportCmIdArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('syn_id', $synId)
                        ->pluck('marking_cm_id', 'id')->toArray();

        $deliverStatusArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('syn_id', $synId)
                        ->where('deliver_status', '1')
                        ->pluck('marking_cm_id', 'id')->toArray();


        $cmList = CmToSyn::join('cm_basic_profile', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                        ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name', 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank', 'cm_basic_profile.photo as photo')
                        ->where('cm_to_syn.course_id', $courseId)
                        ->where('cm_to_syn.term_id', $termId)
                        ->where('cm_to_syn.syn_id', $synId)
                        ->where('cm_basic_profile.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();
//print_r($subSyndicateList);

        $cmList = view('mutualAssessment.showCmList', compact('cmList', 'courseId', 'termId', 'eventId', 'synId', 'subSynId', 'exportCmIdArr', 'deliverStatusArr'))->render();
        return Response::json(['success' => true, 'cmList' => $cmList], 200);
    }

    public function getPreviewBtn(Request $request) {

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $synId = $request->syn_id;
        $eventId = $request->event_id;
        $cmId = $request->cm_id;
        $cmList = CmToSyn::join('cm_basic_profile', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                        ->select('cm_basic_profile.full_name as full_name', 'cm_basic_profile.id as id')
                        ->where('cm_to_syn.course_id', $courseId)
                        ->where('cm_to_syn.term_id', $termId)
                        ->where('cm_to_syn.syn_id', $synId)
                        ->where('cm_basic_profile.status', '1')
                        ->where('cm_basic_profile.id', '!=', $cmId)->count();
        $html = '';
        if (!empty($cmList) && !empty($cmId)) {
            $html = '<div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-8">
                            <button class="btn btn-circle green" type="button" id="previewMarkingSheet">
                                <i class="fa fa-check"></i> ' . __('label.PREVIEW_MARKING_SHEET') .
                    '</button>
                        </div>
                    </div>
                </div>';
        }
        return Response::json(['success' => true, 'html' => $html], 200);
    }

    public function previewMarkingSheet(Request $request) {
        $rules = array(
            'course_id' => 'required',
            'term_id' => 'required',
        );
        $messages = array(
            'course_id.required' => __('label.COURSE_MUST_BE_SELECTED'),
            'term_id.required' => __('label.TERM_MUST_BE_SELECTED'),
        );

        $maProcess = $request->ma_process;

        if ($maProcess == '1') {
            $rules['syn_id'] = 'required';
            $messages['syn_id.required'] = __('label.SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '2') {
            $rules['sub_syn_id'] = 'required';
            $messages['sub_syn_id.required'] = __('label.SUB_SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '3') {
            $rules['event_group_id'] = 'required';
            $messages['event_group_id.required'] = __('label.EVENT_GROUP_MUST_BE_SELECTED');
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;
//print_r($request->all());
        $courseName = Course::select('name')->where('id', $courseId)->first();
        $term = Term::select('name')->where('id', $termId)->first();
        $eventName = Event:: select('event_code as name')->where('id', $eventId)->first();
        $subEventName = SubEvent:: select('event_code as name')->where('id', $subEventId)->first();
        $subSubEventName = SubSubEvent:: select('event_code as name')->where('id', $subSubEventId)->first();
        $subSubSubEventName = SubSubSubEvent:: select('event_code as name')->where('id', $subSubSubEventId)->first();
        $syndicate = CmGroup::select('name')->where('id', $synId)->first();
        $eventGroup = EventGroup::select('name')->where('id', $eventGroupId)->first();
        $subSyndicate = CmGroup::select('name')->where('id', $subSynId)->first();

        $factorList = self::getFactor($request);

        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                            ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->where('cm_group_member_template.course_id', $courseId)
                            ->where('cm_group_member_template.term_id', $termId)
                            ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                            ->where('cm_basic_profile.status', '1')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get();
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
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();


            $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->whereIn('cm_basic_profile.id', $prevCmArr)
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get();
        }

        if ($cmList->isEmpty()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.NO_CM_FOUND')], 401);
        }
        $html = '';
        if (!$cmList->isEmpty()) {
            $html = view('mutualAssessment.previewMarkingSheet', compact('request', 'cmList', 'courseId', 'termId'
                            , 'eventId', 'subEventId', 'subSubEventId', 'subSubSubEventId', 'synId', 'subSynId'
                            , 'eventGroupId', 'maProcess', 'courseName', 'term', 'syndicate'
                            , 'eventGroup', 'subSyndicate', 'eventName', 'subEventName'
                            , 'subSubEventName', 'subSubSubEventName', 'factorList'))->render();
        }
        return Response::json(['success' => true, 'html' => $html], 200);
    }

    public function generate(Request $request) {

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;

        $courseInfo = Course::join('training_year', 'training_year.id', '=', 'course.training_year_id')->select('course.name as course_name', 'training_year.name as training_year_name')->where('course.id', $courseId)->first();
        $courseName = Course::select('name')->where('id', $courseId)->first();
        $term = Term::select('name')->where('id', $termId)->first();
        $eventName = Event:: select('event_code as name')->where('id', $eventId)->first();
        $subEventName = SubEvent:: select('event_code as name')->where('id', $subEventId)->first();
        $subSubEventName = SubSubEvent:: select('event_code as name')->where('id', $subSubEventId)->first();
        $subSubSubEventName = SubSubSubEvent:: select('event_code as name')->where('id', $subSubSubEventId)->first();
        $syndicate = CmGroup::select('name')->where('id', $synId)->first();
        $eventGroup = EventGroup::select('name')->where('id', $eventGroupId)->first();
        $subSyndicate = CmGroup::select('name')->where('id', $subSynId)->first();

        $factorList = self::getFactor($request);

        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                    ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('cm_group_member_template.course_id', $courseId)
                    ->where('cm_group_member_template.term_id', $termId)
                    ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                    ->where('cm_basic_profile.status', '1')
                    ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                            , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                            , 'cm_basic_profile.photo as photo')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->orderBy('cm_basic_profile.full_name', 'asc');
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
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();


            $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                            , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                            , 'cm_basic_profile.photo as photo')
                    ->whereIn('cm_basic_profile.id', $prevCmArr)
                    ->where('cm_basic_profile.status', '1')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->orderBy('cm_basic_profile.full_name', 'asc');
        }

        $cmIdList = $cmList->pluck('cm_id', 'cm_id')->toArray();
        $cmList = $cmList->get();



        $exportInfo = MaMksExport::select('id', 'factor_id', 'marking_cm_id')
                ->where('course_id', $courseId)
                ->where('term_id', $termId)
                ->where('event_id', $eventId)
                ->where('sub_event_id', $subEventId)
                ->where('sub_sub_event_id', $subSubEventId)
                ->where('sub_sub_sub_event_id', $subSubSubEventId)
                ->where('syn_id', $synId)
                ->where('sub_syn_id', $subSynId)
                ->where('event_group_id', $eventGroupId)
                ->get();

        $html = '';

        $expDataArr = [];
        $expI = 0;
        $prevMkCmArr = $prevFactorArr = [];
        if (!$cmList->isEmpty()) {
            if (!$exportInfo->isEmpty()) {
                foreach ($exportInfo as $eInfo) {
                    if (in_array($eInfo->marking_cm_id, $cmIdList) && array_key_exists($eInfo->factor_id, $factorList)) {
                        $export = MaMksExport::find($eInfo->id);
                        $prevMkCmArr[$eInfo->marking_cm_id] = $eInfo->marking_cm_id;
                        $prevFactorArr[$eInfo->factor_id] = $eInfo->factor_id;
                        $export->exported_at = date('Y-m-d H:i:s');
                        $export->exported_by = Auth::user()->id;
                        $export->save();
                    } else {
                        MaMksExport::where('id', $eInfo->id)->delete();
                    }
                }


                if (!empty($cmIdList)) {
                    foreach ($cmIdList as $cmId => $cmId) {
                        if (!empty($factorList)) {
                            foreach ($factorList as $factorId => $factor) {
                                if (!in_array($cmId, $prevMkCmArr) || !in_array($factorId, $prevFactorArr)) {
                                    $expDataArr[$expI]['course_id'] = $courseId;
                                    $expDataArr[$expI]['term_id'] = $termId;
                                    $expDataArr[$expI]['event_id'] = $eventId;
                                    $expDataArr[$expI]['sub_event_id'] = $subEventId;
                                    $expDataArr[$expI]['sub_sub_event_id'] = $subSubEventId;
                                    $expDataArr[$expI]['sub_sub_sub_event_id'] = $subSubSubEventId;
                                    $expDataArr[$expI]['syn_id'] = $synId;
                                    $expDataArr[$expI]['sub_syn_id'] = $subSynId;
                                    $expDataArr[$expI]['event_group_id'] = $eventGroupId;
                                    $expDataArr[$expI]['factor_id'] = $factorId;
                                    $expDataArr[$expI]['marking_cm_id'] = $cmId;
                                    $expDataArr[$expI]['exported_at'] = date('Y-m-d H:i:s');
                                    $expDataArr[$expI]['exported_by'] = Auth::user()->id;

                                    $expI++;
                                }
                            }
                        }
                    }
                }

                MaMksExport::insert($expDataArr);
            } else {
                if (!empty($cmIdList)) {
                    foreach ($cmIdList as $cmId => $cmId) {
                        if (!empty($factorList)) {
                            foreach ($factorList as $factorId => $factor) {
                                $expDataArr[$expI]['course_id'] = $courseId;
                                $expDataArr[$expI]['term_id'] = $termId;
                                $expDataArr[$expI]['event_id'] = $eventId;
                                $expDataArr[$expI]['sub_event_id'] = $subEventId;
                                $expDataArr[$expI]['sub_sub_event_id'] = $subSubEventId;
                                $expDataArr[$expI]['sub_sub_sub_event_id'] = $subSubSubEventId;
                                $expDataArr[$expI]['syn_id'] = $synId;
                                $expDataArr[$expI]['sub_syn_id'] = $subSynId;
                                $expDataArr[$expI]['event_group_id'] = $eventGroupId;
                                $expDataArr[$expI]['factor_id'] = $factorId;
                                $expDataArr[$expI]['marking_cm_id'] = $cmId;
                                $expDataArr[$expI]['exported_at'] = date('Y-m-d H:i:s');
                                $expDataArr[$expI]['exported_by'] = Auth::user()->id;

                                $expI++;
                            }
                        }
                    }
                }



                MaMksExport::insert($expDataArr);
            }

            $viewFile = 'mutualAssessment.excelMarkingSheet';
            $downLoadFileName = $courseInfo->training_year_name . '_'
                    . $courseName->name . '_'
                    . $term->name . '_'
                    . (!empty($synId) ? ($syndicate->name) : '')
                    . (!empty($subSynId) ? ($subSyndicate->name) : '')
                    . (!empty($eventId) ? ($eventName->name . '_') : '')
                    . (!empty($subEventId) ? ($subEventName->name . '_') : '')
                    . (!empty($subSubEventId) ? ($subSubEventName->name . '_') : '')
                    . (!empty($subSubSubEventId) ? ($subSubSubEventName->name . '_') : '')
                    . (!empty($eventGroupId) ? ($eventGroup->name) : '')
                    . '.xlsx';

            return Excel::download(new ExcelExport($viewFile, compact('request', 'cmList', 'courseId', 'termId'
                                    , 'eventId', 'subEventId', 'subSubEventId', 'subSubSubEventId', 'synId', 'subSynId'
                                    , 'eventGroupId', 'factorList', 'maProcess', 'courseName', 'term', 'syndicate'
                                    , 'eventGroup', 'subSyndicate', 'eventName', 'subEventName'
                                    , 'subSubEventName', 'subSubSubEventName'), 1), $downLoadFileName);
        }
    }

    public function changeDeliverStatus(Request $request) {
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $factorId = $request->factor_id;
        $maProcess = $request->ma_process;
        $cmId = $request->cm_id;
// print_r($request->all());

        $exportInfo = MaMksExport::select('id', 'deliver_status')
                        ->where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syn_id', $synId)
                        ->where('sub_syn_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->where('factor_id', $factorId)
                        ->where('marking_cm_id', $cmId)->first();


        if (!empty($exportInfo)) {
            $newStatus = $exportInfo->deliver_status = empty($exportInfo->deliver_status) ? '1' : '0';
            if ($exportInfo->save()) {
                if ($newStatus == 1) {
                    $message = __('label.STATUS_HAS_BEEN_CHANGED_TO_DELIVERED');
                } else {
                    $message = __('label.STATUS_HAS_BEEN_CHANGED_TO_NOT_DELIVERED');
                }

                return Response::json(['success' => true, 'heading' => 'Success', 'message' => $message], 200);
            }
        } else {
            $message = __('label.SOMETING_WRONG');
            return Response::json(['success' => false, 'heading' => 'Error', 'message' => $message], 400);
        }
    }

    public function importMarkingSheet(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.IMPORT_MARKING_SHEET');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }
//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.IMPORT_MARKING_SHEET');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $courseId = $activeCourse->id;
        $eventsList = ['0' => __('label.SELECT_EVENT_OPT')];
        $activeTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name as name', 'term.id as id')
                        ->where('term_to_course.course_id', $courseId)
                        ->where('term_to_course.status', '1')
                        ->where('term_to_course.active', '1')
                        ->orderBy('term.order', 'asc')->first();
        if (empty($activeTerm)) {
            $void['header'] = __('label.IMPORT_MARKING_SHEET');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $activeCourse->name, 'training_year' => $activeTrainingYearInfo->name]);
            return view('layouts.void', compact('void'));
        }

        $maProcessInfo = MaProcess::where('course_id', $courseId)->where('term_id', $activeTerm->id)
                        ->select('process')->first();

        $maProcess = !empty($maProcessInfo->process) ? $maProcessInfo->process : '0';

        $eventsList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $courseId)
                        ->where('term_to_event.term_id', $activeTerm->id)
                        ->where('event.for_ma_grouping', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $synList = ['0' => __('label.SELECT_SYN_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $courseId)->where('cm_group.type', 1)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        $subSynList = ['0' => __('label.SELECT_SUB_SYN_OPT')] + CmGroupToCourse::join('cm_group', 'cm_group.id', 'cm_group_to_course.cm_group_id')
                        ->where('cm_group_to_course.course_id', $courseId)->where('cm_group.type', 2)
                        ->orderBy('cm_group.order', 'asc')
                        ->pluck('cm_group.name', 'cm_group.id')->toArray();

        return view('mutualAssessment.importMarkingSheet', compact('activeTrainingYearInfo', 'activeCourse'
                        , 'activeTerm', 'eventsList', 'maProcess', 'synList', 'subSynList'));
    }

    public function getSubsynAndCmList(Request $request) {

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;
        $html = $html1 = '';

//        $courseName = Course::select('name')->where('id', $courseId)->first();
//        $term = Term::select('name')->where('id', $termId)->first();
//        $eventName = MutualAssessmentEvent:: select('name')->where('id', $eventId)->first();
//        $syndicate = Syndicate::select('name')->where('id', $synId)->first();
        $exportCmIdArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syn_id', $synId)
                        ->where('sub_syn_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->pluck('marking_cm_id', 'marking_cm_id')->toArray();

        $deliverStatusArr = MaMksExport::where('course_id', $courseId)
                        ->where('term_id', $termId)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syn_id', $synId)
                        ->where('sub_syn_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->where('deliver_status', '1')
                        ->pluck('marking_cm_id', 'id')->toArray();
//print_r($deliverStatusArr); exit;
        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                            ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->where('cm_group_member_template.course_id', $courseId)
                            ->where('cm_group_member_template.term_id', $termId)
                            ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                            ->where('cm_basic_profile.status', '1')
                            ->select('cm_basic_profile.id as cm_id', DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (',cm_basic_profile.personal_no, ')') as cm_name"))
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->pluck('cm_name', 'cm_id')->toArray();
        } elseif (in_array($maProcess, ['3'])) {
            $cmList = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                    ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $cmList = $cmList->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $cmList = $cmList->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $cmList = $cmList->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }
            $cmList = $cmList->where('marking_group.event_group_id', $request->event_group_id)
                            ->select('cm_basic_profile.id as cm_id', DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (',cm_basic_profile.personal_no, ')') as cm_name"))
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->pluck('cm_name', 'cm_id')->toArray();
        }


        $cmList = ['0' => __('label.SELECT_CM_OPT')] + $cmList;

        $html = view('mutualAssessment.showCmOptions', compact('cmList'))->render();

        return Response::json(['success' => true, 'cmList' => $html], 200);
    }

    public function getCmListBySubSyn(Request $request) {
        $courseId = $request->course_id;
        $termId = $request->term_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;

        $cmList = ['0' => __('label.SELECT_CM_OPT')] + CmToSyn::join('cm_basic_profile', 'cm_to_syn.cm_id', '=', 'cm_basic_profile.id')
                        ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select('cm_basic_profile.id as cm_id', DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (',cm_basic_profile.personal_no, ')') as cm_name"))
                        ->where('cm_to_syn.course_id', $courseId)
                        ->where('cm_to_syn.term_id', $termId)
                        ->where('cm_to_syn.syn_id', $synId)
                        ->where('cm_basic_profile.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')
                        ->pluck('cm_name', 'cm_id')->toArray();

        $cmList = view('mutualAssessment.showCmOptions', compact('cmList'))->render();

        return Response::json(['success' => true, 'cmList' => $cmList], 200);
    }

    public function getFileUploader(Request $request) {
        $rules = array(
            'course_id' => 'required',
            'term_id' => 'required',
            'cm_id' => 'required',
        );
        $messages = array(
            'course_id.required' => __('label.COURSE_MUST_BE_SELECTED'),
            'term_id.required' => __('label.TERM_MUST_BE_SELECTED'),
            'cm_id.required' => __('label.COURSE_MEMBER_MUST_BE_SELECTED'),
        );

        $maProcess = $request->ma_process;

        if ($maProcess == '1') {
            $rules['syn_id'] = 'required';
            $messages['syn_id.required'] = __('label.SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '2') {
            $rules['sub_syn_id'] = 'required';
            $messages['sub_syn_id.required'] = __('label.SUB_SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '3') {
            $rules['event_group_id'] = 'required';
            $messages['event_group_id.required'] = __('label.EVENT_GROUP_MUST_BE_SELECTED');
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;
        $cmId = $request->cm_id;
//print_r($request->all());
        $courseName = Course::select('name')->where('id', $courseId)->first();
        $term = Term::select('name')->where('id', $termId)->first();
        $eventName = Event:: select('event_code as name')->where('id', $eventId)->first();
        $subEventName = SubEvent:: select('event_code as name')->where('id', $subEventId)->first();
        $subSubEventName = SubSubEvent:: select('event_code as name')->where('id', $subSubEventId)->first();
        $subSubSubEventName = SubSubSubEvent:: select('event_code as name')->where('id', $subSubSubEventId)->first();
        $syndicate = CmGroup::select('name')->where('id', $synId)->first();
        $eventGroup = EventGroup::select('name')->where('id', $eventGroupId)->first();
        $subSyndicate = CmGroup::select('name')->where('id', $subSynId)->first();
        $factorList = self::getFactor($request);

        $cmName = CmBasicProfile::join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' (', cm_basic_profile.personal_no, ')') as name"))
                        ->where('cm_basic_profile.id', $cmId)->first();

        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                            ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->where('cm_group_member_template.course_id', $courseId)
                            ->where('cm_group_member_template.term_id', $termId)
                            ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                            ->where('cm_basic_profile.status', '1')
                            ->where('cm_basic_profile.id', '!=', $cmId)
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get();
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
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();


            $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->whereIn('cm_basic_profile.id', $prevCmArr)
                            ->where('cm_basic_profile.id', '!=', $cmId)
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get();
        }

        $lockInfo = MutualAssessmentMarkingLock::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $request->ma_event_id)
                        ->where('syndicate_id', $request->syn_id)
                        ->where('event_group_id', $request->event_group_id)
                        ->where('marking_cm_id', $request->cm_id)
                        ->where('lock_status', '1')->get();


        $prevMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $eventId)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('syndicate_id', $synId)
                        ->where('sub_syndicate_id', $subSynId)
                        ->where('event_group_id', $eventGroupId)
                        ->where('marking_cm_id', $cmId)
                        ->select('cm_id', 'factor_id', 'position')->get();
        $prevMarkingArr = [];
        if (!$prevMutualAssessmentMarking->isEmpty()) {
            foreach ($prevMutualAssessmentMarking as $mkInfo) {
                $prevMarkingArr[$mkInfo->cm_id][$mkInfo->factor_id] = $mkInfo->position;
            }
        }
        $markingSheet = $fileUpload = '';
        if (!$cmList->isEmpty() && !empty($cmId)) {
            $markingSheet = view('mutualAssessment.showMarkingSheet', compact('request', 'cmList', 'courseId', 'termId'
                            , 'eventId', 'subEventId', 'subSubEventId', 'subSubSubEventId', 'synId', 'subSynId'
                            , 'eventGroupId', 'cmId', 'maProcess', 'courseName', 'term', 'syndicate'
                            , 'eventGroup', 'factorList', 'subSyndicate', 'cmName', 'eventName', 'subEventName'
                            , 'subSubEventName', 'subSubSubEventName', 'prevMarkingArr'))->render();
            if ($lockInfo->isEmpty()) {
                $fileUpload = '<div class="form-group">
                    <div class="row">
                    <div class="col-md-6">
                        <label class="control-label col-md-4" for="markingSheet">' . __('label.UPLOAD_MARKING_SHEET') . ':<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <span class="btn green btn-file">
                                                                <span class="fileinput-new"> Select file </span>
                                                                <span class="fileinput-exists"> Change </span>
                                                                <input type="hidden" value="" name="...">
                                                                <input type="file" name="marking_sheet" id="markingSheet"> </span>
                                                            <span class="fileinput-filename"></span> &nbsp;
                                                            <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                                                        </div>
                          
                        </div>
                    </div>
                    </div>
                </div>
                ';
            }
        }

        return Response::json(['success' => true, 'fileUpload' => $fileUpload, 'markingSheet' => $markingSheet], 200);
    }

    public function import(Request $request) {
//        echo "<pre>";
//        print_r($request->all());
//        exit;

        $courseId = $request->course_id;
        $termId = $request->term_id;
        $eventId = $request->event_id;
        $subEventId = $request->sub_event_id;
        $subSubEventId = $request->sub_sub_event_id;
        $subSubSubEventId = $request->sub_sub_sub_event_id;
        $synId = $request->syn_id;
        $subSynId = $request->sub_syn_id;
        $cmGroupId = $request->cm_group_id;
        $eventGroupId = $request->event_group_id;
        $maProcess = $request->ma_process;
        $cmId = $request->cm_id;

        $factorList = self::getFactor($request);

        $cmPN = CmBasicProfile::select('personal_no')->where('id', $cmId)->first();


        $rules = array(
            'course_id' => 'required',
            'term_id' => 'required',
            'cm_id' => 'required',
            'marking_sheet' => 'required|max:10000|mimes:xlsx,xls',
        );
        $messages = array(
            'course_id.required' => __('label.COURSE_MUST_BE_SELECTED'),
            'term_id.required' => __('label.TERM_MUST_BE_SELECTED'),
            'cm_id.required' => __('label.COURSE_MEMBER_MUST_BE_SELECTED'),
            'marking_sheet.required' => __('label.MARKING_SHEET_MUST_BE_SELECTED'),
            'marking_sheet.mimes' => __('label.INVALID_FILE_FORMAT_EXPECTED_XLSX_XLS'),
        );

        $maProcess = $request->ma_process;

        if ($maProcess == '1') {
            $cmGroupId = $synId;
            $rules['syn_id'] = 'required';
            $messages['syn_id.required'] = __('label.SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '2') {
            $cmGroupId = $subSynId;
            $rules['sub_syn_id'] = 'required';
            $messages['sub_syn_id.required'] = __('label.SUB_SYNDICATE_MUST_BE_SELECTED');
        } else if ($maProcess == '3') {
            $rules['event_group_id'] = 'required';
            $messages['event_group_id.required'] = __('label.EVENT_GROUP_MUST_BE_SELECTED');
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

//        $courseName = Course::select('name')->where('id', $courseId)->first();
//        $term = Term::select('name')->where('id', $termId)->first();
//        $eventName = MutualAssessmentEvent:: select('name')->where('id', $eventId)->first();
//        //print_r($request->all());exit;
//        $syndicate = Syndicate::select('name')->where('id', $synId)->first();
//        $cmName = CmBasicProfile::select('full_name')->where('id', $cmId)->first();

        if (in_array($maProcess, ['1', '2'])) {
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                    ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('cm_group_member_template.course_id', $courseId)
                    ->where('cm_group_member_template.term_id', $termId)
                    ->where('cm_group_member_template.cm_group_id', $cmGroupId)
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_basic_profile.id', '!=', $cmId)
                    ->select('cm_basic_profile.id as cm_basic_id', 'cm_basic_profile.full_name as full_name'
                            , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                            , 'cm_basic_profile.photo as photo')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->orderBy('cm_basic_profile.full_name', 'asc');
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
            $prevCmArr = $prevCmArr->where('marking_group.event_group_id', $request->event_group_id)
                    ->pluck('cm_marking_group.cm_id')
                    ->toArray();


            $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->select('cm_basic_profile.id as cm_basic_id', 'cm_basic_profile.full_name as full_name'
                            , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                            , 'cm_basic_profile.photo as photo')
                    ->whereIn('cm_basic_profile.id', $prevCmArr)
                    ->where('cm_basic_profile.id', '!=', $cmId)
                    ->where('cm_basic_profile.status', '1')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->orderBy('cm_basic_profile.full_name', 'asc');
        }


        $cmIdArr = $cmList->pluck('cm_basic_id')->toArray();
        $cmListData = $cmList->get();

        if ($request->hasFile('marking_sheet')) {
            $file = $request->file('marking_sheet');
            $extension = File::extension($file->getClientOriginalName());
            if (in_array($extension, ['xlsx', 'xls'])) {
                $markSheetData = Excel::toArray(new ExcelImport, $file);
            }
        }
        $personalNumberArr = $positionArr = $pNWisePositionArr = $markingSheetInfo = $factorArr = [];
        $uniqPositionArr = $positionMinMax = [];
        $i = $j = $k = $x = 0;

        $fI = 4;
        if (!empty($factorList)) {
            foreach ($factorList as $factorId => $factor) {
                $factorArr[$fI] = $factorId;
                $fI++;
            }
        }


        foreach ($markSheetData[0] as $rowNumber => $rowData) {

            if ($rowNumber == 1) { //get mark sheet info
                foreach ($rowData as $colNumber => $columnData) {
                    if ($colNumber < 1) {
                        $markingSheetInfo[$x] = $columnData ?? '';
                        $x++;
                    }
                }
            }

            if ($rowNumber > 3) {
                $prevPsnlNo = '';
                foreach ($rowData as $colNumber => $columnData) {
//get cm personal number array
                    if ($colNumber == 1) {
                        if (!empty($cmIdArr) && $i <= (sizeof($cmIdArr) - 1)) {
                            if (!empty($cmPN->personal_no) && $cmPN->personal_no != $columnData) {
                                $personalNumberArr[$i] = $columnData ?? '';
                                $i++;
                            }
                        }
                    }
//get cm position array
                    if (!empty($cmIdArr) && !empty($factorArr) && $k <= ((sizeof($cmIdArr) * sizeof($factorArr)) - 1)) {
                        if (!empty($cmPN->personal_no) && $cmPN->personal_no != $rowData[1]) {
                            if (!empty($factorArr[$colNumber])) {
                                $factorId = $factorArr[$colNumber];
                                $positionArr[$j][$factorId] = $columnData ?? '';
                                $uniqPositionArr[$factorId][$columnData] = $columnData;
                                $k++;
                            }
                        }
                    }
                }

                if (!empty($cmIdArr) && !empty($factorArr) && $k <= ((sizeof($cmIdArr) * sizeof($factorArr)) - 1)) {
                        if (!empty($cmPN->personal_no) && $cmPN->personal_no != $rowData[1]) {
                        $j++;
                    }
                }
            }
        }
//
//        print_r(sizeof($uniqPositionArr[4]));
//        print_r($uniqPositionArr[5]);

        $cmIdArrFromExcelsheet = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->whereIn('cm_basic_profile.personal_no', $personalNumberArr)
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')
                        ->pluck('cm_basic_profile.id')->toArray();

        $cmIdAndPositonArr = []; // cmID in key and position in value


        $errorFlag = $errorIndex = 0;
        $errorMessageArr = [];

        if (array_diff($cmIdArr, $cmIdArrFromExcelsheet) != array_diff($cmIdArrFromExcelsheet, $cmIdArr)) {
            $errorFlag = 1;
            $errorMessageArr['cm_not_match'] = __('label.CM_LIST_IN_THE_PROVIDED_MARKING_SHEET_IS_NOT_ACCURATELY_MATCHING');
        } else {
            if (!empty($positionArr)) {
                foreach ($positionArr as $key => $factor) {
                    foreach ($factor as $factorId => $value) {
                        if (empty($value)) {
                            $errorFlag = 1;
                            $errorMessageArr['empty_position'] = __('label.EACH_POSITION_CELL_MUST_HAVE_VALUE');
                        }
                    }
                }

                if (!empty($factorList)) {
                    foreach ($factorList as $factorId => $factor) {
                        if (!empty($uniqPositionArr[$factorId])) {
                            if (sizeof($cmIdArr) != sizeof($uniqPositionArr[$factorId])) {
                                $errorFlag = 1;
                                $errorMessageArr['positions_not_unique'] = __('label.POSITION_MUST_BE_UNIQUE');
                            }
                            if (max($uniqPositionArr[$factorId]) > sizeof($cmIdArr)) {
                                $errorFlag = 1;
                                $errorMessageArr['max_position'] = __('label.MAX_POSITIION_MUST_NOT_EXCEED_TOTAL_NUMBER_OF_CM');
                            }

                            if (min($uniqPositionArr[$factorId]) != 1) {
                                $errorFlag = 1;
                                $errorMessageArr['min_position'] = __('label.MIN_POSITION_MUST_BE_1');
                            }
                        }
                    }
                }
            }
        }
//        echo '<pre>';
//        print_r($positionArr);
//        exit;

        if ($errorFlag == 0) {
            foreach ($cmIdArrFromExcelsheet as $key => $id) {
                if (!empty($factorList)) {
                    foreach ($factorList as $factorId => $factor) {
                        $cmIdAndPositonArr[$id][$factorId] = $positionArr[$key][$factorId];
                    }
                }
            }
        } else {
            return Response::json(array('success' => false, 'heading' => 'File Validation Error', 'errormessage' => $errorMessageArr), 401);
        }


        $markingSheet = view('mutualAssessment.showMarkingSheetWithPosition', compact('request', 'courseId', 'termId'
                        , 'eventId', 'subEventId', 'subSubEventId', 'subSubSubEventId', 'synId', 'subSynId'
                        , 'eventGroupId', 'cmId', 'factorList', 'maProcess', 'cmIdAndPositonArr', 'cmListData'
                        , 'markingSheetInfo'))->render();

        return Response::json(['success' => true, 'markingSheet' => $markingSheet], 200);
    }

    public function saveImportedData(Request $request) {
        $cmIdAndPositon = $request->cm_id_and_position_arr;
        $cmIdAndPositonArr = json_decode($cmIdAndPositon, true);
        $positionArr = $request->position;
        $data = $lockData = [];
        $i = 0;

        $synId = !empty($request->syn_id) ? $request->syn_id : 0;
        $subSynId = !empty($request->sub_syn_id) ? $request->sub_syn_id : 0;
        $eventId = !empty($request->event_id) ? $request->event_id : 0;
        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;
        $eventGroupId = !empty($request->event_group_id) ? $request->event_group_id : 0;
        if (!empty($positionArr)) {
            foreach ($positionArr as $cmId => $factor) {
                foreach ($factor as $factorId => $position) {
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['term_id'] = $request->term_id;
                    $data[$i]['event_id'] = $eventId;
                    $data[$i]['sub_event_id'] = $subEventId;
                    $data[$i]['sub_sub_event_id'] = $subSubEventId;
                    $data[$i]['sub_sub_sub_event_id'] = $subSubSubEventId;
                    $data[$i]['syndicate_id'] = $synId;
                    $data[$i]['sub_syndicate_id'] = $subSynId;
                    $data[$i]['event_group_id'] = $eventGroupId;
                    $data[$i]['factor_id'] = $factorId;
                    $data[$i]['marking_cm_id'] = $request->cm_id;
                    $data[$i]['cm_id'] = $cmId;
                    $data[$i]['position'] = $position;
                    $data[$i]['updated_at'] = date('Y-m-d H:s:i');
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $i++;
                }
            }
        }


        DB::beginTransaction();

        try {
            $deleMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->course_id)
                            ->where('term_id', $request->term_id)
                            ->where('event_id', $eventId)
                            ->where('sub_event_id', $subEventId)
                            ->where('sub_sub_event_id', $subSubEventId)
                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                            ->where('syndicate_id', $synId)
                            ->where('sub_syndicate_id', $subSynId)
                            ->where('event_group_id', $eventGroupId)
                            ->where('marking_cm_id', $request->cm_id)->delete();

            $successMsg = __('label.MUTUAL_ASSESSMENT_MARKING_HAS_BEEN_ASSIGNED_SUCCESSFULLY');
            $errorMsg = __('label.MUTUAL_ASSESSMENT_MARKING_COULD_NOT_BE_ASSIGNED');

            if (MutualAssessmentMarking::insert($data)) {
                if ($request->save_status == '2') {
                    $target = new MutualAssessmentMarkingLock;
                    $target->course_id = $request->course_id;
                    $target->term_id = $request->term_id;
                    $target->event_id = $request->ma_event_id;
                    $target->syndicate_id = $synId;
                    $target->event_group_id = $eventGroupId;
                    $target->marking_cm_id = $request->cm_id;
                    $target->lock_status = '1';
                    $target->locked_at = date('Y-m-d H:i:s');
                    $target->locked_by = Auth::user()->id;
                    $target->save();

                    $successMsg = __('label.MUTUAL_ASSESSMENT_MARKING_HAS_BEEN_ASSIGNED_AND_LOCKED_SUCCESSFULLY');
                    $errorMsg = __('label.MUTUAL_ASSESSMENT_MARKING_COULD_NOT_BE_ASSIGNED_AND_LOCKED');
                }
            }
            DB::commit();
            return Response::json(['success' => true, 'saveStatus' => $request->save_status, 'message' => $successMsg], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'message' => $errorMsg], 401);
        }
    }

}
