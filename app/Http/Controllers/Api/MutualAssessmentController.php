<?php

namespace App\Http\Controllers\Api;

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
use App\Http\Controllers\Controller;
use Common;
use App\AssessmentActDeact;

class MutualAssessmentController extends Controller {

    private $controller = 'MutualAssessment';

    public function index(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $cmId = $request->data['id'];

        $cmCourseInfo = CmBasicProfile::where('id', $cmId)->select('course_id')->first();
        $cmCourse = !empty($cmCourseInfo->course_id) ? $cmCourseInfo->course_id : 0;
        $apiResponse = [];
        $void = [];
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.MUTUAL_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            $apiResponse['void'] = $void;
            return response()->json(['result' => $apiResponse, 'status' => 410]);
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo['id'])
                        ->where('id', $cmCourse)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        if (empty($activeCourse)) {
            $void['header'] = __('label.MUTUAL_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            $apiResponse['void'] = $void;
            return response()->json(['result' => $apiResponse, 'status' => 410]);
        }

        $courseId = $activeCourse['id'];
        $activeTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name as name', 'term.id as id')
                        ->where('term_to_course.course_id', $courseId)
                        ->where('term_to_course.status', '1')
                        ->where('term_to_course.active', '1')
                        ->orderBy('term.order', 'asc')->first();

        if (empty($activeTerm)) {
            $void['header'] = __('label.MUTUAL_ASSESSMENT');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $activeCourse['name'], 'training_year' => $activeTrainingYearInfo['name']]);
            $apiResponse['void'] = $void;
            return response()->json(['result' => $apiResponse, 'status' => 410]);
        }

        $maProcessInfo = MaProcess::where('course_id', $courseId)->where('term_id', $activeTerm['id'])
                        ->select('process')->first()->toArray();

        $maProcess = !empty($maProcessInfo['process']) ? $maProcessInfo['process'] : '0';

        $eventsList = ['0' => __('label.SELECT_EVENT_OPT')] + TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $courseId)
                        ->where('term_to_event.term_id', $activeTerm['id'])
                        ->where('event.for_ma_grouping', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')->toArray();

        $syn = CmGroupMemberTemplate::join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->where('cm_group_member_template.course_id', $courseId)
                        ->where('cm_group_member_template.term_id', $activeTerm['id'])
                        ->where('cm_group_member_template.cm_basic_profile_id', $cmId)
                        ->where('cm_group.type', 1)
                        ->select('cm_group.name', 'cm_group.id')->first();

        $synCmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_group_member_template.cm_basic_profile_id')
                        ->join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                , 'cm_basic_profile.photo as photo')
                        ->where('cm_group_member_template.course_id', $courseId)
                        ->where('cm_group_member_template.term_id', $activeTerm['id'])
                        ->where('cm_group_member_template.cm_group_id', !empty($syn->id) ? $syn->id : 0)
                        ->where('cm_group_member_template.cm_basic_profile_id', '!=', $cmId)
                        ->where('cm_group.type', 1)
                        ->where('cm_basic_profile.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')->get();


        $subSyn = CmGroupMemberTemplate::join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->where('cm_group_member_template.course_id', $courseId)
                        ->where('cm_group_member_template.term_id', $activeTerm['id'])
                        ->where('cm_group_member_template.cm_basic_profile_id', $cmId)
                        ->where('cm_group.type', 2)
                        ->select('cm_group.name', 'cm_group.id')->first();

        $subSynCmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_group_member_template.cm_basic_profile_id')
                        ->join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                        ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                , 'cm_basic_profile.photo as photo')
                        ->where('cm_group_member_template.course_id', $courseId)
                        ->where('cm_group_member_template.term_id', $activeTerm['id'])
                        ->where('cm_group_member_template.cm_group_id', !empty($subSyn->id) ? $subSyn->id : 0)
                        ->where('cm_group_member_template.cm_basic_profile_id', '!=', $cmId)
                        ->where('cm_group.type', 2)
                        ->where('cm_basic_profile.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')->get();

        $factorList = MutualAssessmentEvent::orderBy('order', 'ASC')->pluck('name', 'id');

        $prevMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $courseId)
                ->where('term_id', $activeTerm['id']);

        if ($maProcess == 1 && !empty($syn->id)) {
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('syndicate_id', $syn->id);
        }
        if ($maProcess == 2 && !empty($subSyn->id)) {
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_syndicate_id', $subSyn->id);
        }
        $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('marking_cm_id', $cmId)->select('cm_id', 'factor_id', 'position')->get();
        $maActivitionStatus = 0;
        if (in_array($maProcess, [1, 2])) {
            $maActivitionStatus = AssessmentActDeact::where('course_id', $courseId)
                    ->where('term_id', $activeTerm['id'])
                    ->where('criteria', '5')
                    ->where('event_id', 0)
                    ->where('sub_event_id', 0)
                    ->where('sub_sub_event_id', 0)
                    ->where('sub_sub_sub_event_id', 0)
                    ->where('status', '1')
                    ->first();
            if (!empty($maActivitionStatus)) {
                $maActivitionStatus = 1;
            }
        }

        $prevMarkingArr = [];
        if (!$prevMutualAssessmentMarking->isEmpty()) {
            foreach ($prevMutualAssessmentMarking as $mkInfo) {
                $prevMarkingArr[$mkInfo->cm_id][$mkInfo->factor_id] = $mkInfo->position;
            }
        }


        // Lock Status
        $mamLock = [];
        $mamLock = MutualAssessmentMarkingLock::where('term_id', $activeTerm['id'])->where('course_id', $courseId);
        if ($maProcess == 1 && !empty($syn->id)) {
            $mamLock = $mamLock->where('syndicate_id', $syn->id)
                    ->where('marking_cm_id', $cmId);
        }

        if ($maProcess == 2 && !empty($subSyn->id)) {
            $mamLock = $mamLock->where('sub_syndicate_id', $subSyn->id);
        }

        $mamLock = $mamLock->first();
        if (!empty($mamLock)) {
            $mamLock = $mamLock->toArray();
        }

        //END:: Lock Status

        $apiResponse['factorList'] = $factorList;
        $apiResponse['activeTrainingYearInfo'] = $activeTrainingYearInfo->toArray();
        $apiResponse['activeCourse'] = $activeCourse->toArray();
        $apiResponse['activeTerm'] = $activeTerm->toArray();
        $apiResponse['eventsList'] = $eventsList;
        $apiResponse['maProcess'] = $maProcess;
        $apiResponse['syn'] = $syn;
        $apiResponse['syn_cm_list'] = $synCmList;
        $apiResponse['subSyn'] = $subSyn;
        $apiResponse['sub_syn_cm_list'] = $subSynCmList;
        $apiResponse['prev_marking_arr'] = $prevMarkingArr;
        $apiResponse['mam_lock_status'] = $mamLock;
        $apiResponse['ma_activition_status'] = $maActivitionStatus;


        return response()->json(['result' => $apiResponse, 'status' => 200]);
    }

    public function getSubEvent(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $apiResponse = [];
        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->data['course_id'])
                        ->where('term_to_sub_event.term_id', $request->data['term_id'])
                        ->where('term_to_sub_event.event_id', $request->data['event_id'])
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')->toArray();

        $has = Event::where('id', $request->data['event_id'])->where('has_ds_assesment', '1')->first();

        if ((!empty($has))) {
            $apiResponse = $this->getCmGroups($request);
            $factorList = MutualAssessmentEvent::orderBy('order', 'ASC')->pluck('name', 'id');
            $apiResponse['factor_list'] = $factorList;
            return response()->json(['result' => $apiResponse, 'status' => 201]);
        } else {
            $apiResponse['sub_event_list'] = $subEventList;
            return response()->json(['result' => $apiResponse, 'status' => 200]);
        }
    }

    public function getSubSubEvent(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }
        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->data['course_id'])
                        ->where('term_to_sub_sub_event.term_id', $request->data['term_id'])
                        ->where('term_to_sub_sub_event.event_id', $request->data['event_id'])
                        ->where('term_to_sub_sub_event.sub_event_id', $request->data['sub_event_id'])
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();

        $has = EventToSubEvent::where('event_id', $request->data['event_id'])
                        ->where('sub_event_id', $request->data['sub_event_id'])
                        ->where('has_ds_assesment', '1')->first();

        if ((!empty($has))) {
            $apiResponse = $this->getCmGroups($request);
            $factorList = MutualAssessmentEvent::orderBy('order', 'ASC')->pluck('name', 'id');
            $apiResponse['factor_list'] = $factorList;
            return response()->json(['result' => $apiResponse, 'status' => 201]);
        } else {
            $apiResponse['sub_sub_event_list'] = $subSubEventList;
            return response()->json(['result' => $apiResponse, 'status' => 200]);
        }
    }

    public function getSubSubSubEvent(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }
        $apiResponse = [];
        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('event_to_sub_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_sub_event.course_id', $request->data['course_id'])
                        ->where('term_to_sub_sub_sub_event.term_id', $request->data['term_id'])
                        ->where('term_to_sub_sub_sub_event.event_id', $request->data['event_id'])
                        ->where('term_to_sub_sub_sub_event.sub_event_id', $request->data['sub_event_id'])
                        ->where('term_to_sub_sub_sub_event.sub_sub_event_id', $request->data['sub_sub_event_id'])
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        $has = EventToSubSubEvent::where('event_id', $request->data['event_id'])
                        ->where('sub_event_id', $request->data['sub_event_id'])
                        ->where('sub_sub_event_id', $request->data['sub_sub_event_id'])
                        ->where('has_ds_assesment', '1')->first();

        if ((!empty($has))) {
            $apiResponse = $this->getCmGroups($request);
            $factorList = MutualAssessmentEvent::orderBy('order', 'ASC')->pluck('name', 'id');
            $apiResponse['factor_list'] = $factorList;
            return response()->json(['result' => $apiResponse, 'status' => 201]);
        } else {
            $apiResponse['sub_sub_sub_event_list'] = $subSubSubEventList;
            return response()->json(['result' => $apiResponse, 'status' => 200]);
        }
    }

    public function getCmGroups(Request $request) {
        $cmGroup = CmMarkingGroup::join('marking_group', 'marking_group.id', '=', 'cm_marking_group.marking_group_id')
                ->join('event_group', 'event_group.id', '=', 'marking_group.event_group_id')
                ->where('marking_group.course_id', $request->data['course_id'])
                ->where('marking_group.term_id', $request->data['term_id'])
                ->where('marking_group.event_id', $request->data['event_id']);

        $maActivitionStatus = 0;
        $maActivitionStatus = AssessmentActDeact::where('course_id', $request->data['course_id'])
                ->where('term_id', $request->data['term_id'])
                ->where('criteria', '5')
                ->where('event_id', $request->data['event_id']);

        if (!empty($request->data['sub_event_id'])) {
            $cmGroup = $cmGroup->where('marking_group.sub_event_id', $request->data['sub_event_id']);
            $maActivitionStatus = $maActivitionStatus->where('sub_event_id', $request->data['sub_event_id']);
        }

        if (!empty($request->data['sub_sub_event_id'])) {
            $cmGroup = $cmGroup->where('marking_group.sub_sub_event_id', $request->data['sub_sub_event_id']);
            $maActivitionStatus = $maActivitionStatus->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
        }

        if (!empty($request->data['sub_sub_sub_event_id'])) {
            $cmGroup = $cmGroup->where('marking_group.sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
            $maActivitionStatus = $maActivitionStatus->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
        }

        $cmGroup = $cmGroup->where('cm_marking_group.cm_id', $request->data['cm_id'])
                        ->select('marking_group.id as id', 'event_group.name as name', 'event_group.id as event_group_id')
                        ->first()->toArray();

        $cmList = CmMarkingGroup::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                        ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                , 'cm_basic_profile.photo as photo')
                        ->where('cm_marking_group.marking_group_id', $cmGroup['id'])
                        ->where('cm_basic_profile.id', '!=', $request->data['cm_id'])
                        ->where('cm_basic_profile.status', '1')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();

        //Prv Mutuall Assesment Marking
        $prevMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->data['course_id'])
                ->where('term_id', $request->data['term_id'])
                ->where('event_id', $request->data['event_id']);

        if (!empty($request->data['sub_event_id'])) {
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_event_id', $request->data['sub_event_id']);
        }

        if (!empty($request->data['sub_sub_event_id'])) {
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
        }

        if (!empty($request->data['sub_sub_sub_event_id'])) {
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
        }

        $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('marking_cm_id', $request->data['cm_id'])->select('cm_id', 'factor_id', 'position')->get();


        $prevMarkingArr = [];
        if (!$prevMutualAssessmentMarking->isEmpty()) {
            foreach ($prevMutualAssessmentMarking as $mkInfo) {
                $prevMarkingArr[$mkInfo->cm_id][$mkInfo->factor_id] = $mkInfo->position;
            }
        }

        //END:: Prv Mutuall Assesment Marking
        // Lock Status
        $mamLock = [];
        $mamLock = MutualAssessmentMarkingLock::where('term_id', $request->data['term_id'])->where('course_id', $request->data['course_id']);
        if (!empty($request->data['event_id'])) {
            $mamLock = $mamLock->where('event_id', $request->data['event_id'])
                    ->where('event_id', $request->data['event_id'])
                    ->where('event_group_id', $cmGroup['event_group_id'])
                    ->where('marking_cm_id', $request->data['cm_id']);
        }

        if (!empty($request->data['sub_event_id'])) {
            $mamLock = $mamLock->where('sub_event_id', $request->data['sub_event_id']);
        }

        if (!empty($request->data['sub_sub_event_id'])) {
            $mamLock = $mamLock->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
        }

        if (!empty($request->data['sub_sub_sub_event_id'])) {
            $mamLock = $mamLock->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
        }

        $mamLock = $mamLock->first();
        if (!empty($mamLock)) {
            $mamLock = $mamLock->toArray();
        }

        $maActivitionStatus = $maActivitionStatus->where('status', '1')->first();

        if (!empty($maActivitionStatus)) {
            $maActivitionStatus = 1;
        }


        //END:: Lock Status

        $apiResponse['cm_group'] = $cmGroup;
        $apiResponse['prev_marking_arr'] = $prevMarkingArr;
        $apiResponse['cm_list'] = $cmList;
        $apiResponse['mam_lock_status'] = $mamLock;
        $apiResponse['ma_activition_status'] = $maActivitionStatus;

        return $apiResponse;
    }

    public function saveMark(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $positionArr = $request->data['position'];


        $synId = !empty($request->data['syn_id']) ? $request->data['syn_id'] : 0;
        $subSynId = !empty($request->data['sub_syn_id']) ? $request->data['sub_syn_id'] : 0;

        $eventId = !empty($request->data['event_id']) ? $request->data['event_id'] : 0;
        $subEventId = !empty($request->data['sub_event_id']) ? $request->data['sub_event_id'] : 0;
        $subSubEventId = !empty($request->data['sub_sub_event_id']) ? $request->data['sub_sub_event_id'] : 0;
        $subSubSubEventId = !empty($request->data['sub_sub_sub_event_id']) ? $request->data['sub_sub_sub_event_id'] : 0;
        $eventGroupId = !empty($request->data['event_group_id']) ? $request->data['event_group_id'] : 0;

        $factorList = MutualAssessmentEvent::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();


        $cmList = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->select('cm_basic_profile.id as id', DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as cm_name"))
                        ->pluck('cm_name', 'id')->toArray();

        $factorPositonArr = [];

        if (!empty($positionArr)) {
            $totalCM = sizeof($positionArr);
            foreach ($positionArr as $cmId => $factor) {
                foreach ($factor as $factorId => $position) {
                    $rules['data.position.' . $cmId . '.' . $factorId] = 'required|gte:1|lte:' . $totalCM;

                    $message['data.position.' . $cmId . '.' . $factorId . '.gte'] = __('label.GIVEN_POSITION_MUST_BE_GRATER_THAN_OR_EQUAL_TO_1');
                    $message['data.position.' . $cmId . '.' . $factorId . '.lte'] = __('label.GIVEN_POSITION_MUST_BE_LESS_THAN_OR_EQUAL_TO', ['total_cm' => $totalCM]);
                    $message['data.position.' . $cmId . '.' . $factorId . '.required'] = __('label.THIS_POSITION_FILED_CANNOT_BE_EMPTY', ['cm_name' => $cmList[$cmId], 'factor_name' => $factorList[$factorId]]);
                    $factorPositonArr[$factorId][] = $position;
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json(['result' => '', 'message' => $validator->errors(), 'status' => 400]);
        }

        $dublicateFactorErr = [];
        if (!empty($factorPositonArr)) {
            foreach ($factorPositonArr as $factorId => $posnArr) {
                if (!empty($posnArr)) {
                    $result = array_diff_assoc($posnArr, array_unique($posnArr));
                    if (!empty($result)) {
                        $dublicateFactorErr[] = __('label.DUPLICATE_POSITION_IS_FOUND_IN_FACTOR', ['factor' => $factorList[$factorId]]);
                    }
                }
            }
        }

        if (!empty($dublicateFactorErr)) {
            return response()->json(['result' => '', 'message' => $dublicateFactorErr, 'status' => 400]);
        }


        $i = 0;
        if (!empty($positionArr)) {
            foreach ($positionArr as $cmId => $factor) {
                foreach ($factor as $factorId => $position) {
                    $data[$i]['course_id'] = $request->data['course_id'];
                    $data[$i]['term_id'] = $request->data['term_id'];
                    $data[$i]['event_id'] = $eventId;
                    $data[$i]['sub_event_id'] = $subEventId;
                    $data[$i]['sub_sub_event_id'] = $subSubEventId;
                    $data[$i]['sub_sub_sub_event_id'] = $subSubSubEventId;
                    $data[$i]['syndicate_id'] = $synId;
                    $data[$i]['sub_syndicate_id'] = $subSynId;
                    $data[$i]['event_group_id'] = $eventGroupId;
                    $data[$i]['factor_id'] = $factorId;
                    $data[$i]['marking_cm_id'] = $request->data['cm_id'];
                    $data[$i]['cm_id'] = $cmId;
                    $data[$i]['position'] = $position;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = $request->data['cm_id'];
                    $i++;
                }
            }
        }

        $requestData = [
            'course_id' => $request->data['course_id'],
            'term_id' => $request->data['term_id'],
            'event_id' => $eventId,
            'sub_event_id' => $subEventId,
            'sub_sub_event_id' => $subSubEventId,
            'sub_sub_sub_event_id' => $subSubSubEventId,
            'syndicate_id' => $synId,
            'sub_syndicate_id' => $subSynId,
            'event_group_id' => $eventGroupId,
            'marking_cm_id' => $request->data['cm_id'],
            'save_status' => $request->data['save_status'],
        ];

        DB::beginTransaction();

        try {
            $deleMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->data['course_id'])
                            ->where('term_id', $request->data['term_id'])
                            ->where('event_id', $eventId)
                            ->where('sub_event_id', $subEventId)
                            ->where('sub_sub_event_id', $subSubEventId)
                            ->where('sub_sub_sub_event_id', $subSubSubEventId)
                            ->where('syndicate_id', $synId)
                            ->where('sub_syndicate_id', $subSynId)
                            ->where('event_group_id', $eventGroupId)
                            ->where('marking_cm_id', $request->data['cm_id'])->delete();

            $successMsg = __('label.MUTUAL_ASSESSMENT_MARKING_HAS_BEEN_ASSIGNED_SUCCESSFULLY');
            $errorMsg = __('label.MUTUAL_ASSESSMENT_MARKING_COULD_NOT_BE_ASSIGNED');

            if (MutualAssessmentMarking::insert($data)) {
                if ($request->data['save_status'] == '2') {
                    $target = new MutualAssessmentMarkingLock;
                    $target->course_id = $request->data['course_id'];
                    $target->term_id = $request->data['term_id'];
                    $target->event_id = $eventId;
                    $target->sub_event_id = $subEventId;
                    $target->sub_sub_event_id = $subSubEventId;
                    $target->sub_sub_sub_event_id = $subSubSubEventId;
                    $target->syndicate_id = $synId;
                    $target->sub_syndicate_id = $subSynId;
                    $target->event_group_id = $eventGroupId;
                    $target->marking_cm_id = $request->data['cm_id'];
                    $target->lock_status = '1';
                    $target->locked_at = date('Y-m-d H:i:s');
                    $target->locked_by = $request->data['cm_id'];
                    $target->save();

                    $successMsg = __('label.MUTUAL_ASSESSMENT_MARKING_HAS_BEEN_ASSIGNED_AND_LOCKED_SUCCESSFULLY');
                    $errorMsg = __('label.MUTUAL_ASSESSMENT_MARKING_COULD_NOT_BE_ASSIGNED_AND_LOCKED');
                }
            }
            DB::commit();

            return response()->json(['result' => $requestData, 'message' => $successMsg, 'status' => 200]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['result' => '', 'message' => $errorMsg, 'status' => 401]);
        }
    }

    public function getUpdatedCmList(Request $request) {
        $maProcessInfo = MaProcess::where('course_id', $request->data['course_id'])->where('term_id', $request->data['term_id'])
                        ->select('process')->first()->toArray();


        $maProcess = !empty($maProcessInfo['process']) ? $maProcessInfo['process'] : '0';

        $maActivitionStatus = 0;

        if (in_array($maProcess, [1, 2])) {  //Syndicate
            $cmList = CmGroupMemberTemplate::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_group_member_template.cm_basic_profile_id')
                            ->join('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                            ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->where('cm_group_member_template.course_id', $request->data['course_id'])
                            ->where('cm_group_member_template.term_id', $request->data['term_id'])
                            ->where('cm_group_member_template.cm_group_id', $maProcess == 1 ? $request->data['syndicate_id'] : $request->data['sub_syndicate_id'])
                            ->where('cm_group_member_template.cm_basic_profile_id', '!=', $request->data['marking_cm_id'])
                            ->where('cm_group.type', 1)
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();


            $prevMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->data['course_id'])
                    ->where('term_id', $request->data['term_id']);

            if ($maProcess == 1 && !empty($request->data['syndicate_id'])) {
                $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('syndicate_id', $request->data['syndicate_id']);
            }
            if ($maProcess == 2 && !empty($request->data['sub_syndicate_id'])) {
                $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_syndicate_id', $request->data['sub_syndicate_id']);
            }
            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('marking_cm_id', $request->data['marking_cm_id'])->select('cm_id', 'factor_id', 'position')->get();


            $prevMarkingArr = [];
            if (!$prevMutualAssessmentMarking->isEmpty()) {
                foreach ($prevMutualAssessmentMarking as $mkInfo) {
                    $prevMarkingArr[$mkInfo->cm_id][$mkInfo->factor_id] = $mkInfo->position;
                }
            }



            $maActivitionStatus = AssessmentActDeact::where('course_id', $request->data['course_id'])
                    ->where('term_id', $request->data['term_id'])
                    ->where('criteria', '5')
                    ->where('event_id', 0)
                    ->where('sub_event_id', 0)
                    ->where('sub_sub_event_id', 0)
                    ->where('sub_sub_sub_event_id', 0)
                    ->where('status', '1')
                    ->first();
            if (!empty($maActivitionStatus)) {
                $maActivitionStatus = 1;
            }
        }

        if ($maProcess == 3) { //Event
            $cmGroup = CmMarkingGroup::join('marking_group', 'marking_group.id', '=', 'cm_marking_group.marking_group_id')
                    ->join('event_group', 'event_group.id', '=', 'marking_group.event_group_id')
                    ->where('marking_group.course_id', $request->data['course_id'])
                    ->where('marking_group.term_id', $request->data['term_id'])
                    ->where('marking_group.event_id', $request->data['event_id']);

            $maActivitionStatus = AssessmentActDeact::where('course_id', $request->data['course_id'])
                    ->where('term_id', $request->data['term_id'])
                    ->where('criteria', '5')
                    ->where('event_id', $request->data['event_id']);


            if (!empty($request->data['sub_event_id'])) {
                $cmGroup = $cmGroup->where('marking_group.sub_event_id', $request->data['sub_event_id']);
                $maActivitionStatus = $maActivitionStatus->where('sub_event_id', $request->data['sub_event_id']);
            }

            if (!empty($request->data['sub_sub_event_id'])) {
                $cmGroup = $cmGroup->where('marking_group.sub_sub_event_id', $request->data['sub_sub_event_id']);
                $maActivitionStatus = $maActivitionStatus->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
            }

            if (!empty($request->data['sub_sub_sub_event_id'])) {
                $cmGroup = $cmGroup->where('marking_group.sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
                $maActivitionStatus = $maActivitionStatus->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
            }

            $cmGroup = $cmGroup->where('cm_marking_group.cm_id', $request->data['marking_cm_id'])
                            ->select('marking_group.id as id', 'event_group.name as name', 'event_group.id as event_group_id')
                            ->first()->toArray();

            $cmList = CmMarkingGroup::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cm_marking_group.cm_id')
                            ->join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->join('wing', 'wing.id', 'cm_basic_profile.wing_id')
                            ->select('cm_basic_profile.id as cm_id', 'cm_basic_profile.full_name as full_name'
                                    , 'cm_basic_profile.personal_no as personal_no', 'rank.code as rank'
                                    , 'cm_basic_profile.photo as photo')
                            ->where('cm_marking_group.marking_group_id', $cmGroup['id'])
                            ->where('cm_basic_profile.id', '!=', $request->data['marking_cm_id'])
                            ->where('cm_basic_profile.status', '1')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc')
                            ->orderBy('cm_basic_profile.full_name', 'asc')->get()->toArray();

            //Prv Mutuall Assesment Marking
            $prevMutualAssessmentMarking = MutualAssessmentMarking::where('course_id', $request->data['course_id'])
                    ->where('term_id', $request->data['term_id'])
                    ->where('event_id', $request->data['event_id']);

            if (!empty($request->data['sub_event_id'])) {
                $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_event_id', $request->data['sub_event_id']);
            }

            if (!empty($request->data['sub_sub_event_id'])) {
                $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
            }

            if (!empty($request->data['sub_sub_sub_event_id'])) {
                $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
            }


            $prevMutualAssessmentMarking = $prevMutualAssessmentMarking->where('marking_cm_id', $request->data['marking_cm_id'])->select('cm_id', 'factor_id', 'position')->get();


            $prevMarkingArr = [];
            if (!$prevMutualAssessmentMarking->isEmpty()) {
                foreach ($prevMutualAssessmentMarking as $mkInfo) {
                    $prevMarkingArr[$mkInfo->cm_id][$mkInfo->factor_id] = $mkInfo->position;
                }
            }

            //END:: Prv Mutuall Assesment Marking

            $maActivitionStatus = $maActivitionStatus->where('status', '1')->first();
            if (!empty($maActivitionStatus)) {
                $maActivitionStatus = 1;
            } else {
                $maActivitionStatus = 0;
            }
        }


        // Lock Status
        $mamLock = [];
        $mamLock = MutualAssessmentMarkingLock::where('term_id', $request->data['term_id'])->where('course_id', $request->data['course_id']);
        if (!empty($request->data['event_id'])) {
            $mamLock = $mamLock->where('event_id', $request->data['event_id'])
                    ->where('event_group_id', $cmGroup['event_group_id'])
                    ->where('marking_cm_id', $request->data['marking_cm_id']);
        }

        if (!empty($request->data['sub_event_id'])) {
            $mamLock = $mamLock->where('sub_event_id', $request->data['sub_event_id']);
        }

        if (!empty($request->data['sub_sub_event_id'])) {
            $mamLock = $mamLock->where('sub_sub_event_id', $request->data['sub_sub_event_id']);
        }

        if (!empty($request->data['sub_sub_sub_event_id'])) {
            $mamLock = $mamLock->where('sub_sub_sub_event_id', $request->data['sub_sub_sub_event_id']);
        }

        if ($maProcess == 1 && !empty($request->data['syndicate_id'])) {
            $mamLock = $mamLock->where('syndicate_id', $request->data['syndicate_id'])
                    ->where('marking_cm_id', $request->data['marking_cm_id']);
        }

        if ($maProcess == 2 && !empty($request->data['sub_syndicate_id'])) {
            $mamLock = $mamLock->where('sub_syndicate_id', $request->data['sub_syndicate_id']);
        }

        $mamLock = $mamLock->first();
        if (!empty($mamLock)) {
            $mamLock = $mamLock->toArray();
        }
        //END:: Lock Status




        $factorList = MutualAssessmentEvent::orderBy('order', 'ASC')->pluck('name', 'id');
        $apiResponse['factor_list'] = $factorList;
        $apiResponse['prev_marking_arr'] = $prevMarkingArr;
        $apiResponse['cm_list'] = $cmList;
        $apiResponse['mam_lock_status'] = $mamLock;
        $apiResponse['ma_activition_status'] = $maActivitionStatus;
        return response()->json(['result' => $apiResponse, 'status' => 200]);
    }

    public function requestForUnlock(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $markingCmId = $request->data['cm_id'];
        $termId = $request->data['term_id'];
        $courseId = $request->data['course_id'];
        $synId = !empty($request->data['syn_id']) ? $request->data['syn_id'] : 0;
        $subSynId = !empty($request->data['sub_syn_id']) ? $request->data['sub_syn_id'] : 0;
        $eventId = !empty($request->data['event_id']) ? $request->data['event_id'] : 0;
        $subEventId = !empty($request->data['sub_event_id']) ? $request->data['sub_event_id'] : 0;
        $subSubEventId = !empty($request->data['sub_sub_event_id']) ? $request->data['sub_sub_event_id'] : 0;
        $subSubSubEventId = !empty($request->data['sub_sub_sub_event_id']) ? $request->data['sub_sub_sub_event_id'] : 0;
        $eventGroupId = !empty($request->data['event_group_id']) ? $request->data['event_group_id'] : 0;

        $requestData = [
            'course_id' => $courseId,
            'term_id' => $termId,
            'event_id' => $eventId,
            'sub_event_id' => $subEventId,
            'sub_sub_event_id' => $subSubEventId,
            'sub_sub_sub_event_id' => $subSubSubEventId,
            'syndicate_id' => $synId,
            'sub_syndicate_id' => $subSynId,
            'event_group_id' => $eventGroupId,
            'marking_cm_id' => $markingCmId,
        ];


        $prevLockInfo = MutualAssessmentMarkingLock::where('course_id', $courseId)
                ->where('term_id', $termId)
                ->where('event_id', $eventId)
                ->where('sub_event_id', $subEventId)
                ->where('sub_sub_event_id', $subSubEventId)
                ->where('sub_sub_sub_event_id', $subSubSubEventId)
                ->where('syndicate_id', $synId)
                ->where('sub_syndicate_id', $subSynId)
                ->where('event_group_id', $eventGroupId)
                ->where('marking_cm_id', $markingCmId)
                ->select('id')
                ->first();
        $successMsg = __('label.UNLOCK_REQUEST_SEND_SUCCESSFULLY');
        $errorMsg = __('label.COULDNOT_SEND_UNLOCK_REQUEST');


        $updateArr = [
            'lock_status' => '2',
            'unlock_message' => $request->data['unlock_message'],
        ];

        if (MutualAssessmentMarkingLock::where('id', $prevLockInfo->id)->update($updateArr)) {
            return response()->json(['result' => $requestData, 'message' => $successMsg, 'status' => 200]);
        } else {
            return response()->json(['result' => $requestData, 'message' => $errorMsg, 'status' => 401]);
        }
    }

}
