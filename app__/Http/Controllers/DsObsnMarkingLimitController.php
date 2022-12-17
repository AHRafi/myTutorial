<?php

namespace App\Http\Controllers;

use App\Course;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\TrainingYear;
use App\TermToCourse;
use App\CriteriaWiseWt;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class DsObsnMarkingLimitController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.DS_OBSN_MARKING_LIMIT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id', 'event_mks_limit')->first();

        if (empty($courseList)) {
            $void['header'] = __('label.CRITERIA_WISE_WT_DISTRIBUTION');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $criteriaWiseWt = CriteriaWiseWt::select('ds_obsn_wt')->where('course_id', $courseList->id)->first();

        $termArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name', 'term.id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->orderBy('term.order', 'asc')->get();

        $prevDataInfo = DsObsnMarkingLimit::select('mks_limit', 'obsn', 'limit_percent', 'term_id')
                ->where('course_id', $courseList->id)
                ->get();

        $total = 0;
        $prevDataArr = [];
        if (!$prevDataInfo->isEmpty()) {
            foreach ($prevDataInfo as $inf) {
                $prevDataArr[$inf->term_id] = $inf->toArray();
                $total += $inf->obsn;
            }
        }
        
        $dsObsnDataArr = DsObsnMarking::where('course_id', $courseList->id)
                        ->whereNotNull('obsn_mks')->pluck('term_id', 'term_id')->toArray();


        return view('dsObsnMarkingLimit.index')->with(compact('activeTrainingYearInfo', 'courseList'
                                , 'request', 'prevDataArr', 'termArr', 'criteriaWiseWt', 'total', 'dsObsnDataArr'));
    }

    public function saveMarkingLimit(Request $request) {

        $dsObsn = $request->ds_obsn;
        $totalWt = $request->total_wt;
        $totalDsObsnWt = $request->total_ds_obsn_wt;
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
        $messages = $errors = [];

        if (!empty($dsObsn)) {
            foreach ($dsObsn as $termId => $inf) {
                $rules['ds_obsn.' . $termId . '.mks_limit'] = 'required';
                $messages['ds_obsn.' . $termId . '.mks_limit.required'] = __('label.DS_OBSN_MKS_IS_REQUIRED_FOR_TERM', ['term' => $inf['term_name'] ?? '']);
                $rules['ds_obsn.' . $termId . '.limit_percent'] = 'required';
                $messages['ds_obsn.' . $termId . '.limit_percent.required'] = __('label.DS_OBSN_MARKING_LIMIT_IS_REQUIRED_FOR_TERM', ['term' => $inf['term_name'] ?? '']);
                $rules['ds_obsn.' . $termId . '.obsn'] = 'required';
                $messages['ds_obsn.' . $termId . '.obsn.required'] = __('label.DS_OBSN_WT_IS_REQUIRED_FOR_TERM', ['term' => $inf['term_name'] ?? '']);
            }
        }
        if($totalDsObsnWt != $totalWt){
            $errors[] = __('label.THE_TOTAL_WT_MUST_BE_EQUAL_TO', ['total_event_wt' => $totalDsObsnWt]);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        if (!empty($errors)) {
            return Response::json(array('success' => false, 'message' => $errors), 400);
        }


        $target = [];
        $i = 0;

        if (!empty($dsObsn)) {
            foreach ($dsObsn as $termId => $inf) {
                $target[$i]['course_id'] = $request->course_id;
                $target[$i]['term_id'] = $termId;
                $target[$i]['mks_limit'] = $inf['mks_limit'];
                $target[$i]['limit_percent'] = $inf['limit_percent'];
                $target[$i]['obsn'] = $inf['obsn'];
                $target[$i]['updated_at'] = date('Y-m-d H:i:s');
                $target[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        DsObsnMarkingLimit::where('course_id', $request->course_id)
                ->delete();

        if (DsObsnMarkingLimit::insert($target)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.DS_OBSN_MARKING_LIMIT_COULD_NOT_ASSIGNED')), 401);
        }
    }
    

}
