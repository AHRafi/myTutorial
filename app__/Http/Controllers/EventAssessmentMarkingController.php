<?php

namespace App\Http\Controllers;

//use App\CenterToCourse;
use App\TermToCourse;
use App\TrainingYear;
use App\EventAssessmentMarking;
use App\EventAssessmentMarkingLock;
use App\MarkingGroup;
use App\CmMarkingGroup;
use App\DsMarkingGroup;
use App\GradingSystem;
use App\CiModerationMarkingLock;
use App\CmBasicProfile;
use App\DsObsnMarking;
use App\CiModerationMarking;
use App\AssessmentActDeact;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class EventAssessmentMarkingController extends Controller {

    public function index(Request $request) {
//get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.EVENT_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->join('course', 'course.id', '=', 'marking_group.course_id')
                        ->where('course.training_year_id', $activeTrainingYearInfo->id)
                        ->where('ds_marking_group.ds_id', Auth::user()->id)
                        ->where('course.status', '1')
                        ->orderBy('course.id', 'desc')
                        ->select('course.name', 'course.id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.EVENT_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
//                        ->join('course', 'course.id', '=', 'marking_group.course_id')
//                        ->where('course.training_year_id', $activeTrainingYearInfo->id)
//                        ->where('ds_marking_group.ds_id', Auth::user()->id)
//                        ->where('course.status', '1')->orderBy('course.id', 'desc')
//                        ->pluck('course.name', 'course.id')->toArray();


        $termList = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.course_id', $activeCourse->id)
                ->where('term_to_course.active', '1')
                ->where('term_to_course.status', '1')
                ->select('term.id', 'term.name')
                ->first();

        $eventList = [];
        if (!empty($termList)) {
            $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                            ->join('event', 'event.id', '=', 'marking_group.event_id')
                            ->where('marking_group.course_id', $activeCourse->id)
                            ->where('marking_group.term_id', $termList->id)
                            ->where('ds_marking_group.ds_id', Auth::user()->id)
                            ->where('event.status', '1')
                            ->orderBy('event.event_code', 'asc')
                            ->pluck('event.event_code', 'event.id')
                            ->toArray();
        }


        return view('eventAssessmentMarking.index')->with(compact('activeTrainingYearInfo'
                                , 'activeCourse', 'termList', 'eventList'));
    }

    public function getSubEvent(Request $request) {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->join('sub_event', 'sub_event.id', '=', 'marking_group.sub_event_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('marking_group.event_id', $request->event_id)
                        ->where('ds_marking_group.ds_id', Auth::user()->id)
                        ->where('sub_event.status', '1')
                        ->orderBy('sub_event.event_code', 'asc')
                        ->pluck('sub_event.event_code', 'sub_event.id')
                        ->toArray();

        if (sizeof($subEventList) > 1) {
            $html = view('eventAssessmentMarking.getSubEvent', compact('subEventList'))->render();
            return response()->json(['html' => $html, 'autoSave' => 0]);
        } else {
            return $this->showMarkingCmList($request);
        }
    }

    public function getSubSubEvent(Request $request) {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->join('sub_sub_event', 'sub_sub_event.id', '=', 'marking_group.sub_sub_event_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('marking_group.event_id', $request->event_id)
                        ->where('marking_group.sub_event_id', $request->sub_event_id)
                        ->where('ds_marking_group.ds_id', Auth::user()->id)
                        ->where('sub_sub_event.status', '1')
                        ->orderBy('sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_event.event_code', 'sub_sub_event.id')
                        ->toArray();

        if (sizeof($subSubEventList) > 1) {
            $html = view('eventAssessmentMarking.getSubSubEvent', compact('subSubEventList'))->render();
            return response()->json(['html' => $html, 'autoSave' => 0]);
        } else {
            return $this->showMarkingCmList($request);
        }
    }

    public function getSubSubSubEvent(Request $request) {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                        ->join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'marking_group.sub_sub_sub_event_id')
                        ->where('marking_group.course_id', $request->course_id)
                        ->where('marking_group.term_id', $request->term_id)
                        ->where('marking_group.event_id', $request->event_id)
                        ->where('marking_group.sub_event_id', $request->sub_event_id)
                        ->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id)
                        ->where('ds_marking_group.ds_id', Auth::user()->id)
                        ->where('sub_sub_sub_event.status', '1')
                        ->orderBy('sub_sub_sub_event.event_code', 'asc')
                        ->pluck('sub_sub_sub_event.event_code', 'sub_sub_sub_event.id')
                        ->toArray();

        if (sizeof($subSubSubEventList) > 1) {
            $html = view('eventAssessmentMarking.getSubSubSubEvent', compact('subSubSubEventList'))->render();
            return response()->json(['html' => $html, 'autoSave' => 0]);
        } else {
            return $this->showMarkingCmList($request);
        }
    }

    public function showMarkingCmList(Request $request) {

        $cmDataList = $cmArr = $assingedMksWtInfo = $prevMksWtArr = [];

        if (!empty($request->sort_by) && is_numeric($request->sort_by)) {
            $cmDataList = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id)
                    ->where('marking_group.sub_event_id', $request->sort_by)
                    ->where('cm_marking_group.active', '1')
                    ->orderBy('event_group.order')
                    ->pluck('event_group.order', 'cm_marking_group.cm_id')
                    ->toArray();
        }
//        echo '<pre>';        print_r($requesmarking_groupt->all()); exit;
        $cmDataArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                ->join('ds_marking_group', 'ds_marking_group.marking_group_id', 'marking_group.id')
                ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                ->join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                ->where('cm_group.order', '<=', '2')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id)
                ->where('cm_marking_group.active', '1');
        if (!empty($request->sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cmDataArr = $cmDataArr->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $cmDataArr = $cmDataArr->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('cm_basic_profile.status', '1')
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                , 'cm_basic_profile.full_name', 'rank.code as rank_name');
        if (!empty($request->sort_by)) {
            if (($request->sort_by == 'official_name')) {
                $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
            } elseif ($request->sort_by == 'svc') {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            } elseif ($request->sort_by == 'svc_alpha') {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
            } elseif (($request->sort_by == 'syn')) {
                $cmDataArr = $cmDataArr->orderBy('cm_group.order', 'asc')
                        ->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            } else {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }
        } else {
            $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc');
        }
        $cmDataArr = $cmDataArr->get();
        $cmTempArr = [];
        if (!$cmDataArr->isEmpty()) {
            foreach ($cmDataArr as $cmData) {
                if (!empty($request->sort_by) && is_numeric($request->sort_by)) {
                    if (!empty($cmDataList) && array_key_exists($cmData->id, $cmDataList)) {
                        $cmTempArr[$cmDataList[$cmData->id]][$cmData->id] = $cmData->toArray();
                    } else {
                        $cmTempArr[1000][$cmData->id] = $cmData->toArray();
                    }
                } else {
                    $cmTempArr[0][$cmData->id] = $cmData->toArray();
                }
            }
        }

        if (!empty($cmTempArr)) {
            foreach ($cmTempArr as $order => $cmInfo) {
                foreach ($cmInfo as $cmId => $cmData) {
                    $cmArr[$cmId] = $cmData;
                }
            }
        }



        // Start:: sorting
        $numOfTotalCm = CmBasicProfile::where('course_id', $request->course_id)->count();
//        echo '<pre>';
//        print_r($numOfTotalCm);
//        exit;
        $groupWiseSortArr = [];
        $dsMarkingScpoe = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsMarkingScpoe = $dsMarkingScpoe->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsMarkingScpoe = $dsMarkingScpoe->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsMarkingScpoe = $dsMarkingScpoe->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $dsMarkingScpoe = $dsMarkingScpoe->where('ds_marking_group.ds_id', Auth::user()->id)
                ->select(DB::raw("COUNT(marking_group.id) as total"))
                ->first();


        if (sizeof($cmArr) == $numOfTotalCm || (!empty($dsMarkingScpoe->total) && $dsMarkingScpoe->total > 1)) {
            $groupWiseSortArr = MarkingGroup::join('sub_event', 'sub_event.id', 'marking_group.sub_event_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                if ((!empty($dsMarkingScpoe->total) && $dsMarkingScpoe->total > 1)) {
                    $groupWiseSortArr = $groupWiseSortArr->where('marking_group.sub_event_id', $request->sub_event_id);
                } else {
                    $groupWiseSortArr = $groupWiseSortArr->where('marking_group.sub_event_id', '<>', $request->sub_event_id);
                }
            }

            $groupWiseSortArr = $groupWiseSortArr->select(DB::raw("CONCAT('Grouping (', sub_event.event_code, ')') as sub_event_name"), 'marking_group.sub_event_id')
                    ->pluck('sub_event_name', 'marking_group.sub_event_id')
                    ->toArray();
        }

        $sortByList = ['svc' => __('label.WING'), 'official_name' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'syn' => __('label.SYN')] + $groupWiseSortArr;


//        echo '<pre>'; print_r($request->sort_by); exit;
        // End:: sorting



        $assignedMksWtModel = !empty($request->sub_sub_sub_event_id) ? 'SubSubSubEventMksWt' : (!empty($request->sub_sub_event_id) ? 'SubSubEventMksWt' : (!empty($request->sub_event_id) ? 'SubEventMksWt' : 'EventMksWt'));

        $namespacedModel = '\\App\\' . $assignedMksWtModel;
        $assingedMksWtInfo = $namespacedModel::where('course_id', $request->course_id)
                ->where('event_id', $request->event_id);

        if (!empty($request->sub_event_id)) {
            $assingedMksWtInfo = $assingedMksWtInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $assingedMksWtInfo = $assingedMksWtInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $assingedMksWtInfo = $assingedMksWtInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $assingedMksWtInfo = $assingedMksWtInfo->select('mks_limit', 'highest_mks_limit', 'lowest_mks_limit', 'wt')
                ->first();

// get previous data
        $prevMksWtDataArr = EventAssessmentMarking::leftJoin('grading_system', 'grading_system.id', 'event_assessment_marking.grade_id')
                ->where('event_assessment_marking.course_id', $request->course_id)
                ->where('event_assessment_marking.term_id', $request->term_id)
                ->where('event_assessment_marking.event_id', $request->event_id);

        if (!empty($request->sub_event_id)) {
            $prevMksWtDataArr = $prevMksWtDataArr->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevMksWtDataArr = $prevMksWtDataArr->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevMksWtDataArr = $prevMksWtDataArr->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        $prevMksWtDataArr = $prevMksWtDataArr->where('event_assessment_marking.updated_by', Auth::user()->id)
                ->select('event_assessment_marking.cm_id', 'event_assessment_marking.mks', 'event_assessment_marking.remarks'
                        , 'event_assessment_marking.wt', 'event_assessment_marking.percentage', 'grading_system.grade_name'
                        , 'grading_system.id as grade_id')
                ->get();

        $totalGivenMks = 0;
        if (!$prevMksWtDataArr->isEmpty()) {
            foreach ($prevMksWtDataArr as $prevMksWtData) {
                $prevMksWtArr[$prevMksWtData->cm_id] = $prevMksWtData->toArray();
                $totalGivenMks += (!empty($prevMksWtData->mks) ? $prevMksWtData->mks : 0);
            }
        }

        $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

// get lock info
        $eventAssessmentMarkingLockInfo = EventAssessmentMarkingLock::select('id', 'status')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('locked_by', Auth::user()->id)->first();
//        echo '<pre>';        print_r($eventAssessmentMarkingLockInfo); exit;
        // if has CI mod marking
        $ciModMarkingInfo = CiModerationMarking::select('id')
                ->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $ciModMarkingInfo = $ciModMarkingInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $ciModMarkingInfo = $ciModMarkingInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $ciModMarkingInfo = $ciModMarkingInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $ciModMarkingInfo = $ciModMarkingInfo->get();

// if has ds obsn marking
        $dsObsnMarkingInfo = DsObsnMarking::select('id')->where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)->whereNotNull('obsn_mks')
                ->get();

        $prevActDeactInfo = AssessmentActDeact::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)->where('criteria', '1')
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevActDeactInfo = $prevActDeactInfo->where('status', '1')->first();


        $autoSave = empty($eventAssessmentMarkingLockInfo) && !empty($assingedMksWtInfo) && !empty($cmArr) && !empty($prevActDeactInfo) ? 1 : 0;

        $html = view('eventAssessmentMarking.showMarkingCmList', compact('cmArr', 'assingedMksWtInfo', 'prevMksWtArr'
                        , 'gradeInfo', 'eventAssessmentMarkingLockInfo', 'ciModMarkingInfo', 'prevMksWtDataArr', 'sortByList'
                        , 'request', 'dsObsnMarkingInfo', 'prevActDeactInfo', 'totalGivenMks'))->render();
        return response()->json(['html' => $html, 'autoSave' => $autoSave]);
    }

    public function saveEventAssessmentMarking(Request $request) {
// Validation
        $rules = $message = $errors = [];
        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'event_id' => 'required|not_in:0',
        ];

        $lowestMks = $request->lowest_mks;
        $highestMks = $request->highest_mks;
        $cmName = $request->cm_name;
        $sum = 0;
        if (!empty($request->mks_wt)) {
            foreach ($request->mks_wt as $key => $mksWtInfo) {
//                if ($request->data_id == '2') {
//                    $rules['mks_wt.' . $key . '.mks'] = 'required';
//                    $message['mks_wt.' . $key . '.mks' . '.required'] = __('label.MKS_FIELD_IS_REQUIRED_FOR', ['CM_name' => $cmName[$key]]);
//                }

                if (!empty($mksWtInfo['mks'])) {
                    $rules['mks_wt.' . $key . '.mks'] = 'gte:' . $lowestMks . '|lte:' . $highestMks;
                    $message['mks_wt.' . $key . '.mks' . '.gte'] = __('label.GIVEN_MKS_MUST_BE_GREATER_THAN_OR_EQUAL_TO_MOD_MINUS_FOR_CM', ['cm_name' => $cmName[$key], 'mod_limit' => $lowestMks]);
                    $message['mks_wt.' . $key . '.mks' . '.lte'] = __('label.GIVEN_MKS_MUST_BE_LESS_THAN_OR_EQUAL_TO_MOD_PLUS_FOR_CM', ['cm_name' => $cmName[$key], 'mod_limit' => $highestMks]);
                } 

                $mks = !empty($mksWtInfo['mks']) ? $mksWtInfo['mks'] : 0;
                $sum += $mks;
            }
        }

        if ($request->data_id == '2' && $sum == 0) {
            $rules['common_rmks'] = 'required';
            $message['common_rmks.required'] = __('label.COMMON_RMKS_VALIDATION');
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $prevActDeactInfo = AssessmentActDeact::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)->where('criteria', '1')
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $prevActDeactInfo = $prevActDeactInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $prevActDeactInfo = $prevActDeactInfo->where('status', '1')->first();


        if (empty($prevActDeactInfo)) {
            $errors = __('label.ASSESSMENT_IS_DEACTIVATED');
        }
        if (!empty($request->auto_saving) && $request->auto_saving == 1 && $sum == 0) {
            $errors = __('label.PUT_MKS_FOR_ATLEAST_ONE_CM');
        }

        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 401);
        }
// End validation
        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;


        $data = [];
        $i = 0;
        if (!empty($request->mks_wt)) {
            foreach ($request->mks_wt as $cmId => $mksWtInfo) {
                $rmks = ($request->data_id == '2' && $sum == 0) ? $request->common_rmks : (!empty($mksWtInfo['remarks']) ? $mksWtInfo['remarks'] : '');
                $data[$i]['course_id'] = $request->course_id;
                $data[$i]['term_id'] = $request->term_id;
                $data[$i]['event_id'] = $request->event_id;
                $data[$i]['sub_event_id'] = $subEventId;
                $data[$i]['sub_sub_event_id'] = $subSubEventId;
                $data[$i]['sub_sub_sub_event_id'] = $subSubSubEventId;
                $data[$i]['cm_id'] = $cmId ?? 0;
                $data[$i]['mks'] = $mksWtInfo['mks'] ?? null;
                $data[$i]['wt'] = $mksWtInfo['wt'] ?? null;
                $data[$i]['percentage'] = $mksWtInfo['percent'] ?? null;
                $data[$i]['grade_id'] = $mksWtInfo['grade_id'] ?? 0;
                $data[$i]['remarks'] = $rmks;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        $loadData['course_id'] = $request->course_id;
        $loadData['term_id'] = $request->term_id;
        $loadData['event_id'] = $request->event_id;
        $loadData['sub_event_id'] = $subEventId;
        $loadData['sub_sub_event_id'] = $subSubEventId;
        $loadData['sub_sub_sub_event_id'] = $subSubSubEventId;
// Save data

        DB::beginTransaction();

        try {
            EventAssessmentMarking::where('course_id', $request->course_id)
                    ->where('term_id', $request->term_id)
                    ->where('event_id', $request->event_id)
                    ->where('sub_event_id', $subEventId)
                    ->where('sub_sub_event_id', $subSubEventId)
                    ->where('sub_sub_sub_event_id', $subSubSubEventId)
                    ->where('updated_by', Auth::user()->id)
                    ->delete();
            if (EventAssessmentMarking::insert($data)) {
                $successMsg = __('label.EVENT_ASSESSMENT_HAS_BEEN_ASSIGNED_SUCCESSFULLY');
                $errorMsg = __('label.EVENT_ASSESSMENT_CUOLD_NOT_BE_ASSIGNED');

                if ($request->data_id == '2') {
                    $target = new EventAssessmentMarkingLock;

                    $target->course_id = $request->course_id;
                    $target->term_id = $request->term_id;
                    $target->event_id = $request->event_id;
                    $target->sub_event_id = $subEventId;
                    $target->sub_sub_event_id = $subSubEventId;
                    $target->sub_sub_sub_event_id = $subSubSubEventId;
                    $target->status = 1;
                    $target->locked_at = date('Y-m-d H:i:s');
                    $target->locked_by = Auth::user()->id;
                    $target->save();

                    $successMsg = __('label.EVENT_ASSESSMENT_HAS_BEEN_ASSIGNED_AND_LOCKED_SUCCESSFULLY');
                    $errorMsg = __('label.EVENT_ASSESSMENT_COULD_NOT_BE_ASSIGNED_AND_LOCKED');
                }
            }
            DB::commit();
            return Response::json(['success' => true, 'message' => $successMsg, 'loadData' => $loadData], 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(['success' => false, 'message' => $errorMsg], 401);
        }
    }

    public function getRequestForUnlockModal(Request $request) {
        $view = view('eventAssessmentMarking.showRequestForUnlockModal')->render();
        return response()->json(['html' => $view]);
    }

    public function saveRequestForUnlock(Request $request) {

// validation
        $rules = [
            'unlock_message' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
// End validation
        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;
// get lock info
        $eventAssessmentMarkingLockInfo = EventAssessmentMarkingLock::select('id')
                        ->where('course_id', $request->course_id)
                        ->where('term_id', $request->term_id)
                        ->where('event_id', $request->event_id)
                        ->where('sub_event_id', $subEventId)
                        ->where('sub_sub_event_id', $subSubEventId)
                        ->where('sub_sub_sub_event_id', $subSubSubEventId)
                        ->where('locked_by', Auth::user()->id)->first();

        $loadData['course_id'] = $request->course_id;
        $loadData['term_id'] = $request->term_id;
        $loadData['event_id'] = $request->event_id;
        $loadData['sub_event_id'] = $subEventId;
        $loadData['sub_sub_event_id'] = $subSubEventId;
        $loadData['sub_sub_sub_event_id'] = $subSubSubEventId;

        if (!empty($eventAssessmentMarkingLockInfo)) {
            $target = EventAssessmentMarkingLock::where('id', $eventAssessmentMarkingLockInfo->id)
                    ->update(['status' => '2', 'unlock_message' => $request->unlock_message]);
            if ($target) {
                return Response::json(['success' => true, 'loadData' => $loadData], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.REQUEST_FOR_UNLOCK_COULD_NOT_BE_SENT_TO_CI')), 401);
            }
        }
    }

    public function deleteEventAssessmentMarking(Request $request) {


        $subEventId = !empty($request->sub_event_id) ? $request->sub_event_id : 0;
        $subSubEventId = !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0;
        $subSubSubEventId = !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0;

        $target = EventAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id)
                ->where('sub_event_id', $subEventId)
                ->where('sub_sub_event_id', $subSubEventId)
                ->where('sub_sub_sub_event_id', $subSubSubEventId)
                ->where('updated_by', Auth::user()->id)
                ->delete();
//        echo '<pre>';
//        print_r($target->toArray());
//        exit;
        $loadData['course_id'] = $request->course_id;
        $loadData['term_id'] = $request->term_id;
        $loadData['event_id'] = $request->event_id;
        $loadData['sub_event_id'] = $subEventId;
        $loadData['sub_sub_event_id'] = $subSubEventId;
        $loadData['sub_sub_sub_event_id'] = $subSubSubEventId;

        if ($target) {
            return Response::json(['success' => true, 'loadData' => $loadData, 'message' => __('label.DELETE_EVENT_ASSESSMENT_MARKING_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.DELETE_EVENT_ASSESSMENT_MARKING_UNSUCCESSFUL')), 401);
        }
    }

}
