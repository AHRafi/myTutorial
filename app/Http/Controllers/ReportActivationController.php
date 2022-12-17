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

class ReportActivationController extends Controller {

    public function index(Request $request) {
        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')];
        $closedTrainingYear = TrainingYear::where('status', '2')->orderBy('start_date', 'desc')
                        ->select('name', 'id')->first();

        $activeTrainingYear = TrainingYear::where('status', '1')->select('name', 'id')->first();

        if (!empty($activeTrainingYear)) {
            $trainingYearList[$activeTrainingYear->id] = $activeTrainingYear->name;
        }
        if (!empty($closedTrainingYear)) {
            $trainingYearList[$closedTrainingYear->id] = $closedTrainingYear->name;
        }


        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        // check all terms are closed


        return view('crSetup.reportActivation.index')->with(compact('trainingYearList', 'courseList'));
    }

    public function setStat(Request $request) {
//        echo '<pre>';
//        print_r($request->all());
//        exit;
        $courseId = $request->course_id;
        $termId = !empty($request->term_id) ? $request->term_id : 0;
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

    public function getCourse(Request $request) {

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $html = view('crSetup.reportActivation.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getActDeactBtn(Request $request) {

        //        assessmentActDeact
        $qpArray = $request->all();

        $assessmentActDeactInfo = AssessmentActDeact::where('course_id', $request->course_id)
                ->where('term_id', 0)->where('status', '1')
                ->where('criteria', '4')
                ->select('criteria', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', 'status')
                ->get();
        $assessmentActDeactArr = [];
        if (!$assessmentActDeactInfo->isEmpty()) {
            foreach ($assessmentActDeactInfo as $info) {
                $assessmentActDeactArr[$info->criteria][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id] = $info->status;
            }
        }
        
        

        $html = view('crSetup.reportActivation.actDeact', compact('assessmentActDeactArr'))->render();
        return Response::json(['html' => $html]);
    }

    
}
