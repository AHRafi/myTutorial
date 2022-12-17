<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Session;
use Redirect;
use Helper;
use Response;
use Auth;
use DB;
use App\MutualAssessmentMarkingLock;
use App\Course;
use App\TrainingYear;
use App\TermToCourse;
use App\MaProcess;
use Common;

class UnlockMaRequestController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {

        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (!empty($activeTrainingYearInfo)) {
            $activeTrainingYearInfo = $activeTrainingYearInfo->toArray();
        }

        $void = [];
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.MA_UNLOCK_REQUEST');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('unlockMaRequest.index', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo['id'])
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (!empty($activeCourse)) {
            $activeCourse = $activeCourse->toArray();
        }

        if (empty($activeCourse)) {
            $void['header'] = __('label.MA_UNLOCK_REQUEST');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('unlockMaRequest.index', compact('void'));
        }

        $courseId = $activeCourse['id'];
        $activeTerm = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name as name', 'term.id as id')
                        ->where('term_to_course.course_id', $courseId)
                        ->where('term_to_course.status', '1')
                        ->where('term_to_course.active', '1')
                        ->orderBy('term.order', 'asc')
                        ->first();

        if (empty($activeTerm)) {
            $void['header'] = __('label.MA_UNLOCK_REQUEST');
            $void['body'] = __('label.NO_ACTIVE_TERM_FOUND_IN_COURSE_OF_TRAINING_TEAR', ['course' => $activeCourse['name'], 'training_year' => $activeTrainingYearInfo['name']]);
            return view('unlockMaRequest.index', compact('void'));
        }

        $maProcessArr = MaProcess::where('term_id', $activeTerm->id)
                        ->pluck('process', 'course_id')->toArray();


        $targetArr = MutualAssessmentMarkingLock::join('course', 'course.id', '=', 'mutual_assessment_marking_lock.course_id')
                        ->join('term', 'term.id', '=', 'mutual_assessment_marking_lock.term_id')
                        ->join('cm_basic_profile', 'cm_basic_profile.id', '=', 'mutual_assessment_marking_lock.locked_by')
                        ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->leftJoin('event', 'event.id', '=', 'mutual_assessment_marking_lock.event_id')
                        ->leftJoin('sub_event', 'sub_event.id', '=', 'mutual_assessment_marking_lock.sub_event_id')
                        ->leftJoin('sub_sub_event', 'sub_sub_event.id', '=', 'mutual_assessment_marking_lock.sub_sub_event_id')
                        ->leftJoin('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'mutual_assessment_marking_lock.sub_sub_sub_event_id')
                        ->leftJoin('syndicate', 'syndicate.id', '=', 'mutual_assessment_marking_lock.syndicate_id')
                        ->leftJoin('sub_syndicate', 'sub_syndicate.id', '=', 'mutual_assessment_marking_lock.sub_syndicate_id')
                        ->leftJoin('event_group', 'event_group.id', '=', 'mutual_assessment_marking_lock.event_group_id')
                        ->select('mutual_assessment_marking_lock.id as id', 'course.id as course_id', 'course.name as course_name', 'event.event_code as event_code', 'sub_event.event_code as sub_event_code'
                                , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_sub_event.event_code as sub_sub_sub_event_code'
                                , 'syndicate.name as syndicate_name', 'sub_syndicate.name as sub_syndicate_name'
                                , 'event_group.name as event_group_name', 'mutual_assessment_marking_lock.unlock_message as unlock_message'
                                , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as requested_by"))
                        ->orderBy('mutual_assessment_marking_lock.id', 'ASC')
                        ->where('mutual_assessment_marking_lock.lock_status', '2')
                        ->where('mutual_assessment_marking_lock.term_id', !empty($activeTerm->id) ? $activeTerm->id : 0)
                        ->where('mutual_assessment_marking_lock.course_id', !empty($courseId) ? $courseId : 0)->get();


        return view('unlockMaRequest.index', compact('targetArr', 'void', 'maProcessArr'));
    }

    public function acceptMaUnlockRequest(Request $request) {
        $target = MutualAssessmentMarkingLock::find($request->id);

        if (!empty($target)) {
            $target->delete();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }

    public function denyMaUnlockRequest(Request $request) {
        $target = MutualAssessmentMarkingLock::find($request->id);
        if (!empty($target)) {
            $target->lock_status = '1';
            $target->save();
            return response()->json(['success' => true, 'message' => ''], 200);
        }
    }

}
